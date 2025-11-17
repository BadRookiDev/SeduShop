<template id="tpl-parent">
    <fieldset class="space-y-3" data-path="" data-is-parent="true"> <!-- added data-is-parent -->
        <legend class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <span data-prod-attr-name></span>
        </legend>
        <div data-prod-attr-child-grid
             class="grid gap-4 w-full [grid-template-columns:repeat(auto-fit,minmax(14rem,1fr))]"></div>
    </fieldset>
</template>

<template id="tpl-radio-option">
    <label class="relative flex flex-col gap-2 p-4 item cursor-pointer select-none radio-option" data-path="">

        <div class="flex items-start gap-3">
            <input type="radio" class="mt-1 option-input"/>
            <div class="flex-1">
                <span class="font-medium option-name"></span>
                <p class="text-xs mt-1 text-gray-600 leading-relaxed option-desc hidden"></p>
            </div>
        </div>

        <div class="mt-auto rounded-item overflow-hidden option-image-wrapper hidden">
            <img src="" alt="" class="object-cover option-image"/>
        </div>
    </label>
</template>

<template id="tpl-number-option">
    <div class="p-4 item flex flex-col gap-2" data-path="">
        <label class="text-sm font-medium option-name"></label>
        <div class="flex flex-wrap items-center gap-2">
            <input type="number" class="input input-normal h-10 px-3 option-input grow"/>
            <span class="text-xs text-gray-500 limits shrink-0"></span>
        </div>
        <p class="text-xs text-gray-600 option-desc hidden"></p>
    </div>
</template>
