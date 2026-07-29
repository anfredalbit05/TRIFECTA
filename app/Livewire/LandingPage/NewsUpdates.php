<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Context;
use App\Models\NewsUpdate;

#[Layout('layouts.app')]
class NewsUpdates extends Component
{
    public array $contexts = [];
    public string $selectedCategory = 'all';

    public function mount(): void
    {
        $this->loadContexts();
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = trim($category);
    }

    public function loadContexts(): void
    {
        $this->contexts = Context::pluck('details', 'context_key')->toArray();
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

        $newsUpdates = $query->latest()->get();

        return view('livewire.landing-page.news', [
            'featuredPost'      => $featuredPost,
            'newsUpdates'       => $newsUpdates,
            'allowedCategories' => $allowedCategories,
        ]);
    }
}
