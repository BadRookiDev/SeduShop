<script>
    (function () {
        const input = document.getElementById('q');
        if (!input) return;
        const panel = document.getElementById('quickSearchPanel');
        const resultsGrid = document.getElementById('quickSearchResults');
        const moreEl = document.getElementById('quickSearchMore');
        const tmpl = document.getElementById('quickSearchResultTemplate');
        const closeBtn = document.getElementById('closeQuickSearch');
        let lastQuery = '';
        let ctrl = null;

        function hidePanel() {
            panel.classList.add('hidden');
            resultsGrid.innerHTML = '';
            moreEl.innerHTML = '';
            moreEl.classList.add('hidden');
        }

        // NEW: helper to highlight matched query inside product name (case-insensitive)
        function highlightName(name, query) {
            if (!query || query.length < 2) return escapeHtml(name);
            const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(safeQuery, 'ig');
            return escapeHtml(name).replace(regex, m => '<mark class="text-primary !bg-base-100 rounded-sedu">' + escapeHtml(m) + '</mark>');
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        closeBtn.addEventListener('click', hidePanel);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') hidePanel();
        });

        input.addEventListener('input', async function () {
            const q = input.value.trim();
            if (!q) {
                hidePanel();
                return;
            }

            lastQuery = q;
            if (ctrl) ctrl.abort();
            ctrl = new AbortController();
            const url = '/producten/quick-search?q=' + encodeURIComponent(q);
            try {
                const res = await fetch(url, {signal: ctrl.signal});
                if (!res.ok) throw new Error('network');
                const data = await res.json();
                if (q !== lastQuery) return; // stale
                resultsGrid.innerHTML = '';
                data.results.forEach(r => {
                    const node = tmpl.content.firstElementChild.cloneNode(true);
                    node.href = '/producten/' + r.id;
                    const img = node.querySelector('img');
                    if (r.imageUrl) img.src = r.imageUrl; else img.remove();
                    // CHANGED: highlight matching part in product name
                    node.querySelector('[data-qs-result-text]').innerHTML = highlightName(r.name, q);
                    resultsGrid.appendChild(node);
                });
                if (data.results.length) {
                    panel.classList.remove('hidden');
                } else {
                    hidePanel();
                }
                if (data.matchCount > data.results.length) { //todo: button hieronder moet al in template om met blade component te kunnen doen en de href aan te kunnen passen
                    moreEl.innerHTML = `<a href="#" class="${moreEl.getAttribute('data-qs-css-child')}">Zie alle ${data.matchCount} resultaten</a>`;
                    moreEl.classList.remove('hidden');
                } else {
                    moreEl.classList.add('hidden');
                    moreEl.innerHTML = '';
                }
            } catch (e) {
                if (e.name === 'AbortError') return; // canceled
                hidePanel();
            }
        });
    })();
</script>
