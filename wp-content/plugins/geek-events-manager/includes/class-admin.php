<?php

defined('ABSPATH') || exit;

class Geek_Events_Admin {

    // Registra todos os hooks de personalização do admin
    public static function init() {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
        add_filter('manage_geek_events_event_posts_columns', [__CLASS__, 'event_columns']);
        add_action('manage_geek_events_event_posts_custom_column', [__CLASS__, 'event_columns_content'], 10, 2);
        add_filter('manage_edit-geek_events_event_sortable_columns', [__CLASS__, 'sortable_columns']);
        add_action('pre_get_posts', [__CLASS__, 'sortable_columns_query']);
        add_action('restrict_manage_posts', [__CLASS__, 'add_admin_filters']);
        add_filter('parse_query', [__CLASS__, 'handle_admin_filters']);
    }

    public static function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['geek_events_event', 'geek_registration'], true)) {
            wp_enqueue_style('geek-events-admin', GEEK_EVENTS_PLUGIN_URL . 'assets/css/admin.css', [], GEEK_EVENTS_VERSION);
        }
    }

    // Adiciona colunas de Data, Status, Ingressos e Vagas na listagem
    public static function event_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['event_date'] = __('Data', 'geek-events-manager');
                $new_columns['event_status'] = __('Status', 'geek-events-manager');
            } elseif ($key === 'date') {
                continue;
            } else {
                $new_columns[$key] = $value;
            }
        }
        $new_columns['tickets'] = __('Ingressos', 'geek-events-manager');
        $new_columns['vacancies'] = __('Vagas', 'geek-events-manager');
        $new_columns['registrations'] = __('Inscrições', 'geek-events-manager');
        return $new_columns;
    }

    // Exibe o conteúdo de cada coluna personalizada
    public static function event_columns_content($column, $post_id) {
        switch ($column) {
            case 'event_date':
                $date = get_field('geek_events_date', $post_id);
                $time = get_field('geek_events_time', $post_id);
                if ($date) {
                    $formatted = date_i18n(get_option('date_format'), strtotime($date));
                    echo esc_html($formatted);
                    if ($time) {
                        echo '<br><small>' . esc_html($time) . '</small>';
                    }
                } else {
                    echo '—';
                }
                break;

            case 'event_status':
                $status = get_field('geek_events_status', $post_id);
                $statuses = [
                    'agendado'    => __('Agendado', 'geek-events-manager'),
                    'acontecendo' => __('Acontecendo', 'geek-events-manager'),
                    'encerrado'   => __('Encerrado', 'geek-events-manager'),
                    'cancelado'   => __('Cancelado', 'geek-events-manager'),
                ];
                $label = isset($statuses[$status]) ? $statuses[$status] : '—';
                printf('<span class="geek-events-status geek-events-status--%s">%s</span>', esc_attr($status), esc_html($label));
                break;

            case 'tickets':
                $total = (int) get_field('geek_events_total_tickets', $post_id);
                $sold  = (int) get_field('geek_events_tickets_sold', $post_id);
                if ($total > 0) {
                    printf('%d / %d', $sold, $total);
                } else {
                    echo '—';
                }
                break;

            case 'registrations':
                $regs = get_posts([
                    'post_type'      => 'geek_registration',
                    'posts_per_page' => -1,
                    'post_status'    => ['pending', 'confirmed'],
                    'fields'         => 'ids',
                    'meta_query'     => [
                        ['key' => 'registration_event', 'value' => $post_id],
                    ],
                ]);
                $pending = 0;
                $confirmed = 0;
                foreach ($regs as $rid) {
                    $s = get_post_status($rid);
                    if ($s === 'confirmed') $confirmed++;
                    elseif ($s === 'pending') $pending++;
                }
                $url = admin_url('edit.php?post_type=geek_registration&registration_event_filter=' . $post_id);
                $parts = [];
                if ($confirmed) {
                    $parts[] = '<a href="' . esc_url($url . '&registration_status_filter=confirmed') . '" style="color:#39ff14">' . $confirmed . ' ✓</a>';
                }
                if ($pending) {
                    $parts[] = '<a href="' . esc_url($url . '&registration_status_filter=pending') . '" style="color:#ffd700">' . $pending . ' ⏳</a>';
                }
                echo $parts ? implode(' ', $parts) : '0';
                break;

            case 'vacancies':
                $total    = (int) get_field('geek_events_total_vacancies', $post_id);
                $filled   = (int) get_field('geek_events_vacancies_filled', $post_id);
                if ($total > 0) {
                    printf('%d / %d', $filled, $total);
                } else {
                    echo '—';
                }
                break;
        }
    }

    // Torna as colunas Data e Status ordenáveis
    public static function sortable_columns($columns) {
        $columns['event_date'] = 'event_date';
        $columns['event_status'] = 'event_status';
        return $columns;
    }

    // Ajusta a ordenação da query por metadado de data
    public static function sortable_columns_query($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'geek_events_event') {
            return;
        }

        $orderby = $query->get('orderby');
        if ($orderby === 'event_date') {
            $query->set('meta_key', 'geek_events_date');
            $query->set('orderby', 'meta_value');
        }
    }

    // Adiciona dropdowns de filtro por status e categoria
    public static function add_admin_filters() {
        global $typenow;
        if ($typenow !== 'geek_events_event') {
            return;
        }

        $status = isset($_GET['geek_events_status']) ? $_GET['geek_events_status'] : '';
        ?>
        <select name="geek_events_status">
            <option value=""><?php _e('Todos os status', 'geek-events-manager'); ?></option>
            <option value="agendado" <?php selected($status, 'agendado'); ?>><?php _e('Agendado', 'geek-events-manager'); ?></option>
            <option value="acontecendo" <?php selected($status, 'acontecendo'); ?>><?php _e('Acontecendo', 'geek-events-manager'); ?></option>
            <option value="encerrado" <?php selected($status, 'encerrado'); ?>><?php _e('Encerrado', 'geek-events-manager'); ?></option>
            <option value="cancelado" <?php selected($status, 'cancelado'); ?>><?php _e('Cancelado', 'geek-events-manager'); ?></option>
        </select>
        <?php

        $category = isset($_GET['geek_events_category']) ? $_GET['geek_events_category'] : '';
        wp_dropdown_categories([
            'show_option_all' => __('Todas as categorias', 'geek-events-manager'),
            'taxonomy'        => 'geek_events_category',
            'name'            => 'geek_events_category',
            'value_field'     => 'slug',
            'selected'        => $category,
            'show_count'      => false,
            'hierarchical'    => true,
        ]);
    }

    // Aplica os filtros selecionados na query da listagem
    public static function handle_admin_filters($query) {
        global $pagenow;
        if ($pagenow !== 'edit.php' || !isset($_GET['post_type']) || $_GET['post_type'] !== 'geek_events_event') {
            return $query;
        }

        $vars = &$query->query_vars;

        if (!empty($_GET['geek_events_status'])) {
            $meta_query = isset($vars['meta_query']) ? $vars['meta_query'] : [];
            $meta_query[] = [
                'key'   => 'geek_events_status',
                'value' => sanitize_text_field($_GET['geek_events_status']),
            ];
            $vars['meta_query'] = $meta_query;
        }

        if (!empty($_GET['geek_events_category'])) {
            $tax_query = isset($vars['tax_query']) ? $vars['tax_query'] : [];
            $tax_query[] = [
                'taxonomy' => 'geek_events_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['geek_events_category']),
            ];
            $vars['tax_query'] = $tax_query;
        }

        return $query;
    }
}
