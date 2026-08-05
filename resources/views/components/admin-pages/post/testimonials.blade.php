<div class="members-container w-full px-3 sm:px-6 lg:px-8 py-6 font-sans text-slate-800">

    <!-- HEADER & POST TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">Testimonials</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Manage user feedback and platform ratings</p>
        </div>

        @if($canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Testimonial
            </button>
        @endif
    </div>

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live="search" placeholder="Search testimonials..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
            <span wire:loading.remove wire:target="resetTable">🔄</span>
            <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>
    <!-- CREATE / EDIT MODAL -->
    @if($showCreateModal)
        <div
            wire:click.self="$set('showCreateModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity overflow-y-auto"
        >
            <div class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0">
                    <h3 class="text-base font-bold text-slate-900">
                        {{ $testimonialIdBeingEdited ? 'Edit Testimonial' : 'Add New Testimonial' }}
                    </h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 overflow-y-auto flex-1">
                    <form wire:submit="saveTestimonial" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Full Name</label>
                            <input type="text" wire:model="fullname" placeholder="e.g. Bobby Dagla" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('fullname') <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Designation / Role</label>
                            <input type="text" wire:model="designation" placeholder="e.g. Royal Member" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('designation') <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Testimony Message</label>
                            <textarea wire:model="testimony" placeholder="Share your experience..." rows="3" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"></textarea>
                            @error('testimony') <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                            <div class="w-full sm:w-48">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Rating (Stars)</label>
                                <select wire:model="rate" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                    <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                                    <option value="3">⭐⭐⭐ (3 Stars)</option>
                                    <option value="2">⭐⭐ (2 Stars)</option>
                                    <option value="1">⭐ (1 Star)</option>
                                </select>
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-5 py-2.5 bg-[#0C2C6B] hover:bg-[#09204f] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition shadow cursor-pointer text-center">
                                <span wire:loading.remove>{{ $testimonialIdBeingEdited ? 'Update Testimonial' : 'Publish Testimonial' }}</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- VIEW MODAL -->
    @if($selectedTestimonial)
        <div
            wire:click.self="closeDetails"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity overflow-y-auto"
        >
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0 bg-[#0C2C6B] text-white">
                    <h3 class="text-base font-bold">
                        Testimonial Details
                    </h3>
                    <button type="button" wire:click="closeDetails" class="p-1.5 text-slate-200 hover:text-white rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Full Name</span>
                        <p class="text-base font-semibold text-slate-900 mt-0.5">{{ $selectedTestimonial->fullname }}</p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Designation / Role</span>
                        <p class="text-slate-700 mt-0.5">{{ $selectedTestimonial->designation ?? 'Not specified' }}</p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Rating</span>
                        <p class="text-amber-500 font-bold mt-0.5 text-base">{{ str_repeat('★', $selectedTestimonial->rate) }} ({{ $selectedTestimonial->rate }}/5)</p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Testimony Message</span>
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 mt-1 whitespace-pre-wrap leading-relaxed">{{ $selectedTestimonial->testimony }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100 text-xs text-slate-500">
                        <div>
                            <span class="block font-semibold text-slate-400 uppercase tracking-wider">Status</span>
                            <span class="inline-block mt-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 font-medium rounded-md">{{ $selectedTestimonial->status }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-400 uppercase tracking-wider">Added By</span>
                            <p class="text-slate-700 mt-1 font-medium">{{ $selectedTestimonial->user->name ?? 'System User' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
                    <button type="button" wire:click="closeDetails" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-xs transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- TABLE VIEW WRAPPED IN CARD CONTAINER -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse min-w-[650px]">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4 w-12">#</th>
                    <th class="py-3 px-4">Author & Designation</th>
                    <th class="py-3 px-4">Message</th>
                    <th class="py-3 px-4 w-28 text-center">Rating</th>
                    @if($canRead || $canUpdate || $canDelete)
                        <th class="py-3 px-4 text-center w-36">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($testimonials as $index => $item)
                    <tr wire:key="testimonial-row-{{ $item->TestimonialsID }}" class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500 align-middle">
                            {{ $testimonials->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="font-semibold text-slate-900">{{ $item->fullname }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $item->designation ?? 'Member' }}</div>
                        </td>
                        <td class="py-4 px-4 align-middle max-w-xs lg:max-w-md">
                            <div class="text-xs text-slate-600 line-clamp-2">{{ Str::limit($item->testimony, 80) }}</div>
                        </td>
                        <td class="py-4 px-4 text-center align-middle whitespace-nowrap">
                            <span class="text-amber-500 font-bold text-xs">{{ str_repeat('★', $item->rate) }}</span>
                        </td>
                        @if($canRead || $canUpdate || $canDelete)
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex gap-1.5 justify-center items-center">
                                    @if($canRead)
                                        <button type="button" wire:click="openDetails({{ $item->TestimonialsID }})" class="px-3 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 text-xs shadow transition cursor-pointer" title="View">👁️</button>
                                    @endif
                                    @if($canUpdate)
                                        <button type="button" wire:click="editTestimonial({{ $item->TestimonialsID }})" class="px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-xs shadow transition cursor-pointer" title="Edit">✏️</button>
                                    @endif
                                    @if($canDelete)
                                        <button type="button" onclick="confirmDelete({{ $item->TestimonialsID }})" class="px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 text-xs shadow transition cursor-pointer" title="Delete">🗑️</button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-500">No testimonials found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

       <!-- PAGINATION -->
       @if($testimonials->hasPages())
           <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">
               @if ($testimonials->onFirstPage())
                   <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">Previous</span>
               @else
                   <button wire:click="previousPage" wire:loading.attr="disabled" class="px-4 py-2 text-white rounded transition cursor-pointer" style="background-color: #D79B10;">Previous</button>
               @endif

               <span class="text-sm text-gray-600">Page {{ $testimonials->currentPage() }} of {{ $testimonials->lastPage() }}</span>

               @if ($testimonials->hasMorePages())
                   <button wire:click="nextPage" wire:loading.attr="disabled" class="px-4 py-2 text-white rounded transition cursor-pointer" style="background-color: #0C2C6B;">Next</button>
               @else
                   <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">Next</span>
               @endif
           </div>
       @endif
    </div>
</div>

@script
<script>
    // Listen for custom success alerts
    $wire.on('swal:success', (event) => {
        const data = event[0] || event;
        Swal.fire({
            title: data.title,
            text: data.text,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Listen for custom error alerts with HTML support
    $wire.on('swal:error', (event) => {
        const data = event[0] || event;
        Swal.fire({
            title: data.title,
            html: data.text,
            icon: 'error',
            confirmButtonColor: '#0C2C6B'
        });
    });

    // Listen for update confirmation prompt
    $wire.on('confirm-update', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update this testimonial?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0C2C6B',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.call('performUpdate');
            }
        });
    });

    // Global function for delete confirmation inside Livewire script scope
    window.confirmDelete = function(testimonialId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E5B83B',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.call('deleteTestimonial', testimonialId);
            }
        });
    }
</script>
@endscript
