@extends('storefront.layout')

@section('content')
    <x-storefront.search-modal />

    <x-storefront.cart />

    <x-storefront.top-nav :store="$store"/>

    <x-storefront.header-nav :store="$store"/>

    <x-storefront.banner-hero :store="$store"/>

    <x-storefront.month-category :store="$store" />

    <x-storefront.featured-product :store="$store" />

    <x-storefront.footer :store="$store" />

@endsection