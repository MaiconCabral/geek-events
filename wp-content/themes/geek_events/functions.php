<?php

function geek_events_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary' => __('Menu Principal', 'geek-events'),
        'footer'  => __('Menu Rodapé', 'geek-events'),
    ]);
}
add_action('after_setup_theme', 'geek_events_setup');

function geek_events_scripts() {
    wp_enqueue_style('geek-events-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_style('geek-events-main', get_template_directory_uri() . '/assets/css/style.css', [], wp_get_theme()->get('Version'));
    wp_enqueue_script('geek-events-script', get_template_directory_uri() . '/assets/js/script.js', [], wp_get_theme()->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'geek_events_scripts');
