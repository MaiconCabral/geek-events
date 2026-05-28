<a href="#" class="category-card" data-category="<?php echo esc_attr($category['slug']); ?>">
    <div class="category-card-icon"><?php echo esc_html($category['emoji'] ?? '🎮'); ?></div>
    <h3 class="category-card-name"><?php echo esc_html($category['name']); ?></h3>
    <span class="category-card-count">(<?php echo intval($category['count']); ?>)</span>
</a>
