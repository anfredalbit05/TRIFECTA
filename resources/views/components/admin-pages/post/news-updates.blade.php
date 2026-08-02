<div class="members-container w-full px-4 sm:px-6 lg:px-8 py-6 font-sans text-slate-800">

    <!-- HEADER & POST TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">News & Updates</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Manage and view system announcements</p>
        </div>

        @if(auth()->check() && (int) auth()->user()->RoleID === 1)
            <button
                type="button"
                wire:click="$set('showCreateModal', true)"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f]"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Post
            </button>
        @endif
    </div>

    <p class="text-sm text-slate-500 mb-6">System broadcast and general announcements dashboard.</p>

    <!-- SUCCESS NOTIFICATION -->
    @if(session()->has('success'))
        <div class="flex items-center gap-3 p-4 mb-6 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- CREATE POST MODAL -->
    @if(auth()->check() && (int) auth()->user()->RoleID === 1 && $showCreateModal)
        <div
            wire:click.self="$set('showCreateModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity"
        >
            <div class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ asset('images/user.png') }}"
                            alt="Profile Picture"
                            class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100"
                        >
                        <div>
                            <div class="font-semibold text-slate-900 text-sm">
                                {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-[#0C2C6B]/10 text-[#0C2C6B]">
                                Administrator
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showCreateModal', false)"
                        class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <div class="p-5 overflow-y-auto">
                    <form wire:submit="createPost" class="space-y-4">

                        <!-- Post Title -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Title</label>
                            <input
                                type="text"
                                wire:model="title"
                                placeholder="Enter post title..."
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] transition-all placeholder:text-slate-400"
                            >
                            @error('title')
                                <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Post Details -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Details</label>
                            <textarea
                                wire:model="details"
                                placeholder="What's on your mind?"
                                rows="3"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] transition-all placeholder:text-slate-400"
                            ></textarea>
                            @error('details')
                                <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IMAGE UPLOAD -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <label for="post-image" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Add Photo
                                </label>

                                <input
                                    id="post-image"
                                    type="file"
                                    wire:model="image"
                                    accept=".jpg,.jpeg,.png,.webp,image/*"
                                    class="hidden"
                                >

                                <span wire:loading wire:target="image" class="text-xs text-[#0C2C6B] font-medium animate-pulse">
                                    Uploading image...
                                </span>
                            </div>

                            @error('image')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror

                        @if ($image)
    <div class="relative inline-block mt-2 group">
        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-full max-h-48 object-contain rounded-xl border border-slate-200 bg-slate-50">
        <button
            type="button"
            wire:click="$set('image', null)"
            class="absolute -top-2 -right-2 bg-rose-500 hover:bg-rose-600 text-white p-1 rounded-full shadow-md transition-all cursor-pointer"
            title="Remove Image"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif
                        </div>

                        <!-- CATEGORY & SUBMIT -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                            <div class="w-full sm:w-64">
                                <select wire:model="category" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                                    <option value="">-- Select Category --</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Member Benefits">Member Benefits</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Advisory">Advisory</option>
                                    <option value="Event">Event</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('category')
                                    <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 bg-[#0C2C6B] hover:bg-[#09204f] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition shadow cursor-pointer"
                            >
                                <span wire:loading.remove>Publish Post</span>
                                <span wire:loading>Posting...</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    @endif

    <!-- NEWS TABLE SECTION -->
    <div class="member-table overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Title & Summary</th>
                    <th class="py-3 px-4">Category</th>
                    <th class="py-3 px-4">Date Posted</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($newsUpdates as $index => $news)
                    <tr wire:key="news-row-{{ $news->ID }}" class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ method_exists($newsUpdates, 'firstItem') ? $newsUpdates->firstItem() + $index : $loop->iteration }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-900 line-clamp-1">{{ $news->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ Str::limit($news->details, 60) }}</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $news->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-slate-500 text-xs">
                            {{ $news->created_at ? $news->created_at->format('F d, Y') : '—' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex gap-2 justify-center items-center">
                                <button
                                    type="button"
                                    wire:click="openDetails({{ $news->ID }})"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 text-xs font-semibold shadow transition cursor-pointer"
                                    title="View Post"
                                >
                                    👁️
                                </button>

                              @if(auth()->check() && (int) auth()->user()->RoleID === 1)
    <button
        type="button"
        onclick="confirmDelete({{ $news->ID }})"
        class="inline-flex items-center justify-center px-3 py-2 rounded-lg border-amber-400 bg-amber-500 text-white hover:bg-amber-500 transition cursor-pointer text-xs font-semibold shadow"
        title="Delete Post"
    >
        🗑️
    </button>
@endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-500">
                            No news updates found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if(method_exists($newsUpdates, 'hasPages') && $newsUpdates->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center">
                {{ $newsUpdates->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    <!-- POST DETAILS MODAL -->
    @if($selectedPost)
        <div
            wire:click.self="closeDetails"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity"
        >
            <div class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ asset('images/user.png') }}"
                            alt="Profile Picture"
                            class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100"
                        >
                        <div>
                            <div class="font-semibold text-slate-900 text-sm">
                                {{ $selectedPost->user ? $selectedPost->user->firstname . ' ' . $selectedPost->user->lastname : 'System User' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span>{{ $selectedPost->created_at ? $selectedPost->created_at->format('F d, Y') : '' }}</span>
                                <span>•</span>
                                <span class="font-medium text-[#0C2C6B]">{{ $selectedPost->category }}</span>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetails"
                        class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 overflow-y-auto space-y-4">
                    <h3 class="text-lg font-bold text-slate-900">{{ $selectedPost->title }}</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{!! nl2br(e($selectedPost->details)) !!}</p>
@if ($selectedPost->image)
    <div class="pt-2 flex justify-center bg-slate-900 rounded-xl overflow-hidden">
        <img
            src="{{ asset('storage/' . $selectedPost->image) }}"
            alt="{{ $selectedPost->title }}"
            class="w-auto h-auto max-h-72 object-contain rounded-xl"
        >
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
