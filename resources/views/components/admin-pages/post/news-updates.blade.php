<div class="members-container w-full px-3 sm:px-6 lg:px-8 py-6 font-sans text-slate-800">

    <!-- HEADER & POST TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">News & Updates</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Manage and view system announcements</p>
        </div>

        @if ($this->canCreate)
            <button type="button" wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Post
            </button>
        @endif
    </div>

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <!-- SEARCH, STATUS FILTER & REFRESH -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">

        <!-- Search -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>

            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search updates by title, details..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <div class="flex flex-col sm:flex-row gap-3">

            <!-- Status Filter -->
            <select wire:model.live="statusFilter"
                class="px-3.5 py-2 text-sm border border-slate-200 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                <option value="All">All Status</option>
                <option value="Published">Published</option>
                <option value="Draft">Draft</option>
                <option value="Archived">Archived</option>
            </select>

            <!-- Refresh -->
            {{-- REFRESH TABLE --}}
            <button
                type="button"
                wire:click="resetTable"
                wire:loading.attr="disabled"
                title="Refresh Table"
                class="refresh-btn"
            >
                {{-- NORMAL STATE --}}
                <span
                    wire:loading.remove
                    wire:target="resetTable"
                    class="refresh-icon"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                        />
                    </svg>
                </span>

                {{-- LOADING STATE --}}
                <span
                    wire:loading
                    wire:target="resetTable"
                    class="refresh-loading"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="3"
                            class="refresh-circle"
                        />
                        <path
                            d="M21 12a9 9 0 0 1-9 9"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <span>Refresh</span>
            </button>

        </div>

    </div>

    <!-- SUCCESS NOTIFICATION -->
    @if (session()->has('success'))
        <div
            class="flex items-center gap-3 p-4 mb-6 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- ERROR NOTIFICATION (e.g. Unauthorized via session) -->
    @if (session()->has('error'))
        <div
            class="flex items-center gap-3 p-4 mb-6 text-sm text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- CREATE / EDIT POST MODAL -->
    @if ($showCreateModal)
        <div wire:click.self="$set('showCreateModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity overflow-y-auto">
            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/user.png') }}" alt="Profile Picture"
                            class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100 shrink-0">
                        <div>
                            <div class="font-semibold text-slate-900 text-sm">
                                {{ auth()->check() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : 'System User' }}
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-[#0C2C6B]/10 text-[#0C2C6B]">
                                {{ (int) optional(auth()->user())->RoleID === 1 ? 'Administrator' : 'Authorized User' }}
                            </span>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showCreateModal', false)"
                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <div class="p-4 sm:p-5 overflow-y-auto flex-1">
                    <form wire:submit="savePost" class="space-y-4">

                        <!-- Modal Title / Indicator -->
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                {{ $postIdBeingEdited ? 'Edit News Update' : 'Create News Update' }}
                            </h3>
                        </div>

                        <!-- Post Title -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Title</label>
                            <input type="text" wire:model="title" placeholder="Enter post title..."
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] transition-all placeholder:text-slate-400">
                            @error('title')
                                <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Post Details -->
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Details</label>
                            <textarea wire:model="details" placeholder="What's on your mind?" rows="3"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] transition-all placeholder:text-slate-400"></textarea>
                            @error('details')
                                <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IMAGE UPLOAD -->
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <label for="post-image"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $existingImage ? 'Change Photo' : 'Add Photo' }}
                                </label>

                                <input id="post-image" type="file" wire:model="image"
                                    accept=".jpg,.jpeg,.png,.webp,image/*" class="hidden">

                                <span wire:loading wire:target="image"
                                    class="text-xs text-[#0C2C6B] font-medium animate-pulse">
                                    Uploading image...
                                </span>
                            </div>

                            @error('image')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror

                            @if ($image)
                                <div class="relative inline-block mt-2 group w-full">
                                    <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                        class="w-full max-h-48 object-contain rounded-xl border border-slate-200 bg-slate-50">
                                    <button type="button" wire:click="$set('image', null)"
                                        class="absolute -top-2 -right-2 bg-rose-500 hover:bg-rose-600 text-white p-1 rounded-full shadow-md transition-all cursor-pointer"
                                        title="Remove Image">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @elseif ($existingImage)
                                <div class="relative inline-block mt-2 group w-full">
                                    <img src="{{ asset('storage/' . $existingImage) }}" alt="Current Image"
                                        class="w-full max-h-48 object-contain rounded-xl border border-slate-200 bg-slate-50">
                                    <span
                                        class="absolute bottom-2 left-2 bg-slate-900/75 text-white text-[10px] px-2 py-0.5 rounded">Current
                                        Image</span>
                                </div>
                            @endif
                        </div>

                        <!-- CATEGORY & STATUS & SUBMIT -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-end justify-between gap-3 pt-2">

                            <!-- Category -->
                            <div class="w-full sm:w-52">
                                <label
                                    class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                    Category
                                </label>

                                <select wire:model="category"
                                    class="w-full px-3.5 py-2.5 sm:py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                    <option value="">-- Select Category --</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Member Benefits">Member Benefits</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Advisory">Advisory</option>
                                    <option value="Event">Event</option>
                                    <option value="Other">Other</option>
                                </select>

                                @error('category')
                                    <p class="text-xs text-rose-500 font-medium mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="w-full sm:w-44">
                                <label
                                    class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                    Status
                                </label>

                                <select wire:model="status"
                                    class="w-full px-3.5 py-2.5 sm:py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                    <option value="Published">Published</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Archived">Archived</option>
                                </select>

                                @error('status')
                                    <p class="text-xs text-rose-500 font-medium mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Submit -->
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-[#0C2C6B] hover:bg-[#09204f] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition shadow cursor-pointer text-center">
                                <span wire:loading.remove>
                                    {{ $postIdBeingEdited ? 'Save Changes' : 'Save Post' }}
                                </span>

                                <span wire:loading>
                                    {{ $postIdBeingEdited ? 'Saving...' : 'Saving...' }}
                                </span>
                            </button>

                        </div>

                    </form>
                </div>

            </div>
        </div>
    @endif

    <!-- TABLE VIEW WRAPPED IN CARD CONTAINER -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- Desktop / Tablet Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr
                        class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="py-3 px-4 w-12">#</th>
                        <th class="py-3 px-4">Title & Summary</th>
                        <th class="py-3 px-4">Details</th>
                        <th class="py-3 px-4 w-36">Category</th>
                        <th class="py-3 px-4 w-32">Status</th>
                        <th class="py-3 px-4 w-36">Date Posted</th>
                        @if ($this->canRead || $this->canUpdate || $this->canDelete)
                            <th class="py-3 px-4 text-center w-36">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                    @forelse($newsUpdates as $index => $news)
                        <tr wire:key="news-row-desktop-{{ $news->ID }}"
                            class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 font-medium text-slate-500 align-middle">
                                {{ method_exists($newsUpdates, 'firstItem') ? $newsUpdates->firstItem() + $index : $loop->iteration }}
                            </td>
                            <td class="py-4 px-4 align-middle max-w-xs lg:max-w-md">
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $news->title }}</div>
                            </td>

                            <td class="py-4 px-4 align-middle max-w-xs lg:max-w-md">
                                <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                    {{ Str::limit($news->details, 60) }}</div>
                            </td>


                            <td class="py-4 px-4 whitespace-nowrap align-middle">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                    {{ $news->category ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap align-middle">

                                @if ($news->status === 'Published')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                        Published
                                    </span>
                                @elseif($news->status === 'Draft')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-100 text-amber-700 text-xs font-semibold">
                                        Draft
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-200 text-slate-600 text-xs font-semibold">
                                        Archived
                                    </span>
                                @endif

                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-slate-500 text-xs align-middle">
                                {{ $news->created_at ? $news->created_at->format('F d, Y') : '—' }}
                            </td>

                            @if ($this->canRead || $this->canUpdate || $this->canDelete)
                                <td class="py-4 px-4 text-center align-middle">
                                    <div class="flex gap-1.5 justify-center items-center">
                                        <!-- View Button -->
                                        @if ($this->canRead)
                                            <button type="button" wire:click="openDetails({{ $news->ID }})"
                                                class="inline-flex items-center justify-center p-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 text-xs font-semibold shadow transition cursor-pointer"
                                                title="View Post">
                                                👁️
                                            </button>
                                        @endif

                                        <!-- Update Button -->
                                        @if ($this->canUpdate)
                                            <button type="button" wire:click="editPost({{ $news->ID }})"
                                                class="inline-flex items-center justify-center p-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-xs font-semibold shadow transition cursor-pointer"
                                                title="Update Post">
                                                ✏️
                                            </button>
                                        @endif

                                        @if ($this->canDelete)
                                            <button type="button" wire:click="deletePost({{ $news->ID }})"
                                                wire:confirm="Are you sure you want to delete this news update?"
                                                class="inline-flex items-center justify-center p-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow"
                                                title="Delete Post">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $this->canRead || $this->canUpdate || $this->canDelete ? 7 : 6 }}"
                                class="text-center py-8 text-slate-500">
                                No news updates found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-slate-200">

            @forelse($newsUpdates as $index => $news)

                <div wire:key="news-row-mobile-{{ $news->ID }}"
                    class="p-4 space-y-3 hover:bg-slate-50 transition-colors">

                    <!-- Top Row -->
                    <div class="flex items-start justify-between gap-2">

                        <div class="flex items-center gap-2 flex-wrap">

                            <!-- Number -->
                            <span class="text-xs font-bold text-slate-400">
                                #{{ method_exists($newsUpdates, 'firstItem') ? $newsUpdates->firstItem() + $index : $loop->iteration }}
                            </span>

                            <!-- Category -->
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                {{ $news->category ?? 'General' }}
                            </span>

                            <!-- Status -->
                            @if ($news->status === 'Published')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                                    Published
                                </span>
                            @elseif ($news->status === 'Draft')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 text-[11px] font-semibold">
                                    Draft
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-200 text-slate-600 text-[11px] font-semibold">
                                    Archived
                                </span>
                            @endif

                        </div>

                        <!-- Date -->
                        <span class="text-[11px] text-slate-400 whitespace-nowrap">
                            {{ $news->created_at ? $news->created_at->format('M d, Y') : '—' }}
                        </span>

                    </div>


                    <!-- Title & Details -->
                    <div>

                        <div class="font-semibold text-slate-900 text-sm line-clamp-1">
                            {{ $news->title }}
                        </div>

                        <div class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ Str::limit($news->details, 90) }}
                        </div>

                    </div>


                    <!-- Actions -->
                    @if ($this->canRead || $this->canUpdate || $this->canDelete)
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">

                            <!-- View -->
                            @if ($this->canRead)
                                <button type="button" wire:click="openDetails({{ $news->ID }})"
                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-slate-600 text-white hover:bg-slate-700 text-xs font-semibold shadow transition cursor-pointer"
                                    title="View Post">
                                    👁️ View
                                </button>
                            @endif


                            <!-- Edit -->
                            @if ($this->canUpdate)
                                <button type="button" wire:click="editPost({{ $news->ID }})"
                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-xs font-semibold shadow transition cursor-pointer"
                                    title="Update Post">
                                    ✏️ Edit
                                </button>
                            @endif


                            <!-- Delete -->
                            @if ($this->canDelete)
                                <button type="button" wire:click="deletePost({{ $news->ID }})"
                                    wire:confirm="Are you sure you want to delete this news update?"
                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 text-xs font-semibold shadow transition cursor-pointer"
                                    title="Delete Post">
                                    🗑️ Delete
                                </button>
                            @endif

                        </div>
                    @endif

                </div>

            @empty

                <div class="text-center py-8 text-slate-500 text-sm">
                    No news updates found.
                </div>

            @endforelse

        </div>

        <!-- PAGINATION -->
        @if (method_exists($newsUpdates, 'hasPages') && $newsUpdates->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                {{-- Previous Page Link --}}
                @if ($newsUpdates->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #D79B10;" onmouseover="this.style.backgroundColor='#c58d0f'"
                        onmouseout="this.style.backgroundColor='#D79B10'">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $newsUpdates->currentPage() }} of {{ $newsUpdates->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($newsUpdates->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #0C2C6B;" onmouseover="this.style.backgroundColor='#0a2559'"
                        onmouseout="this.style.backgroundColor='#0C2C6B'">
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    <!-- POST DETAILS MODAL -->
    @if ($selectedPost)
        <div wire:click.self="closeDetails"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity overflow-y-auto">
            <div
                class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/user.png') }}" alt="Profile Picture"
                            class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100 shrink-0">
                        <div>
                            <div class="font-semibold text-slate-900 text-sm">
                                {{ $selectedPost->user ? $selectedPost->user->firstname . ' ' . $selectedPost->user->lastname : 'System User' }}
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                                <span>{{ $selectedPost->created_at ? $selectedPost->created_at->format('F d, Y') : '' }}</span>
                                <span class="hidden sm:inline">•</span>
                                <span class="font-medium text-[#0C2C6B]">{{ $selectedPost->category }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetails"
                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                    <h3 class="text-lg font-bold text-slate-900">{{ $selectedPost->title }}</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{!! nl2br(e($selectedPost->details)) !!}</p>
                    @if ($selectedPost->image)
                        <div class="pt-2 flex justify-center bg-slate-900 rounded-xl overflow-hidden">
                            <img src="{{ asset('storage/' . $selectedPost->image) }}"
                                alt="{{ $selectedPost->title }}"
                                class="w-auto h-auto max-h-72 object-contain rounded-xl">
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

    <script>
        function confirmDelete(postId) {
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
                    @this.call('deletePost', postId);
                }
            });
        }
    </script>

</div>
