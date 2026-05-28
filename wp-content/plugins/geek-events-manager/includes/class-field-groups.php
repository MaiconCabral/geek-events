<?php
// Registra os campos personalizados do evento via SCF

defined('ABSPATH') || exit;

// Dispara no acf/include_fields para registrar o field group local
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_geek_events_event',
        'title'    => __('Informações do Evento', 'geek-events-manager'),
        'fields'   => [
            [
                'key'           => 'field_geek_events_date',
                'label'         => __('Data do Evento', 'geek-events-manager'),
                'name'          => 'geek_events_date',
                'type'          => 'date_picker',
                'required'      => 1,
                'display_format' => 'd/m/Y',
                'return_format'  => 'Y-m-d',
                'first_day'     => 0,
            ],
            [
                'key'           => 'field_geek_events_time',
                'label'         => __('Horário', 'geek-events-manager'),
                'name'          => 'geek_events_time',
                'type'          => 'time_picker',
                'display_format' => 'H:i',
                'return_format'  => 'H:i',
            ],
            [
                'key'           => 'field_geek_events_location',
                'label'         => __('Local', 'geek-events-manager'),
                'name'          => 'geek_events_location',
                'type'          => 'text',
                'placeholder'   => __('Nome do local do evento', 'geek-events-manager'),
            ],
            [
                'key'           => 'field_geek_events_address',
                'label'         => __('Endereço', 'geek-events-manager'),
                'name'          => 'geek_events_address',
                'type'          => 'textarea',
                'rows'          => 3,
                'placeholder'   => __('Endereço completo do evento', 'geek-events-manager'),
            ],
            [
                'key'           => 'field_geek_events_total_tickets',
                'label'         => __('Total de Ingressos', 'geek-events-manager'),
                'name'          => 'geek_events_total_tickets',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_geek_events_tickets_sold',
                'label'         => __('Ingressos Vendidos', 'geek-events-manager'),
                'name'          => 'geek_events_tickets_sold',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_geek_events_total_vacancies',
                'label'         => __('Total de Vagas', 'geek-events-manager'),
                'name'          => 'geek_events_total_vacancies',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_geek_events_vacancies_filled',
                'label'         => __('Vagas Preenchidas', 'geek-events-manager'),
                'name'          => 'geek_events_vacancies_filled',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_geek_events_ticket_price',
                'label'         => __('Preço do Ingresso (R$)', 'geek-events-manager'),
                'name'          => 'geek_events_ticket_price',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
                'step'          => 0.01,
                'placeholder'   => '0.00',
            ],
            [
                'key'           => 'field_geek_events_status',
                'label'         => __('Status do Evento', 'geek-events-manager'),
                'name'          => 'geek_events_status',
                'type'          => 'select',
                'required'      => 1,
                'default_value' => 'agendado',
                'choices'       => [
                    'agendado'    => __('Agendado', 'geek-events-manager'),
                    'acontecendo' => __('Acontecendo', 'geek-events-manager'),
                    'encerrado'   => __('Encerrado', 'geek-events-manager'),
                    'cancelado'   => __('Cancelado', 'geek-events-manager'),
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'geek_events_event',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => '',
        'active'                => true,
        'show_in_rest'          => true,
    ]);
});
