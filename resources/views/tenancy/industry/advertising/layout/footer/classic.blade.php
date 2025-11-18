{{-- Classic footer (refactored) --}}
@php
    $supportPhone = config('app.support_phone', '+31 20 123 4567');
    $supportHours = config('app.support_hours', '09:00–17:30');
@endphp
<footer class="panel panel-bottom bg-base-100">
    <div class="container mx-auto px-8 py-24 space-y-24">
        {{-- Brand + Support + Newsletter side column --}}
        <div class="grid gap-16 lg:gap-20 grid-cols-1 lg:grid-cols-4">
            <div class="space-y-10 lg:col-span-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-8">
                    <a href="/" class="inline-flex items-center h-14 shrink-0" aria-label="Home">
                        <div class="p-4 bg-base-100 rounded-btn">
                            @include('temp.example-logo')
                        </div>
                    </a>
                    <div class="flex items-center gap-4 bg-shader text-base-100 rounded-btn pl-4 pr-6 h-14">
                        <span class="iconify size-11 p-3 bg-secondary rounded-btn" data-icon="mdi-telephone"></span>
                        <div class="text-sm leading-snug">
                            <div><a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="font-semibold hover:underline">{{ $supportPhone }}</a></div>
                            <div class="opacity-75 text-[11px]">Bereikbaar: {{ $supportHours }}</div>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed max-w-3xl">Wij leveren promotionele producten met focus op kwaliteit, snelheid en duurzame keuzes. Ontdek een zorgvuldig samengestelde catalogus en persoonlijk advies voor elke campagne.</p>

                {{-- Link groups --}}
                <div class="grid gap-12 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-semibold tracking-wider uppercase text-gray-500">Shop</h3>
                        <nav class="space-y-2 text-sm">
                            <a href="/" class="block hover:text-primary">Alle producten</a>
                            <a href="/promotions" class="block hover:text-primary">Acties</a>
                            <a href="/new" class="block hover:text-primary">Nieuw</a>
                            <a href="/popular" class="block hover:text-primary">Populair</a>
                        </nav>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-semibold tracking-wider uppercase text-gray-500">Service</h3>
                        <nav class="space-y-2 text-sm">
                            <a href="/customer-service" class="block hover:text-primary">Helpcentrum</a>
                            <a href="/shipping" class="block hover:text-primary">Verzending</a>
                            <a href="/returns" class="block hover:text-primary">Retouren</a>
                            <a href="/contact" class="block hover:text-primary">Contact</a>
                        </nav>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-semibold tracking-wider uppercase text-gray-500">Account</h3>
                        <nav class="space-y-2 text-sm">
                            @guest
                                <a href="/login" class="block hover:text-primary">Inloggen</a>
                                <a href="/register" class="block hover:text-primary">Registreren</a>
                            @endguest
                            @auth
                                <a href="/profile" class="block hover:text-primary">Profiel</a>
                                <a href="/orders" class="block hover:text-primary">Bestellingen</a>
                            @endauth
                            <a href="/cart" class="block hover:text-primary">Winkelwagen</a>
                        </nav>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-semibold tracking-wider uppercase text-gray-500">Over ons</h3>
                        <nav class="space-y-2 text-sm">
                            <a href="/about" class="block hover:text-primary">Ons verhaal</a>
                            <a href="/sustainability" class="block hover:text-primary">Duurzaamheid</a>
                            <a href="/careers" class="block hover:text-primary">Werken bij</a>
                            <a href="/affiliates" class="block hover:text-primary">Affiliates</a>
                        </nav>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-semibold tracking-wider uppercase text-gray-500">Extra</h3>
                        <nav class="space-y-2 text-sm">
                            <a href="/blog" class="block hover:text-primary">Blog</a>
                            <a href="/inspiration" class="block hover:text-primary">Inspiratie</a>
                            <a href="/press" class="block hover:text-primary">Pers</a>
                            <a href="/disclaimer" class="block hover:text-primary">Disclaimer</a>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Newsletter side column --}}
            <aside class="space-y-6 lg:pt-4">
                <div class="card p-8 flex flex-col gap-6">
                    <div>
                        <h2 class="text-xl font-bold leading-tight">Nieuwsbrief</h2>
                        <p class="mt-2 text-sm text-gray-600">1x per week inzichten & exclusieve aanbiedingen. Geen spam.</p>
                    </div>
                    <form class="flex flex-col gap-4" novalidate>
                        <label for="classic-news-email" class="sr-only">E-mailadres</label>
                        <input id="classic-news-email" type="email" required placeholder="Uw e-mailadres" class="input input-normal h-11 px-4">
                        <button class="btn btn-primary h-11" type="submit">Inschrijven</button>
                    </form>
                    <p class="text-[11px] text-gray-500">Door in te schrijven gaat u akkoord met de <a href="/privacy" class="underline hover:text-primary">privacyverklaring</a>.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="item p-4 flex items-center gap-3">
                        <span class="iconify size-6 text-primary" data-icon="mdi-truck-fast"></span>
                        <span class="text-xs font-medium">Snelle levering</span>
                    </div>
                    <div class="item p-4 flex items-center gap-3">
                        <span class="iconify size-6 text-primary" data-icon="mdi-shield-check"></span>
                        <span class="text-xs font-medium">Veilig betalen</span>
                    </div>
                    <div class="item p-4 flex items-center gap-3">
                        <span class="iconify size-6 text-primary" data-icon="mdi-leaf"></span>
                        <span class="text-xs font-medium">Duurzaam</span>
                    </div>
                    <div class="item p-4 flex items-center gap-3">
                        <span class="iconify size-6 text-primary" data-icon="mdi-star-circle"></span>
                        <span class="text-xs font-medium">Topkwaliteit</span>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Bottom unique bar --}}
        <div class="card p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-8 bg-base-100 border border-gray-200">
            <div class="flex items-center gap-4">
                <a href="/" class="inline-flex items-center h-10" aria-label="Home">
                    @include('temp.example-logo')
                </a>
                <span class="text-sm font-medium">Sedushop – Promotionele producten met aandacht</span>
            </div>
            <nav class="flex flex-wrap gap-4 text-xs font-medium">
                <a href="/privacy" class="hover:text-primary">Privacy</a>
                <a href="/terms" class="hover:text-primary">Voorwaarden</a>
                <a href="/cookies" class="hover:text-primary">Cookies</a>
                <a href="/disclaimer" class="hover:text-primary">Disclaimer</a>
            </nav>
            <div class="flex items-center gap-3 text-xs">
                <span class="opacity-60">Betaalmethoden:</span>
                <span class="iconify size-5" data-icon="mdi-credit-card" title="Creditcard"></span>
                <span class="iconify size-5" data-icon="mdi-paypal" title="PayPal"></span>
                <span class="iconify size-5" data-icon="mdi-bank" title="Bankoverschrijving"></span>
            </div>
        </div>
        <div class="text-center text-[11px] text-gray-500">&copy; {{ date('Y') }} SEDUSHOP. Alle rechten voorbehouden.</div>
    </div>
</footer>
