<template id="tpl-parent">
    <fieldset class="space-y-3" data-path="" data-is-parent="true"> <!-- added data-is-parent -->
        <legend class="text-sm title flex items-center gap-2">
            <span data-prod-attr-name></span>
        </legend>
        <div class="border-l border-l-secondary pl-8">
            <div data-prod-attr-child-grid class="grid gap-4 grid-cols-2"></div>
        </div>
    </fieldset>
</template>

<template id="tpl-radio-option">
    <label class="relative flex gap-4 p-4 item cursor-pointer select-none radio-option" data-path="">
        <div class="rounded-item overflow-hidden option-image-wrapper hidden shrink-0">
            <img src="" alt="" class="object-cover h-16 option-image"/>
        </div>

        <div>
            <div class="flex items-center gap-2">
                <input type="radio" class="option-input"/>
                <p class="font-medium option-name"></p>
            </div>
            <p class="text-xs mt-1 paragraph leading-relaxed option-desc hidden"></p>
        </div>
    </label>
</template>

<template id="tpl-number-option">
    <div class="p-4 item flex flex-col gap-2" data-path="">
        <label class="text-sm font-medium option-name"></label>
        <div class="flex flex-wrap items-center gap-2">
            <input type="number" class="input input-normal h-10 px-3 option-input grow"/>
            <span class="text-xs paragraph limits shrink-0"></span>
        </div>
        <p class="text-xs paragraph option-desc hidden"></p>
    </div>
</template>

