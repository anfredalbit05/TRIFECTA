<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\Activation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GenealogyTree extends Component
{
    public int $rootUserId;

    public string $activeTab = 'invitee';

    public string $viewMode = 'generation'; // Default view mode

    /**
     * Name search filter — blanks out non-matching member cards in the
     * generation/tree views (matching cards stay in their real position).
     */
    public string $search = '';

    /**
     * Initialize genealogy root.
     */
    public function mount(?int $userId = null)
    {
        $this->rootUserId = $userId ?? Auth::id();
    }

    public function setViewMode(string $mode)
    {
        if (in_array($mode, ['generation', 'tree'])) {
            $this->viewMode = $mode;
        }
    }

    /**
     * Change active tab.
     */
    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Build the complete genealogy tree recursively.
     */
    public function buildTree(
        int $userId,
        array $ancestors = [],
        int $level = 1
    ): ?array {

        /**
         * Prevent circular / infinite loops.
         */
        if (in_array($userId, $ancestors, true)) {
            return null;
        }

        /**
         * Get current user.
         */
        $user = User::find($userId);

        if (! $user || empty($user->referralCode)) {
            return null;
        }

        /**
         * Add current user to visited ancestors.
         */
        $ancestors[] = $user->UserID;

        /**
         * Store child nodes.
         */
        $children = [];

        /**
         * Get all direct downlines.
         */
        $downlines = Activation::query()
            ->where('upline_code', $user->referralCode)
            ->whereIn('status', ['Approved', 'Pending'])
            ->with('user')
            ->get();

        /**
         * Build each child's subtree.
         */
        foreach ($downlines as $activation) {

            $childUser = $activation->user;

            if (! $childUser) {
                continue;
            }

            $subTree = $this->buildTree(
                $childUser->UserID,
                $ancestors,
                $level + 1
            );

            if ($subTree) {
                $children[] = $subTree;
            }
        }

        /**
         * Return genealogy node.
         */
        return [
            'id' => $user->UserID,

            'name' => trim(
                $user->firstname.' '.$user->lastname
            ),

            'referral_code' => $user->referralCode,

            'status' => $user->status,

            // Generation level
            'level' => $level,

            // Number of direct downlines
            'direct_count' => count($children),

            // Child nodes
            'downlines' => $children,
        ];
    }

    /**
     * Focus / explore a specific member's genealogy.
     */
    public function focusUser(int $userId)
    {
        $this->rootUserId = $userId;
    }

    public function updatingSearch()
    {
        // Rebuild from the top when the filter changes
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    /**
    |--------------------------------------------------------------------------
    | Name Search Filter
    |
    | Recursively removes nodes whose name does not match the search term.
    | When a node ITSELF matches, the node AND its entire downline subtree
    | are kept intact — so searching a member shows that member plus every
    | downline beneath them. A non-matching node is still kept when some
    | descendant matches (so the matching branch stays reachable through
    | its ancestors). Matching nodes are flagged "highlighted" for UI.
    |--------------------------------------------------------------------------
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

        // The matched member keeps their WHOLE downline subtree.
        if ($selfMatches) {
            $node['highlighted'] = true;

            return $node;
        }

        $children = [];

        foreach ($node['downlines'] ?? [] as $child) {
            $filteredChild = $this->filterTree($child, $term);

            if ($filteredChild !== null) {
                $children[] = $filteredChild;
            }
        }

        // Non-matching node survives only as an ancestor of a match.
        if (empty($children)) {
            return null;
        }

        $node['downlines'] = $children;
        $node['direct_count'] = count($children);
        $node['highlighted'] = false;

        return $node;
    }

    /**
     * Render component.
     */
    public function render()
    {
        $treeData = $this->buildTree(
            $this->rootUserId
        );

        // Apply the name search filter (no-op when the term is blank)
        $treeData = $this->filterTree($treeData, mb_strtolower(trim($this->search)));

        if ($this->activeTab === 'board-member') {

            return view(
                'livewire.member-pages.genealogy.board-members'
            );

        }

        return view(
            'livewire.member-pages.genealogy.invitee',
            [
                'tree' => $treeData,
                'search' => $this->search,
            ]
        );
    }
}
