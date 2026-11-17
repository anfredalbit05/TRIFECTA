<?php

namespace App\Livewire\AdminPages\Post;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\NewsUpdate;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CreateNewsUpdates extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithFileUploads;
    use WithPagination;

    protected function module(): int
    {
        return Modules::POST_NEWS;
    }

    public $title = '';

    public $details = '';

    public $category = 'General';

    public $status = 'Published';

    public $image = null;

    public $existingImage = null;

    public bool $showCreateModal = false;

    public $selectedPost = null;

    public $postIdBeingEdited = null;

    public string $search = ''; // Added search property

    public string $statusFilter = 'All';

    // Helper to check permissions dynamically
    protected function hasPermission($action)
    {
        return $this->can($action);
    }

    public function openCreateModal(): void
    {
        if (! $this->canCreate) {
            session()->flash('error', 'Unauthorized action.');

            return;
        }

        $this->resetValidation();

        $this->reset([
            'title',
            'details',
            'image',
            'existingImage',
            'postIdBeingEdited',
        ]);

        $this->category = 'General';
        $this->status = 'Published';

        $this->showCreateModal = true;
    }

    public function editPost($id): void
    {
        if (! $this->canUpdate) {
            session()->flash('error', 'Unauthorized action.');

            return;
        }

        $this->resetValidation();
        $news = NewsUpdate::findOrFail($id);

        $this->postIdBeingEdited = $news->ID;
        $this->title = $news->title;
        $this->details = $news->details;
        $this->category = $news->category ?? 'General';
        $this->status = $news->status ?? 'Published';
        $this->existingImage = $news->image;
        $this->image = null;

        $this->showCreateModal = true;
    }

    public function savePost(): void
    {
        // Check permissions based on whether it's an update or create
        if ($this->postIdBeingEdited && ! $this->canUpdate) {
            $this->addError('title', 'Unauthorized action.');

            return;
        }

        if (! $this->postIdBeingEdited && ! $this->canCreate) {
            $this->addError('title', 'Unauthorized action.');

            return;
        }

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:Published,Draft,Archived'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if ($this->postIdBeingEdited) {
            $news = NewsUpdate::findOrFail($this->postIdBeingEdited);
            $imagePath = $news->image;

            if ($this->image) {
                if ($news->image) {
                    Storage::disk('public')->delete($news->image);
                }

                // 1. Generate custom filename for update (e.g., NAU_UserID_timestamp.png)
                $filename = 'NAU_'.($user->UserID ?? '0').'_'.time().'.'.$this->image->extension();

                // 2. Use storeAs instead of store
                $imagePath = $this->image->storeAs('news-images', $filename, 'public');
            }

            $news->update([
                'title' => $this->title,
                'details' => $this->details,
                'image' => $imagePath,
                'category' => $this->category,
                'status' => $this->status,
            ]);

            $message = 'News update updated successfully.';
        } else {
            $imagePath = null;
            if ($this->image) {
                // 1. Generate custom filename for creation
                $filename = 'NAU_'.($user->UserID ?? '0').'_'.time().'.'.$this->image->extension();

                // 2. Use storeAs instead of store
                $imagePath = $this->image->storeAs('news-images', $filename, 'public');
            }

            NewsUpdate::create([
                'title' => $this->title,
                'details' => $this->details,
                'image' => $imagePath,
                'category' => $this->category,
                'status' => $this->status,
                'UserID' => $user->UserID ?? null,
            ]);

            $message = 'News update published successfully.';
        }

        $this->reset([
            'title',
            'details',
            'image',
            'existingImage',
            'postIdBeingEdited',
        ]);

        $this->category = 'General';
        $this->status = 'Published';
        $this->showCreateModal = false;

        session()->flash('success', $message);
    }

    public function createPost(): void
    {
        $this->savePost();
    }

    public function openDetails($id): void
    {
        $this->selectedPost = NewsUpdate::with('user')->find($id);
    }

    public function closeDetails(): void
    {
        $this->selectedPost = null;
    }

    public function deletePost($id): void
    {
        if (! $this->canDelete) {
            return;
        }

        $news = NewsUpdate::where('ID', $id)->first();

        if (! $news) {
            return;
        }

        // Delete the image using Laravel's Storage facade
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }

        NewsUpdate::where('ID', $id)->delete();

        if ($this->selectedPost && $this->selectedPost->ID == $id) {
            $this->selectedPost = null;
        }

        $this->resetPage();

        session()->flash('success', 'News update deleted successfully.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->statusFilter = 'All';
        $this->resetPage();
    }

    public function render()
    {
        $newsUpdates = NewsUpdate::with('user')
            ->when(
                $this->statusFilter !== 'All',
                function ($query) {
                    $query->where('status', $this->statusFilter);
                }
            )
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('details', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin-pages.post.news-updates', [
            'newsUpdates' => $newsUpdates,
        ]);
    }
}
