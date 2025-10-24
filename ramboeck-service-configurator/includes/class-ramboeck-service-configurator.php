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
            recommended_for text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Leads Table with extended fields
        $sql_leads = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_leads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            email varchar(200) NOT NULL,
            phone varchar(50),
            company varchar(200),
            industry varchar(100),
            company_size varchar(50),
            locations int DEFAULT 1,
            configuration text NOT NULL,
            total_setup decimal(10,2) NOT NULL,
            total_monthly decimal(10,2) NOT NULL,
            status varchar(50) DEFAULT 'new',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Industry Presets Table
        $sql_presets = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_industry_presets (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            industry_key varchar(100) NOT NULL,
            industry_name varchar(200) NOT NULL,
            description text,
            recommended_services text,
            icon varchar(50),
            sort_order int NOT NULL DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_services);
        dbDelta($sql_leads);
        dbDelta($sql_presets);

        // Insert default data
        self::insert_default_services();
        self::insert_default_industry_presets();
        self::set_default_options();
    }
    
    private static function insert_default_services() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_services';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;

        $services = array(
            array('Cloud Arbeitsplatz', 'Vollständig verwalteter Cloud-Arbeitsplatz', 'Microsoft 365, Windows, Support', 150.00, 45.00, 1, 'all'),
            array('Backup & Recovery', 'Automatische Cloud-Backups', 'Tägliche Backups, 30 Tage', 200.00, 25.00, 2, 'all'),
            array('Security & Antivirus', 'Enterprise Security', 'Next-Gen Antivirus, Firewall', 100.00, 15.00, 3, 'all'),
            array('Email-Archivierung', 'GoBD-konforme Archivierung', '10 Jahre Aufbewahrung', 150.00, 8.00, 4, 'healthcare,legal,accounting,finance'),
            array('Mobile Device Management', 'Smartphone & Tablet', 'iOS/Android verwalten', 100.00, 5.00, 5, 'all'),
            array('VPN & Remote Access', 'Sicherer Fernzugriff', 'Enterprise VPN, MFA', 300.00, 20.00, 6, 'all'),
            array('Patch Management', 'Automatische Updates', 'Windows & Apps patchen', 150.00, 12.00, 7, 'all'),
            array('Monitoring & Alerting', '24/7 Überwachung', 'Proaktive Überwachung', 200.00, 30.00, 8, 'all'),
            array('Helpdesk & Support', 'IT-Support', 'Ticket-System, Remote', 0.00, 35.00, 9, 'all'),
            array('Compliance Management', 'DSGVO & ISO 27001', 'Audits, Dokumentation', 500.00, 50.00, 10, 'healthcare,legal,accounting,finance')
        );

        foreach ($services as $s) {
            $wpdb->insert($table, array(
                'name' => $s[0],
                'description' => $s[1],
                'tooltip' => $s[2],
                'setup_price' => $s[3],
                'monthly_price' => $s[4],
                'sort_order' => $s[5],
                'is_active' => 1,
                'recommended_for' => $s[6]
            ));
        }
    }

    private static function insert_default_industry_presets() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_industry_presets';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;

        $presets = array(
            array('healthcare', 'Arztpraxis / Gesundheitswesen', 'DSGVO-kritisch, hohe Verfügbarkeit', '1,2,3,4,5,7,8,9,10', 'medical', 1),
            array('legal', 'Rechtsanwälte / Kanzlei', 'Mandantendaten, GoBD-konform', '1,2,3,4,5,6,7,9,10', 'legal', 2),
            array('accounting', 'Steuerberater / Buchhaltung', 'Finanzdaten, Datev-Integration', '1,2,3,4,5,7,9,10', 'calculator', 3),
            array('engineering', 'Ingenieurbüro / Architekten', 'CAD-Arbeitsplätze, große Dateien', '1,2,3,5,6,7,8,9', 'drafting', 4),
            array('retail', 'Einzelhandel', 'Kassensysteme, Warenwirtschaft', '1,2,3,5,7,8,9', 'store', 5),
            array('manufacturing', 'Produktion / Industrie', 'OT-Security, hohe Verfügbarkeit', '1,2,3,6,7,8,9', 'factory', 6),
            array('consulting', 'Beratung / Dienstleistung', 'Mobile Arbeitsplätze, Flexibilität', '1,2,3,5,6,9', 'consulting', 7),
            array('finance', 'Finanzdienstleister', 'Höchste Sicherheit, Compliance', '1,2,3,4,5,6,7,8,9,10', 'bank', 8),
            array('education', 'Bildungseinrichtung', 'Viele Nutzer, WLAN', '1,2,3,5,7,9', 'school', 9),
            array('general', 'Allgemeine Dienstleistung', 'Standard IT-Infrastruktur', '1,2,3,5,7,9', 'business', 10)
        );

        foreach ($presets as $p) {
            $wpdb->insert($table, array(
                'industry_key' => $p[0],
                'industry_name' => $p[1],
                'description' => $p[2],
                'recommended_services' => $p[3],
                'icon' => $p[4],
                'sort_order' => $p[5]
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
    
    // Asset Loading
    public function enqueue_scripts() {
        // Check if we're on a page or post
        if (!is_singular() && !is_page()) {
            return;
        }

        // Check if shortcode is present
        global $post;
        if (empty($post) || !has_shortcode($post->post_content, 'ramboeck_configurator')) {
            return;
        }

        wp_enqueue_style(
            'rsc-frontend',
            RSC_PLUGIN_URL . 'assets/css/style.css',
            array(),
            RSC_VERSION
        );

        wp_enqueue_script(
            'rsc-frontend',
            RSC_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            RSC_VERSION,
            true
        );

        wp_localize_script('rsc-frontend', 'rscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rsc_nonce'),
            'currency' => get_option('rsc_currency', 'EUR'),
            'primaryColor' => get_option('rsc_primary_color', '#F27024'),
            'secondaryColor' => get_option('rsc_secondary_color', '#36313E'),
            'i18n' => array(
                'step1Title' => __('Ihr Unternehmen', 'ramboeck-configurator'),
                'step2Title' => __('Services auswählen', 'ramboeck-configurator'),
                'step3Title' => __('Kontaktdaten', 'ramboeck-configurator'),
                'nextBtn' => __('Weiter', 'ramboeck-configurator'),
                'prevBtn' => __('Zurück', 'ramboeck-configurator'),
                'submitBtn' => __('Angebot anfordern', 'ramboeck-configurator'),
                'setupCost' => __('Einmalige Kosten', 'ramboeck-configurator'),
                'monthlyCost' => __('Monatliche Kosten', 'ramboeck-configurator'),
                'perMonth' => __('pro Monat', 'ramboeck-configurator'),
                'oneTime' => __('einmalig', 'ramboeck-configurator'),
            )
        ));
    }

    public function admin_enqueue_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'ramboeck-configurator') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            'rsc-admin',
            RSC_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            RSC_VERSION
        );

        wp_enqueue_script(
            'rsc-admin',
            RSC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            RSC_VERSION,
            true
        );

        wp_localize_script('rsc-admin', 'rscAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rsc_admin_nonce')
        ));
    }

    // Admin Menu
    public function add_admin_menu() {
        add_menu_page(
            __('Service Konfigurator', 'ramboeck-configurator'),
            __('IT Konfigurator', 'ramboeck-configurator'),
            'manage_options',
            'ramboeck-configurator',
            array($this, 'render_leads_page'),
            'dashicons-clipboard',
            30
        );

        add_submenu_page(
            'ramboeck-configurator',
            __('Anfragen', 'ramboeck-configurator'),
            __('Anfragen', 'ramboeck-configurator'),
            'manage_options',
            'ramboeck-configurator',
            array($this, 'render_leads_page')
        );

        add_submenu_page(
            'ramboeck-configurator',
            __('Services', 'ramboeck-configurator'),
            __('Services', 'ramboeck-configurator'),
            'manage_options',
            'ramboeck-configurator-services',
            array($this, 'render_services_page')
        );

        add_submenu_page(
            'ramboeck-configurator',
            __('Branchen', 'ramboeck-configurator'),
            __('Branchen', 'ramboeck-configurator'),
            'manage_options',
            'ramboeck-configurator-industries',
            array($this, 'render_industries_page')
        );

        add_submenu_page(
            'ramboeck-configurator',
            __('Einstellungen', 'ramboeck-configurator'),
            __('Einstellungen', 'ramboeck-configurator'),
            'manage_options',
            'ramboeck-configurator-settings',
            array($this, 'render_settings_page')
        );
    }

    // Settings Registration
    public function register_settings() {
        register_setting('rsc_settings', 'rsc_admin_email', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default' => get_option('admin_email')
        ));

        register_setting('rsc_settings', 'rsc_currency', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'EUR'
        ));

        register_setting('rsc_settings', 'rsc_primary_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#F27024'
        ));

        register_setting('rsc_settings', 'rsc_secondary_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#36313E'
        ));

        register_setting('rsc_settings', 'rsc_email_subject', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Neue Anfrage über IT-Konfigurator', 'ramboeck-configurator')
        ));

        register_setting('rsc_settings', 'rsc_email_template', array(
            'type' => 'string',
            'sanitize_callback' => 'wp_kses_post',
            'default' => $this->get_default_email_template()
        ));
    }

    private function get_default_email_template() {
        return "Neue Anfrage erhalten:\n\nName: {{name}}\nEmail: {{email}}\nTelefon: {{phone}}\nFirma: {{company}}\nBranche: {{industry}}\nMitarbeiter: {{company_size}}\n\nGewählte Services:\n{{services}}\n\nEinmalige Kosten: {{setup_cost}}\nMonatliche Kosten: {{monthly_cost}}";
    }

    // Frontend Shortcode
    public function render_configurator($atts) {
        $atts = shortcode_atts(array(
            'title' => __('IT-Service Konfigurator', 'ramboeck-configurator'),
            'subtitle' => __('Stellen Sie Ihre IT-Lösung zusammen', 'ramboeck-configurator')
        ), $atts);

        ob_start();
        include RSC_PLUGIN_DIR . 'templates/configurator.php';
        return ob_get_clean();
    }

    // AJAX Handlers
    public function ajax_get_services() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rsc_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsprüfung fehlgeschlagen.'));
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'rsc_services';

        $industry = isset($_POST['industry']) ? sanitize_text_field($_POST['industry']) : '';

        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        if (!$table_exists) {
            wp_send_json_error(array('message' => 'Services-Tabelle nicht gefunden. Bitte Plugin deaktivieren und erneut aktivieren.'));
            return;
        }

        $services = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order ASC"
        );

        // Check for database errors
        if ($wpdb->last_error) {
            wp_send_json_error(array('message' => 'Datenbankfehler: ' . $wpdb->last_error));
            return;
        }

        // Filter and mark recommended services
        if ($services) {
            foreach ($services as $service) {
                $service->is_recommended = false;

                if ($industry && !empty($service->recommended_for)) {
                    $recommended_industries = explode(',', $service->recommended_for);
                    if (in_array($industry, $recommended_industries) || in_array('all', $recommended_industries)) {
                        $service->is_recommended = true;
                    }
                }
            }
        }

        wp_send_json_success($services ? $services : array());
    }

    public function ajax_submit() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rsc_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsprüfung fehlgeschlagen.'));
            return;
        }

        // Validate and sanitize input
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';
        $industry = isset($_POST['industry']) ? sanitize_text_field($_POST['industry']) : '';
        $company_size = isset($_POST['company_size']) ? sanitize_text_field($_POST['company_size']) : '';
        $locations = isset($_POST['locations']) ? intval($_POST['locations']) : 1;
        $selected_services = isset($_POST['services']) ? json_decode(stripslashes($_POST['services']), true) : array();
        $total_setup = isset($_POST['total_setup']) ? floatval($_POST['total_setup']) : 0;
        $total_monthly = isset($_POST['total_monthly']) ? floatval($_POST['total_monthly']) : 0;

        // Validation
        if (empty($name) || empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => __('Bitte füllen Sie alle Pflichtfelder aus.', 'ramboeck-configurator')));
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'rsc_leads';

        // Insert lead
        $inserted = $wpdb->insert($table, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'industry' => $industry,
            'company_size' => $company_size,
            'locations' => $locations,
            'configuration' => json_encode($selected_services),
            'total_setup' => $total_setup,
            'total_monthly' => $total_monthly,
            'status' => 'new'
        ));

        if ($inserted) {
            // Send email notification
            $this->send_lead_notification($wpdb->insert_id);

            wp_send_json_success(array(
                'message' => __('Vielen Dank! Wir melden uns in Kürze bei Ihnen.', 'ramboeck-configurator')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'ramboeck-configurator')
            ));
        }
    }

    private function send_lead_notification($lead_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_leads';

        $lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $lead_id));

        if (!$lead) return;

        $admin_email = get_option('rsc_admin_email', get_option('admin_email'));
        $subject = get_option('rsc_email_subject', __('Neue Anfrage über IT-Konfigurator', 'ramboeck-configurator'));

        // Get service details
        $services = json_decode($lead->configuration, true);
        $service_list = '';

        if (is_array($services)) {
            foreach ($services as $service) {
                $service_list .= sprintf(
                    "- %s (Einmalig: %.2f EUR, Monatlich: %.2f EUR)\n",
                    $service['name'],
                    $service['setup_price'],
                    $service['monthly_price']
                );
            }
        }

        $template = get_option('rsc_email_template', $this->get_default_email_template());

        $replacements = array(
            '{{name}}' => $lead->name,
            '{{email}}' => $lead->email,
            '{{phone}}' => $lead->phone,
            '{{company}}' => $lead->company,
            '{{industry}}' => $lead->industry,
            '{{company_size}}' => $lead->company_size,
            '{{services}}' => $service_list,
            '{{setup_cost}}' => number_format($lead->total_setup, 2, ',', '.') . ' EUR',
            '{{monthly_cost}}' => number_format($lead->total_monthly, 2, ',', '.') . ' EUR'
        );

        $message = str_replace(array_keys($replacements), array_values($replacements), $template);

        wp_mail($admin_email, $subject, $message);
    }

    // Admin Page Renderers (Stubs - will be implemented next)
    public function render_leads_page() {
        include RSC_PLUGIN_DIR . 'admin/leads.php';
    }

    public function render_services_page() {
        include RSC_PLUGIN_DIR . 'admin/services.php';
    }

    public function render_industries_page() {
        include RSC_PLUGIN_DIR . 'admin/industries.php';
    }

    public function render_settings_page() {
        include RSC_PLUGIN_DIR . 'admin/settings.php';
    }
}
