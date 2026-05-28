<?php

function geek_events_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    add_theme_support('align-wide');

    register_nav_menus([
        'primary' => __('Menu Principal', 'geek-events'),
        'footer'  => __('Menu Rodapé', 'geek-events'),
    ]);
}
add_action('after_setup_theme', 'geek_events_setup');

function geek_events_fonts() {
    wp_enqueue_style(
        'geek-events-fonts',
        'https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Space+Mono:wght@400;700&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'geek_events_fonts');

function geek_events_scripts() {
    wp_enqueue_style('geek-events-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_style('geek-events-main', get_template_directory_uri() . '/assets/css/style.css', ['geek-events-style'], wp_get_theme()->get('Version'));
    $js_src = file_exists(get_template_directory() . '/dist/js/script.js')
        ? get_template_directory_uri() . '/dist/js/script.js'
        : get_template_directory_uri() . '/assets/js/script.js';
    wp_enqueue_script('geek-events-script', $js_src, [], wp_get_theme()->get('Version'), true);

    wp_localize_script('geek-events-script', 'geekEvents', [
        'restUrl'  => rest_url('geek-events/v1/'),
        'siteUrl'  => home_url('/'),
        'ajaxUrl'  => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'geek_events_scripts');
