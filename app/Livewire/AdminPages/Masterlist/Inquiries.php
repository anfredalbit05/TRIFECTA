<?php

namespace App\Livewire\AdminPages\Masterlist;

use Livewire\Component;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination; // 1. Import the pagination trait

class Inquiries extends Component
{
    use WithPagination; // 2. Use the pagination trait here

    protected $paginationTheme = 'tailwind';
    public $selectedInquiry = null;
    public $showModal = false;
    public string $search = '';

    // Reply fields
    public $replySubject = '';
    public $replyMessage = '';
    public $successMessage = '';

    public function viewInquiry($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        // Mark as read automatically if it hasn't been read yet
        if (is_null($inquiry->date_read)) {
            $inquiry->markAsRead();
        }

        $this->selectedInquiry = $inquiry;
        $this->replySubject = '';
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

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $inquiries = Inquiry::when(filled($this->search), function ($query) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->paginate(10);

        return view('components.admin-pages.masterlist.inquiries', [
            'inquiries' => $inquiries,
        ]);
    }
}
