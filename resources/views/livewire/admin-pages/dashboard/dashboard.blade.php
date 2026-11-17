{{-- <div class="dashboard-container">

    <!-- TOP SECTION: CREATE POST CARD -->
    @if(auth()->check() && (int) auth()->user()->RoleID === 1)
        <div class="create-post-card">

            @if(session()->has('success'))
                <div class="post-success">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="createPost">

                <div class="create-post-header">
                    <img
                        src="{{ asset('images/user.png') }}"
                        alt="Profile Picture"
                        class="user-avatar"
                    >
                    <div>
                        <strong>
                            {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}
                        </strong>
                        <small class="admin-badge">
                            Administrator
                        </small>
                    </div>
                </div>

                <input
                    type="text"
                    wire:model="title"
                    placeholder="Post title"
                    class="post-title-input"
                >

                @error('title')
                    <span class="validation-error">
                        {{ $message }}
                    </span>
                @enderror

                <textarea
                    wire:model="details"
                    placeholder="What's on your mind?"
                    rows="3"
                    class="post-details-input mt-2"
                ></textarea>

                <!-- OPTIONAL POST IMAGE -->
                <div class="post-image-upload">
                    <label for="post-image" class="post-image-label">
                        📷 Add Photo
                    </label>

                    <input
                        id="post-image"
                        type="file"
                        wire:model="image"
                        accept=".jpg,.jpeg,.png,.webp,image/*"
                        hidden
                    >

                    <span
                        wire:loading
                        wire:target="image"
                        class="post-uploading-text"
                    >
                        Uploading image...
                    </span>

                    @error('image')
                        <span class="validation-error">
                            {{ $message }}
                        </span>
                    @enderror

                    <!-- IMAGE PREVIEW -->
                    @if ($image)
                        <div class="post-image-preview">
                            <img
                                src="{{ $image->temporaryUrl() }}"
                                alt="Selected post image"
                            >
                            <button
                                type="button"
                                wire:click="$set('image', null)"
                                class="remove-post-image"
                            >
                                Remove Photo
                            </button>
                        </div>
                    @endif
                </div>

                @error('details')
                    <span class="validation-error">
                        {{ $message }}
                    </span>
                @enderror

                <div class="create-post-actions">
                    <select wire:model="category" class="post-category-select">
                        <option value="">-- Select Category --</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Member Benefits">Member Benefits</option>
                    </select>

                    <button
                        type="submit"
                        class="publish-post-btn"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Post</span>
                        <span wire:loading>Posting...</span>
                    </button>
                </div>

                @error('category')
                    <span class="validation-error">
                        {{ $message }}
                    </span>
                @enderror

            </form>

        </div>
    @endif

    <!-- BOTTOM SECTION: POSTS MANAGEMENT TABLE -->
    <div class="posts-table-card">
        <div class="table-header">
            <h3>Manage News & Updates</h3>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsUpdates as $news)
                        <tr wire:key="news-row-{{ $news->ID }}">
                            <td>
                                {{ $news->user ? $news->user->firstname . ' ' . $news->user->lastname : 'TRIFECTA' }}
                            </td>
                            <td>
                                <strong>{{ $news->title }}</strong>
                            </td>
                            <td>
                                <span class="badge-category">{{ $news->category }}</span>
                            </td>
                            <td>
                                {{ $news->created_at->diffForHumans() }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <x-action-btn icon="view" tone="view" label="View"
                                        wire:click="openDetails({{ $news->ID }})"
                                        title="View post" />

                                    @if(auth()->check() && (int) auth()->user()->RoleID === 1)
                                        <x-action-btn icon="delete" tone="delete" label="Delete"
                                            wire:click="deletePost({{ $news->ID }})"
                                            wire:confirm="Are you sure you want to delete this post?"
                                            title="Delete post" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-table">
                                <div class="empty-news">
                                    <div class="empty-news-icon">📰</div>
                                    <h3>No news updates yet</h3>
                                    <p>There are currently no published news updates.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- POST DETAILS MODAL -->
    @if($selectedPost)
        <div class="modal-overlay" wire:click.self="closeDetails">
            <div class="modal-content">
                <header class="card-header">
                    <div class="user-info">
                        <img
                            src="{{ asset('images/user.png') }}"
                            alt="Profile Picture"
                            class="user-avatar"
                        >
                        <div class="user-details">
                            <div class="user-name-row">
                                <span class="user-name">
                                    {{ $selectedPost->user ? $selectedPost->user->firstname . ' ' . $selectedPost->user->lastname : 'TRIFECTA' }}
                                </span>
                            </div>
                            <div class="post-meta">
                                <span>{{ $selectedPost->created_at->diffForHumans() }}</span>
                                <span class="dot-separator">•</span>
                                <span class="news-category">{{ $selectedPost->category }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="close-modal-btn" wire:click="closeDetails">&times;</button>
                </header>

                <div class="card-body">
                    <h3 class="news-title">{{ $selectedPost->title }}</h3>
                    <p class="post-text">{!! nl2br(e($selectedPost->details)) !!}</p>

                    @if ($selectedPost->image)
                        <div class="news-image-container">
                            <img
                                src="{{ asset('storage/' . $selectedPost->image) }}"
                                alt="{{ $selectedPost->title }}"
                                class="news-post-image"
                            >
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div> --}}
