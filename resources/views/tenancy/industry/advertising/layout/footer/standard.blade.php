<footer class="bg-base-100 panel panel-bottom">
    <div class="container mx-auto px-8 py-24">
        {{-- Newsletter / CTA --}}
        <div class="card p-10 flex flex-col lg:flex-row gap-8 items-start lg:items-center justify-between">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold leading-tight">Blijf op de hoogte</h2>
                <p class="mt-2 text-gray-600 text-sm leading-relaxed">Ontvang inspiratie, productupdates en exclusieve acties direct in uw inbox.</p>
            </div>
            <form class="w-full max-w-md flex flex-col sm:flex-row gap-4 relative">
                <label for="newsletter-email" class="sr-only">E-mailadres</label>
                <input id="newsletter-email" type="email" placeholder="Uw e-mailadres" class="flex-1 input input-normal h-12 px-4" required>
                <button type="submit" class="btn btn-primary h-12 px-6 whitespace-nowrap">Inschrijven</button>
            </form>
        </div>

        {{-- Link grids --}}
        <div class="mt-24 grid gap-16 md:gap-12 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Shop</h3>
                <nav class="space-y-2 text-sm">
                    <a href="/" class="block hover:text-primary">Alle producten</a>
                    <a href="/promotions" class="block hover:text-primary">Acties</a>
                    <a href="/new" class="block hover:text-primary">Nieuw</a>
                    <a href="/popular" class="block hover:text-primary">Populair</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Klantenservice</h3>
                <nav class="space-y-2 text-sm">
                    <a href="/customer-service" class="block hover:text-primary">Helpcentrum</a>
                    <a href="/shipping" class="block hover:text-primary">Verzending & levering</a>
                    <a href="/returns" class="block hover:text-primary">Retourbeleid</a>
                    <a href="/contact" class="block hover:text-primary">Contact</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Account</h3>
                <nav class="space-y-2 text-sm">
                    <a href="/login" class="block hover:text-primary">Inloggen</a>
                    <a href="/register" class="block hover:text-primary">Registreren</a>
                    <a href="/orders" class="block hover:text-primary">Bestellingen</a>
                    <a href="/profile" class="block hover:text-primary">Profiel</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Over ons</h3>
                <nav class="space-y-2 text-sm">
                    <a href="/about" class="block hover:text-primary">Ons verhaal</a>
                    <a href="/sustainability" class="block hover:text-primary">Duurzaamheid</a>
                    <a href="/partners" class="block hover:text-primary">Partners</a>
                    <a href="/careers" class="block hover:text-primary">Werken bij</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Meer</h3>
                <nav class="space-y-2 text-sm">
                    <a href="/blog" class="block hover:text-primary">Blog</a>
                    <a href="/inspiration" class="block hover:text-primary">Inspiratie</a>
                    <a href="/press" class="block hover:text-primary">Pers</a>
                    <a href="/affiliates" class="block hover:text-primary">Affiliates</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-sm font-semibold tracking-wide uppercase text-gray-500">Contact</h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p class="flex items-center gap-2"><span class="iconify size-4 text-primary" data-icon="mdi-map-marker"></span> Amsterdam, NL</p>
                    <p class="flex items-center gap-2"><span class="iconify size-4 text-primary" data-icon="mdi-telephone"></span> +31 20 123 4567</p>
                    <p class="flex items-center gap-2"><span class="iconify size-4 text-primary" data-icon="mdi-email"></span> info@sedushop.nl</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <a href="#" class="btn btn-secondary p-0 h-10 w-10 flex items-center justify-center"><span class="iconify size-5" data-icon="mdi-facebook"></span></a>
                    <a href="#" class="btn btn-secondary p-0 h-10 w-10 flex items-center justify-center"><span class="iconify size-5" data-icon="mdi-instagram"></span></a>
                    <a href="#" class="btn btn-secondary p-0 h-10 w-10 flex items-center justify-center"><span class="iconify size-5" data-icon="mdi-linkedin"></span></a>
                    <a href="#" class="btn btn-secondary p-0 h-10 w-10 flex items-center justify-center"><span class="iconify size-5" data-icon="mdi-youtube"></span></a>
                </div>
            </div>
        </div>

        {{-- Badges / Trust row --}}
        <div class="mt-24 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="item p-4 flex items-center gap-3">
                <span class="iconify size-6 text-primary" data-icon="mdi-truck-fast"></span>
                <p class="text-sm font-medium">Snelle levering</p>
            </div>
            <div class="item p-4 flex items-center gap-3">
                <span class="iconify size-6 text-primary" data-icon="mdi-shield-check"></span>
                <p class="text-sm font-medium">Veilig betalen</p>
            </div>
            <div class="item p-4 flex items-center gap-3">
                <span class="iconify size-6 text-primary" data-icon="mdi-leaf"></span>
                <p class="text-sm font-medium">Duurzame opties</p>
            </div>
            <div class="item p-4 flex items-center gap-3">
                <span class="iconify size-6 text-primary" data-icon="mdi-star-circle"></span>
                <p class="text-sm font-medium">Topkwaliteit</p>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-24 pt-12 border-t border-gray-200 flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center justify-between text-sm text-gray-600">
            <div class="flex items-center gap-3">
                <a href="/" class="inline-flex items-center h-6">@include('temp.example-logo')</a>
                <span class="hidden sm:inline">&copy; {{ date('Y') }} SEDUSHOP. Alle rechten voorbehouden.</span>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-1">
                <a href="/privacy" class="hover:text-primary">Privacy</a>
                <a href="/terms" class="hover:text-primary">Voorwaarden</a>
                <a href="/cookies" class="hover:text-primary">Cookies</a>
                <a href="/disclaimer" class="hover:text-primary">Disclaimer</a>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="opacity-60">Betaalmethoden:</span>
                <span class="iconify size-6" data-icon="mdi-credit-card" title="Creditcard"></span>
                <span class="iconify size-6" data-icon="mdi-paypal" title="PayPal"></span>
                <span class="iconify size-6" data-icon="mdi-bank" title="Bankoverschrijving"></span>
            </div>
        </div>
    </div>
</footer>
