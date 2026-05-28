<?php

defined('ABSPATH') || exit;

class Geek_Events_Helpers {

    // Retorna array com todos os metadados do evento
    public static function get_event_meta($post_id) {
        $meta = [
            'date'              => get_field('geek_events_date', $post_id),
            'time'              => get_field('geek_events_time', $post_id),
            'location'          => get_field('geek_events_location', $post_id),
            'address'           => get_field('geek_events_address', $post_id),
            'total_tickets'     => (int) get_field('geek_events_total_tickets', $post_id),
            'tickets_sold'      => (int) get_field('geek_events_tickets_sold', $post_id),
            'total_vacancies'   => (int) get_field('geek_events_total_vacancies', $post_id),
            'vacancies_filled'  => (int) get_field('geek_events_vacancies_filled', $post_id),
            'ticket_price'      => (float) get_field('geek_events_ticket_price', $post_id),
            'status'            => get_field('geek_events_status', $post_id),
        ];
        return $meta;
    }

    // Calcula ingressos disponíveis (total - vendidos)
    public static function get_available_tickets($post_id) {
        $total = (int) get_field('geek_events_total_tickets', $post_id);
        $sold  = (int) get_field('geek_events_tickets_sold', $post_id);
        return max(0, $total - $sold);
    }

    // Calcula vagas disponíveis (total - preenchidas)
    public static function get_available_vacancies($post_id) {
        $total  = (int) get_field('geek_events_total_vacancies', $post_id);
        $filled = (int) get_field('geek_events_vacancies_filled', $post_id);
        return max(0, $total - $filled);
    }

    // Verifica se há ingressos disponíveis
    public static function has_available_tickets($post_id) {
        return self::get_available_tickets($post_id) > 0;
    }

    // Verifica se há vagas disponíveis
    public static function has_available_vacancies($post_id) {
        return self::get_available_vacancies($post_id) > 0;
    }

    // Formata data e hora do evento para exibição
    public static function format_event_date($post_id, $format = null) {
        $date = get_field('geek_events_date', $post_id);
        $time = get_field('geek_events_time', $post_id);
        if (!$date) {
            return '';
        }
        $format = $format ?: get_option('date_format');
        $output = date_i18n($format, strtotime($date));
        if ($time) {
            $output .= ' às ' . $time;
        }
        return $output;
    }

    // Retorna o label traduzido do status do evento
    public static function get_status_label($status) {
        $labels = [
            'agendado'    => __('Agendado', 'geek-events-manager'),
            'acontecendo' => __('Acontecendo', 'geek-events-manager'),
            'encerrado'   => __('Encerrado', 'geek-events-manager'),
            'cancelado'   => __('Cancelado', 'geek-events-manager'),
        ];
        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    // Query de eventos futuros ordenados por data (exceto cancelados)
    public static function query_upcoming_events($limit = 10) {
        $args = [
            'post_type'      => 'geek_events_event',
            'posts_per_page' => $limit,
            'meta_key'       => 'geek_events_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'geek_events_date',
                    'value'   => current_time('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
                [
                    'key'     => 'geek_events_status',
                    'value'   => 'cancelado',
                    'compare' => '!=',
                ],
            ],
        ];
        return new WP_Query($args);
    }

    // Query de eventos filtrados por slug da categoria
    public static function query_events_by_category($category_slug, $limit = 10) {
        $args = [
            'post_type'      => 'geek_events_event',
            'posts_per_page' => $limit,
            'meta_key'       => 'geek_events_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'tax_query'      => [
                [
                    'taxonomy' => 'geek_events_category',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ],
            ],
        ];
        return new WP_Query($args);
    }
}
