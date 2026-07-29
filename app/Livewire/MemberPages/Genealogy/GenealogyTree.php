<?php

namespace App\Livewire\MemberPages\Genealogy;

use App\Models\User;
use App\Models\Activation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

   public function buildTree($userId, $depth = 1, $maxDepth = 2, $ancestors = [])
{
    if ($depth > $maxDepth || in_array($userId, $ancestors)) {
        return null;
    }

    $user = User::find($userId);

    if (!$user) {
        return null;
    }

    $ancestors[] = $user->UserID;

    $children = [];

    $downlines = Activation::query()
        ->where('upline_code', $user->referralCode)
        ->whereIn('status', ['Approved', 'Pending'])
        ->with('user')
        ->get();

    foreach ($downlines as $activation) {

        $child = $activation->user;

        if (!$child) {
            continue;
        }

        if (in_array($child->UserID, $ancestors)) {
            continue;
        }

        $children[] = $this->buildTree(
            $child->UserID,
            $depth + 1,
            $maxDepth,
            $ancestors
        );
    }

    return [
        'id' => $user->UserID,
        'name' => trim($user->firstname . ' ' . $user->lastname),
        'referral_code' => $user->referralCode,
        'status' => $user->status,
        'downlines' => array_filter($children),
    ];
}

    public function focusUser($userId)
    {
        $this->rootUserId = $userId;
    }

    public function render()
    {
        $treeData = $this->buildTree($this->rootUserId);

        if ($this->activeTab === 'board-member') {
            return view('livewire.member-pages.genealogy.board-members');
        }

        return view('livewire.member-pages.genealogy.invitee', [
            'tree' => $treeData,
        ]);
    }
}
