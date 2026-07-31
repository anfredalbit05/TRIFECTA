<div class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6 font-sans text-slate-800">

    <!-- HEADER & POST TOGGLE -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">News & Updates</h2>
            <p class="text-sm text-slate-500 mt-0.5">Manage and view system announcements</p>
        </div>

        @if(auth()->check() && (int) auth()->user()->RoleID === 1)
            <button
                type="button"
                wire:click="$toggle('showCreateForm')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $showCreateForm ? 'bg-slate-200 text-slate-700 hover:bg-slate-300 focus:ring-slate-400' : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm' }}"
            >
                @if($showCreateForm)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Close Form
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Post
                @endif
            </button>
        @endif
    </div>

    <!-- SUCCESS NOTIFICATION -->
    @if(session()->has('success'))
        <div class="flex items-center gap-3 p-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- CREATE POST FORM (TOGGLABLE / ADMIN ONLY) -->
    @if(auth()->check() && (int) auth()->user()->RoleID === 1 && $showCreateForm)
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 sm:p-6 transition-all">
            <form wire:submit="createPost" class="space-y-4">

                <!-- Admin Info Header -->
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <img
                        src="{{ asset('images/user.png') }}"
                        alt="Profile Picture"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100"
                    >
                    <div>
                        <div class="font-semibold text-slate-900 text-sm">
                            {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                            Administrator
                        </span>
                    </div>
                </div>

                <!-- Post Title -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Title</label>
                    <input
                        type="text"
                        wire:model="title"
                        placeholder="Enter post title..."
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder:text-slate-400"
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
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder:text-slate-400"
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

                        <span wire:loading wire:target="image" class="text-xs text-indigo-600 font-medium animate-pulse">
                            Uploading image...
                        </span>
                    </div>

                    @error('image')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror

                    @if ($image)
                        <div class="relative inline-block mt-2 group">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-32 h-32 object-cover rounded-xl border border-slate-200">
                            <button
                                type="button"
                                wire:click="$set('image', null)"
                                class="absolute -top-2 -right-2 bg-rose-500 hover:bg-rose-600 text-white p-1 rounded-full shadow-md transition-all"
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
                        <select wire:model="category" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Select Category --</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Member Benefits">Member Benefits</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Maintenance">Advisory</option>
                            <option value="Maintenance">Event</option>
                            <option value="Maintenance">Other</option>
                        </select>
                        @error('category')
                            <p class="text-xs text-rose-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors shadow-xs"
                    >
                        <span wire:loading.remove>Publish Post</span>
                        <span wire:loading>Posting...</span>
                    </button>
                </div>

            </form>
        </div>
    @endif

    <!-- NEWS TABLE SECTION -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Title & Summary</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Date Posted</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($newsUpdates as $news)
                        <tr wire:key="news-row-{{ $news->ID }}" class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $news->title }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ Str::limit($news->details, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $news->category ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                {{ $news->created_at ? $news->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openDetails({{ $news->ID }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </button>

                                    @if(auth()->check() && (int) auth()->user()->RoleID === 1)
                                        <button
                                            type="button"
                                            wire:click="deletePost({{ $news->ID }})"
                                            wire:confirm="Are you sure you want to delete this news update?"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-md transition-colors"
                                        >
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                <h3 class="text-sm font-semibold text-slate-700">No news updates yet</h3>
                                <p class="text-xs text-slate-500 mt-1">There are currently no published news updates available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                                <span class="font-medium text-indigo-600">{{ $selectedPost->category }}</span>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeDetails"
                        class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 overflow-y-auto space-y-4">
                    <h3 class="text-lg font-bold text-slate-900">{{ $selectedPost->title }}</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{!! nl2br(e($selectedPost->details)) !!}</p>

                    @if ($selectedPost->image)
                        <div class="pt-2">
                            <img
                                src="{{ asset('storage/' . $selectedPost->image) }}"
                                alt="{{ $selectedPost->title }}"
                                class="w-full h-auto max-h-80 object-cover rounded-xl border border-slate-100"
                            >
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>
