<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zinc-950 antialiased">
        <div class="min-h-screen bg-[#f5f5f7] px-4 py-6 sm:px-6">
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center justify-center">
                <div class="hidden flex-1 pr-12 lg:block">
                    <a href="/" class="inline-flex">
                        <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="SENAI" loading="eager">
                    </a>

                    <div class="mt-14 max-w-xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-[#e30613]">Acesso institucional</p>
                        <h1 class="mt-4 text-5xl font-semibold tracking-normal text-zinc-950">Gestão escolar simples, segura e objetiva.</h1>
                        <p class="mt-5 text-lg leading-8 text-zinc-600">Controle autorizações, turmas e alunos em um ambiente desenhado para a rotina do SENAI-SP.</p>
                    </div>
                </div>

                <div class="w-full max-w-md rounded-lg border border-zinc-200 bg-white px-6 py-6 shadow-sm sm:px-8">
                    <div class="mb-8 lg:hidden">
                        <a href="/" class="inline-flex">
                            <img class="h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="SENAI" loading="eager">
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
