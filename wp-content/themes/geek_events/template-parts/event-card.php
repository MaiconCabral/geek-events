<article class="event-card" data-id="<?php echo esc_attr($event['id']); ?>">
    <div class="event-card-thumb">
        <?php if ($event['featured_image_url']) : ?>
            <img src="<?php echo esc_url($event['featured_image_url']); ?>" alt="<?php echo esc_attr($event['title']); ?>" loading="lazy">
        <?php else : ?>
            <div class="event-card-thumb-placeholder">GEEK</div>
        <?php endif; ?>
        <span class="event-card-badge status-<?php echo esc_attr($event['meta']['status']); ?>"><?php echo esc_html($event['meta']['status_label']); ?></span>
        <?php if (!empty($event['categories'])) : ?>
            <span class="event-card-category"><?php echo esc_html($event['categories'][0]['name']); ?></span>
        <?php endif; ?>
    </div>
    <div class="event-card-body">
        <h3 class="event-card-title"><?php echo esc_html($event['title']); ?></h3>
        <div class="event-card-meta">
            <span class="event-card-date">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><rect x="1" y="2" width="10" height="9" rx="1" stroke="currentColor" stroke-width="1.2"/><path d="M1 5h10" stroke="currentColor" stroke-width="1.2"/><path d="M4 1v2M8 1v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                <?php echo esc_html($event['meta']['event_date']); ?>
            </span>
            <?php if ($event['meta']['event_time']) : ?>
                <span class="event-card-time">
                    <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"/><path d="M6 3.5V6l2 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                    <?php echo esc_html($event['meta']['event_time']); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php if ($event['meta']['location']) : ?>
            <div class="event-card-location">
                <svg class="meta-icon" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 11S2 7.5 2 5a4 4 0 018 0c0 2.5-4 6-4 6z" stroke="currentColor" stroke-width="1.2"/><circle cx="6" cy="5" r="1.5" fill="currentColor"/></svg>
                <?php echo esc_html($event['meta']['location']); ?>
            </div>
        <?php endif; ?>
        <div class="event-card-footer">
            <?php if ($event['meta']['ticket_price']) : ?>
                <span class="event-card-price">R$ <?php echo number_format($event['meta']['ticket_price'], 2, ',', '.'); ?></span>
            <?php endif; ?>
            <a href="<?php echo esc_url($event['permalink']); ?>" class="event-card-link"><?php _e('DETALHES', 'geek-events'); ?> →</a>
        </div>
    </div>
</article>
