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

    public function mount(?int $userId = null)
    {
        $this->rootUserId = $userId ?? Auth::id();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function buildTree($userId, int $depth = 1, int $maxDepth = 3, array $ancestors = []): ?array
    {
        // Prevent infinite loops and exceeding max depth
        if ($depth > $maxDepth || in_array($userId, $ancestors)) {
            return null;
        }

        $user = User::find($userId);

        if (!$user || empty($user->referralCode)) {
            return null;
        }

        // Add current user to tracking array to prevent circular references
        $ancestors[] = $user->UserID;

        $children = [];

        // Fetch all downlines whose upline_code matches this user's referral code
        $downlines = Activation::query()
            ->where('upline_code', $user->referralCode)
            ->whereIn('status', ['Approved', 'Pending'])
            ->with('user')
            ->get();

        foreach ($downlines as $activation) {
            $childUser = $activation->user;

            if (!$childUser || in_array($childUser->UserID, $ancestors)) {
                continue;
            }

            // Recursively build the subtree for each downline user
            $subTree = $this->buildTree(
                $childUser->UserID,
                $depth + 1,
                $maxDepth,
                $ancestors
            );

            if ($subTree) {
                $children[] = $subTree;
            }
        }

        return [
            'id' => $user->UserID,
            'name' => trim($user->firstname . ' ' . $user->lastname),
            'referral_code' => $user->referralCode,
            'status' => $user->status,
            'downlines' => $children,
        ];
    }

    public function focusUser($userId)
    {
        $this->rootUserId = $userId;
    }

    public function render()
    {
        // You can adjust maxDepth here (e.g., 3 or 5 levels deep)
        $treeData = $this->buildTree($this->rootUserId, 1, 3);

        if ($this->activeTab === 'board-member') {
            return view('livewire.member-pages.genealogy.board-members');
        }

        return view('livewire.member-pages.genealogy.invitee', [
            'tree' => $treeData,
        ]);
    }
}
