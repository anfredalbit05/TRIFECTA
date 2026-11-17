<?php

namespace App\Livewire\AdminPages\Post;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Testimonial;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTestimonials extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    protected function module(): int
    {
        return Modules::POST_TESTIMONIALS;
    }

    public $fullname = '';

    public $designation = '';

    public $testimony = '';

    public $rate = 5;

    public bool $showCreateModal = false;

    public $selectedTestimonial = null;

    public $testimonialIdBeingEdited = null;

    public $search = ''; // Added search property

    public function openCreateModal(): void
    {
        if (! $this->canCreate) {
            return;
        }

        $this->resetValidation();
        $this->reset(['fullname', 'designation', 'testimony', 'rate', 'testimonialIdBeingEdited']);
        $this->rate = 5;
        $this->showCreateModal = true;
    }

    public function editTestimonial($id): void
    {
        if (! $this->canUpdate) {
            return;
        }

        $this->resetValidation();
        $testimonial = Testimonial::findOrFail($id);

        $this->testimonialIdBeingEdited = $testimonial->TestimonialsID;
        $this->fullname = $testimonial->fullname;
        $this->designation = $testimonial->designation;
        $this->testimony = $testimonial->testimony;
        $this->rate = $testimonial->rate;

        $this->showCreateModal = true;
    }

    public function saveTestimonial(): void
    {
        if ($this->testimonialIdBeingEdited && ! $this->canUpdate) {
            return;
        }

        if (! $this->testimonialIdBeingEdited && ! $this->canCreate) {
            return;
        }

        // Check which required fields are empty
        $emptyFields = [];

        if (empty(trim($this->fullname))) {
            $emptyFields[] = 'Full Name';
        }

        if (empty(trim($this->testimony))) {
            $emptyFields[] = 'Testimony Message';
        }

        if (! empty($emptyFields)) {
            if (count($emptyFields) > 1) {
                $text = 'The following fields are empty:<br><br><b>• '.implode('</b><br><b>• ', $emptyFields).'</b>';
            } else {
                $text = 'The <b>'.$emptyFields[0].'</b> field is empty.';
            }

            $this->dispatch('swal:error', [
                'title' => 'Missing Fields',
                'text' => $text,
            ]);

            return;
        }

        // Run standard validation for max length, etc.
        $this->validate([
            'fullname' => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'testimony' => ['required', 'string'],
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        // If updating, trigger SweetAlert confirmation first
        if ($this->testimonialIdBeingEdited) {
            $this->dispatch('confirm-update');

            return;
        }

        /** @var User|null $user */
        $user = Auth::user();

        Testimonial::create([
            'fullname' => $this->fullname,
            'designation' => $this->designation,
            'testimony' => $this->testimony,
            'rate' => $this->rate,
            'status' => 'Published',
            'UserID' => $user->UserID ?? null,
        ]);

        $this->reset(['fullname', 'designation', 'testimony', 'rate', 'testimonialIdBeingEdited']);
        $this->showCreateModal = false;

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Testimonial added successfully.',
        ]);
    }

    public function performUpdate(): void
    {
        if (! $this->testimonialIdBeingEdited || ! $this->canUpdate) {
            return;
        }

        $testimonial = Testimonial::findOrFail($this->testimonialIdBeingEdited);

        // Check if any data actually changed
        if (
            $testimonial->fullname === $this->fullname &&
            $testimonial->designation === $this->designation &&
            $testimonial->testimony === $this->testimony &&
            (int) $testimonial->rate === (int) $this->rate
        ) {
            $this->showCreateModal = false;
            $this->dispatch('swal:success', [
                'title' => 'No Changes!',
                'text' => 'Nothing was changed.',
            ]);

            return;
        }

        $testimonial->update([
            'fullname' => $this->fullname,
            'designation' => $this->designation,
            'testimony' => $this->testimony,
            'rate' => $this->rate,
        ]);

        $this->reset(['fullname', 'designation', 'testimony', 'rate', 'testimonialIdBeingEdited']);
        $this->showCreateModal = false;

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Testimonial updated successfully.',
        ]);
    }

    public function openDetails($id): void
    {
        $this->selectedTestimonial = Testimonial::with('user')->find($id);
    }

    public function closeDetails(): void
    {
        $this->selectedTestimonial = null;
    }

    public function deleteTestimonial($id): void
    {
        if (! $this->canDelete) {
            return;
        }

        $testimonial = Testimonial::find($id);

        if ($testimonial) {
            $testimonial->delete();
        }

        if ($this->selectedTestimonial && $this->selectedTestimonial->TestimonialsID == $id) {
            $this->selectedTestimonial = null;
        }

        $this->dispatch('swal:success', [
            'title' => 'Deleted!',
            'text' => 'Testimonial deleted successfully.',
        ]);
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $testimonials = Testimonial::with('user')
            ->where('status', 'Published')
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('fullname', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('testimony', 'like', "%{$search}%");
                });
            })
            ->latest('TestimonialsID')
            ->paginate(10);

        return view('livewire.admin-pages.post.testimonials', [
            'testimonials' => $testimonials,
            'canCreate' => $this->canCreate,
            'canRead' => $this->canRead,
            'canUpdate' => $this->canUpdate,
            'canDelete' => $this->canDelete,
        ]);
    }
}
