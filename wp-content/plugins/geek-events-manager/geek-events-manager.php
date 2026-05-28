<?php
/**
 * Plugin Name: Geek Events Manager
 * Plugin URI: https://geekevents.com.br
 * Description: Gerencia eventos, categorias, ingressos e vagas para o Geek Events
 * Version: 1.0.0
 * Author: Geek Events
 * Text Domain: geek-events-manager
 * Domain Path: /languages
 * Requires Plugins: secure-custom-fields
 */

defined('ABSPATH') || exit;

define('GEEK_EVENTS_VERSION', '1.0.0');
define('GEEK_EVENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GEEK_EVENTS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GEEK_EVENTS_PLUGIN_DIR . 'includes/class-post-types.php';
require_once GEEK_EVENTS_PLUGIN_DIR . 'includes/class-admin.php';
require_once GEEK_EVENTS_PLUGIN_DIR . 'includes/class-helpers.php';
require_once GEEK_EVENTS_PLUGIN_DIR . 'includes/class-rest-api.php';
require_once GEEK_EVENTS_PLUGIN_DIR . 'includes/class-field-groups.php';

// Inicializa as classes principais do plugin
function geek_events_init() {
    Geek_Events_Post_Types::init();
    Geek_Events_Admin::init();
    Geek_Events_Rest_API::init();
}
add_action('plugins_loaded', 'geek_events_init');

// Executado na ativação: registra CPTs e atualiza rewrite rules
function geek_events_activate() {
    Geek_Events_Post_Types::init();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'geek_events_activate');

// Executado na desativação: limpa rewrite rules
function geek_events_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'geek_events_deactivate');

// Verifica se o SCF está ativo e exibe aviso no admin se necessário
function geek_events_check_scf() {
    if (!function_exists('acf_add_local_field_group')) {
        add_action('admin_notices', function () {
            $class = 'notice notice-warning is-dismissible';
            $message = __('Geek Events Manager requer o plugin Secure Custom Fields (SCF) ativo.', 'geek-events-manager');
            printf('<div class="%s"><p>%s</p></div>', esc_attr($class), esc_html($message));
        });
    }
}
add_action('admin_init', 'geek_events_check_scf');
