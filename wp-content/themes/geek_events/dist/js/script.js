(function(){const l=geekEvents.restUrl,u="events-with-meta",f="categories",r={events:[],categories:[],page:1,perPage:6,totalPages:0,total:0,filters:{search:"",category:"",status:"",orderby:"geek_events_date",order:"ASC"},isLoading:!1,hasMore:!0},t={grid:document.getElementById("events-grid"),skeleton:document.getElementById("skeleton-grid"),loadMore:document.getElementById("load-more"),categoriesGrid:document.getElementById("categories-grid"),search:document.getElementById("filter-search"),category:document.getElementById("filter-category"),status:document.getElementById("filter-status"),order:document.getElementById("filter-order"),clear:document.getElementById("filter-clear"),hamburger:document.querySelector(".hamburger"),nav:document.getElementById("primary-nav")};async function y(e){const a=new URLSearchParams;a.set("per_page",e.perPage||r.perPage),a.set("page",e.page||1),a.set("orderby",e.orderby||r.filters.orderby),a.set("order",e.order||r.filters.order),e.search&&a.set("search",e.search),e.category&&a.set("category",e.category),e.status&&a.set("status",e.status);const s=l+u+"?"+a.toString(),n=await fetch(s);if(!n.ok)throw new Error("Falha ao carregar eventos");return await n.json()}async function v(){const e=l+f,a=await fetch(e);if(!a.ok)throw new Error("Falha ao carregar categorias");return await a.json()}const h={jogos:"🎮",games:"🎮",anime:"🎌",animes:"🎌",rpg:"🎲",cosplay:"👘",filmes:"🎬",cinema:"🎬",hq:"📚",quadrinhos:"📚",musica:"🎵",tecnologia:"💻",tech:"💻",kpop:"🎤",pop:"🎤"};function m(e){const a=e.toLowerCase().trim();return h[a]||"🎮"}function p(e){const a=e.featured_image_url?`<img src="${e.featured_image_url}" alt="${e.title}" loading="lazy">`:'<div class="event-card-thumb-placeholder">GEEK</div>',s=e.categories&&e.categories.length>0?`<span class="event-card-category">${e.categories[0].name}</span>`:"",n=e.meta.event_time?`<span class="event-card-time">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"/><path d="M6 3.5V6l2 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                ${e.meta.event_time}
            </span>`:"",g=e.meta.location?`<div class="event-card-location">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 11S2 7.5 2 5a4 4 0 018 0c0 2.5-4 6-4 6z" stroke="currentColor" stroke-width="1.2"/><circle cx="6" cy="5" r="1.5" fill="currentColor"/></svg>
                ${e.meta.location}
            </div>`:"",N=e.meta.ticket_price?`<span class="event-card-price">R$ ${parseFloat(e.meta.ticket_price).toFixed(2).replace(".",",")}</span>`:"";return`
            <article class="event-card" data-id="${e.id}">
                <div class="event-card-thumb">
                    ${a}
                    <span class="event-card-badge status-${e.meta.status}">${e.meta.status_label}</span>
                    ${s}
                </div>
                <div class="event-card-body">
                    <h3 class="event-card-title">${e.title}</h3>
                    <div class="event-card-meta">
                        <span class="event-card-date">
                            <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><rect x="1" y="2" width="10" height="9" rx="1" stroke="currentColor" stroke-width="1.2"/><path d="M1 5h10" stroke="currentColor" stroke-width="1.2"/><path d="M4 1v2M8 1v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            ${e.meta.event_date}
                        </span>
                        ${n}
                    </div>
                    ${g}
                    <div class="event-card-footer">
                        ${N}
                        <a href="${e.permalink}" class="event-card-link">DETALHES →</a>
                    </div>
                </div>
            </article>
        `}function E(e,a){if(a||(t.grid.innerHTML=""),e.length===0){t.grid.innerHTML=`
                <div class="empty-state">
                    <div class="empty-state-icon">🕹️</div>
                    <p class="empty-state-text">NENHUM EVENTO ENCONTRADO</p>
                </div>
            `,t.loadMore.style.display="none";return}const s=document.createElement("div");if(s.className="events-grid-inner",e.forEach(function(n){s.insertAdjacentHTML("beforeend",p(n))}),a){const n=t.grid.querySelector(".events-grid-inner");n?n.appendChild(s):(t.grid.innerHTML="",t.grid.appendChild(s))}else t.grid.innerHTML="",t.grid.appendChild(s);r.hasMore?t.loadMore.style.display="inline-flex":t.loadMore.style.display="none"}function k(e){t.grid.innerHTML=`
            <div class="error-state">
                <p class="error-state-text">⚠️ ${e}</p>
                <button class="btn btn-secondary" onclick="location.reload()">TENTAR NOVAMENTE</button>
            </div>
        `,t.loadMore.style.display="none"}function L(e){if(!e||e.length===0){t.categoriesGrid.innerHTML='<p style="color:var(--text-muted);text-align:center;grid-column:1/-1;">NENHUMA CATEGORIA ENCONTRADA</p>';return}let a="";e.forEach(function(s){const n=m(s.name);a+=`
                <a href="#" class="category-card" data-category="${s.slug}">
                    <div class="category-card-icon">${n}</div>
                    <h3 class="category-card-name">${s.name}</h3>
                    <span class="category-card-count">(${s.count})</span>
                </a>
            `}),t.categoriesGrid.innerHTML=a}function w(){t.skeleton&&(t.skeleton.style.display="grid")}function d(){t.skeleton&&(t.skeleton.style.display="none")}async function i(e){if(!r.isLoading){r.isLoading=!0,e||(r.page=1,w());try{const a={page:r.page,perPage:r.perPage,search:r.filters.search,category:r.filters.category,status:r.filters.status,orderby:r.filters.orderby,order:r.filters.order},s=await y(a);r.totalPages=s.total_pages||1,r.total=s.total||0,r.hasMore=r.page<r.totalPages,d(),E(s.events||[],e)}catch(a){d(),k(a.message||"ERRO AO CARREGAR EVENTOS"),console.error(a)}finally{r.isLoading=!1}}}async function b(){r.isLoading||!r.hasMore||(r.page++,await i(!0))}async function M(){try{const e=await v();r.categories=e,L(e);const a=t.category;e.forEach(function(s){const n=document.createElement("option");n.value=s.slug,n.textContent=s.name,a.appendChild(n)})}catch(e){t.categoriesGrid.innerHTML='<p style="color:var(--neon-pink);text-align:center;grid-column:1/-1;">ERRO AO CARREGAR CATEGORIAS</p>',console.error(e)}}function o(){r.filters.search=t.search.value.trim();const e=t.order.value;e==="title"?(r.filters.orderby="title",r.filters.order="ASC"):e==="geek_events_date&order=desc"?(r.filters.orderby="geek_events_date",r.filters.order="DESC"):(r.filters.orderby=e,r.filters.order="ASC"),r.filters.category=t.category.value,r.filters.status=t.status.value,C(),i(!1)}function C(){const e=r.filters.search||r.filters.category||r.filters.status||r.filters.orderby!=="geek_events_date";t.clear.style.display=e?"inline-flex":"none"}function T(){t.search.value="",t.category.value="",t.status.value="",t.order.value="geek_events_date",r.filters.search="",r.filters.category="",r.filters.status="",r.filters.orderby="geek_events_date",r.filters.order="ASC",t.clear.style.display="none",i(!1)}function _(e){t.category.value=e,o()}function A(){t.hamburger.classList.toggle("active"),t.nav.classList.toggle("active");const e=t.nav.classList.contains("active");if(t.hamburger.setAttribute("aria-expanded",e),e){const a=document.createElement("div");a.className="nav-overlay active",a.id="nav-overlay",a.addEventListener("click",c),document.body.appendChild(a),document.body.style.overflow="hidden"}else c()}function c(){t.hamburger.classList.remove("active"),t.nav.classList.remove("active"),t.hamburger.setAttribute("aria-expanded","false");const e=document.getElementById("nav-overlay");e&&e.remove(),document.body.style.overflow=""}function $(){i(!1),M();let e;t.search.addEventListener("input",function(){clearTimeout(e),e=setTimeout(o,400)}),t.category.addEventListener("change",o),t.status.addEventListener("change",o),t.order.addEventListener("change",o),t.clear.addEventListener("click",T),t.loadMore.addEventListener("click",b),t.hamburger&&t.hamburger.addEventListener("click",A),t.categoriesGrid.addEventListener("click",function(a){const s=a.target.closest(".category-card");if(s){a.preventDefault();const n=s.dataset.category;_(n),document.getElementById("eventos").scrollIntoView({behavior:"smooth"})}}),t.nav.addEventListener("click",function(a){a.target.tagName==="A"&&window.innerWidth<=768&&c()}),window.addEventListener("resize",function(){window.innerWidth>768&&c()})}document.addEventListener("DOMContentLoaded",$)})();
