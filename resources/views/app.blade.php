<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title inertia>{{ config('app.name', 'tokumsede - delivery') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://unpkg.com/tailwindcss-intersect@2.x.x/dist/observer.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script
        {{-- src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg&loading=async&libraries=places"> --}}
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxP_e0Xopzv9ptFJ_ZKkgjonXgx0TBivE&loading=async&libraries=places">
    </script>

    <!-- Styles -->

    <!--  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">

        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />

        <script type="text/javascript" src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>-->

    <!-- Scripts -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        }
    </script>

    <!-- BEGIN QZ TRAY CONFIGURATIONS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/11.1.0/jsrsasign-all-min.js">
    </script>
    <!-- END QZ TRAY CONFIGURATIONS -->

    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>

<body class="antialiased">
    @inertia
</body>

</html>
