<?php
/**
 * Plugin Name: Ramböck IT Service Konfigurator
 * Plugin URI: https://github.com/ramboeck-it/service-configurator
 * Description: Professioneller Service-Konfigurator für IT-Dienstleister
 * Version: 5.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Ramböck IT
 * Author URI: https://ramboeck-it.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ramboeck-configurator
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RSC_VERSION', '5.0.0');
define('RSC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RSC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RSC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader (optional, für spätere OOP-Struktur)
if (file_exists(RSC_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once RSC_PLUGIN_DIR . 'vendor/autoload.php';
}

// Hauptklasse einbinden
require_once RSC_PLUGIN_DIR . 'includes/class-ramboeck-service-configurator.php';

// Plugin initialisieren
function rsc_init() {
    return RamboeckServiceConfigurator::get_instance();
}
add_action('plugins_loaded', 'rsc_init');

// Aktivierung
register_activation_hook(__FILE__, array('RamboeckServiceConfigurator', 'activate'));

// Deaktivierung
register_deactivation_hook(__FILE__, array('RamboeckServiceConfigurator', 'deactivate'));
