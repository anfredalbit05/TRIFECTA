<?php

namespace App\Livewire\AdminPages\Masterlist;

use Livewire\Component;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination; // 1. Import the pagination trait
use App\Models\Benefit;
use App\Models\Privilege;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Inquiries extends Component
{
    use WithPagination; // 2. Use the pagination trait here

    protected $paginationTheme = 'tailwind';
    public $selectedInquiry = null;
    public $showModal = false;
    public string $search = '';
    public string $dateRange = '';   // e.g. "2026-08-01 to 2026-08-16"

    // Reply fields
    public $replySubject = '';
    public $replyMessage = '';
    public $successMessage = '';

     public function mount()
    {
        $this->dateRange = '';
    }

    public function viewInquiry($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        // Mark as read automatically if it hasn't been read yet
        if (is_null($inquiry->date_read)) {
            $inquiry->markAsRead();
        }

        $this->selectedInquiry = $inquiry;
        $this->replySubject = 'Reply: '.$inquiry->subject;
        $this->replyMessage = '';
        $this->successMessage = '';
        $this->showModal = true;
    }

    public function sendReply()
    {
        $this->validate([
            'replySubject' => 'required|string|max:255',
            'replyMessage' => 'required|string',
        ]);

        try {
            Log::info('Attempting to send inquiry reply to: ' . $this->selectedInquiry->email);

            Mail::raw($this->replyMessage, function ($mail) {
                $mail->to($this->selectedInquiry->email)
                    ->subject($this->replySubject);
            });

            $this->successMessage = 'Reply sent successfully to ' . $this->selectedInquiry->email;
            $this->replyMessage = '';

            Log::info('Inquiry reply sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send inquiry reply: ' . $e->getMessage());

            $this->successMessage = '';
            session()->flash('error', 'Failed to send email. Check logs for details.');
        }
    }

    protected function hasPermission($action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super Admin override (RoleID 1)
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = 'can_' . $action;

        if ($user->role && method_exists($user->role, 'permissions')) {
            $hasPerm = optional($user->role->permissions)
                ->where('ModuleID', 5)
                ->where($column, 1)
                ->isNotEmpty();

            if ($hasPerm) {
                return true;
            }
        }

        return Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 5)
            ->where($column, 1)
            ->exists();
    }

    public function getCanCreateProperty(): bool
    {
        return $this->hasPermission('create');
    }

    public function getCanReadProperty(): bool
    {
        return $this->hasPermission('read');
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->hasPermission('update');
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->hasPermission('delete');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedInquiry = null;
        $this->replySubject = '';
        $this->replyMessage = '';
        $this->successMessage = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

     protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || !str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        return [trim($from) ?: null, trim($to) ?: null];
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }


     public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $inquiries = Inquiry::when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->latest()
            ->paginate(10);

        return view('components.admin-pages.masterlist.inquiries', [
            'inquiries' => $inquiries,
        ]);
    }
}
