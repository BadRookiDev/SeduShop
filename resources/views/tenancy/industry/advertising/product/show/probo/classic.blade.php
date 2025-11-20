@extends('tenancy.industry.advertising.layout.app.standard')

@section('content')

    @php
        //todo: move this later

        /** @var App\Models\Product $product */
        $locale = 'nl';
        $vp = $product->product_data_override ?? [];
        if (empty($vp)) {
            $vp = $product->vendor_product_data ?? [];
        }

        $title = data_get($vp, "translations.$locale.title") ?? data_get($vp, 'translations.en.title') ?? $product->name;
        $description = data_get($vp, "translations.$locale.description") ?? data_get($vp, 'translations.en.description');
        $images = data_get($vp, 'images', []);

        $imageUrls = collect($images)
            ->filter(fn($img) => ($img['language'] ?? 'all') === 'all' || $img['language'] === $locale)
            ->pluck('url');
    @endphp

    <div class="container mx-auto px-8 py-32">
        <div class="flex flex-col md:flex-row gap-12">
            <div class="md:w-1/3">
                <div class="w-full rounded-card flex items-center justify-center overflow-hidden">
                    @if($imageUrls->isNotEmpty())
                        <img src="{{ $imageUrls->first() }}" alt="{{ $title }}" class="w-full h-full object-cover">
                    @else
                        <span class="paragraph">Geen afbeelding</span>
                    @endif
                </div>
                @if($imageUrls->count() > 1)
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($imageUrls->slice(1) as $thumb)
                            <button type="button"
                                    class="relative aspect-square overflow-hidden border border-gray-200 hover:border-primary">
                                <img src="{{ $thumb }}" alt="{{ $title }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="md:w-2/3 space-y-8">
                <div>
                    <h1 class="text-3xl font-bold leading-tight">{{ $title }}</h1>
                    @if($description)
                        <p class="mt-4 paragraph leading-relaxed">{{ $description }}</p>
                    @endif
                </div>

            </div>
        </div>

        <form id="product-config-form" class="mt-16">
            <div id="config-root" class="space-y-8"></div>

            <div class="grid grid-cols-2 gap-8 mt-16">
                <div class="p-8 card">
                    <h2 class="text-xl font-semibold">Samenvatting</h2>
                    <ul class="text-sm paragraph space-y-2 mt-8" id="config-summary">
                        <li class="paragraph">Selecteer opties om de configuratie te zien.</li>
                    </ul>
                </div>

                <div class="h-full" id="price-indication">
                    <div class="card p-8 flex items-center gap-4 justify-between h-full">
                        <div>
                            <p data-total-price class="text-2xl title title-color-alternate"></p>
                            <p data-unit-price class="text-xl paragraph"></p>
                        </div>

                        <div>
                            <label for="prod-time" class="text-sm font-medium option-name block">Productie Tijd</label>
                            <select id="prod-time" class="input input-normal p-2 mt-1">
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-to-cart-btn" data-disabled-hover-message="U moet eerst nog enkele opties selecteren!"
                    class="btn btn-primary p-4 w-full mt-16" disabled>Voeg toe aan winkelwagen</button>
        </form>

        @if(count($productData['related']) > 0)
            <hr class="my-16">

            <section>
                <div class="flex items-center gap-4 -mt-1">
                    <h2 class="text-2xl font-semibold">Gerelateerd</h2>
                    -
                    <p class="mt-1">Misschien bent u ook opzoek naar deze producten!</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 mt-16">
                    @foreach($productData['related'] as $accessory)
                        <div class="p-4 item flex flex-col gap-4 h-full" data-path="">
                            <label class="text-sm font-medium option-name">{{ $accessory['translations'][$locale]['name'] }}</label>

                            <div class="rounded-item overflow-hidden">
                                @if(isset($accessory['images'][0]))
                                    <img data-src="{{ $accessory['images'][0]['url'] }}" data-ll-thresh="sm" class="object-cover option-image"
                                         alt="{{ $accessory['translations'][$locale]['name'] }}"/>
                                @endif
                            </div>

                            <div class="flex gap-2 mt-auto">
                                <input class="w-full input input-normal py-1 px-2" type="number" min="0" value="0">
                                <a class="btn btn-secondary py-1 px-3">
                                    <span class="iconify" data-icon="mdi-eye"></span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    {{-- Templates for dynamic rendering --}}
    @include('tenancy.industry.advertising.product.show.probo.attributes.auto-row-list')

    @include('tenancy.industry.advertising.product.show.probo.utils.script')
@endsection

