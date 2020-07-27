<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- user -->
        <meta name='signed-in' content="{{ auth()->check() }}">
        <meta name='user-info' content="{{ auth()->user() ?? 'false' }}">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <style>
            body { padding-bottom: 100px }
            .level { display: flex; align-items: center; }
            .flex { flex: 1; }
            .mbe-1 { margin-block-end: 1em; }
            .ml-a { margin-left: auto; }
            .bg-success { background-color: rgb(213, 253, 181) !important }
            .card-footer { padding: 0.5rem !important }
            [v-cloak] { display: none }
            .search-modal-header { background-color: rgb(133, 170, 238) }
            .search-highlight { background-color: rgb(245, 229, 91) }
            .text-omit {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .min-width-0 { min-width: 0; }
        </style>

        @yield('head')

    </head>
    <body>
        <div id="app">

            @include('layouts.nav')

            <main class="py-4">
                @yield('content')
            </main>

            <flash init-message="{{ session('flash') }}"></flash>
        </div>
    </body>
</html>
