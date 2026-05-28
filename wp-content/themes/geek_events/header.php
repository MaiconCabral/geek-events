<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header id="site-header">
        <div class="container">
            <div class="site-branding">
                <?php if (has_custom_logo()) {
                    the_custom_logo();
                } else { ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                <?php } ?>
            </div>

            <nav id="primary-nav">
                <?php wp_nav_menu(['theme_location' => 'primary', 'menu_class' => 'nav-menu']); ?>
            </nav>
        </div>
    </header>

    <main id="main-content">
