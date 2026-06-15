@extends('frontend.layout')
@section('frontend_content')

<style>
    :root {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --accent-orange: #f39c12;
        --accent-maroon: #8B3A3A;
        --light-bg: #f8f9fa;
        --dark-text: #212529;
        --muted-text: #6c757d;
        --danger-red: #e74c3c;
    }

    /* ===== MODERN QUICK DELIVERY SECTION ===== */
    .branch-top-section {
        background: #ffffff;
        padding: 30px 0;
        border-bottom: 1px solid #f1f3f5;
    }

    .branch-top-section .container {
        display: flex;
        justify-content: space-between; /* Space out title and service buttons */
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .branch-delivery-label {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        color: var(--dark-text);
        font-size: 2rem;
        font-family: 'Poppins', sans-serif;
        letter-spacing: -0.5px;
    }

    .branch-delivery-label i {
        font-size: 2.2rem;
        color: var(--danger-red);
        background: rgba(231, 76, 60, 0.1); /* Subtle badge background for icon */
        padding: 10px;
        border-radius: 12px;
    }

    .branch-delivery-services {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    /* Modern Service Pill Buttons */
    .delivery-service-btn {
        display: flex;
        align-items: center; /* Linear alignment for icon and brand text */
        gap: 12px;
        padding: 10px 20px;
        border-radius: 50px; /* Modern pill style */
        background: #ffffff;
        border: 1px solid #e9ecef;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #343a40;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .delivery-service-btn:hover {
        border-color: transparent;
        color: #ffffff;
        transform: translateY(-3px);
    }

    /* Brand Specific Interactive Hover Transitions */
    .delivery-service-btn[data-brand="foodpanda"]:hover {
        background: #D70F64; /* Brand Pink */
        box-shadow: 0 8px 20px rgba(215, 15, 100, 0.3);
    }

    .delivery-service-btn[data-brand="pathao"]:hover {
        background: #E31313; /* Brand Red */
        box-shadow: 0 8px 20px rgba(227, 19, 19, 0.3);
    }

    .delivery-service-btn[data-brand="foodi"]:hover {
        background: #FF6B00; /* Brand Orange */
        box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
    }

    .delivery-service-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .delivery-service-btn:hover .delivery-service-img {
        transform: scale(1.1);
    }

    .branch-special-btn {
        background: var(--danger-red);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .branch-special-btn:hover {
        background: darken(#e74c3c, 10%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    .branch-special-btn i {
        font-size: 1rem;
    }

    /* ===== MENU HEADER & NAVIGATION ===== */
    .menu-header-section {
        background: white;
        padding: 20px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .menu-header-label {
        display: inline-block;
        background: var(--danger-red);
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-right: 15px;
        margin-bottom: 15px;
    }

    .category-nav {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 5px;
        padding-top: 5px;
        -webkit-overflow-scrolling: touch;
    }

    .category-nav::-webkit-scrollbar {
        height: 4px;
    }

    .category-nav::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .category-nav::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .category-btn {
        padding: 10px 18px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
        white-space: nowrap;
        color: var(--muted-text);
    }

    .category-btn:hover {
        border-color: #CC4632;
        background: rgba(102, 126, 234, 0.05);
    }

    .category-btn.active {
        background: #f05a43;
        color: white;
    }

    /* ===== SEARCH SECTION ===== */
    .search-filter-section {
        background: white;
        padding: 20px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e9ecef;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: -2px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .search-result-item {
        padding: 15px 18px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-result-item:hover {
        background: var(--light-bg);
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-name {
        font-weight: 700;
        color: var(--dark-text);
    }

    .search-result-meta {
        font-size: 0.85rem;
        color: var(--accent-orange);
        font-weight: 700;
    }

    /* ===== MENU CARD GRID ===== */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        padding: 30px 0;
    }

    .menu-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        border: 1px solid #e9ecef;
    }

    .menu-card.hidden {
        display: none;
    }

    .menu-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        border-color: var(--accent-maroon);
    }

    .menu-card-image {
        width: 100%;
        height: 200px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        object-fit: cover;
        border-radius: 12px 12px 0 0;
    }

    .menu-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .menu-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .menu-card-title {
        font-weight: 700;
        font-size: 1rem;
        color: var(--accent-maroon);
        margin: 0 0 10px 0;
        line-height: 1.3;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .menu-card-description {
        font-size: 0.85rem;
        color: var(--muted-text);
        margin-bottom: 15px;
        line-height: 1.5;
        flex: 1;
    }

    .menu-card-price-section {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .menu-card-price-label {
        font-size: 0.7rem;
        color: var(--muted-text);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }

    .menu-card-price {
        font-weight: 700;
        font-size: 1.3rem;
        color: var(--accent-orange);
    }

    .order-now-btn {
        width: 100%;
        padding: 12px;
        background: transparent;
        color: var(--accent-maroon);
        border: 2px solid var(--accent-maroon);
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.95rem;
        margin-top: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .order-now-btn:hover {
        background: var(--accent-maroon);
        color: white;
    }

    .no-items {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted-text);
        font-size: 1.05rem;
        grid-column: 1 / -1;
    }

    /* ===== RESPONSIVE MEDIA QUERIES ===== */
    @media (max-width: 768px) {
        .branch-top-section {
            padding: 20px 0;
        }

        .branch-top-section .container {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .branch-delivery-label {
            font-size: 1.6rem;
        }

        .branch-delivery-label i {
            font-size: 1.8rem;
            padding: 8px;
        }

        .branch-delivery-services {
            width: 100%;
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .menu-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .category-nav {
            flex-wrap: wrap;
        }
    }
</style>

<div class="branch-top-section">
    <div class="container px-4 px-lg-5">
        <div class="branch-delivery-label">
            <i class="ri-e-bike-2-fill"></i>
            For Quick Delivery
        </div>
        <div class="branch-delivery-services">
            @if(!empty($deliveryServices))
                @foreach($deliveryServices as $key => $service)
                    <a href="{{ $service['url'] }}" target="_blank" rel="noopener noreferrer" class="delivery-service-btn" data-brand="{{ $key }}">
                        @php
                            $logoUrl = null;
                            if ($key === 'foodpanda' && $branch->foodpanda_logo) {
                                $logoUrl = strpos($branch->foodpanda_logo, 'http') === 0 
                                    ? $branch->foodpanda_logo 
                                    : asset('uploads/branches/' . $branch->foodpanda_logo);
                            } elseif ($key === 'pathao' && $branch->pathao_logo) {
                                $logoUrl = strpos($branch->pathao_logo, 'http') === 0 
                                    ? $branch->pathao_logo 
                                    : asset('uploads/branches/' . $branch->pathao_logo);
                            } elseif ($key === 'foodi' && $branch->foodi_logo) {
                                $logoUrl = strpos($branch->foodi_logo, 'http') === 0 
                                    ? $branch->foodi_logo 
                                    : asset('uploads/branches/' . $branch->foodi_logo);
                            }
                        @endphp
                        
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" class="delivery-service-img" alt="{{ $service['name'] }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23667eea'/%3E%3Ctext x='50' y='55' font-size='40' font-weight='bold' fill='white' text-anchor='middle'%3E{{ strtoupper(substr($key, 0, 1)) }}%3C/text%3E%3C/svg%3E" class="delivery-service-img" alt="{{ $service['name'] }}" style="display:none;">
                        @else
                            @if($key === 'foodpanda')
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23E62E04'/%3E%3Ctext x='50' y='55' font-size='40' font-weight='bold' fill='white' text-anchor='middle'%3EF%3C/text%3E%3C/svg%3E" class="delivery-service-img" alt="FoodPanda">
                            @elseif($key === 'pathao')
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23F71735'/%3E%3Ctext x='50' y='55' font-size='50' font-weight='bold' fill='white' text-anchor='middle'%3EP%3C/text%3E%3C/svg%3E" class="delivery-service-img" alt="Pathao">
                            @elseif($key === 'foodi')
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23C71E1E'/%3E%3Ctext x='50' y='52' font-size='35' font-weight='bold' fill='white' text-anchor='middle'%3E%26%3C/text%3E%3C/svg%3E" class="delivery-service-img" alt="Foodi">
                            @endif
                        @endif
                        <span>{{ $service['name'] }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="menu-header-section">
    <div class="container px-4 px-lg-5">
        <div class="category-nav" id="categoryNav">
            <button class="category-btn active" data-category="all">All Items</button>
            @foreach($categories as $category)
                <button class="category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>
</div>

<div class="search-filter-section">
    <div class="container px-4 px-lg-5">
        <div class="search-wrapper">
            <input type="text" class="search-input" id="menuSearch" placeholder=" Search menu or category...">
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>
</div>

<div class="container px-4 px-lg-5 pb-5">
    @if($categories->isEmpty() || $categories->every(fn($c) => $c->menus->isEmpty()))
        <div class="no-items">
            <p>No menu items available for this branch.</p>
        </div>
    @else
        <div class="menu-grid" id="menuGrid">
            @foreach($categories as $category)
                @foreach($category->menus as $menu)
                    @php
                        $firstVariation = $menu->variations->sortBy('price')->first();
                        $imagePath = $firstVariation?->image ?? null;
                        if ($imagePath) {
                            $imageUrl = strpos($imagePath, 'http') === 0
                                ? $imagePath
                                : asset($imagePath);
                        } else {
                            $imageUrl = null;
                        }
                        $minPrice = $menu->variations->min('price') ?? 0;
                    @endphp
                    <div class="menu-card menu-offer-card" data-category="{{ $category->id }}" data-menu-id="{{ $menu->id }}" data-menu-name="{{ $menu->name }}" data-menu-price="{{ $minPrice }}">
                        <div class="menu-card-image menu-offer-image-wrap">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="menu-offer-image" onerror="this.parentElement.style.background='#d3d3d3'; this.style.display='none';">
                            @else
                                <i class="ri-restaurant-2-line"></i>
                            @endif
                        </div>
                        <div class="menu-card-body">
                            <h5 class="menu-card-title menu-offer-title">{{ $menu->name }}</h5>
                            <p class="menu-card-description menu-offer-meta">{{ Str::limit($menu->description ?? 'Fresh and delicious item', 80) }}</p>
                            
                            <div class="menu-card-price-section">
                                <div>
                                    <div class="menu-card-price-label menu-offer-price-label">Starts from</div>
                                    <div class="menu-card-price menu-offer-price">৳ {{ number_format((float) $minPrice, 2) }}</div>
                                </div>
                            </div>

                            <button class="order-now-btn menu-offer-cart-btn" type="button" data-variation-id="{{ $firstVariation?->id }}" data-original-price="{{ $minPrice }}">
                                <i class="ri-shopping-bag-line"></i> Order Now
                            </button>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    const menuSearch = document.getElementById('menuSearch');
    const searchResults = document.getElementById('searchResults');
    const menuGrid = document.getElementById('menuGrid');
    
    console.log('Branch page loaded');

    // ===== CATEGORY FILTER ENGINE =====
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const categoryId = this.getAttribute('data-category');
            console.log('Filter by category:', categoryId);
            
            const menuCards = document.querySelectorAll('.menu-card');
            menuCards.forEach(card => {
                if (categoryId === 'all' || card.getAttribute('data-category') === categoryId) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
            
            menuSearch.value = '';
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
        });
    });

    // ===== SEARCH FUNCTIONALITY MOTOR =====
    let searchTimeout;
    menuSearch.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim().toLowerCase();
        
        if (query.length < 1) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            document.querySelectorAll('.menu-card').forEach(card => card.classList.remove('hidden'));
            return;
        }

        searchTimeout = setTimeout(function() {
            let matchCount = 0;
            let html = '';
            const menuCards = document.querySelectorAll('.menu-card');

            menuCards.forEach(card => {
                const menuName = card.getAttribute('data-menu-name').toLowerCase();
                
                if (menuName.includes(query)) {
                    card.classList.remove('hidden');
                    matchCount++;
                    
                    const id = card.getAttribute('data-menu-id');
                    const name = card.getAttribute('data-menu-name');
                    const price = card.getAttribute('data-menu-price');
                    
                    html += `<div class="search-result-item" data-menu-id="${id}" data-menu-name="${name}" data-menu-price="${price}">
                        <div class="search-result-name">${name}</div>
                        <div class="search-result-meta">৳${parseFloat(price).toFixed(2)}</div>
                    </div>`;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (matchCount === 0) {
                html = '<div class="search-result-item" style="cursor: default;"><div class="search-result-name">No items found</div></div>';
            }

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }, 300);
    });

    // ===== INTERACTIVE SEARCH RESULTS CLICK =====
    document.addEventListener('click', function(e) {
        const resultItem = e.target.closest('.search-result-item');
        if (resultItem && resultItem.hasAttribute('data-menu-id')) {
            e.preventDefault();
            const menuId = resultItem.getAttribute('data-menu-id');
            
            // Dismiss active search dropdown
            menuSearch.value = '';
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            
            // Filter target item display configuration
            const menuCards = document.querySelectorAll('.menu-card');
            menuCards.forEach(card => {
                if (card.getAttribute('data-menu-id') === menuId) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
    });

    // ===== CLICK OUTSIDE TRIGGER CLOSES SEARCH DRopdown =====
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-wrapper')) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

@endsection