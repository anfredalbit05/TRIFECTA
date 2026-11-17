
<div class="news-page">

    {{-- NEWSROOM HERO + FEATURED STORY --}}
    <section class="news-masthead">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="news-masthead-grid">
                <div class="news-intro">
                    <span class="news-eyebrow">
                        <span class="news-eyebrow-line"></span>
                        <x-editable-text key="news_hero_badge" :value="$contexts['news_hero_badge'] ?? ''" tag="span"/>
                    </span>
                    <h1 class="news-intro-title">
                        <x-editable-text key="news_hero_title_1" :value="$contexts['news_hero_title_1'] ?? ''" tag="span"/>
                        <span class="news-intro-highlight">
                            <x-editable-text key="news_hero_title_highlight" :value="$contexts['news_hero_title_highlight'] ?? ''" tag="span"/>
                        </span>
                    </h1>
                    <p class="news-intro-description">
                        <x-editable-text key="news_hero_description" :value="$contexts['news_hero_description'] ?? ''" tag="span"/>
                    </p>
                    <div class="news-intro-bottom">
                        <span class="news-brand-mark">TRIFECTA</span>
                        <span class="news-brand-divider"></span>
                        <span>Health • Wellness • Protection</span>
                    </div>
                </div>

                @if($featuredPost)
                    <article class="news-lead-story">
                        <img src="{{ $featuredPost->image ? asset('storage/' . $featuredPost->image) : asset('images/hero-bg.jpg') }}"
                             alt="{{ $featuredPost->title }}" class="news-lead-image">
                        <div class="news-lead-overlay"></div>
                        <div class="news-lead-content">
                            <span class="news-lead-label">FEATURED STORY</span>
                            <span class="news-lead-meta">
                                {{ $featuredPost->category ?? 'General' }} • {{ $featuredPost->created_at?->format('M d, Y') ?? '' }}
                            </span>
                            <h2 class="news-lead-title">{{ $featuredPost->title }}</h2>
                            <p class="news-lead-description">
                                {{ Str::limit(strip_tags($featuredPost->details), 145) }}
                            </p>
                            <a href="#news-{{ $featuredPost->ID }}" class="news-lead-link">
                                Explore Story <span>↗</span>
                            </a>
                        </div>
                    </article>
                @else
                    <div class="news-lead-placeholder">
                        <div class="news-placeholder-icon">
                            <flux:icon.newspaper class="w-12 h-12"/>
                        </div>
                        <h2>Latest from TRIFECTA</h2>
                        <p>Discover our latest news, announcements, and updates.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- NEWSROOM NAVIGATION --}}
    <section class="news-navigation">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="news-navigation-inner">
                <div class="news-navigation-heading">
                    <span>THE NEWSROOM</span>
                    <small>Explore our latest updates</small>
                </div>
                <div class="news-category-list">
                    <button type="button" wire:click="setCategory('all')"
                        class="news-category-link {{ $selectedCategory === 'all' ? 'active' : '' }}">
                        All News
                    </button>
                    @foreach($allowedCategories as $category)
                        <button type="button" wire:click="setCategory('{{ $category }}')"
                            class="news-category-link {{ $selectedCategory === $category ? 'active' : '' }}">
                            {{ $category }}
                        </button>
                    @endforeach
                </div>
                <div class="news-results-count">
                    {{ $newsUpdates->total() }} <span>ARTICLES</span>
                </div>
            </div>
        </div>
    </section>

    {{-- EDITORIAL NEWSROOM --}}
    <section class="newsroom-section">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="newsroom-heading">
                <div>
                    <span class="newsroom-kicker">LATEST STORIES & ANNOUNCEMENTS</span>
                    <h2 class="newsroom-title">What's Happening</h2>
                </div>
                <div class="newsroom-heading-note">
                    <span class="newsroom-dot"></span>
                    Stay informed. Stay connected.
                </div>
            </div>

            <div class="news-editorial-grid">
                @forelse($newsUpdates as $index => $article)
                    @if($index === 0)
                        {{-- LEAD ARTICLE --}}
                        <article class="news-editorial-lead" id="news-{{ $article->ID }}" wire:key="article-{{ $article->ID }}">
                            <div class="news-editorial-lead-image">
                                <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('images/hero-bg.jpg') }}"
                                     alt="{{ $article->title }}" loading="lazy">
                                <span class="news-image-category">{{ $article->category ?? 'General' }}</span>
                            </div>
                            <div class="news-editorial-lead-body">
                                <span class="news-article-date">{{ $article->created_at?->format('F d, Y') ?? '' }}</span>
                                <h3 class="news-editorial-lead-title">{{ $article->title }}</h3>
                                <p class="news-editorial-excerpt">{{ Str::limit(strip_tags($article->details), 200) }}</p>
                                <div class="news-article-bottom">
                                    <span>{{ $article->created_at?->diffForHumans() ?? '' }}</span>
                                    <a href="#news-{{ $article->ID }}">Read Article <span>↗</span></a>
                                </div>
                            </div>
                        </article>
                    @else
                        {{-- STANDARD EDITORIAL CARDS --}}
                        <article class="news-editorial-card" id="news-{{ $article->ID }}" wire:key="article-{{ $article->ID }}">
                            <div class="news-editorial-card-image">
                                <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('images/hero-bg.jpg') }}"
                                     alt="{{ $article->title }}" loading="lazy">
                                <span class="news-image-category">{{ $article->category ?? 'General' }}</span>
                            </div>
                            <div class="news-editorial-card-body">
                                <span class="news-article-date">{{ $article->created_at?->format('M d, Y') ?? '' }}</span>
                                <h3 class="news-editorial-card-title">{{ $article->title }}</h3>
                                <p class="news-editorial-excerpt">{{ Str::limit(strip_tags($article->details), 115) }}</p>
                                <div class="news-article-bottom">
                                    <span>{{ $article->created_at?->diffForHumans() ?? '' }}</span>
                                    <a href="#news-{{ $article->ID }}">Read More ↗</a>
                                </div>
                            </div>
                        </article>
                    @endif
                @empty
                    <div class="news-empty">
                        <flux:icon.newspaper class="w-12 h-12"/>
                        <h3>No News Available</h3>
                        <p>There are currently no updates in this category. Please check back later.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if($newsUpdates->total() > 0)
                <div class="news-pagination">
                    @if($newsUpdates->onFirstPage())
                        <span class="news-page-disabled">← Previous</span>
                    @else
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="news-page-button">
                            ← Previous
                        </button>
                    @endif

                    <span class="news-page-number">
                        PAGE <strong>{{ $newsUpdates->currentPage() }}</strong> / {{ max($newsUpdates->lastPage(), 1) }}
                    </span>

                    @if($newsUpdates->hasMorePages())
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="news-page-button">
                            Next →
                        </button>
                    @else
                        <span class="news-page-disabled">Next →</span>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- BRAND CLOSING --}}
    <section class="news-closing">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="news-closing-inner">
                <div>
                    <span class="news-closing-label">TRIFECTA • HEALTH • WELLNESS • PROTECTION</span>
                    <h2>Stay Connected With Us.</h2>
                    <p>Follow our latest announcements and stay updated with everything happening at TRIFECTA.</p>
                </div>
                <a href="{{ $contexts['contact_fb_url'] ?? 'https://facebook.com' }}" target="_blank" rel="noopener noreferrer" class="news-closing-button">
                    Follow Our Updates <span>↗</span>
                </a>
            </div>
        </div>
    </section>
</div>
