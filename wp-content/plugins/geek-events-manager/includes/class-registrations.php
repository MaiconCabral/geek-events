<?php

defined('ABSPATH') || exit;

class Geek_Events_Registrations {

    public static function init() {
        add_action('init', [__CLASS__, 'register_registration_cpt']);
        add_action('init', [__CLASS__, 'register_registration_status']);
        add_action('acf/include_fields', [__CLASS__, 'register_meta_fields']);
        add_filter('manage_geek_registration_posts_columns', [__CLASS__, 'registration_columns']);
        add_action('manage_geek_registration_posts_custom_column', [__CLASS__, 'registration_columns_content'], 10, 2);
        add_filter('manage_edit-geek_registration_sortable_columns', [__CLASS__, 'registration_sortable_columns']);
        add_action('restrict_manage_posts', [__CLASS__, 'registration_admin_filters']);
        add_filter('parse_query', [__CLASS__, 'handle_registration_admin_filters']);
        add_action('add_meta_boxes', [__CLASS__, 'remove_meta_boxes']);
        add_action('wp_ajax_geek_events_register', [__CLASS__, 'ajax_register']);
        add_action('wp_ajax_nopriv_geek_events_register', [__CLASS__, 'ajax_register']);
    }

    public static function register_registration_cpt() {
        $labels = [
            'name'               => __('Inscrições', 'geek-events-manager'),
            'singular_name'      => __('Inscrição', 'geek-events-manager'),
            'menu_name'          => __('Inscrições', 'geek-events-manager'),
            'all_items'          => __('Todas as Inscrições', 'geek-events-manager'),
            'edit_item'          => __('Editar Inscrição', 'geek-events-manager'),
            'view_item'          => __('Ver Inscrição', 'geek-events-manager'),
            'search_items'       => __('Buscar Inscrições', 'geek-events-manager'),
            'not_found'          => __('Nenhuma inscrição encontrada', 'geek-events-manager'),
            'not_found_in_trash' => __('Nenhuma inscrição na lixeira', 'geek-events-manager'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=geek_events_event',
            'menu_icon'          => 'dashicons-feedback',
            'supports'           => ['title', 'custom-fields'],
            'show_in_rest'       => true,
            'rest_base'          => 'registrations',
            'capability_type'    => 'post',
            'capabilities'       => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap'       => true,
        ];

        register_post_type('geek_registration', $args);
    }

    public static function register_registration_status() {
        register_post_status('confirmed', [
            'label'                     => __('Confirmado', 'geek-events-manager'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Confirmado <span class="count">(%s)</span>', 'Confirmados <span class="count">(%s)</span>', 'geek-events-manager'),
        ]);

        register_post_status('cancelled', [
            'label'                     => __('Cancelado', 'geek-events-manager'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Cancelado <span class="count">(%s)</span>', 'Cancelados <span class="count">(%s)</span>', 'geek-events-manager'),
        ]);
    }

    public static function register_meta_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key'    => 'group_geek_registration',
            'title'  => __('Dados da Inscrição', 'geek-events-manager'),
            'fields' => [
                [
                    'key'           => 'field_registration_event',
                    'label'         => __('Evento', 'geek-events-manager'),
                    'name'          => 'registration_event',
                    'type'          => 'post_object',
                    'required'      => 1,
                    'post_type'     => ['geek_events_event'],
                    'return_format' => 'id',
                    'allow_null'    => 0,
                ],
                [
                    'key'           => 'field_registration_email',
                    'label'         => __('E-mail', 'geek-events-manager'),
                    'name'          => 'registration_email',
                    'type'          => 'email',
                    'required'      => 1,
                ],
                [
                    'key'           => 'field_registration_phone',
                    'label'         => __('Telefone', 'geek-events-manager'),
                    'name'          => 'registration_phone',
                    'type'          => 'text',
                    'required'      => 1,
                    'placeholder'   => '(11) 99999-8888',
                ],
                [
                    'key'           => 'field_registration_quantity',
                    'label'         => __('Quantidade de Ingressos', 'geek-events-manager'),
                    'name'          => 'registration_quantity',
                    'type'          => 'number',
                    'required'      => 1,
                    'default_value' => 1,
                    'min'           => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'geek_registration',
                    ],
                ],
            ],
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'active'                => true,
            'show_in_rest'          => true,
        ]);
    }

    public static function registration_columns($columns) {
        $new = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new[$key] = __('Participante', 'geek-events-manager');
                $new['registration_event'] = __('Evento', 'geek-events-manager');
                $new['registration_email'] = __('E-mail', 'geek-events-manager');
                $new['registration_quantity'] = __('Ingressos', 'geek-events-manager');
            } elseif ($key === 'date') {
                $new['registration_date'] = __('Data Inscrição', 'geek-events-manager');
            } else {
                $new[$key] = $value;
            }
        }
        return $new;
    }

    public static function registration_columns_content($column, $post_id) {
        switch ($column) {
            case 'registration_event':
                $event_id = get_field('registration_event', $post_id);
                if ($event_id) {
                    $title = get_the_title($event_id);
                    $link = get_edit_post_link($event_id);
                    echo $link ? '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>' : esc_html($title);
                } else {
                    echo '—';
                }
                break;

            case 'registration_email':
                $email = get_field('registration_email', $post_id);
                echo $email ? esc_html($email) : '—';
                break;

            case 'registration_quantity':
                $qty = (int) get_field('registration_quantity', $post_id);
                echo $qty ?: '—';
                break;

            case 'registration_date':
                echo get_the_date('d/m/Y H:i', $post_id);
                break;
        }
    }

    public static function registration_sortable_columns($columns) {
        $columns['registration_date'] = 'date';
        return $columns;
    }

    public static function remove_meta_boxes() {
        remove_meta_box('postcustom', 'geek_registration', 'normal');
    }

    public static function registration_admin_filters() {
        global $typenow;
        if ($typenow !== 'geek_registration') return;

        $selected = isset($_GET['registration_event_filter']) ? (int) $_GET['registration_event_filter'] : 0;
        $status = isset($_GET['registration_status_filter']) ? $_GET['registration_status_filter'] : '';

        $events = get_posts([
            'post_type'      => 'geek_events_event',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>
        <select name="registration_event_filter">
            <option value=""><?php _e('Todos os eventos', 'geek-events-manager'); ?></option>
            <?php foreach ($events as $event) : ?>
                <option value="<?php echo $event->ID; ?>" <?php selected($selected, $event->ID); ?>><?php echo esc_html($event->post_title); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="registration_status_filter">
            <option value=""><?php _e('Todos os status', 'geek-events-manager'); ?></option>
            <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pendente', 'geek-events-manager'); ?></option>
            <option value="confirmed" <?php selected($status, 'confirmed'); ?>><?php _e('Confirmado', 'geek-events-manager'); ?></option>
            <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelado', 'geek-events-manager'); ?></option>
        </select>
        <?php
    }

    public static function handle_registration_admin_filters($query) {
        global $pagenow;
        if ($pagenow !== 'edit.php' || !isset($_GET['post_type']) || $_GET['post_type'] !== 'geek_registration') {
            return $query;
        }

        $vars = &$query->query_vars;

        if (!empty($_GET['registration_event_filter'])) {
            $meta_query = isset($vars['meta_query']) ? $vars['meta_query'] : [];
            $meta_query[] = [
                'key'   => 'registration_event',
                'value' => (int) $_GET['registration_event_filter'],
            ];
            $vars['meta_query'] = $meta_query;
        }

        if (!empty($_GET['registration_status_filter'])) {
            $vars['post_status'] = sanitize_text_field($_GET['registration_status_filter']);
        }

        return $query;
    }

    public static function ajax_register() {
        check_ajax_referer('geek_events_register_nonce', 'nonce');

        $event_id   = (int) ($_POST['event_id'] ?? 0);
        $name       = sanitize_text_field($_POST['name'] ?? '');
        $email      = sanitize_email($_POST['email'] ?? '');
        $phone      = sanitize_text_field($_POST['phone'] ?? '');
        $quantity   = max(1, (int) ($_POST['quantity'] ?? 1));

        if (!$event_id || !$name || !$email || !$phone) {
            wp_send_json_error(['message' => __('Preencha todos os campos obrigatórios.', 'geek-events-manager')]);
        }

        $event = get_post($event_id);
        if (!$event || $event->post_type !== 'geek_events_event') {
            wp_send_json_error(['message' => __('Evento não encontrado.', 'geek-events-manager')]);
        }

        $event_status = get_field('geek_events_status', $event_id);
        if (in_array($event_status, ['encerrado', 'cancelado'], true)) {
            wp_send_json_error(['message' => __('Este evento já foi encerrado.', 'geek-events-manager')]);
        }

        $available = Geek_Events_Helpers::get_available_tickets($event_id);
        if ($quantity > $available) {
            wp_send_json_error(['message' => sprintf(__('Só %d ingresso(s) disponível(is).', 'geek-events-manager'), $available)]);
        }

        $existing = get_posts([
            'post_type'      => 'geek_registration',
            'posts_per_page' => 1,
            'post_status'    => ['pending', 'confirmed'],
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'   => 'registration_event',
                    'value' => $event_id,
                ],
                [
                    'key'   => 'registration_email',
                    'value' => $email,
                ],
            ],
        ]);

        if (!empty($existing)) {
            wp_send_json_error(['message' => __('Você já se inscreveu neste evento.', 'geek-events-manager')]);
        }

        $registration_id = wp_insert_post([
            'post_type'   => 'geek_registration',
            'post_title'  => $name,
            'post_status' => 'pending',
            'post_parent' => $event_id,
        ]);

        if (is_wp_error($registration_id)) {
            wp_send_json_error(['message' => __('Erro ao realizar inscrição. Tente novamente.', 'geek-events-manager')]);
        }

        update_field('registration_event', $event_id, $registration_id);
        update_field('registration_email', $email, $registration_id);
        update_field('registration_phone', $phone, $registration_id);
        update_field('registration_quantity', $quantity, $registration_id);

        $sold = (int) get_field('geek_events_tickets_sold', $event_id);
        update_field('geek_events_tickets_sold', $sold + $quantity, $event_id);

        wp_send_json_success([
            'message' => __('Inscrição realizada com sucesso! Seu cadastro está pendente de confirmação.', 'geek-events-manager'),
            'registration_id' => $registration_id,
        ]);
    }
}
