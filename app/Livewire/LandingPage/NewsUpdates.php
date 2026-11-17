<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // <-- 1. Import pagination trait
use App\Models\Context;
use App\Models\NewsUpdate;

#[Layout('layouts.app')]
class NewsUpdates extends Component
{
    use WithPagination; // <-- 2. Use the trait

    protected $paginationTheme = 'tailwind'; // <-- 3. Ensure Tailwind style

    public array $contexts = [];
    public string $selectedCategory = 'all';

    public function mount(): void
    {
        $this->loadContexts();
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = trim($category);
        $this->resetPage(); // <-- 4. Reset pagination back to page 1 when filter changes!
    }

    public function loadContexts(): void
    {
        $this->contexts = Context::merged();
    }

    #[On('context-updated')]
    public function refreshContexts(): void
    {
        $this->loadContexts();
    }

    public function render()
    {
        // 1. Featured Post
        $featuredPost = NewsUpdate::with('user')
            ->where('status', 'Published')
            ->latest()
            ->first();

        // 2. Dynamically fetch distinct non-null categories from the DB
        $allowedCategories = NewsUpdate::where('status', 'Published')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->unique()
            ->values()
            ->toArray();

        // 3. Query filtered news updates
        $query = NewsUpdate::with('user')
            ->where('status', 'Published');

        if ($this->selectedCategory !== 'all') {
            $query->whereRaw('TRIM(category) = ?', [trim($this->selectedCategory)]);
        }

        // 5. Replace ->get() with ->paginate(6) (e.g., 2 rows of 3 columns)
        $newsUpdates = $query->latest()->paginate(6);

        return view('livewire.landing-page.news', [
            'featuredPost'      => $featuredPost,
            'newsUpdates'       => $newsUpdates,
            'allowedCategories' => $allowedCategories,
        ]);
    }
}
