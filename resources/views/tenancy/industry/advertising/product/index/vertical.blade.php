{{-- Product index (advertising) standard layout --}}
@extends('tenancy.industry.advertising.layout.app.standard')

@section('title', 'Producten')

@section('content')

    <div class="container mx-auto px-8 py-32">

        {{-- Heading & meta --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div class="space-y-3">
                <h1 class="text-3xl title font-bold leading-tight">Producten</h1>
                <p class="text-sm paragraph">Bekijk ons aanbod promotionele producten.</p>
            </div>

            <div class="flex gap-3 items-center">
                <div class="item p-3 flex items-center gap-2">
                    <span class="iconify size-5 text-primary" data-icon="mdi-package-variant"></span>
                    <span class="text-xs font-medium">Totaal: {{ $products->total() }}</span>
                </div>
                {{-- Placeholder for future filter trigger --}}
                <button type="button" class="btn btn-secondary h-11 px-5 flex items-center gap-2">
                    <span class="iconify size-5" data-icon="mdi-filter-outline"></span>
                    <span class="text-sm">Filters</span>
                </button>
            </div>
        </div>

        <div class="my-16">
            {{ $products->links() }}
        </div>

        {{-- Products grid --}}
        <div class="grid gap-4 lg:gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <div class="group @if(true) card @else item @endif p-0 overflow-hidden flex flex-col">

                    <a href="{{ route('product.show', $product->id) }}"
                       class="aspect-square bg-base-100 relative flex items-center justify-center overflow-hidden">
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                             class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                             loading="lazy">
                    </a>

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        <h2 class="title font-semibold leading-snug">{{ $product->name }}</h2>
                        <div class="mt-auto flex items-center justify-between">
                            <a href="{{ route('product.index', ['categorie' => $product->category_level_3 ?? $product->category_level_2 ?? $product->category_level_1 ?? 'Overige Producten']) }}"
                               class="paragraph">
                                <small>
                                    {{ $product->category_level_3 ?? $product->category_level_2 ?? $product->category_level_1 ?? 'Overige Producten'}}
                                </small>
                            </a>

                            <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary py-1 px-3">
                                <span class="iconify size-4 inline -translate-y-[1px]" data-icon="mdi-arrow-right"></span>
                                Bekijk
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16">
            {{ $products->links() }}
        </div>
    </div>
@endsection
