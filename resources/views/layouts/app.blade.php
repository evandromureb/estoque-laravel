<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100" x-data>
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>

            <div
                x-show="$store.deleteConfirm.open"
                x-cloak
                class="fixed inset-0 z-[100] overflow-y-auto"
                style="display: none;"
            >
                <div class="flex min-h-screen items-center justify-center px-4 py-8">
                    <div
                        class="fixed inset-0 z-[90] bg-gray-900/70 backdrop-blur-sm"
                        @click="$store.deleteConfirm.close()"
                    ></div>
                    <div
                        class="relative z-[100] w-full max-w-md overflow-hidden rounded-2xl border border-red-100 bg-white shadow-2xl"
                        @click.stop
                    >
                        <div class="border-b border-red-50 bg-red-50 px-6 py-4">
                            <h3 class="text-lg font-black text-red-900" x-text="$store.deleteConfirm.title"></h3>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm leading-relaxed text-gray-700" x-text="$store.deleteConfirm.message"></p>
                        </div>
                        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <button
                                type="button"
                                class="rounded-xl px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-200/80"
                                @click="$store.deleteConfirm.close()"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                class="rounded-xl bg-red-600 px-5 py-2 text-sm font-bold text-white shadow hover:bg-red-700"
                                @click="$store.deleteConfirm.confirm()"
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
