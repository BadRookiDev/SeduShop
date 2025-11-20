<div id="quickSearchPanel" class="hidden absolute left-0 right-0 top-full">
    <div class="bg-base-100 panel pb-8 pt-4">
        <div class="container mx-auto px-8">
            <div class="flex justify-between items-center mb-4">
                <p class="text-lg title">Zoekresultaten</p>
                <button id="closeQuickSearch" class="text-sm paragraph hover:text-secondary">Sluiten</button>
            </div>

            <div id="quickSearchResults"
                 class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"></div>

            <div id="quickSearchMore" class="mt-8 text-center hidden"
                 data-qs-css-child="inline-block px-4 py-2 btn btn-primary text-sm"></div>
        </div>
    </div>
</div>

<template id="quickSearchResultTemplate">
    <a class="group block item p-3 text-center">

        <div class="aspect-square mb-2 overflow-hidden rounded-item bg-shader flex items-center justify-center">
            <img class="w-full h-full object-contain group-hover:scale-105 transition-transform"/>
        </div>

        <div data-qs-result-text class="text-sm font-medium truncate"></div>
    </a>
</template>
