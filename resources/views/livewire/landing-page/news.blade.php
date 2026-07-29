<div>

    <!-- =========================
         HERO / FEATURED STORY
    ========================== -->
    <section class="news-hero">
        <div class="news-hero-glow-top"></div>
        <div class="news-hero-glow-bottom"></div>

        <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="text-center max-w-3xl mx-auto">
                <span class="news-hero-badge">
                    TrueLife Dayongan Solidaritas Social Welfare Services, Inc.
                </span>

                <h1 class="news-hero-title">
                    News, Stories &
                    <span class="news-hero-title-highlight">
                        Community Updates
                    </span>
                </h1>
                <p class="news-hero-description">
                    Stay informed with the latest announcements, branch updates, claim stories, and official
                    partnerships from DAYONGAN. Discover important updates, community activities, and milestones
                    that highlight our continued commitment to serving, supporting, and empowering every
                    member and their families.
                </p>
            </div>
        </div>
    </section>

    @if($featuredPost)
        <!-- =========================
             FEATURED NEWS SECTION
        ========================== -->
        <section class="news-featured-section">
            <div class="news-featured-container">

                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="news-section-label">
                        Featured Update
                    </span>

                    <h2 class="news-section-title">
                        Latest News & Community Highlights
                    </h2>

                    <p class="news-section-description">
                        Explore the latest announcements, partnerships, community activities, and important updates from
                        DAYONGAN as we continue strengthening protection and support for every family.
                    </p>
                </div>

                <!-- FEATURED POST BANNER -->
                <div class="news-featured-card">
                    <div class="news-featured-image-wrapper">
                        <img src="{{ $featuredPost->image ? asset('storage/' . $featuredPost->image) : asset('images/hero-bg.jpg') }}"
                             alt="{{ $featuredPost->title }}"
                             class="news-featured-image">

                        <div class="news-featured-tag">
                            Featured Update
                        </div>
                    </div>

                    <div class="news-featured-content">
                        <div>
                            <span class="news-featured-meta">
                                {{ $featuredPost->created_at ? $featuredPost->created_at->format('F Y') : '' }} • {{ $featuredPost->category }}
                            </span>

                            <h2 class="news-featured-heading">
                                {{ $featuredPost->title }}
                            </h2>

                            <p class="news-featured-text">
                                {{ Str::limit(strip_tags($featuredPost->details), 180) }}
                            </p>
                        </div>

                        <div class="news-featured-footer">
                            <span class="news-featured-author">
                                By {{ $featuredPost->user ? $featuredPost->user->firstname . ' ' . $featuredPost->user->lastname : 'Dayongan Media Team' }}
                            </span>

                            <a href="#news-{{ $featuredPost->ID }}" class="news-featured-link">
                                Read Full Story →
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    @endif

<!-- =========================
     CATEGORY FILTER BAR
========================== -->
<section class="news-filter-section">
    <div class="news-filter-container">

        <div class="news-filter-group">
            <button type="button"
                    wire:click="setCategory('all')"
                    class="{{ $selectedCategory === 'all' ? 'news-filter-btn-active' : 'news-filter-btn' }}">
                All Articles
            </button>

            @foreach($allowedCategories as $category)
                <button type="button"
                        wire:click="setCategory('{{ $category }}')"
                        class="{{ $selectedCategory === $category ? 'news-filter-btn-active' : 'news-filter-btn' }}">
                    {{ $category }}
                </button>
            @endforeach
        </div>

        <div class="news-filter-count">
            Showing <span class="font-bold text-[#0B2A5B]">{{ $newsUpdates->count() }} Latest Updates</span>
        </div>

    </div>
</section>

    <!-- =========================
         ARTICLE GRID SECTION
    ========================== -->
    <section class="news-grid-section">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="news-grid">

                @forelse($newsUpdates as $article)
                    <!-- Article Card -->
                    <article class="news-card" id="news-{{ $article->ID }}" wire:key="article-{{ $article->ID }}">
                        <div>
                            <div class="news-card-image-wrapper">
                                <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('images/hero-bg.jpg') }}"
                                     alt="{{ $article->title }}"
                                     class="news-card-image">
                                <span class="news-card-badge {{ $article->category === 'Insurance' ? 'news-badge-health' : 'news-badge-service' }}">
                                    {{ $article->category ?? 'General' }}
                                </span>
                            </div>
                            <div class="news-card-body">
                                <span class="news-card-date">{{ $article->created_at ? $article->created_at->format('M d, Y') : '' }}</span>
                                <h3 class="news-card-title">
                                    {{ $article->title }}
                                </h3>
                                <p class="news-card-excerpt">
                                    {{ Str::limit(strip_tags($article->details), 120) }}
                                </p>
                            </div>
                        </div>
                        <div class="news-card-footer">
                            <span class="news-card-read-time">{{ $article->created_at ? $article->created_at->diffForHumans() : '' }}</span>
                            <a href="#news-{{ $article->ID }}" class="news-card-link">Read More &rarr;</a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-4xl mb-3">📰</div>
                        <h3 class="text-lg font-bold text-gray-700">No news updates found</h3>
                        <p class="text-sm text-gray-500">There are currently no updates under this category.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </section>

    <!-- =========================
         NEWSLETTER SUBSCRIPTION
    ========================== -->
    <section class="news-newsletter-section">
        <div class="news-newsletter-container">
            <span class="news-newsletter-subtitle">Never Miss an Update</span>
            <h2 class="news-newsletter-title">
                Subscribe to Official Bulletins
            </h2>
            <p class="news-newsletter-description">
                Get official announcements, policy advisories, and community health perk updates sent directly to your email inbox or phone.
            </p>

            <form class="news-newsletter-form" wire:submit.prevent="subscribe">
                <input type="email" placeholder="Enter your email address" class="news-newsletter-input">
                <button type="submit" class="news-newsletter-btn">
                    Subscribe Now
                </button>
            </form>
        </div>
    </section>

</div>
