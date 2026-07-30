<?php

namespace App\Livewire\AdminPages\Masterlist;

use Livewire\Component;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Inquiries extends Component
{
    public $inquiries = [];
    public $selectedInquiry = null;
    public $showModal = false;

    // Reply fields
    public $replySubject = '';
    public $replyMessage = '';
    public $successMessage = '';

    public function mount()
    {
        $this->loadInquiries();
    }

    public function loadInquiries()
    {
        $this->inquiries = Inquiry::latest()->get();
    }

    public function viewInquiry($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        // Mark as read automatically if it hasn't been read yet
        if (is_null($inquiry->date_read)) {
            $inquiry->markAsRead();
            $this->loadInquiries();
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
        // Log that the reply attempt has started
        Log::info('Attempting to send inquiry reply to: ' . $this->selectedInquiry->email);

        Mail::raw($this->replyMessage, function ($mail) {
            $mail->to($this->selectedInquiry->email)
                 ->subject($this->replySubject);
        });

        $this->successMessage = 'Reply sent successfully to ' . $this->selectedInquiry->email;
        $this->replyMessage = '';

        Log::info('Inquiry reply sent successfully.');

    } catch (\Exception $e) {
        // Log any error/exception that occurs during sending
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

    public function render()
    {
        return view('components.admin-pages.masterlist.inquiries', [
            'inquiries' => $this->inquiries,
        ]);
    }
}
