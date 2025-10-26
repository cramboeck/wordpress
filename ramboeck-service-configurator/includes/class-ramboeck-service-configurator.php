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

        // Services Table (Extended)
        $sql_services = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_services (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            description text NOT NULL,
            long_description text,
            tooltip text,
            setup_price decimal(10,2) NOT NULL DEFAULT 0.00,
            monthly_price decimal(10,2) NOT NULL DEFAULT 0.00,
            standalone_price decimal(10,2) DEFAULT NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            sort_order int NOT NULL DEFAULT 0,
            recommended_for text,
            service_type varchar(50) DEFAULT 'standalone',
            package_only tinyint(1) DEFAULT 0,
            features text,
            target_audience text,
            icon varchar(50),
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

        // Packages Table (KERN-PAKET, etc.)
        $sql_packages = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_packages (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            package_key varchar(50) NOT NULL,
            name varchar(200) NOT NULL,
            tagline text,
            description text,
            included_services text,
            features text,
            guarantees text,
            is_active tinyint(1) DEFAULT 1,
            sort_order int NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY package_key (package_key)
        ) $charset_collate;";

        // Pricing Tiers Table (Staffelpreise)
        $sql_tiers = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rsc_pricing_tiers (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            service_id mediumint(9) NOT NULL,
            min_quantity int NOT NULL,
            max_quantity int,
            price_per_unit decimal(10,2) NOT NULL,
            discount_percent decimal(5,2) DEFAULT 0.00,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_services);
        dbDelta($sql_leads);
        dbDelta($sql_presets);
        dbDelta($sql_packages);
        dbDelta($sql_tiers);

        // Insert default data
        self::insert_default_services();
        self::insert_default_industry_presets();
        self::insert_default_packages();
        self::insert_default_pricing_tiers();
        self::set_default_options();
    }
    
    private static function insert_default_services() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_services';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;

        // Managed Service Pauschale (Staffelpreis wird über pricing_tiers gehandhabt)
        $wpdb->insert($table, array(
            'name' => 'Managed Service Pauschale',
            'description' => 'Rundum-Sorglos IT-Betreuung - alles enthalten',
            'long_description' => 'Komplette IT-Verwaltung inkl. Monitoring, Security, Backup und unbegrenztem Support',
            'tooltip' => '24/7 Monitoring, Patching, Security, Backup, Support',
            'setup_price' => 0.00,
            'monthly_price' => 80.00, // Basis-Preis (wird durch Staffelung überschrieben)
            'standalone_price' => NULL, // Nur im KERN-PAKET
            'service_type' => 'core',
            'package_only' => 1,
            'is_active' => 1,
            'sort_order' => 1,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🔍', 'title' => '24/7 RMM Monitoring', 'description' => '500+ Checkpoints, Echtzeit-Alerts'),
                array('icon' => '🔄', 'title' => 'Automatisches Patchmanagement', 'description' => 'Windows, Office, 500+ Apps'),
                array('icon' => '🔒', 'title' => 'Bitdefender Endpoint Security', 'description' => 'Viren-, Malware-, Ransomware-Schutz'),
                array('icon' => '📨', 'title' => 'Hornetsecurity E-Mail Security', 'description' => 'Spam, Phishing, GoBD-Archivierung'),
                array('icon' => '💾', 'title' => 'Veeam Backup für M365', 'description' => 'Täglich, 30 Tage Retention'),
                array('icon' => '🎧', 'title' => 'Unbegrenzter Remote-Support', 'description' => 'Kein Minutenzählen'),
                array('icon' => '🚗', 'title' => 'Quartals-Vor-Ort-Check', 'description' => '1x pro Quartal kostenlos'),
                array('icon' => '⚙️', 'title' => 'Wartungs-Credits', 'description' => '5h pro 10 Geräte/Jahr')
            )),
            'target_audience' => 'Unternehmen 1-50 Mitarbeiter ohne eigene IT-Abteilung',
            'icon' => 'laptop'
        ));

        // Microsoft 365 Business Standard
        $wpdb->insert($table, array(
            'name' => 'Microsoft 365 Business Standard',
            'description' => 'Office-Suite, E-Mail, Cloud-Speicher',
            'long_description' => 'Komplette Microsoft 365 Suite mit Office-Apps, Teams, OneDrive und Exchange',
            'tooltip' => 'Word, Excel, PowerPoint, Outlook, Teams, 1TB OneDrive',
            'setup_price' => 0.00,
            'monthly_price' => 11.70, // Direkt über Microsoft
            'standalone_price' => 11.70,
            'service_type' => 'core',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 2,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '📝', 'title' => 'Office-Suite', 'description' => 'Word, Excel, PowerPoint, Outlook'),
                array('icon' => '👥', 'title' => 'Microsoft Teams', 'description' => 'Chat, Video, Zusammenarbeit'),
                array('icon' => '☁️', 'title' => '1 TB OneDrive', 'description' => 'Pro Benutzer'),
                array('icon' => '📧', 'title' => '50 GB E-Mail', 'description' => 'Exchange Online Postfach'),
                array('icon' => '📱', 'title' => '5 Geräte', 'description' => 'Desktop, Laptop, Tablet, Smartphone'),
                array('icon' => '🌐', 'title' => 'Überall verfügbar', 'description' => 'Web, Desktop, Mobile')
            )),
            'target_audience' => 'Alle Unternehmen',
            'icon' => 'microsoft'
        ));

        // RMM Monitoring (einzeln buchbar)
        $wpdb->insert($table, array(
            'name' => 'RMM Monitoring (24/7)',
            'description' => 'Proaktive Überwachung Ihrer gesamten IT',
            'long_description' => '24/7 Echtzeit-Überwachung mit 500+ Checkpoints pro Gerät',
            'tooltip' => 'Hardware, Software, Security, Performance',
            'setup_price' => 0.00,
            'monthly_price' => 0.00, // Im KERN-PAKET enthalten
            'standalone_price' => 25.00, // Premium wenn einzeln
            'service_type' => 'standalone',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 3,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🖥️', 'title' => 'Hardware-Gesundheit', 'description' => 'CPU, RAM, Festplatten, Temperatur'),
                array('icon' => '💾', 'title' => 'Software-Status', 'description' => 'Updates, Antivirus, Dienste'),
                array('icon' => '🔐', 'title' => 'Security-Checks', 'description' => 'Firewall, Failed Logins, Threats'),
                array('icon' => '📊', 'title' => 'Reporting', 'description' => 'Täglich, Monatlich, SLA-Tracking'),
                array('icon' => '🚨', 'title' => 'Proaktive Alerts', 'description' => 'Probleme bevor sie kritisch werden'),
                array('icon' => '⏱️', 'title' => 'Echtzeit-Überwachung', 'description' => '24/7/365')
            )),
            'target_audience' => 'Unternehmen die volle Transparenz über ihre IT wollen',
            'icon' => 'monitor'
        ));

        // Patchmanagement (einzeln buchbar)
        $wpdb->insert($table, array(
            'name' => 'Automatisches Patchmanagement',
            'description' => 'Sicherheitsupdates ohne Unterbrechung',
            'long_description' => 'Automatische Verwaltung aller Updates für Windows, Office und 500+ Anwendungen',
            'tooltip' => 'Windows, Office, Browser, Adobe, Java, etc.',
            'setup_price' => 0.00,
            'monthly_price' => 0.00, // Im KERN-PAKET enthalten
            'standalone_price' => 20.00, // Premium wenn einzeln
            'service_type' => 'standalone',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 4,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🪟', 'title' => 'Windows Updates', 'description' => 'Sicherheits- und Funktionsupdates'),
                array('icon' => '📦', 'title' => 'Software-Updates', 'description' => 'Office, Browser, Adobe, Java, etc.'),
                array('icon' => '🔧', 'title' => 'Firmware-Updates', 'description' => 'BIOS/UEFI, Hardware-Controller'),
                array('icon' => '⏰', 'title' => 'Wartungsfenster', 'description' => 'Außerhalb Ihrer Arbeitszeiten'),
                array('icon' => '↩️', 'title' => 'Rollback-Garantie', 'description' => 'Bei Problemen automatisch'),
                array('icon' => '🧪', 'title' => 'Test-Strategie', 'description' => 'Konservativ, Balanced, Aggressiv')
            )),
            'target_audience' => 'Unternehmen die immer aktuell und sicher sein wollen',
            'icon' => 'refresh'
        ));

        // Endpoint Security (einzeln buchbar)
        $wpdb->insert($table, array(
            'name' => 'Endpoint Security (Bitdefender)',
            'description' => 'Umfassender Schutz vor Bedrohungen',
            'long_description' => 'Enterprise-Grade Security mit Bitdefender GravityZone',
            'tooltip' => 'Antivirus, Firewall, Ransomware-Schutz, EDR',
            'setup_price' => 0.00,
            'monthly_price' => 0.00, // Im KERN-PAKET enthalten
            'standalone_price' => 12.00, // Premium wenn einzeln
            'service_type' => 'standalone',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 5,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🦠', 'title' => 'Antivirus/Antimalware', 'description' => 'Echtzeitschutz vor Viren'),
                array('icon' => '🛡️', 'title' => 'Ransomware-Schutz', 'description' => 'Behavior-based Detection'),
                array('icon' => '🔥', 'title' => 'Firewall', 'description' => 'Network Attack Defense'),
                array('icon' => '🕵️', 'title' => 'EDR', 'description' => 'Endpoint Detection & Response'),
                array('icon' => '🎯', 'title' => 'Zentrale Verwaltung', 'description' => 'Cloud-basiertes Management'),
                array('icon' => '⚡', 'title' => 'Leichtgewichtig', 'description' => 'Minimale Performance-Auswirkung')
            )),
            'target_audience' => 'Alle Unternehmen - Security ist Pflicht',
            'icon' => 'shield'
        ));

        // E-Mail Security (einzeln buchbar)
        $wpdb->insert($table, array(
            'name' => 'E-Mail Security + Archivierung',
            'description' => 'Hornetsecurity Total Protection',
            'long_description' => 'Umfassender E-Mail-Schutz mit GoBD-konformer Archivierung',
            'tooltip' => 'Spam, Phishing, Malware, Archivierung',
            'setup_price' => 0.00,
            'monthly_price' => 0.00, // Im KERN-PAKET enthalten
            'standalone_price' => 8.00, // Premium wenn einzeln
            'service_type' => 'standalone',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 6,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🗑️', 'title' => 'Spam-Filter', 'description' => '99.9% Erkennungsrate'),
                array('icon' => '🎣', 'title' => 'Phishing-Schutz', 'description' => 'AI-basierte Erkennung'),
                array('icon' => '🦠', 'title' => 'Malware-Filter', 'description' => 'Anhänge & Links scannen'),
                array('icon' => '📁', 'title' => 'E-Mail-Archivierung', 'description' => 'GoBD-konform, revisionssicher'),
                array('icon' => '📮', 'title' => 'Alle Postfächer', 'description' => 'inkl. info@, shared mailboxes'),
                array('icon' => '⚖️', 'title' => 'Compliance', 'description' => 'DSGVO, GoBD, EU-Server')
            )),
            'target_audience' => 'Besonders wichtig für: Kanzleien, Steuerberater, Gesundheitswesen',
            'icon' => 'mail'
        ));

        // Veeam Backup (einzeln buchbar)
        $wpdb->insert($table, array(
            'name' => 'Veeam Backup für Microsoft 365',
            'description' => 'Tägliche Sicherung Ihrer M365-Daten',
            'long_description' => 'Automatische Backups für E-Mails, OneDrive, SharePoint und Teams',
            'tooltip' => '30 Tage Aufbewahrung, schnelle Wiederherstellung',
            'setup_price' => 0.00,
            'monthly_price' => 0.00, // Im KERN-PAKET enthalten
            'standalone_price' => 6.00, // Premium wenn einzeln
            'service_type' => 'standalone',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 7,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '📧', 'title' => 'E-Mail-Backup', 'description' => 'Exchange Online komplett'),
                array('icon' => '☁️', 'title' => 'OneDrive-Backup', 'description' => 'Alle Benutzer-Dateien'),
                array('icon' => '📚', 'title' => 'SharePoint-Backup', 'description' => 'Sites, Listen, Libraries'),
                array('icon' => '💬', 'title' => 'Teams-Backup', 'description' => 'Chats, Dateien, Kanäle'),
                array('icon' => '⏰', 'title' => '30 Tage Retention', 'description' => 'Standard-Aufbewahrung'),
                array('icon' => '⚡', 'title' => 'Schnelle Wiederherstellung', 'description' => 'Einzelne Items oder komplett')
            )),
            'target_audience' => 'Alle M365-Nutzer - Microsoft löscht nach 30 Tagen!',
            'icon' => 'database'
        ));

        // ADD-ONs

        // MDM
        $wpdb->insert($table, array(
            'name' => 'Mobile Device Management (MDM)',
            'description' => 'Zentrale Verwaltung mobiler Geräte',
            'long_description' => 'Sichere Verwaltung von Tablets und Smartphones mit Microsoft Intune',
            'tooltip' => 'iOS, Android, BYOD-Unterstützung',
            'setup_price' => 0.00,
            'monthly_price' => 5.00,
            'standalone_price' => 5.00,
            'service_type' => 'addon',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 10,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '📱', 'title' => 'Zentrale Verwaltung', 'description' => 'Microsoft Intune'),
                array('icon' => '📲', 'title' => 'App-Verteilung', 'description' => 'Automatische Installation'),
                array('icon' => '🗑️', 'title' => 'Remote-Wipe', 'description' => 'Bei Verlust/Diebstahl'),
                array('icon' => '🔒', 'title' => 'Compliance-Richtlinien', 'description' => 'PIN, Verschlüsselung, etc.'),
                array('icon' => '👔', 'title' => 'BYOD-Unterstützung', 'description' => 'Bring Your Own Device'),
                array('icon' => '📊', 'title' => 'Reporting', 'description' => 'Geräte-Inventar, Compliance')
            )),
            'target_audience' => 'Unternehmen mit mobilen Mitarbeitern, Außendienst',
            'icon' => 'smartphone'
        ));

        // Server-Management
        $wpdb->insert($table, array(
            'name' => 'Server-Management',
            'description' => 'Vollständige Verwaltung Ihrer Server',
            'long_description' => 'Umfassende Betreuung für lokale Server, VMs oder Cloud-Server',
            'tooltip' => 'Monitoring, Patching, Backup, Performance-Tuning',
            'setup_price' => 0.00,
            'monthly_price' => 150.00,
            'standalone_price' => 150.00,
            'service_type' => 'addon',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 11,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '🔍', 'title' => 'Erweitertes Monitoring', 'description' => 'Services, Performance, Kapazität'),
                array('icon' => '💾', 'title' => 'Erweiterte Backup-Strategie', 'description' => 'Täglich, wöchentlich, monatlich'),
                array('icon' => '⚡', 'title' => 'Performance-Tuning', 'description' => 'Optimierung & Troubleshooting'),
                array('icon' => '🚨', 'title' => 'Priorisierter Support', 'description' => 'Server-Probleme = Priorität 1'),
                array('icon' => '📋', 'title' => 'Disaster Recovery', 'description' => 'Planung & Testing'),
                array('icon' => '🔐', 'title' => 'Security-Hardening', 'description' => 'Best Practices, Audits')
            )),
            'target_audience' => 'Unternehmen mit eigenen Servern (lokal oder Cloud)',
            'icon' => 'server'
        ));

        // Erweiterte Backup-Retention
        $wpdb->insert($table, array(
            'name' => 'Erweiterte Backup-Retention (90 Tage)',
            'description' => 'Längere Aufbewahrung Ihrer Backups',
            'long_description' => '90 Tage statt 30 Tage Backup-Aufbewahrung für M365',
            'tooltip' => 'Compliance, längere Wiederherstellungsfenster',
            'setup_price' => 0.00,
            'monthly_price' => 10.00,
            'standalone_price' => 10.00,
            'service_type' => 'addon',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 12,
            'recommended_for' => 'healthcare,legal,accounting,finance',
            'features' => json_encode(array(
                array('icon' => '📅', 'title' => '90 Tage Aufbewahrung', 'description' => 'Statt 30 Tage Standard'),
                array('icon' => '⚖️', 'title' => 'Compliance-Anforderungen', 'description' => 'Erfüllt erweiterte Vorgaben'),
                array('icon' => '🕰️', 'title' => 'Längeres Wiederherstellungsfenster', 'description' => 'Bis zu 3 Monate zurück'),
                array('icon' => '💼', 'title' => 'Audit-sicher', 'description' => 'Für Prüfungen & Audits'),
                array('icon' => '🔒', 'title' => 'Immutable Backups', 'description' => 'Schutz vor Ransomware'),
                array('icon' => '📊', 'title' => 'Extended Reporting', 'description' => 'Detaillierte Backup-History')
            )),
            'target_audience' => 'Branchen mit Compliance-Vorgaben: Anwälte, Steuerberater, Ärzte',
            'icon' => 'clock'
        ));

        // Premium-Support
        $wpdb->insert($table, array(
            'name' => 'Premium-Support (Erweiterte Zeiten)',
            'description' => 'Verlängerter Support mit kürzeren Reaktionszeiten',
            'long_description' => 'Extended Support-Hours und prioritäre Behandlung',
            'tooltip' => 'Mo-Fr 07:00-20:00, Sa 09:00-14:00, 2h Reaktion',
            'setup_price' => 0.00,
            'monthly_price' => 25.00,
            'standalone_price' => 25.00,
            'service_type' => 'addon',
            'package_only' => 0,
            'is_active' => 1,
            'sort_order' => 13,
            'recommended_for' => 'all',
            'features' => json_encode(array(
                array('icon' => '⏰', 'title' => 'Erweiterte Zeiten', 'description' => 'Mo-Fr 07:00-20:00 + Sa 09:00-14:00'),
                array('icon' => '⚡', 'title' => '2h Reaktionszeit', 'description' => 'Kritische Probleme (statt 4h)'),
                array('icon' => '📞', 'title' => 'Dedicated Hotline', 'description' => 'Direkte Nummer zu mir'),
                array('icon' => '🎯', 'title' => 'Höchste Priorität', 'description' => 'Ihre Tickets zuerst'),
                array('icon' => '👤', 'title' => 'Persönlicher Ansprechpartner', 'description' => 'Immer derselbe Techniker'),
                array('icon' => '🔔', 'title' => 'Proaktive Benachrichtigung', 'description' => 'Bei Problemen sofort informiert')
            )),
            'target_audience' => 'Unternehmen mit hohen Verfügbarkeitsanforderungen',
            'icon' => 'headset'
        ));
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

    private static function insert_default_packages() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_packages';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;

        // KERN-PAKET
        $wpdb->insert($table, array(
            'package_key' => 'kern-paket',
            'name' => 'KERN-PAKET: Rundum-Sorglos-Betreuung',
            'tagline' => 'Ihre IT - komplett betreut, keine Überraschungen',
            'description' => 'Alles aus einer Hand: Microsoft 365, komplettes Monitoring, Security, Backup und unbegrenzter Support - ohne Zeitlimit, ohne Kleingedrucktes.',
            'included_services' => '1,2', // Managed Service Pauschale + M365
            'features' => json_encode(array(
                '✅ Microsoft 365 Business Standard (11,70 € pro User)',
                '✅ 24/7 RMM Monitoring',
                '✅ Automatisches Patchmanagement',
                '✅ Bitdefender Endpoint Security',
                '✅ Hornetsecurity E-Mail Security + Archivierung',
                '✅ Veeam Backup für M365 (30 Tage)',
                '✅ Unbegrenzter Remote-Support (kein Minutenzählen!)',
                '✅ Quartals-Vor-Ort-Check (1x/Quartal kostenlos)',
                '✅ Wartungs-Credits (5h pro 10 Geräte/Jahr)',
                '✅ Ticketsystem mit transparenter Nachverfolgung'
            )),
            'guarantees' => json_encode(array(
                '🎯 Keine-Überraschungen-Garantie',
                '📊 Transparente Pauschalen',
                '🔒 Maximale Sicherheit',
                '📈 Skalierbar & Flexibel',
                '🤝 Persönlicher Ansprechpartner'
            )),
            'is_active' => 1,
            'sort_order' => 1
        ));
    }

    private static function insert_default_pricing_tiers() {
        global $wpdb;
        $table = $wpdb->prefix . 'rsc_pricing_tiers';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) return;

        // Staffelpreise für Managed Service Pauschale (Service ID 1)
        $tiers = array(
            array(1, 1, 4, 90.00, 0.00),      // 1-4 Geräte: 90€
            array(1, 5, 9, 85.00, 5.56),      // 5-9 Geräte: 85€ (-5,6%)
            array(1, 10, 19, 80.00, 11.11),   // 10-19 Geräte: 80€ (-11,1%)
            array(1, 20, 49, 75.00, 16.67),   // 20-49 Geräte: 75€ (-16,7%)
            array(1, 50, NULL, 70.00, 22.22)  // 50+ Geräte: 70€ (-22,2%)
        );

        foreach ($tiers as $tier) {
            $wpdb->insert($table, array(
                'service_id' => $tier[0],
                'min_quantity' => $tier[1],
                'max_quantity' => $tier[2],
                'price_per_unit' => $tier[3],
                'discount_percent' => $tier[4]
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

        // Get currency and ensure it's a valid ISO code
        $currency = get_option('rsc_currency', 'EUR');
        $currency_map = array(
            '€' => 'EUR',
            '$' => 'USD',
            '£' => 'GBP',
            '¥' => 'JPY',
            'CHF' => 'CHF'
        );
        // Convert symbol to ISO code if needed
        if (isset($currency_map[$currency])) {
            $currency = $currency_map[$currency];
        }
        // Ensure it's a valid 3-letter ISO code, fallback to EUR
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            $currency = 'EUR';
        }

        wp_localize_script('rsc-frontend', 'rscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rsc_nonce'),
            'currency' => $currency,
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
        // Debug logging
        error_log('RSC: ajax_get_services called');

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rsc_nonce')) {
            error_log('RSC: Nonce verification failed');
            wp_send_json_error(array('message' => 'Sicherheitsprüfung fehlgeschlagen.'));
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'rsc_services';

        $industry = isset($_POST['industry']) ? sanitize_text_field($_POST['industry']) : '';
        error_log('RSC: Requested industry: ' . $industry);

        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        if (!$table_exists) {
            error_log('RSC: Table does not exist: ' . $table);
            wp_send_json_error(array('message' => 'Services-Tabelle nicht gefunden. Bitte Plugin deaktivieren und erneut aktivieren.'));
            return;
        }

        $services = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order ASC"
        );

        error_log('RSC: Found ' . count($services) . ' services');

        // Check for database errors
        if ($wpdb->last_error) {
            error_log('RSC: Database error: ' . $wpdb->last_error);
            wp_send_json_error(array('message' => 'Datenbankfehler: ' . $wpdb->last_error));
            return;
        }

        // Check if services array is empty
        if (empty($services)) {
            error_log('RSC: No services found in database');
            wp_send_json_error(array('message' => 'Keine Services gefunden. Bitte Plugin deaktivieren und erneut aktivieren um Standard-Services zu laden.'));
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

        error_log('RSC: Sending ' . count($services) . ' services to frontend');
        wp_send_json_success($services);
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
