<?php

defined('ABSPATH') || exit;

class Geek_Events_Rest_API {

    // Registra o hook de inicialização das rotas REST
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    // Registra as rotas /events-with-meta e /categories
    public static function register_routes() {
        register_rest_route('geek-events/v1', '/events-with-meta', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [__CLASS__, 'get_events_with_meta'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('geek-events/v1', '/events-with-meta/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [__CLASS__, 'get_single_event_with_meta'],
            'permission_callback' => '__return_true',
            'args'               => [
                'id' => [
                    'required'          => true,
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                ],
            ],
        ]);

        register_rest_route('geek-events/v1', '/register', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [__CLASS__, 'register_for_event'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('geek-events/v1', '/categories', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [__CLASS__, 'get_categories'],
            'permission_callback' => '__return_true',
        ]);
    }

    // GET /events-with-meta — Lista eventos com metadados, paginação e filtros
    public static function get_events_with_meta($request) {
        $per_page = $request->get_param('per_page') ?: 10;
        $page     = $request->get_param('page') ?: 1;
        $status   = $request->get_param('status');
        $category = $request->get_param('category');
        $search   = $request->get_param('search');
        $orderby  = $request->get_param('orderby') ?: 'geek_events_date';
        $order    = strtoupper($request->get_param('order')) === 'DESC' ? 'DESC' : 'ASC';

        $args = [
            'post_type'      => 'geek_events_event',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => $orderby === 'title' ? 'title' : 'meta_value',
            'order'          => $order,
        ];

        if ($orderby !== 'title') {
            $args['meta_key'] = $orderby;
        }

        if ($status) {
            $args['meta_query'][] = [
                'key'   => 'geek_events_status',
                'value' => sanitize_text_field($status),
            ];
        }

        if ($category) {
            $args['tax_query'][] = [
                'taxonomy' => 'geek_events_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($category),
            ];
        }

        if ($search) {
            $args['s'] = sanitize_text_field($search);
        }

        $query = new WP_Query($args);
        $events = [];

        foreach ($query->posts as $post) {
            $events[] = self::format_event($post);
        }

        $response = new WP_REST_Response([
            'events'       => $events,
            'total'        => $query->found_posts,
            'total_pages'  => $query->max_num_pages,
            'page'         => (int) $page,
            'per_page'     => (int) $per_page,
        ]);

        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);

        return $response;
    }

    // GET /events-with-meta/{id} — Retorna um evento com metadados
    public static function get_single_event_with_meta($request) {
        $post_id = (int) $request->get_param('id');
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'geek_events_event') {
            return new WP_Error('not_found', __('Evento não encontrado', 'geek-events-manager'), ['status' => 404]);
        }

        return new WP_REST_Response(self::format_event($post), 200);
    }

    // GET /categories — Lista todas as categorias de eventos
    public static function get_categories($request) {
        $args = [
            'taxonomy'   => 'geek_events_category',
            'hide_empty' => false,
        ];

        $per_page = $request->get_param('per_page') ?: 0;
        if ($per_page) {
            $args['number'] = $per_page;
        }

        $terms = get_terms($args);

        if (is_wp_error($terms)) {
            return new WP_Error('not_found', __('Nenhuma categoria encontrada', 'geek-events-manager'), ['status' => 404]);
        }

        $data = array_map(function ($term) {
            return [
                'id'          => $term->term_id,
                'name'        => $term->name,
                'slug'        => $term->slug,
                'description' => $term->description,
                'count'       => $term->count,
                'parent'      => $term->parent,
            ];
        }, $terms);

        return new WP_REST_Response($data, 200);
    }

    // POST /register — Cria uma inscrição em um evento
    public static function register_for_event($request) {
        $event_id  = (int) $request->get_param('event_id');
        $name      = sanitize_text_field($request->get_param('name'));
        $email     = sanitize_email($request->get_param('email'));
        $phone     = sanitize_text_field($request->get_param('phone'));
        $quantity  = max(1, (int) $request->get_param('quantity'));

        if (!$event_id || !$name || !$email || !$phone) {
            return new WP_Error('missing_fields', __('Preencha todos os campos obrigatórios.', 'geek-events-manager'), ['status' => 400]);
        }

        $event = get_post($event_id);
        if (!$event || $event->post_type !== 'geek_events_event') {
            return new WP_Error('not_found', __('Evento não encontrado.', 'geek-events-manager'), ['status' => 404]);
        }

        $event_status = get_field('geek_events_status', $event_id);
        if (in_array($event_status, ['encerrado', 'cancelado'], true)) {
            return new WP_Error('event_closed', __('Este evento já foi encerrado.', 'geek-events-manager'), ['status' => 400]);
        }

        $available = Geek_Events_Helpers::get_available_tickets($event_id);
        if ($quantity > $available) {
            return new WP_Error('no_tickets', sprintf(__('Só %d ingresso(s) disponível(is).', 'geek-events-manager'), $available), ['status' => 400]);
        }

        $existing = get_posts([
            'post_type'      => 'geek_registration',
            'posts_per_page' => 1,
            'post_status'    => ['pending', 'confirmed'],
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'registration_event', 'value' => $event_id],
                ['key' => 'registration_email', 'value' => $email],
            ],
        ]);

        if (!empty($existing)) {
            return new WP_Error('duplicate', __('Você já se inscreveu neste evento.', 'geek-events-manager'), ['status' => 400]);
        }

        $registration_id = wp_insert_post([
            'post_type'   => 'geek_registration',
            'post_title'  => $name,
            'post_status' => 'pending',
            'post_parent' => $event_id,
        ]);

        if (is_wp_error($registration_id)) {
            return new WP_Error('insert_error', __('Erro ao realizar inscrição.', 'geek-events-manager'), ['status' => 500]);
        }

        update_field('registration_event', $event_id, $registration_id);
        update_field('registration_email', $email, $registration_id);
        update_field('registration_phone', $phone, $registration_id);
        update_field('registration_quantity', $quantity, $registration_id);

        $sold = (int) get_field('geek_events_tickets_sold', $event_id);
        update_field('geek_events_tickets_sold', $sold + $quantity, $event_id);

        return new WP_REST_Response([
            'message'           => __('Inscrição realizada com sucesso!', 'geek-events-manager'),
            'registration_id'   => $registration_id,
            'status'            => 'pending',
        ], 201);
    }

    // Formata os dados do evento para o response JSON (uso interno)
    private static function format_event($post) {
        $categories = wp_get_post_terms($post->ID, 'geek_events_category', ['fields' => 'all']);
        $categories_data = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $cat) {
                $categories_data[] = [
                    'id'   => $cat->term_id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ];
            }
        }

        $meta = Geek_Events_Helpers::get_event_meta($post->ID);

        return [
            'id'                 => $post->ID,
            'title'              => get_the_title($post),
            'slug'               => $post->post_name,
            'content'            => apply_filters('the_content', $post->post_content),
            'excerpt'            => apply_filters('the_excerpt', $post->post_excerpt),
            'date'               => $post->post_date,
            'modified'           => $post->post_modified,
            'featured_image_url' => get_the_post_thumbnail_url($post->ID, 'full'),
            'permalink'          => get_permalink($post),
            'categories'         => $categories_data,
            'meta'               => [
                'event_date'        => $meta['date'],
                'event_time'        => $meta['time'],
                'location'          => $meta['location'],
                'address'           => $meta['address'],
                'total_tickets'     => $meta['total_tickets'],
                'tickets_sold'      => $meta['tickets_sold'],
                'available_tickets' => Geek_Events_Helpers::get_available_tickets($post->ID),
                'total_vacancies'   => $meta['total_vacancies'],
                'vacancies_filled'  => $meta['vacancies_filled'],
                'available_vacancies' => Geek_Events_Helpers::get_available_vacancies($post->ID),
                'ticket_price'      => $meta['ticket_price'],
                'status'            => $meta['status'],
                'status_label'      => Geek_Events_Helpers::get_status_label($meta['status']),
            ],
        ];
    }
}
