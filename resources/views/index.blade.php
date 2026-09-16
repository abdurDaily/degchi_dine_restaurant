@extends('frontend.layout')

@section('meta_title', 'Home')
@section('meta_description',
    'Degchi Dine — authentic kacchi, biriyani and clay-pot dining in Halishahar, Chittagong.
    Order online or book your table today.')

@section('frontend_content')
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.branches')
    @include('frontend.partials.home.menu-slider')
    @include('frontend.partials.home.signature-platters')
    @include('frontend.partials.home.reels')
    @include('frontend.partials.home.about_partials')
    @include('frontend.partials.home.reviews')
    @include('frontend.partials.home.visit-us')
    @include('frontend.partials.home.offer-popup')
    @include('frontend.partials.home.menu-modals')
   {{-- Js script Code --}}
    @include('frontend.partials.home.scripts')
@endsection
