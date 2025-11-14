<div id="quickSearchPanel" class="hidden absolute left-0 right-0 top-full">
    <div class="bg-base-100 panel pb-8 pt-4">
        <div class="container mx-auto px-8">
            <div class="flex justify-between items-center mb-4">
                <p class="text-lg font-semibold">Zoekresultaten</p>

                <div class="flex items-center gap-8">
                    <div id="quickSearchMore" class="text-center hidden"
                         data-qs-css-child="underline decoration decoration-secondary text-sm text-secondary">
                    </div>

                    <button id="closeQuickSearch" class="text-sm text-gray-500 hover:text-gray-700">Sluiten</button>
                </div>
            </div>
            <div id="quickSearchResults"
                 class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"></div>
        </div>
    </div>
</div>

<template id="quickSearchResultTemplate">
    <a class="group flex w-full items-center gap-4 item text-center overflow-hidden pr-4">

        <div class="aspect-square w-24 overflow-hidden rounded-item bg-shader shrink-0">
            <img class="w-full h-full object-contain group-hover:scale-105 transition-transform"/>
        </div>

        <div data-qs-result-text class="text-sm font-medium truncate"></div>
    </a>
</template>
