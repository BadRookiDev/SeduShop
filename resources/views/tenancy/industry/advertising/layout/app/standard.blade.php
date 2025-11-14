<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SEDUSHOP' }}</title>

    <!-- CSRF Token -->
    <meta id="csrf-meta" name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts & icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        {{ $style ?? '' }}
    </style>

    <style>
        :root {
            --global-border-radius: 0px;
            --btn-font-weight: 700;

            --item-background: none;
            --item-border: 1px solid var(--color-shader);

            --panel-border-bottom: 2px solid var(--color-shader);

            --input-background: none;
            --input-normal-border: 1px solid var(--color-shader);

            --color-primary: #FF500B;
            --color-secondary: #A62F00;

            /*
            --color-primary: #b23a48;
            --color-primary-fade: #fcb9b2;
            --color-secondary: #461220;
            --color-base-100: #f2e4e8;
            */


            --btn-secondary-background: transparent;
            --btn-secondary-color: var(--color-primary);
            --btn-secondary-border: 1px solid var(--color-primary);

            --card-background: var(--color-base-100);
            --card-border: 2px solid var(--color-shader);
        }

        .btn-primary:hover {
            background-color: var(--color-secondary);
        }

        .btn-secondary:hover {
            color: var(--color-base-100);
            background-color: var(--color-primary);
        }

        .item:hover {
            border-color: var(--color-primary);
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-base-200">

@include('tenancy.industry.advertising.layout.header.standard')

<main class="flex-grow">
    @yield('content')
</main>

@stack('scripts')
@include('tenancy.industry.advertising.layout.utils.quicksearch')
</body>
</html>


