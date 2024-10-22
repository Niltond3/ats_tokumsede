@extends('templates.application.master')


{{-- ### Attributes for Layout are added here ### --}}
{{--Possibilities:  'fix-header'  'fix-sidebar' 'boxed' 'logo-center' 'single-column' --}}
{{--You can make combinations with them--}}
@section('body-classes','fix-header fix-sidebar')

@section('template-css')
    <link href="{{ mix('/css/material/style.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/colors/blue.css') }}" id="theme" rel="stylesheet">
@endsection

@section('template-custom-js')
    <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/custom.min.js"></script>
@endsection

@section('layout-content')

    @include('templates.application.includes.topbar')

    @include('templates.material.left-sidebar')

    <div class="page-wrapper">

        <div class="container-fluid">

            @if(true)
                @include('templates.application.includes.breadcrumb')
            @else
                <div class="row mb-4"></div>
            @endif

            @yield('content')

            @include('templates.application.includes.right-sidebar')

        </div>

    </div>

@endsection
