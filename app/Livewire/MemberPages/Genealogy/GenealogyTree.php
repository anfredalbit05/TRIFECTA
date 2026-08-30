<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\User;
use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GenealogyTree extends Component
{
    public int $rootUserId;

    public string $activeTab = 'invitee';
    public string $viewMode = 'generation'; // Default view mode


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

        if (!$user || empty($user->referralCode)) {
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

            if (!$childUser) {
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
                $user->firstname . ' ' . $user->lastname
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


    /**
     * Render component.
     */
    public function render()
    {
        $treeData = $this->buildTree(
            $this->rootUserId
        );


        if ($this->activeTab === 'board-member') {

            return view(
                'livewire.member-pages.genealogy.board-members'
            );

        }


        return view(
            'livewire.member-pages.genealogy.invitee',
            [
                'tree' => $treeData,
            ]
        );
    }
}
