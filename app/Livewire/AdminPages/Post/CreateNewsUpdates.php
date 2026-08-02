<?php

namespace App\Livewire\AdminPages\Post;

use App\Models\NewsUpdate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateNewsUpdates extends Component
{
    use WithFileUploads;

    public $title = '';
    public $details = '';
    public $category = 'General';
    public $image = null;

    public bool $showCreateModal = false;
    public $selectedPost = null;

    protected $listeners = ['confirmedDelete' => 'deletePost'];

    public function createPost(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            $this->addError('title', 'You must be logged in to create a post.');
            return;
        }

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('news-images', 'public');
        }

        NewsUpdate::create([
            'title'    => $this->title,
            'details'  => $this->details,
            'image'    => $imagePath,
            'category' => $this->category,
            'status'   => 'Published',
            'UserID'   => $user->UserID,
        ]);

        $this->reset(['title', 'details', 'image']);
        $this->category = 'General';
        $this->showCreateModal = false;

        session()->flash('success', 'News update published successfully.');
    }

    public function openDetails($id): void
    {
        $this->selectedPost = NewsUpdate::with('user')->find($id);
    }

    public function closeDetails(): void
    {
        $this->selectedPost = null;
    }

    /**
     * Delete the news update post.
     */
    public function deletePost($id): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || (int) $user->RoleID !== 1) {
            return;
        }

        $news = NewsUpdate::find($id);

        if (!$news) {
            return;
        }

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        if ($this->selectedPost && $this->selectedPost->ID == $id) {
            $this->selectedPost = null;
        }

        session()->flash('success', 'News update deleted successfully.');
    }

    public function render()
    {
        $newsUpdates = NewsUpdate::with('user')
            ->where('status', 'Published')
            ->latest()
            ->get();

        return view('components.admin-pages.post.news-updates', [
            'newsUpdates' => $newsUpdates,
        ]);
    }
}
