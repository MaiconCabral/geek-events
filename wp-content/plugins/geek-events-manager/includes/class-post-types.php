<?php

defined('ABSPATH') || exit;

class Geek_Events_Post_Types {

    // Registra os hooks de init para CPT e taxonomia
    public static function init() {
        add_action('init', [__CLASS__, 'register_event_post_type']);
        add_action('init', [__CLASS__, 'register_event_category_taxonomy']);
    }

    // Registra o Custom Post Type geek_events_event
    public static function register_event_post_type() {
        $labels = [
            'name'                  => __('Eventos', 'geek-events-manager'),
            'singular_name'         => __('Evento', 'geek-events-manager'),
            'menu_name'             => __('Eventos', 'geek-events-manager'),
            'add_new'               => __('Adicionar Novo', 'geek-events-manager'),
            'add_new_item'          => __('Adicionar Novo Evento', 'geek-events-manager'),
            'edit_item'             => __('Editar Evento', 'geek-events-manager'),
            'new_item'              => __('Novo Evento', 'geek-events-manager'),
            'view_item'             => __('Ver Evento', 'geek-events-manager'),
            'search_items'          => __('Buscar Eventos', 'geek-events-manager'),
            'not_found'             => __('Nenhum evento encontrado', 'geek-events-manager'),
            'not_found_in_trash'    => __('Nenhum evento encontrado na lixeira', 'geek-events-manager'),
            'all_items'             => __('Todos os Eventos', 'geek-events-manager'),
            'archives'              => __('Arquivo de Eventos', 'geek-events-manager'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-calendar-alt',
            'menu_position'      => 5,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest'       => true,
            'rest_base'          => 'events',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite'            => ['slug' => 'eventos'],
            'capability_type'    => 'post',
        ];

        register_post_type('geek_events_event', $args);
    }

    // Registra a taxonomia geek_events_category (hierárquica)
    public static function register_event_category_taxonomy() {
        $labels = [
            'name'              => __('Categorias de Evento', 'geek-events-manager'),
            'singular_name'     => __('Categoria de Evento', 'geek-events-manager'),
            'search_items'      => __('Buscar Categorias', 'geek-events-manager'),
            'all_items'         => __('Todas as Categorias', 'geek-events-manager'),
            'parent_item'       => __('Categoria Pai', 'geek-events-manager'),
            'parent_item_colon' => __('Categoria Pai:', 'geek-events-manager'),
            'edit_item'         => __('Editar Categoria', 'geek-events-manager'),
            'update_item'       => __('Atualizar Categoria', 'geek-events-manager'),
            'add_new_item'      => __('Adicionar Nova Categoria', 'geek-events-manager'),
            'new_item_name'     => __('Novo Nome da Categoria', 'geek-events-manager'),
            'menu_name'         => __('Categorias', 'geek-events-manager'),
        ];

        $args = [
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_in_menu'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rest_base'         => 'event-categories',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
            'rewrite'           => ['slug' => 'categoria-evento'],
        ];

        register_taxonomy('geek_events_category', 'geek_events_event', $args);
    }
}
