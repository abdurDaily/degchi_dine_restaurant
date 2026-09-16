<!-- BRANCHES -->
<section class="branch-container" id="new_branch" style="margin-top: 2px;">
    <div class="container px-3 px-sm-4 px-lg-5">
        <x-frontend.section-heading title="Our Branches" subtitle="Our signature experience across the city" />

        <div class="row g-4">
            @forelse($branches as $branch)
                <div class="col-12 col-lg-4">
                    <a href="{{ route('frontend.branches.show', $branch->slug) }}" class="branch-card-new">
                        <div class="branch-card-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h3>{{ $branch->name }}</h3>
                        <span class="phone-number">
                            <i class="fa-solid fa-phone"></i> {{ $branch->phone }}
                        </span>
                        <p class="branch-address-text">{{ $branch->location }}</p>
                        <div class="branch-card-action">
                            <span>Explore <i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No branches are available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
