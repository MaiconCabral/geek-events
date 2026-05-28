<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="site-header" class="site-header">
    <div class="container header-inner">
        <div class="site-branding">
            <?php if (has_custom_logo()) {
                the_custom_logo();
            } else { ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">GEEK EVENTS</a>
            <?php } ?>
        </div>

        <button class="hamburger" aria-label="Abrir menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <nav id="primary-nav" class="primary-nav" role="navigation" aria-label="<?php esc_attr_e('Menu Principal', 'geek-events'); ?>">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ]); ?>
        </nav>
    </div>
</header>

<main id="main-content" class="main-content">
