<?php get_header(); ?>

<?php while (have_posts()) : the_post();
    $meta = Geek_Events_Helpers::get_event_meta(get_the_ID());
    $available = Geek_Events_Helpers::get_available_tickets(get_the_ID());
    $categories = wp_get_post_terms(get_the_ID(), 'geek_events_category', ['fields' => 'all']);
    $is_open = !in_array($meta['status'], ['encerrado', 'cancelado'], true);
    $is_soldout = $available <= 0;
    $progress = $meta['total_tickets'] > 0 ? round(($meta['tickets_sold'] / $meta['total_tickets']) * 100) : 0;
?>

<section class="single-event-hero">
    <?php if (has_post_thumbnail()) : ?>
        <div class="single-event-bg" style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(null, 'full')); ?>');"></div>
        <div class="single-event-overlay"></div>
    <?php endif; ?>
    <div class="container single-event-hero-content">
        <div class="single-event-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a>
            <span class="sep">/</span>
            <a href="<?php echo esc_url(home_url('/eventos/')); ?>">EVENTOS</a>
            <span class="sep">/</span>
            <span><?php the_title(); ?></span>
        </div>
        <h1 class="single-event-title"><?php the_title(); ?></h1>
        <div class="single-event-hero-meta">
            <span class="event-card-badge status-<?php echo esc_attr($meta['status']); ?>">
                <?php echo esc_html(Geek_Events_Helpers::get_status_label($meta['status'])); ?>
            </span>
            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                <span class="single-event-category"><?php echo esc_html($categories[0]->name); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="single-event-body">
    <div class="container single-event-layout">

        <div class="single-event-main">
            <div class="single-event-description">
                <h2 class="section-subtitle">▓▓ <?php _e('SOBRE O EVENTO', 'geek-events'); ?> ▓▓</h2>
                <div class="event-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <aside class="single-event-sidebar">
            <div class="sidebar-card">
                <h3 class="sidebar-card-title"><?php _e('DETALHES', 'geek-events'); ?></h3>

                <div class="sidebar-info">
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-icon">📅</span>
                        <div>
                            <span class="sidebar-info-label"><?php _e('Data', 'geek-events'); ?></span>
                            <span class="sidebar-info-value"><?php echo esc_html(date_i18n('d/m/Y', strtotime($meta['date']))); ?></span>
                        </div>
                    </div>

                    <?php if ($meta['time']) : ?>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-icon">⏰</span>
                        <div>
                            <span class="sidebar-info-label"><?php _e('Horário', 'geek-events'); ?></span>
                            <span class="sidebar-info-value"><?php echo esc_html($meta['time']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($meta['location']) : ?>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-icon">📍</span>
                        <div>
                            <span class="sidebar-info-label"><?php _e('Local', 'geek-events'); ?></span>
                            <span class="sidebar-info-value"><?php echo esc_html($meta['location']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($meta['address']) : ?>
                    <div class="sidebar-info-item">
                        <span class="sidebar-info-icon">🏠</span>
                        <div>
                            <span class="sidebar-info-label"><?php _e('Endereço', 'geek-events'); ?></span>
                            <span class="sidebar-info-value"><?php echo nl2br(esc_html($meta['address'])); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($meta['total_tickets'] > 0) : ?>
                <div class="sidebar-section">
                    <div class="ticket-progress-header">
                        <span><?php _e('Ingressos vendidos', 'geek-events'); ?></span>
                        <span><?php echo $meta['tickets_sold']; ?> / <?php echo $meta['total_tickets']; ?></span>
                    </div>
                    <div class="ticket-progress-bar">
                        <div class="ticket-progress-fill" style="width: <?php echo min(100, $progress); ?>%;"></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($meta['ticket_price'] > 0) : ?>
                <div class="sidebar-price">
                    <span class="sidebar-price-label"><?php _e('Preço', 'geek-events'); ?></span>
                    <span class="sidebar-price-value">R$ <?php echo number_format($meta['ticket_price'], 2, ',', '.'); ?></span>
                </div>
                <?php else : ?>
                <div class="sidebar-price">
                    <span class="sidebar-price-label"><?php _e('Entrada', 'geek-events'); ?></span>
                    <span class="sidebar-price-value sidebar-price-free"><?php _e('GRATUITA', 'geek-events'); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php get_template_part('template-parts/event-registration-form'); ?>
        </aside>

    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
