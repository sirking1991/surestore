@extends('storefront.layout')

@section('content')

    <x-storefront.top-nav :store="$store"  :storeFront="$storeFront"/>

    <x-storefront.header-nav :store="$store"  :storeFront="$storeFront"/>

    <x-storefront.about :store="$store"  :storeFront="$storeFront"/>

    <x-storefront.footer :store="$store"  :storeFront="$storeFront" />

@endsection