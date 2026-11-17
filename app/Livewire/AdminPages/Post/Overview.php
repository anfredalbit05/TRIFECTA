<?php

namespace App\Livewire\AdminPages\Post;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\NewsUpdate;
use App\Models\Testimonial;
use App\Support\Modules;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;

class Overview extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;

    /*
    | Overview spans both Post modules: showing what either News & Update
    | (10) or Testimonials (11) allow, so the overview stats/cards follow
    | whichever the viewer can actually manage.
    */
    protected function modules(): array
    {
        return [Modules::POST_NEWS, Modules::POST_TESTIMONIALS];
    }

    public function refreshOverview()
    {
        session()->flash('message', 'Overview data refreshed successfully.');
    }

    public function render()
    {
        return view('livewire.admin-pages.post.overview', [
            'canCreate' => $this->canCreate,
            'canRead' => $this->canRead,
            'canUpdate' => $this->canUpdate,
            'canDelete' => $this->canDelete,
            'totalTestimonials' => Testimonial::where('status', 'Published')->count(),
            'totalNews' => NewsUpdate::where('status', 'Published')->count(),
        ]);
    }
}
