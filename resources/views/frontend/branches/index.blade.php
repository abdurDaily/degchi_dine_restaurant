@extends('frontend.layout')

@section('meta_title', 'Our Branches')
@section('meta_description', 'Find Degchi Dine branch locations, menus and contact details across Chittagong.')

@section('frontend_content')

<style>
    .branches-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0;
        color: white;
        text-align: center;
        margin-bottom: 40px;
    }

    .branches-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .branches-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .branch-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .branch-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }

    .branch-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        text-align: center;
        cursor: pointer;
    }

    .branch-card-header:hover {
        opacity: 0.9;
    }

    .branch-card-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .branch-card-body {
        padding: 20px;
        flex: 1;
    }

    .branch-info {
        margin-bottom: 15px;
    }

    .branch-info-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .branch-info-value {
        font-size: 1rem;
        color: #212529;
        font-weight: 500;
    }

    .branch-phone {
        color: #667eea;
        cursor: pointer;
        font-weight: 600;
    }

    .branch-phone:hover {
        text-decoration: underline;
    }

    .branch-card-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 10px;
    }

    .btn-branch {
        flex: 1;
        padding: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        display: block;
    }

    .btn-branch:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        text-decoration: none;
    }

    .branches-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .delivery-icons {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .delivery-icon-link {
        display: inline-block;
        cursor: pointer;
    }

    .delivery-icon-link img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: transform 0.2s;
    }

    .delivery-icon-link img:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .branches-hero h1 {
            font-size: 1.8rem;
        }

        .branches-grid {
            grid-template-columns: 1fr;
        }

        .branch-card-footer {
            flex-direction: column;
        }

        .btn-branch {
            width: 100%;
        }
    }
</style>

<div class="branches-hero">
    <div class="container">
        <h1>Our Branches</h1>
        <p>Choose your nearest location to explore our menu and delivery services</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="branches-grid">
            @forelse($branches as $branch)
                <div class="branch-card">
                    <div class="branch-card-header" onclick="goToBranch('{{ route('frontend.branches.show', $branch) }}')">
                        <h3>{{ $branch->name }}</h3>
                    </div>
                    <div class="branch-card-body">
                        <div class="branch-info">
                            <div class="branch-info-label">📍 Location</div>
                            <div class="branch-info-value">{{ $branch->location }}</div>
                        </div>
                        <div class="branch-info">
                            <div class="branch-info-label">📞 Phone</div>
                            <div class="branch-info-value">
                                <span class="branch-phone" onclick="callPhone('{{ $branch->phone }}'); return false;">{{ $branch->phone }}</span>
                            </div>
                        </div>
                        
                        @if($branch->foodpanda_url || $branch->pathao_url || $branch->foodi_url)
                            <div class="branch-info">
                                <div class="branch-info-label">🚚 Delivery Services</div>
                                <div class="delivery-icons">
                                    @if($branch->foodpanda_url)
                                        <a href="{{ $branch->foodpanda_url }}" target="_blank" rel="noopener noreferrer" title="Order from FoodPanda" class="delivery-icon-link">
                                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23E62E04'/%3E%3Ctext x='50' y='55' font-size='40' font-weight='bold' fill='white' text-anchor='middle'%3EF%3C/text%3E%3C/svg%3E" alt="FoodPanda">
                                        </a>
                                    @endif
                                    @if($branch->pathao_url)
                                        <a href="{{ $branch->pathao_url }}" target="_blank" rel="noopener noreferrer" title="Order from Pathao" class="delivery-icon-link">
                                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23F71735'/%3E%3Ctext x='50' y='55' font-size='50' font-weight='bold' fill='white' text-anchor='middle'%3EP%3C/text%3E%3C/svg%3E" alt="Pathao">
                                        </a>
                                    @endif
                                    @if($branch->foodi_url)
                                        <a href="{{ $branch->foodi_url }}" target="_blank" rel="noopener noreferrer" title="Order from Foodi" class="delivery-icon-link">
                                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23C71E1E'/%3E%3Ctext x='50' y='52' font-size='35' font-weight='bold' fill='white' text-anchor='middle'%3E%26%3C/text%3E%3C/svg%3E" alt="Foodi">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="branch-card-footer">
                        <button class="btn-branch" onclick="goToBranch('{{ route('frontend.branches.show', $branch) }}')">
                            <i class="ri-store-2-line me-1"></i> View Menu
                        </button>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="font-size: 1.1rem; color: #6c757d;">No branches available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
    function goToBranch(url) {
        window.location.href = url;
    }

    function callPhone(phone) {
        window.location.href = 'tel:' + phone;
    }
</script>

@endsection
