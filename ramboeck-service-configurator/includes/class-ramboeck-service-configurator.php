<?php
/**
 * Main Plugin Class
 *
 * @package RamboeckIT\ServiceConfigurator
 * @since 4.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class RamboeckServiceConfigurator {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new RamboeckServiceConfigurator();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        add_shortcode('ramboeck_configurator', array($this, 'render_configurator'));
        
        // AJAX
        add_action('wp_ajax_rsc_get_services', array($this, 'ajax_get_services'));
        add_action('wp_ajax_nopriv_rsc_get_services', array($this, 'ajax_get_services'));
        add_action('wp_ajax_rsc_submit_configuration', array($this, 'ajax_submit'));
        add_action('wp_ajax_nopriv_rsc_submit_configuration', array($this, 'ajax_submit'));
    }
    
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Services Table
        $sql_services = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_services (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            description text NOT NULL,
            tooltip text,
            setup_price decimal(10,2) NOT NULL DEFAULT 0.00,
            monthly_price decimal(10,2) NOT NULL DEFAULT 0.00,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            sort_order int NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Leads Table
        $sql_leads = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_leads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            email varchar(200) NOT NULL,
            phone varchar(50),
            company varchar(200),
            configuration text NOT NULL,
            total_setup decimal(10,2) NOT NULL,
            total_monthly decimal(10,2) NOT NULL,
            status varchar(50) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_services);
        dbDelta($sql_leads);
        
        // Insert default services
        self::insert_default_services();
        
        // Set default options
        self::set_default_options();
    }
    
    private static function insert_default_services() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_services';
        
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;
        
        $services = array(
            array('Cloud Arbeitsplatz', 'Vollständig verwalteter Cloud-Arbeitsplatz', 'Microsoft 365, Windows, Support', 150.00, 45.00, 1),
            array('Backup & Recovery', 'Automatische Cloud-Backups', 'Tägliche Backups, 30 Tage', 200.00, 25.00, 2),
            array('Security & Antivirus', 'Enterprise Security', 'Next-Gen Antivirus, Firewall', 100.00, 15.00, 3),
            array('Email-Archivierung', 'GoBD-konforme Archivierung', '10 Jahre Aufbewahrung', 150.00, 8.00, 4),
            array('Mobile Device Management', 'Smartphone & Tablet', 'iOS/Android verwalten', 100.00, 5.00, 5),
            array('VPN & Remote Access', 'Sicherer Fernzugriff', 'Enterprise VPN, MFA', 300.00, 20.00, 6),
            array('Patch Management', 'Automatische Updates', 'Windows & Apps patchen', 150.00, 12.00, 7),
            array('Monitoring & Alerting', '24/7 Überwachung', 'Proaktive Überwachung', 200.00, 30.00, 8),
            array('Helpdesk & Support', 'IT-Support', 'Ticket-System, Remote', 0.00, 35.00, 9),
            array('Compliance Management', 'DSGVO & ISO 27001', 'Audits, Dokumentation', 500.00, 50.00, 10)
        );
        
        foreach ($services as $s) {
            $wpdb->insert($table, array(
                'name' => $s[0],
                'description' => $s[1],
                'tooltip' => $s[2],
                'setup_price' => $s[3],
                'monthly_price' => $s[4],
                'sort_order' => $s[5],
                'is_active' => 1
            ));
        }
    }
    
    private static function set_default_options() {
        $defaults = array(
            'rsc_admin_email' => get_option('admin_email'),
            'rsc_currency' => 'EUR',
            'rsc_primary_color' => '#F27024',
            'rsc_secondary_color' => '#36313E'
        );
        
        foreach ($defaults as $key => $value) {
            if (!get_option($key)) {
                update_option($key, $value);
            }
        }
    }
    
    public static function deactivate() {
        // Cleanup if needed
    }
    
    // Rest der Methoden...
    public function enqueue_scripts() {}
    public function admin_enqueue_scripts($hook) {}
    public function add_admin_menu() {}
    public function register_settings() {}
    public function render_configurator($atts) { return '<div id="rsc-configurator">Loading...</div>'; }
    public function ajax_get_services() {}
    public function ajax_submit() {}
}
