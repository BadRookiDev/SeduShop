@extends('tenancy.industry.advertising.layout.app.standard')

@section('content')

    @php
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
                        <img src="{{ $imageUrls->first() }}" alt="{{ $title }}" class="w-full h-full object-cover"
                             loading="lazy">
                    @else
                        <span class="text-gray-400">Geen afbeelding</span>
                    @endif
                </div>
                @if($imageUrls->count() > 1)
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($imageUrls->slice(1) as $thumb)
                            <button type="button"
                                    class="relative aspect-square overflow-hidden border border-gray-200 hover:border-primary">
                                <img src="{{ $thumb }}" alt="{{ $title }}" class="w-full h-full object-cover"
                                     loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="p-8 card mt-16">
                    <h2 class="text-xl font-semibold">Samenvatting</h2>
                    <ul class="text-sm text-gray-700 space-y-2 mt-8" id="config-summary">
                        <li class="text-gray-500">Selecteer opties om de configuratie te zien.</li>
                    </ul>
                </div>
            </div>

            <div class="md:w-2/3 space-y-8">
                <div>
                    <h1 class="text-3xl font-bold leading-tight">{{ $title }}</h1>
                    @if($description)
                        <p class="mt-4 text-gray-700 leading-relaxed">{{ $description }}</p>
                    @endif
                </div>

                {{-- Progressive attribute tree form --}}
                <form method="POST" action="#" id="product-config-form">
                    @csrf

                    <div id="config-root" class="space-y-8"></div>

                    <button type="submit" class="btn btn-primary p-4 w-full mt-16">Voeg toe aan winkelwagen</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 mt-16">
            @foreach($productData['accessories'] as $accessory)

                <div class="p-4 item flex flex-col gap-4 h-full" data-path="">
                    <label class="text-sm font-medium option-name">{{ $accessory['translations'][$locale]['name'] }}</label>

                    <div class="rounded-item overflow-hidden">
                        <img src="{{ $accessory['images'][0]['url'] }}" class="object-cover option-image"
                             alt="{{ $accessory['translations'][$locale]['name'] }}" loading="lazy"/>
                    </div>

                    <input class="w-full input input-normal py-1 px-2 mt-auto" type="number" min="0" value="0">
                </div>

            @endforeach
        </div>
    </div>

    {{-- Templates for dynamic rendering --}}
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

            <div class="mt-3 rounded-item overflow-hidden option-image-wrapper hidden">
                <img src="" alt="" class="object-cover option-image" loading="lazy"/>
            </div>
        </label>
    </template>

    <template id="tpl-number-option">
        <div class="p-4 item flex flex-col gap-2" data-path="">
            <label class="text-sm font-medium option-name"></label>
            <div class="flex items-center gap-2">
                <input type="number" class="input input-normal h-10 px-3 option-input grow"/>
                <span class="text-xs text-gray-500 limits shrink-0"></span>
            </div>
            <p class="text-xs text-gray-600 option-desc hidden"></p>
        </div>
    </template>

    <script>
        (function () {
            const locale = @json($locale);
            const rootData = @json($productData['options']);
            const detailsUrl = @json(route('product.show.details', $product->id)); // new details endpoint URL
            const configRoot = document.getElementById('config-root');
            const summaryEl = document.getElementById('config-summary');
            const formEl = document.getElementById('product-config-form');

            const tplParent = document.getElementById('tpl-parent');
            const tplRadio = document.getElementById('tpl-radio-option');
            const tplNumber = document.getElementById('tpl-number-option');

            const nodeIndex = new Map(); // path => node
            let detailsLoaded = false;
            let pendingDetailsPromise = null;

            function makePath(trail) {
                return trail.join('__');
            }

            function translate(node) {
                const t = node.translations || {};
                return (t[locale] && (t[locale].name || t[locale].title)) || (t.en && (t.en.name || t.en.title)) || node.code;
            }

            function translateDesc(node) {
                const t = node.translations || {};
                return (t[locale] && t[locale].description) || (t.en && t.en.description) || '';
            }

            function indexNode(node, trail) {
                const path = makePath(trail);
                nodeIndex.set(path, node);
                if (node.children) {
                    node.children.forEach(child => indexNode(child, trail.concat(child.code)));
                }
            }

            // Load second level details once and reintegrate them into rootData structure
            function ensureDetailsLoaded() {
                if (detailsLoaded) return Promise.resolve();
                if (pendingDetailsPromise) return pendingDetailsPromise;

                pendingDetailsPromise = fetch(detailsUrl, {headers: {'Accept': 'application/json'}})
                    .then(r => r.json())
                    .then(mapping => {
                        console.log(mapping);

                        Object.entries(mapping).forEach(([pathKey, children]) => {
                            const [parentCode, childCode] = pathKey.split('__');
                            const parentNode = rootData.find(p => p.code === parentCode);
                            if (!parentNode) return;
                            const optionNode = (parentNode.children || []).find(c => c.code === childCode);
                            if (!optionNode) return;
                            // attach children list if not already present
                            if (!optionNode.children || optionNode.children.length === 0) {
                                optionNode.children = children;
                                children.forEach(ch => indexNode(ch, [parentCode, childCode, ch.code]));
                            }
                        });
                        detailsLoaded = true;
                    })
                    .catch(err => {
                        console.error('Failed to load product details', err);
                    });
                return pendingDetailsPromise;
            }

            rootData.forEach(parent => indexNode(parent, [parent.code]));

            function renderParent(node, trail) {
                const path = makePath(trail);
                console.log(path);
                const parentEl = tplParent.content.firstElementChild.cloneNode(true);

                parentEl.dataset.path = path;
                parentEl.querySelector('[data-prod-attr-name]').textContent = translate(node);

                const grid = parentEl.querySelector('[data-prod-attr-child-grid]');

                (node.children || []).filter(c => !c.is_parent).forEach(child => {
                    const childPath = trail.concat(child.code);
                    grid.appendChild(renderLeaf(child, childPath, trail));
                });

                return parentEl;
            }

            function renderLeaf(node, trail, parentTrail) {
                const path = makePath(trail);
                const typeCode = node.type_code || 'radio';
                let el;

                if (['width', 'height', 'amount'].includes(typeCode)) {
                    el = tplNumber.content.firstElementChild.cloneNode(true);
                    el.dataset.path = path;
                    el.querySelector('.option-name').innerHTML = translate(node) + (node.unit_code ? ' <span class="text-gray-500">(' + node.unit_code + ')</span>' : '');

                    const inp = el.querySelector('input.option-input');
                    inp.name = path;
                    inp.min = node.min_value ?? 0;
                    inp.max = node.max_value;
                    inp.step = node.step_size ?? 1;

                    const limits = [];
                    if (node.min_value != null) limits.push('min ' + node.min_value);
                    if (node.max_value != null && node.max_value != 99999) limits.push('max ' + node.max_value);
                    if (limits.length) el.querySelector('.limits').textContent = limits.join(' · ');

                    const desc = translateDesc(node);
                    if (desc) {
                        const d = el.querySelector('.option-desc');
                        d.textContent = desc;
                        d.classList.remove('hidden');
                    }
                } else {
                    el = tplRadio.content.firstElementChild.cloneNode(true);
                    el.dataset.path = path;
                    el.querySelector('.option-name').textContent = translate(node);
                    const desc = translateDesc(node);
                    if (desc) {
                        const d = el.querySelector('.option-desc');
                        d.textContent = desc;
                        d.classList.remove('hidden');
                    }

                    const image = (node.images || []).find(i => i.language === 'all' || i.language === locale);

                    if (image) {
                        const wrap = el.querySelector('.option-image-wrapper');
                        const img = el.querySelector('.option-image');
                        img.src = image.url;
                        img.alt = translate(node);
                        wrap.classList.remove('hidden');
                    }

                    const inp = el.querySelector('input.option-input');
                    inp.name = makePath(parentTrail); // radio group = parent path
                    inp.value = node.code;
                }
                return el;
            }

            function clearDescendants(startPath) {
                const prefix = startPath + '__';
                [...configRoot.querySelectorAll('[data-path]')].forEach(el => {
                    const p = el.dataset.path;
                    // only remove descendant parent fieldsets, keep immediate option labels/inputs
                    if (p !== startPath && p.startsWith(prefix) && el.dataset.isParent === 'true') {
                        el.remove();
                    }
                });
            }

            function attachDependentParents(optionNode, optionTrail, anchorPath) {
                let insertAfter = configRoot.querySelector(`[data-path="${anchorPath}"]`);

                (optionNode.children || []).filter(c => c.is_parent).forEach(parent => {
                    const parentTrail = optionTrail.concat(parent.code);
                    const parentEl = renderParent(parent, parentTrail);
                    if (insertAfter && insertAfter.nextSibling) {
                        insertAfter.parentNode.insertBefore(parentEl, insertAfter.nextSibling);
                    } else if (insertAfter) {
                        insertAfter.parentNode.appendChild(parentEl);
                    } else {
                        configRoot.appendChild(parentEl);
                    }
                    insertAfter = parentEl;
                });
            }

            function updateSummary() {
                const items = [];
                const radios = formEl.querySelectorAll('input[type=radio]:checked');
                console.log(radios);

                radios.forEach(r => {
                    const parentPath = r.name;
                    const valCode = r.value;
                    const parentNode = nodeIndex.get(parentPath);
                    if (!parentNode) return;
                    const optionNode = (parentNode.children || []).find(c => c.code === valCode);
                    if (optionNode) {
                        items.push(translate(parentNode) + ': ' + translate(optionNode));
                    }
                });
                const nums = formEl.querySelectorAll('input[type=number]');
                nums.forEach(inp => {
                    if (inp.value) {
                        const node = nodeIndex.get(inp.name);
                        if (node) {
                            items.push(translate(node) + ': ' + inp.value + (node.unit_code ? node.unit_code : ''));
                        }
                    }
                });
                summaryEl.innerHTML = '';
                if (!items.length) {
                    summaryEl.innerHTML = '<li class="text-gray-500">Selecteer opties om de configuratie te zien.</li>';
                } else {
                    items.forEach(txt => {
                        const li = document.createElement('li');
                        li.textContent = txt;
                        summaryEl.appendChild(li);
                    });
                }
            }

            function handleRadioChange(e) {
                const input = e.target;
                const groupPath = input.name;
                const chosenCode = input.value;
                const parentNode = nodeIndex.get(groupPath);
                if (!parentNode) return;
                const optionNode = (parentNode.children || []).find(c => c.code === chosenCode);
                if (!optionNode) return;

                ensureDetailsLoaded().then(() => {
                    clearDescendants(groupPath);
                    attachDependentParents(optionNode, groupPath.split('__').concat(chosenCode), groupPath);
                    updateSummary();
                });
            }

            function handleNumberChange() {
                updateSummary();
            }

            function init() {
                rootData.forEach(parent => {
                    configRoot.appendChild(renderParent(parent, [parent.code]));
                });
                formEl.addEventListener('change', e => {
                    if (e.target.matches('input[type=radio]')) handleRadioChange(e);
                    if (e.target.matches('input[type=number]')) handleNumberChange(e);
                });
            }

            init();
        })();
    </script>
@endsection

