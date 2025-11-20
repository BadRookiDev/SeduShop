<script>
    (function () {
        const locale = @json($locale);
        const rootData = @json($productData['options']);
        const detailsUrl = @json(route('product.show.details', ['productId' => $product->id])); // new details endpoint URL
        const priceUrl = @json(route('product.calculate-price', ['productId' => $product->id]));
        const configRoot = document.getElementById('config-root');
        const summaryEl = document.getElementById('config-summary');
        const formEl = document.getElementById('product-config-form');
        const submitBtn = document.getElementById('add-to-cart-btn');

        const tplParent = document.getElementById('tpl-parent');
        const tplRadio = document.getElementById('tpl-radio-option');
        const tplNumber = document.getElementById('tpl-number-option');

        const nodeIndex = new Map(); // path => node
        let detailsLoaded = false;
        let pendingDetailsPromise = null;
        let lastPriceSignature = null;

        let prodPrices = [];

        const productConfig = {
            code: "{{ $product->vendor_product_id }}",
            options: []
        };

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

            if (['width', 'height', 'amount', 'decimal', 'number'].includes(typeCode)) {
                el = tplNumber.content.firstElementChild.cloneNode(true);
                el.dataset.path = path;
                el.querySelector('.option-name').innerHTML = translate(node) + (node.unit_code ? ' <span class="paragraph">(' + node.unit_code + ')</span>' : '');

                const inp = el.querySelector('input.option-input');
                inp.name = path;
                inp.min = node.min_value ?? 0;
                inp.max = node.max_value;
                inp.step = node.step_size ?? 1;

                const limits = [];
                if (node.min_value != null && node.min_value != 0) limits.push('min ' + node.min_value);
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
            // after structure change, re-evaluate completeness
            evaluateCompleteness();
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
            evaluateCompleteness();
        }

        function updateSummary() {
            const items = [];
            const options = [];

            // Iterate per parent fieldset to enforce mixed group mutual exclusivity
            const parents = configRoot.querySelectorAll('fieldset[data-is-parent="true"]');
            parents.forEach(fs => {
                const parentPath = fs.dataset.path;
                const parentNode = nodeIndex.get(parentPath);
                if (!parentNode) return;

                const radios = fs.querySelectorAll('input[type=radio]');
                const checkedRadio = Array.from(radios).find(r => r.checked);
                const nums = fs.querySelectorAll('input[type=number]');
                const filledNums = Array.from(nums).filter(n => n.value !== '');

                const isMixed = radios.length > 0 && nums.length > 0;

                if (isMixed) {
                    if (checkedRadio) {
                        // Radio chosen: ignore numbers entirely
                        const valCode = checkedRadio.value;
                        const optionNode = (parentNode.children || []).find(c => c.code === valCode);
                        if (optionNode) {
                            items.push(translate(parentNode) + ': ' + translate(optionNode));
                            options.push({code: valCode});
                        }
                    } else if (filledNums.length) {
                        // Numbers filled: ignore radios entirely
                        filledNums.forEach(inp => {
                            const numNode = nodeIndex.get(inp.name);
                            if (numNode) {
                                items.push(translate(numNode) + ': ' + inp.value + (numNode.unit_code ? numNode.unit_code : ''));
                                options.push({code: numNode.code, value: inp.value});
                            }
                        });
                    }
                    // If mixed but neither chosen, nothing added.
                    return;
                }

                // Pure radio group
                if (radios.length && checkedRadio) {
                    const valCode = checkedRadio.value;
                    const optionNode = (parentNode.children || []).find(c => c.code === valCode);
                    if (optionNode) {
                        items.push(translate(parentNode) + ': ' + translate(optionNode));
                        options.push({code: valCode});
                    }
                    return;
                }

                // Pure number group
                if (!radios.length && nums.length) {
                    nums.forEach(inp => {
                        if (!inp.value) return;
                        const numNode = nodeIndex.get(inp.name);
                        if (numNode) {
                            items.push(translate(numNode) + ': ' + inp.value + (numNode.unit_code ? numNode.unit_code : ''));
                            options.push({code: numNode.code, value: inp.value});
                        }
                    });
                }
            });

            summaryEl.innerHTML = '';
            if (!items.length) {
                summaryEl.innerHTML = '<li class="paragraph">Selecteer opties om de configuratie te zien.</li>';
            } else {
                items.forEach(txt => {
                    const li = document.createElement('li');
                    li.textContent = txt;
                    summaryEl.appendChild(li);
                });
            }

            productConfig.options = options;
        }

        function handleRadioChange(e) {
            const input = e.target;
            const groupPath = input.name;
            const chosenCode = input.value;
            const parentNode = nodeIndex.get(groupPath);
            if (!parentNode) return;
            const optionNode = (parentNode.children || []).find(c => c.code === chosenCode);
            if (!optionNode) return;

            // Mutual exclusivity: clear number inputs in same fieldset when a radio is chosen (mixed group case)
            const fs = configRoot.querySelector(`fieldset[data-path="${groupPath}"]`);
            if (fs) {
                const nums = fs.querySelectorAll('input[type=number]');
                nums.forEach(n => { if (n.value !== '') n.value = ''; });
            }

            ensureDetailsLoaded().then(() => {
                clearDescendants(groupPath);
                attachDependentParents(optionNode, groupPath.split('__').concat(chosenCode), groupPath);
                updateSummary();
                evaluateCompleteness();
            });
        }

        // NEW: number inputs can trigger loading of child attributes
        function handleNumberChange(e) {
            const input = e.target;
            const fullPath = input.name; // path of the numeric option node
            const parts = fullPath.split('__');
            const parentPath = parts.slice(0, -1).join('__');

            // Mutual exclusivity: if this number has a value, uncheck radios in same fieldset
            const fs = input.closest('fieldset[data-is-parent="true"]');
            if (fs && input.value !== '') {
                fs.querySelectorAll('input[type=radio]:checked').forEach(r => { r.checked = false; });
            }

            updateSummary(); // after potential unchecking

            if (!input.value) { // if cleared, remove descendants anchored at parent
                if (parentPath) clearDescendants(parentPath);
                evaluateCompleteness();
                return;
            }
            const optionCode = parts[parts.length - 1];
            const parentNode = nodeIndex.get(parentPath);
            if (!parentNode) return;
            const optionNode = (parentNode.children || []).find(c => c.code === optionCode);
            if (!optionNode) return;
            ensureDetailsLoaded().then(() => {
                clearDescendants(parentPath);
                attachDependentParents(optionNode, parts, parentPath);
                evaluateCompleteness();
            });
        }

        function isConfigComplete() {
            // Each rendered parent fieldset must have all its child inputs satisfied
            const parents = configRoot.querySelectorAll('fieldset[data-is-parent="true"]');
            if (!parents.length) return false;
            for (const fs of parents) {
                const radios = fs.querySelectorAll('input[type=radio]');
                const nums = fs.querySelectorAll('input[type=number]');

                const anyRadioChecked = Array.from(radios).some(r => r.checked);
                const anyNumberFilled = Array.from(nums).some(n => n.value !== '');
                const allNumbersFilled = Array.from(nums).every(n => n.value !== '');

                if (radios.length && nums.length) {
                    // Mixed group: satisfied if a radio is chosen OR any number has a value.
                    if (!anyRadioChecked && !anyNumberFilled) return false;
                    continue;
                }

                if (radios.length && !anyRadioChecked) return false; // pure radio group
                if (!radios.length && nums.length && !allNumbersFilled) return false; // pure number group requires all filled
            }
            return true;
        }

        function requestPrice() {
            const signature = JSON.stringify(productConfig); // naive signature
            if (signature === lastPriceSignature) return; // skip duplicate
            lastPriceSignature = signature;
            fetch(priceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    vendor: 'probo',
                    products: [productConfig]
                })
            }).then(r => r.json())
                .then(data => {
                    updatePrice(data);
                })
                .catch(err => console.error('Failed to calculate price', err, productConfig));
        }

        function updatePrice(priceData) {
            const prices = [...priceData.prices || []];

            // remove dominated entries: if there exists an entry with the same
            // products_purchase_price but lower production_hours, drop the slower one.
            const filtered = (() => {
                const kept = new Map(); // price -> lowest production_hours seen
                const result = [];

                for (const p of prices) {
                    const priceKey = Number(p.products_purchase_price); // normalize key
                    const ph = parseFloat(p.production_hours) || 0;

                    if (!kept.has(priceKey)) {
                        // first time we see this price => keep
                        kept.set(priceKey, ph);
                        result.push(p);
                    } else {
                        const bestPh = kept.get(priceKey);
                        // only keep if this has strictly lower production hours than previously kept
                        if (ph < bestPh) {
                            // replace previously kept entry with this one (faster)
                            // find and replace in result
                            const idx = result.findIndex(r => Number(r.products_purchase_price) === priceKey && (parseFloat(r.production_hours) || 0) === bestPh);
                            if (idx !== -1) result.splice(idx, 1, p);
                            kept.set(priceKey, ph);
                        }
                        // otherwise skip because we already have same price with <= production_hours
                    }
                }

                return result;
            })();

            prodPrices = filtered.sort((a,b) => {
                if (a.products_purchase_price < b.products_purchase_price) return -1;
                if (a.products_purchase_price > b.products_purchase_price) return 1;

                if (parseFloat(a.production_hours) < parseFloat(b.production_hours)) return -1;
                if (parseFloat(a.production_hours) > parseFloat(b.production_hours)) return 1;

                return 0;
            });

            displayPrices();
        }

        function evaluateCompleteness() {
            updateSummary(); // ensure latest summary
            const complete = isConfigComplete();
            submitBtn.disabled = !complete;
            if (complete) {
                requestPrice();
            }
        }

        function displayPrices(prodPricesIndex = 0) {
            const priceIndicationEl = formEl.querySelector('#price-indication');
            priceIndicationEl.classList.remove('hidden');

            const select = priceIndicationEl.querySelector('select');
            select.innerHTML = '';

            prodPrices.forEach((pp, idx) => {
                const opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = `Prijs: €${pp.products_purchase_price.toFixed(2)} | Productietijd: ${pp.production_hours} uur`;
                if(idx === prodPricesIndex) opt.selected = true;
                select.appendChild(opt);
            });

            priceIndicationEl.querySelector('[data-total-price]').innerText = `Totaal: €${prodPrices[prodPricesIndex].products_purchase_price.toFixed(2)}`;
            priceIndicationEl.querySelector('[data-unit-price]').innerText = `Prijs per stuk: €${prodPrices[prodPricesIndex].products[0].prices_per_product.purchase_price.toFixed(2)}`;
        }

        function init() {
            rootData.forEach(parent => {
                configRoot.appendChild(renderParent(parent, [parent.code]));
            });

            formEl.addEventListener('change', e => {
                if (e.target.matches('input[type=radio]')) handleRadioChange(e);
                if (e.target.matches('input[type=number]')) handleNumberChange(e);
            });

            // use input event for numbers to react immediately
            //todo: debounce 1s
            formEl.addEventListener('input', e => {
                if (e.target.matches('input[type=number]')) handleNumberChange(e);
            });

            formEl.querySelector('#prod-time').addEventListener('change', e => {
                displayPrices(parseInt(e.target.value));
            });

            evaluateCompleteness();
        }

        init();
    })();
</script>
