<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\Activation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GenealogyTree extends Component
{
    use HasTabSkeleton;

    /**
     * Excel supports 16,384 columns.
     * Each leaf occupies 6 columns.
     */
    private const MAX_EXPORT_LEAVES = 2500;

    /**
     * Prevent extremely large exports.
     */
    private const MAX_EXPORT_NODES = 10000;

    /**
     * Maximum export execution time.
     */
    private const EXPORT_TIMEOUT = 300;

    public int $rootUserId;

    public string $activeTab = 'invitee';

    public string $viewMode = 'generation';

    /**
     * Name search filter.
     */
    public string $search = '';

    /**
     * Initialize genealogy root.
     */
    public function mount(?int $userId = null): void
    {
        $this->rootUserId = $userId ?? Auth::id();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['generation', 'tree'], true)) {
            $this->viewMode = $mode;
        }
    }

    /**
     * Change active tab.
     */
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /*
    |--------------------------------------------------------------------------
    | Genealogy Tree Builder
    |--------------------------------------------------------------------------
    |
    | Instead of querying the database for every individual member,
    | this implementation retrieves downlines by generation.
    |
    | This significantly reduces database queries.
    |
    */

    public function buildTree(
        int $userId,
        array $ancestors = [],
        int $level = 1
    ): ?array {

        $rootUser = User::find($userId);

        if (! $rootUser || empty($rootUser->referralCode)) {
            return null;
        }

        $users = [
            $rootUser->UserID => $rootUser,
        ];

        $childrenByReferral = [];

        $visited = array_fill_keys(
            array_map('intval', $ancestors),
            true
        );

        if (isset($visited[$rootUser->UserID])) {
            return null;
        }

        $visited[$rootUser->UserID] = true;

        /*
         * Current generation's referral codes.
         */
        $frontier = [
            $rootUser->referralCode,
        ];

        /*
         * Load all descendants generation by generation.
         */
        while (! empty($frontier)) {

            $frontier = array_values(array_unique(
                array_filter($frontier)
            ));

            if (empty($frontier)) {
                break;
            }

            $activations = Activation::query()
                ->whereIn('upline_code', $frontier)
                ->whereIn('status', ['Approved', 'Pending'])
                ->with('user')
                ->get();

            $nextFrontier = [];

            foreach ($activations as $activation) {

                $childUser = $activation->user;

                if (! $childUser || empty($childUser->referralCode)) {
                    continue;
                }

                $childId = (int) $childUser->UserID;

                /*
                 * Prevent circular relationships and duplicate nodes.
                 */
                if (isset($visited[$childId])) {
                    continue;
                }

                $visited[$childId] = true;

                $users[$childId] = $childUser;

                $parentCode = $activation->upline_code;

                $childrenByReferral[$parentCode][] = $childId;

                $nextFrontier[] = $childUser->referralCode;
            }

            $frontier = $nextFrontier;
        }

        /*
         * Construct the actual nested tree in memory.
         */
        return $this->buildTreeNode(
            $rootUser,
            $users,
            $childrenByReferral,
            $level
        );
    }

    /**
     * Recursively construct nodes using already-loaded records.
     *
     * No database queries are executed here.
     */
    private function buildTreeNode(
        User $user,
        array &$users,
        array &$childrenByReferral,
        int $level
    ): array {

        $children = [];

        $referralCode = $user->referralCode;

        foreach ($childrenByReferral[$referralCode] ?? [] as $childId) {

            if (! isset($users[$childId])) {
                continue;
            }

            $child = $this->buildTreeNode(
                $users[$childId],
                $users,
                $childrenByReferral,
                $level + 1
            );

            $children[] = $child;
        }

        return [
            'id' => $user->UserID,

            'name' => trim(
                $user->firstname . ' ' . $user->lastname
            ),

            'referral_code' => $user->referralCode,

            'status' => $user->status,

            'level' => $level,

            'direct_count' => count($children),

            'downlines' => $children,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Member Navigation
    |--------------------------------------------------------------------------
    */

    /**
     * Focus / explore a specific member's genealogy.
     */
    public function focusUser(int $userId): void
    {
        $this->rootUserId = $userId;
    }

    public function updatingSearch(): void
    {
        // Search is applied to the existing genealogy structure.
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Current Tree
    |--------------------------------------------------------------------------
    */

    private function currentTree(): ?array
    {
        return $this->filterTree(
            $this->buildTree($this->rootUserId),
            mb_strtolower(trim($this->search))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Name Search Filter
    |--------------------------------------------------------------------------
    |
    | Matching nodes retain their entire subtree.
    | Non-matching parents remain visible when a descendant matches.
    |
    */

    private function filterTree(?array $node, string $term): ?array
    {
        if ($node === null) {
            return null;
        }

        $selfMatches = $term === '' || str_contains(
            mb_strtolower($node['name']),
            $term
        );

        if ($selfMatches) {

            $node['highlighted'] = true;

            return $node;
        }

        $children = [];

        foreach ($node['downlines'] ?? [] as $child) {

            $filteredChild = $this->filterTree(
                $child,
                $term
            );

            if ($filteredChild !== null) {
                $children[] = $filteredChild;
            }
        }

        if (empty($children)) {
            return null;
        }

        $node['downlines'] = $children;

        $node['direct_count'] = count($children);

        $node['highlighted'] = false;

        return $node;
    }

    /*
    |--------------------------------------------------------------------------
    | Excel Export - Visual Genealogy Tree
    |--------------------------------------------------------------------------
    */

    public function exportTreeExcel(): StreamedResponse
    {
        /*
         * Increase PHP execution time for this operation.
         */
        @set_time_limit(self::EXPORT_TIMEOUT);

        @ini_set(
            'max_execution_time',
            (string) self::EXPORT_TIMEOUT
        );

        @ini_set('memory_limit', '1024M');

        /*
         * Build genealogy.
         */
        $tree = $this->currentTree();

        abort_unless($tree, 404);

        /*
         * Validate total number of nodes.
         */
        abort_if(
            $this->countNodes($tree) > self::MAX_EXPORT_NODES,
            422,
            'This genealogy network is too large to export to Excel. Please narrow your search.'
        );

        /*
         * Validate leaf count.
         */
        abort_if(
            $this->countLeaves($tree) > self::MAX_EXPORT_LEAVES,
            422,
            'This genealogy network exceeds the Excel column limit. Please narrow your search.'
        );

        /*
         * Create spreadsheet.
         */
        $book = new Spreadsheet();

        $ws = $book->getActiveSheet();

        $ws->setTitle('Visual Tree');

        $ws->setShowGridlines(false);

        $ws->getDefaultColumnDimension()->setWidth(6);

        /*
         * Header.
         */
        $ws->setCellValue(
            'A1',
            'Genealogy Network - ' . $tree['name']
        );

        $ws->setCellValue(
            'A2',
            'Generated ' . now()->format('M d, Y h:i A')
        );

        $ws->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(14);

        /*
         * Reusable Excel styles.
         */
        $styles = $this->getExportStyles();

        $leaf = 0;

        $configuredRows = [];

        /*
         * Draw genealogy.
         */
        $this->drawNode(
            $ws,
            $tree,
            0,
            $leaf,
            $styles,
            $configuredRows
        );

        /*
         * Page configuration.
         */
        $ws->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        /*
         * Page margins.
         */
        $ws->getPageMargins()
            ->setTop(0.25)
            ->setBottom(0.25)
            ->setLeft(0.25)
            ->setRight(0.25);

        $file = 'genealogy-tree-' .
            Str::slug($tree['name']) .
            '.xlsx';

        /*
         * Stream the Excel file.
         */
        return response()->streamDownload(
            function () use ($book) {

                $writer = new Xlsx($book);

                /*
                 * Formula calculations are unnecessary.
                 */
                $writer->setPreCalculateFormulas(false);

                $writer->save('php://output');

                /*
                 * Release spreadsheet resources.
                 */
                $book->disconnectWorksheets();

                unset($writer, $book);
            },
            $file,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Excel Styles
    |--------------------------------------------------------------------------
    */

    private function getExportStyles(): array
    {
        $base = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],

            'font' => [
                'size' => 9,
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
        ];

        $normal = $base;

        $normal['borders'] = [
            'allBorders' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => '93C5FD',
                ],
            ],
        ];

        $active = $base;

        $active['borders'] = [
            'allBorders' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => '2563EB',
                ],
            ],
        ];

        $highlighted = $base;

        $highlighted['fill']['color']['rgb'] = 'FEF3C7';

        $highlighted['borders'] = [
            'allBorders' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => 'D4A017',
                ],
            ],
        ];

        return [
            'normal' => $normal,
            'active' => $active,
            'highlighted' => $highlighted,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Count Genealogy Members
    |--------------------------------------------------------------------------
    */

    private function countNodes(array $node): int
    {
        $count = 1;

        foreach ($node['downlines'] ?? [] as $child) {
            $count += $this->countNodes($child);
        }

        return $count;
    }

    private function countLeaves(array $node): int
    {
        $children = $node['downlines'] ?? [];

        if (empty($children)) {
            return 1;
        }

        $count = 0;

        foreach ($children as $child) {
            $count += $this->countLeaves($child);
        }

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | Draw Excel Genealogy Node
    |--------------------------------------------------------------------------
    |
    | Each leaf occupies six columns.
    | Each member node occupies four merged columns.
    |
    */

    private function drawNode(
        Worksheet $ws,
        array $node,
        int $depth,
        int &$leaf,
        array &$styles,
        array &$configuredRows
    ): int {

        $row = 4 + ($depth * 3);

        $kids = $node['downlines'] ?? [];

        $centers = [];

        /*
         * Draw children first.
         */
        foreach ($kids as $kid) {

            $centers[] = $this->drawNode(
                $ws,
                $kid,
                $depth + 1,
                $leaf,
                $styles,
                $configuredRows
            );
        }

        /*
         * Calculate node center.
         */
        if (! empty($centers)) {

            $center = intdiv(
                $centers[0] + end($centers),
                2
            );

        } else {

            $center = (6 * $leaf++) + 3;
        }

        /*
         * Node box coordinates.
         */
        $from = Coordinate::stringFromColumnIndex(
            $center - 1
        ) . $row;

        $to = Coordinate::stringFromColumnIndex(
            $center + 2
        ) . $row;

        /*
         * Merge node cells.
         */
        $ws->mergeCells("{$from}:{$to}");

        /*
         * Member information.
         */
        $ws->setCellValue(
            $from,
            $node['name'] . "\nRef: " .
                ($node['referral_code'] ?? 'N/A')
        );

        /*
         * Select reusable style.
         */
        $highlighted = $node['highlighted'] ?? false;

        $active = ($node['status'] ?? '') === 'Active';

        if ($highlighted) {

            $style = $styles['highlighted'];

        } elseif ($active) {

            $style = $styles['active'];

        } else {

            $style = $styles['normal'];
        }

        /*
         * Apply member style.
         */
        $ws->getStyle("{$from}:{$to}")
            ->applyFromArray($style);

        /*
         * Configure row heights only once per row.
         */
        if (! isset($configuredRows[$row])) {

            $ws->getRowDimension($row)->setRowHeight(32);

            $ws->getRowDimension($row + 1)->setRowHeight(10);

            $ws->getRowDimension($row + 2)->setRowHeight(10);

            $configuredRows[$row] = true;
        }

        /*
         * Draw connectors.
         */
        if (! empty($centers)) {

            /*
             * Parent vertical stem.
             */
            $this->edge(
                $ws,
                $center,
                $row + 1,
                'left'
            );

            /*
             * Child vertical drops.
             */
            foreach ($centers as $childCenter) {

                $this->edge(
                    $ws,
                    $childCenter,
                    $row + 2,
                    'left'
                );
            }

            /*
             * Horizontal connector bus.
             *
             * Apply one border to the entire range instead
             * of formatting each individual cell.
             */
            $firstColumn = Coordinate::stringFromColumnIndex(
                $centers[0] + 1
            );

            $lastColumn = Coordinate::stringFromColumnIndex(
                end($centers)
            );

            $busRange = "{$firstColumn}" . ($row + 2) .
                ":{$lastColumn}" . ($row + 2);

            $ws->getStyle($busRange)
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_MEDIUM)
                ->getColor()
                ->setRGB('94A3B8');
        }

        return $center;
    }

    /*
    |--------------------------------------------------------------------------
    | Excel Connector Borders
    |--------------------------------------------------------------------------
    */

    private function edge(
        Worksheet $ws,
        int $col0,
        int $row,
        string $side
    ): void {

        $column = Coordinate::stringFromColumnIndex(
            $col0 + 1
        );

        $borders = $ws
            ->getStyle($column . $row)
            ->getBorders();

        $border = $side === 'top'
            ? $borders->getTop()
            : $borders->getLeft();

        $border
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()
            ->setRGB('94A3B8');
    }

    /*
    |--------------------------------------------------------------------------
    | Render Component
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        if ($this->activeTab === 'board-member') {

            return view(
                'livewire.member-pages.genealogy.board-members'
            );
        }

        return view(
            'livewire.member-pages.genealogy.invitee',
            [
                'tree' => $this->currentTree(),
                'search' => $this->search,
            ]
        );
    }
}
