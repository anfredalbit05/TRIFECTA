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

        $user = User::where('UserID', $userId)->first() ?? User::find($userId);

        if (!$user) {
            return null;
        }

        $ancestors[] = $user->UserID;
        $children = [];

        // 1. Get all activation rows where UserID matches the current user's ID
        // 2. Pluck/get the ProcessedBy column values from those rows
        $processedByValues = Activation::where('UserID', $user->UserID)
            ->whereIn('status', ['Approved', 'approved', 'ACTIVE', 'Active'])
            ->pluck('ProcessedBy')
            ->unique()
            ->toArray();

        Log::info("GenealogyTree: Found ProcessedBy IDs for UserID {$user->UserID}", [
            'userId' => $user->UserID,
            'processedByValues' => $processedByValues
        ]);

        // 3. Match those ProcessedBy values against the users table (UserID)
        foreach ($processedByValues as $processorId) {
            if ($processorId !== $this->rootUserId && !in_array($processorId, $ancestors)) {
                $processorUser = User::where('UserID', $processorId)->first() ?? User::find($processorId);

                if ($processorUser) {
                    $children[] = [
                        'id' => $processorUser->UserID,
                        'name' => trim(($processorUser->firstname ?? '') . ' ' . ($processorUser->lastname ?? '')),
                        'upline_code' => $processorUser->uplineCode ?? '',
                        'status' => $processorUser->status ?? 'Active',
                        'downlines' => [],
                    ];
                }
            }
        }

        return [
            'id' => $user->UserID,
            'name' => trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')),
            'upline_code' => $user->uplineCode ?? '',
            'status' => $user->status ?? 'Active',
            'downlines' => $children,
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
