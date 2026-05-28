<?php get_header(); ?>

<?php get_template_part('template-parts/hero'); ?>

<section id="eventos" class="section-eventos">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">▓▓ <?php _e('PRÓXIMOS EVENTOS', 'geek-events'); ?> ▓▓</h2>
        </div>

        <div class="filters-bar" id="filters-bar">
            <div class="filter-search">
                <input type="text" id="filter-search" class="filter-input" placeholder="<?php esc_attr_e('BUSCAR EVENTOS...', 'geek-events'); ?>">
            </div>
            <div class="filter-selects">
                <select id="filter-category" class="filter-select">
                    <option value=""><?php _e('TODAS CATEGORIAS', 'geek-events'); ?></option>
                </select>
                <select id="filter-status" class="filter-select">
                    <option value=""><?php _e('TODOS OS STATUS', 'geek-events'); ?></option>
                    <option value="agendado"><?php _e('AGENDADO', 'geek-events'); ?></option>
                    <option value="acontecendo"><?php _e('ACONTECENDO', 'geek-events'); ?></option>
                    <option value="encerrado"><?php _e('ENCERRADO', 'geek-events'); ?></option>
                </select>
                <select id="filter-order" class="filter-select">
                    <option value="geek_events_date"><?php _e('DATA ↑', 'geek-events'); ?></option>
                    <option value="geek_events_date&order=desc"><?php _e('DATA ↓', 'geek-events'); ?></option>
                    <option value="title"><?php _e('TÍTULO A-Z', 'geek-events'); ?></option>
                </select>
            </div>
            <button id="filter-clear" class="btn btn-ghost filter-clear" style="display:none;"><?php _e('LIMPAR FILTROS', 'geek-events'); ?> ✕</button>
        </div>

        <div class="events-grid" id="events-grid">
            <div class="skeleton-grid" id="skeleton-grid">
                <?php for ($i = 0; $i < 6; $i++) : ?>
                    <div class="skeleton-card">
                        <div class="skeleton-thumb"></div>
                        <div class="skeleton-body">
                            <div class="skeleton-line w-80"></div>
                            <div class="skeleton-line w-60"></div>
                            <div class="skeleton-line w-40"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="events-footer" id="events-footer">
            <button id="load-more" class="btn btn-secondary load-more" style="display:none;"><?php _e('CARREGAR MAIS', 'geek-events'); ?></button>
        </div>
    </div>
</section>

<section class="section-categorias">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">▓▓ <?php _e('CATEGORIAS', 'geek-events'); ?> ▓▓</h2>
        </div>
        <div class="categories-grid" id="categories-grid">
            <div class="skeleton-categories">
                <?php for ($i = 0; $i < 4; $i++) : ?>
                    <div class="skeleton-card">
                        <div class="skeleton-line w-40" style="height:40px;"></div>
                        <div class="skeleton-line w-60"></div>
                        <div class="skeleton-line w-30"></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
