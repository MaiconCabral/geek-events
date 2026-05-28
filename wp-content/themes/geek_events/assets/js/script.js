(function () {
    'use strict';

    const REST_URL = geekEvents.restUrl;
    const EVENTS_ENDPOINT = 'events-with-meta';
    const CATEGORIES_ENDPOINT = 'categories';

    const state = {
        events: [],
        categories: [],
        page: 1,
        perPage: 6,
        totalPages: 0,
        total: 0,
        filters: {
            search: '',
            category: '',
            status: '',
            orderby: 'geek_events_date',
            order: 'ASC',
        },
        isLoading: false,
        hasMore: true,
    };

    const els = {
        grid: document.getElementById('events-grid'),
        skeleton: document.getElementById('skeleton-grid'),
        loadMore: document.getElementById('load-more'),
        categoriesGrid: document.getElementById('categories-grid'),
        search: document.getElementById('filter-search'),
        category: document.getElementById('filter-category'),
        status: document.getElementById('filter-status'),
        order: document.getElementById('filter-order'),
        clear: document.getElementById('filter-clear'),
        hamburger: document.querySelector('.hamburger'),
        nav: document.getElementById('primary-nav'),
    };

    /* =========================================
       API
       ========================================= */
    async function fetchEvents(params) {
        const query = new URLSearchParams();

        query.set('per_page', params.perPage || state.perPage);
        query.set('page', params.page || 1);
        query.set('orderby', params.orderby || state.filters.orderby);
        query.set('order', params.order || state.filters.order);

        if (params.search) query.set('search', params.search);
        if (params.category) query.set('category', params.category);
        if (params.status) query.set('status', params.status);

        const url = REST_URL + EVENTS_ENDPOINT + '?' + query.toString();

        const res = await fetch(url);
        if (!res.ok) throw new Error('Falha ao carregar eventos');

        const data = await res.json();
        return data;
    }

    async function fetchCategories() {
        const url = REST_URL + CATEGORIES_ENDPOINT;
        const res = await fetch(url);
        if (!res.ok) throw new Error('Falha ao carregar categorias');
        return await res.json();
    }

    /* =========================================
       RENDER
       ========================================= */
    const categoryEmojis = {
        'jogos': '🎮',
        'games': '🎮',
        'anime': '🎌',
        'animes': '🎌',
        'rpg': '🎲',
        'cosplay': '👘',
        'filmes': '🎬',
        'cinema': '🎬',
        'hq': '📚',
        'quadrinhos': '📚',
        'musica': '🎵',
        'tecnologia': '💻',
        'tech': '💻',
        'kpop': '🎤',
        'pop': '🎤',
    };

    function getEmojiForCategory(name) {
        const key = name.toLowerCase().trim();
        return categoryEmojis[key] || '🎮';
    }

    function createEventHTML(event) {
        const thumb = event.featured_image_url
            ? `<img src="${event.featured_image_url}" alt="${event.title}" loading="lazy">`
            : `<div class="event-card-thumb-placeholder">GEEK</div>`;

        const categoryBadge = event.categories && event.categories.length > 0
            ? `<span class="event-card-category">${event.categories[0].name}</span>`
            : '';

        const timeHTML = event.meta.event_time
            ? `<span class="event-card-time">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"/><path d="M6 3.5V6l2 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                ${event.meta.event_time}
            </span>`
            : '';

        const locationHTML = event.meta.location
            ? `<div class="event-card-location">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 11S2 7.5 2 5a4 4 0 018 0c0 2.5-4 6-4 6z" stroke="currentColor" stroke-width="1.2"/><circle cx="6" cy="5" r="1.5" fill="currentColor"/></svg>
                ${event.meta.location}
            </div>`
            : '';

        const priceHTML = event.meta.ticket_price
            ? `<span class="event-card-price">R$ ${parseFloat(event.meta.ticket_price).toFixed(2).replace('.', ',')}</span>`
            : '';

        return `
            <article class="event-card" data-id="${event.id}">
                <div class="event-card-thumb">
                    ${thumb}
                    <span class="event-card-badge status-${event.meta.status}">${event.meta.status_label}</span>
                    ${categoryBadge}
                </div>
                <div class="event-card-body">
                    <h3 class="event-card-title">${event.title}</h3>
                    <div class="event-card-meta">
                        <span class="event-card-date">
                            <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><rect x="1" y="2" width="10" height="9" rx="1" stroke="currentColor" stroke-width="1.2"/><path d="M1 5h10" stroke="currentColor" stroke-width="1.2"/><path d="M4 1v2M8 1v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            ${event.meta.event_date}
                        </span>
                        ${timeHTML}
                    </div>
                    ${locationHTML}
                    <div class="event-card-footer">
                        ${priceHTML}
                        <a href="${event.permalink}" class="event-card-link">DETALHES →</a>
                    </div>
                </div>
            </article>
        `;
    }

    function renderEvents(events, append) {
        if (!append) {
            els.grid.innerHTML = '';
        }

        if (events.length === 0) {
            els.grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🕹️</div>
                    <p class="empty-state-text">NENHUM EVENTO ENCONTRADO</p>
                </div>
            `;
            els.loadMore.style.display = 'none';
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'events-grid-inner';

        events.forEach(function (event) {
            wrapper.insertAdjacentHTML('beforeend', createEventHTML(event));
        });

        if (append) {
            const existing = els.grid.querySelector('.events-grid-inner');
            if (existing) {
                existing.appendChild(wrapper);
            } else {
                els.grid.innerHTML = '';
                els.grid.appendChild(wrapper);
            }
        } else {
            els.grid.innerHTML = '';
            els.grid.appendChild(wrapper);
        }

        if (state.hasMore) {
            els.loadMore.style.display = 'inline-flex';
        } else {
            els.loadMore.style.display = 'none';
        }
    }

    function renderError(message) {
        els.grid.innerHTML = `
            <div class="error-state">
                <p class="error-state-text">⚠️ ${message}</p>
                <button class="btn btn-secondary" onclick="location.reload()">TENTAR NOVAMENTE</button>
            </div>
        `;
        els.loadMore.style.display = 'none';
    }

    function renderCategories(categories) {
        if (!categories || categories.length === 0) {
            els.categoriesGrid.innerHTML = '<p style="color:var(--text-muted);text-align:center;grid-column:1/-1;">NENHUMA CATEGORIA ENCONTRADA</p>';
            return;
        }

        let html = '';
        categories.forEach(function (cat) {
            const emoji = getEmojiForCategory(cat.name);
            html += `
                <a href="#" class="category-card" data-category="${cat.slug}">
                    <div class="category-card-icon">${emoji}</div>
                    <h3 class="category-card-name">${cat.name}</h3>
                    <span class="category-card-count">(${cat.count})</span>
                </a>
            `;
        });

        els.categoriesGrid.innerHTML = html;
    }

    /* =========================================
       LOADING
       ========================================= */
    function showSkeleton() {
        if (els.skeleton) {
            els.skeleton.style.display = 'grid';
        }
    }

    function hideSkeleton() {
        if (els.skeleton) {
            els.skeleton.style.display = 'none';
        }
    }

    /* =========================================
       LOAD ACTIONS
       ========================================= */
    async function loadEvents(append) {
        if (state.isLoading) return;
        state.isLoading = true;

        if (!append) {
            state.page = 1;
            showSkeleton();
        }

        try {
            const params = {
                page: state.page,
                perPage: state.perPage,
                search: state.filters.search,
                category: state.filters.category,
                status: state.filters.status,
                orderby: state.filters.orderby,
                order: state.filters.order,
            };

            const data = await fetchEvents(params);

            state.totalPages = data.total_pages || 1;
            state.total = data.total || 0;
            state.hasMore = state.page < state.totalPages;

            hideSkeleton();
            renderEvents(data.events || [], append);

        } catch (err) {
            hideSkeleton();
            renderError(err.message || 'ERRO AO CARREGAR EVENTOS');
            console.error(err);
        } finally {
            state.isLoading = false;
        }
    }

    async function loadMore() {
        if (state.isLoading || !state.hasMore) return;
        state.page++;
        await loadEvents(true);
    }

    async function loadCategories() {
        try {
            const categories = await fetchCategories();

            state.categories = categories;
            renderCategories(categories);

            const select = els.category;
            categories.forEach(function (cat) {
                const opt = document.createElement('option');
                opt.value = cat.slug;
                opt.textContent = cat.name;
                select.appendChild(opt);
            });

        } catch (err) {
            els.categoriesGrid.innerHTML = `<p style="color:var(--neon-pink);text-align:center;grid-column:1/-1;">ERRO AO CARREGAR CATEGORIAS</p>`;
            console.error(err);
        }
    }

    /* =========================================
       FILTERS
       ========================================= */
    function updateFilters() {
        state.filters.search = els.search.value.trim();

        const orderVal = els.order.value;
        if (orderVal === 'title') {
            state.filters.orderby = 'title';
            state.filters.order = 'ASC';
        } else if (orderVal === 'geek_events_date&order=desc') {
            state.filters.orderby = 'geek_events_date';
            state.filters.order = 'DESC';
        } else {
            state.filters.orderby = orderVal;
            state.filters.order = 'ASC';
        }

        state.filters.category = els.category.value;
        state.filters.status = els.status.value;

        updateClearButton();
        loadEvents(false);
    }

    function updateClearButton() {
        const hasFilters = state.filters.search
            || state.filters.category
            || state.filters.status
            || state.filters.orderby !== 'geek_events_date';

        els.clear.style.display = hasFilters ? 'inline-flex' : 'none';
    }

    function clearFilters() {
        els.search.value = '';
        els.category.value = '';
        els.status.value = '';
        els.order.value = 'geek_events_date';

        state.filters.search = '';
        state.filters.category = '';
        state.filters.status = '';
        state.filters.orderby = 'geek_events_date';
        state.filters.order = 'ASC';

        els.clear.style.display = 'none';
        loadEvents(false);
    }

    function applyCategoryFilter(slug) {
        els.category.value = slug;
        updateFilters();
    }

    /* =========================================
       HAMBURGER
       ========================================= */
    function toggleMenu() {
        els.hamburger.classList.toggle('active');
        els.nav.classList.toggle('active');
        const isOpen = els.nav.classList.contains('active');
        els.hamburger.setAttribute('aria-expanded', isOpen);

        if (isOpen) {
            const overlay = document.createElement('div');
            overlay.className = 'nav-overlay active';
            overlay.id = 'nav-overlay';
            overlay.addEventListener('click', closeMenu);
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
        } else {
            closeMenu();
        }
    }

    function closeMenu() {
        els.hamburger.classList.remove('active');
        els.nav.classList.remove('active');
        els.hamburger.setAttribute('aria-expanded', 'false');

        const overlay = document.getElementById('nav-overlay');
        if (overlay) {
            overlay.remove();
        }
        document.body.style.overflow = '';
    }

    /* =========================================
       INIT
       ========================================= */
    function init() {
        loadEvents(false);
        loadCategories();

        /* Filters */
        let searchTimeout;
        els.search.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(updateFilters, 400);
        });

        els.category.addEventListener('change', updateFilters);
        els.status.addEventListener('change', updateFilters);
        els.order.addEventListener('change', updateFilters);
        els.clear.addEventListener('click', clearFilters);

        /* Load More */
        els.loadMore.addEventListener('click', loadMore);

        /* Hamburger */
        if (els.hamburger) {
            els.hamburger.addEventListener('click', toggleMenu);
        }

        /* Category click */
        els.categoriesGrid.addEventListener('click', function (e) {
            const card = e.target.closest('.category-card');
            if (card) {
                e.preventDefault();
                const slug = card.dataset.category;
                applyCategoryFilter(slug);

                document.getElementById('eventos').scrollIntoView({ behavior: 'smooth' });
            }
        });

        /* Close menu on nav link click (mobile) */
        els.nav.addEventListener('click', function (e) {
            if (e.target.tagName === 'A' && window.innerWidth <= 768) {
                closeMenu();
            }
        });

        /* Close menu on resize */
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', init);

})();
