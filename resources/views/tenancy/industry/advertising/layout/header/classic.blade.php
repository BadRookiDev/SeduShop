{{-- resources/views/components/header.blade.php --}}
@php
    // Customize these, or pull from config/settings table.
    $supportPhone = config('app.support_phone', '+31 20 123 4567');
    $supportHours = config('app.support_hours', '09:00–17:30');
    $logoUrl      = asset('images/logo.svg');
    $cartCount    = session('cart.count', 0); // swap with your cart service if needed
@endphp

<header class="sticky top-0 z-40 bg-base-100 panel">
    <div class="absolute h-1/2 bg-primary w-full top-0 left-0"></div>

    <nav class="container px-8 mx-auto py-8 relative">

        <div class="flex flex-row-reverse">
            <div class="grow">
                {{-- Utility bar --}}
                <div class="hidden md:flex items-center justify-between pl-4 lg:pl-8 h-12 text-sm paragraph">
                    <div class="flex items-center bg-shader text-base-100 rounded-btn">
                        <span class="iconify size-11 p-3 bg-secondary rounded-btn" data-icon="mdi-telephone"></span>

                        <span class="whitespace-nowrap px-4">
                            Vragen? bel ons op <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}"
                                                  class="hover:underline font-bold">{{ $supportPhone }}</a>
                            <span class="opacity-75 ml-4">({{ $supportHours }})</span>
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-8">
                        <a href="/"
                           class="hidden md:inline-flex items-center gap-2 h-10 text-base-100 hover:scale-110">
                            <span class="iconify size-4" data-icon="mdi-home"></span>
                            <span>Home</span>
                        </a>

                        <a href=" /customer-service"
                           class="hidden md:inline-flex items-center gap-2 h-10 text-base-100 hover:scale-110">
                            <span class="iconify size-4" data-icon="mdi-headset"></span>
                            <span>Klantenservice</span>
                        </a>

                        {{-- Auth area --}}
                        @guest
                            <a href="/login"
                               class="hidden md:inline-flex items-center gap-2 h-10 text-base-100 hover:scale-110">
                                <span class="iconify size-4"
                                      data-icon="mdi-user"></span>
                                <span>Log in</span>
                            </a>
                        @endguest

                        @auth
                            {{-- Account dropdown (details/summary is keyboard-accessible) --}}
                            <details class="relative">
                                <summary
                                    class="list-none inline-flex items-center gap-2 px-3 h-10 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer select-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-width="1.5"
                                              d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Account</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 opacity-70"
                                         viewBox="0 0 20 20"
                                         fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.19l3.71-3.96a.75.75 0 211.08 1.04l-4.25 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </summary>
                                <div
                                    class="absolute right-0 mt-2 w-56 rounded-2xl border border-gray-200 bg-white shadow-lg overflow-hidden">
                                    <nav class="p-2">
                                        <a href="{{ '/profile' }}" class="block px-3 py-2 rounded-lg hover:bg-gray-50">Profile</a>
                                        <a href="{{ '/orders' }}"
                                           class="block px-3 py-2 rounded-lg hover:bg-gray-50">Orders</a>
                                        <form method="POST" action="{{ '/logout' }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-50">Log
                                                out
                                            </button>
                                        </form>
                                    </nav>
                                </div>
                            </details>
                        @endauth
                    </div>
                </div>

                {{-- Main bar --}}
                <div class="pl-4 lg:pl-8 mt-16">
                    <div class="flex items-center justify-between gap-8">
                        <div class="flex flex-1 w-full">
                            <label for="q" class="sr-only">zoek producten</label>

                            <div class="relative flex-1 flex">
                                <input id="q" name="q" type="search" autocomplete="off" placeholder="Zoek producten…"
                                       class="w-full h-12 input input-normal px-4 pr-12 placeholder:text-gray-400 outline-0">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="size-6 absolute right-0 mt-3 mr-3 pointer-events-none text-secondary"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-width="2"
                                          d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                                </svg>
                            </div>
                        </div>

                        <a href="/cart"
                           class="inline-flex items-center justify-center h-12 px-4 w-fit btn btn-secondary">
                            <span class="iconify size-5" data-icon="mdi-cart"></span>

                            @if($cartCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-black text-white text-[11px] leading-5 text-center">
                                    {{ $cartCount }}
                                </span>
                            @endif

                            <span class="sr-only">Open cart ({{ $cartCount }})</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-16">
                <button class="block px-4 h-12 btn btn-primary">
                    productoverzicht
                </button>

                <a href="/" class="inline-flex items-center shrink-0 h-12">
                    <div class="p-3 w-full bg-base-100 rounded-btn">
                        @include('temp.example-logo')
                    </div>
                </a>
            </div>
        </div>

    </nav>
    {{-- Quick search dropdown panel (hidden by default) --}}
    @include('tenancy.industry.advertising.layout.utils.quicksearch.minimal')
</header>
