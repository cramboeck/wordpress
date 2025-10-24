<?php
/**
 * Settings Admin Page
 *
 * @package RamboeckIT\ServiceConfigurator
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['rsc_save_settings'])) {
    check_admin_referer('rsc_settings_action', 'rsc_settings_nonce');

    update_option('rsc_admin_email', sanitize_email($_POST['rsc_admin_email']));
    update_option('rsc_currency', sanitize_text_field($_POST['rsc_currency']));
    update_option('rsc_primary_color', sanitize_hex_color($_POST['rsc_primary_color']));
    update_option('rsc_secondary_color', sanitize_hex_color($_POST['rsc_secondary_color']));
    update_option('rsc_email_subject', sanitize_text_field($_POST['rsc_email_subject']));
    update_option('rsc_email_template', wp_kses_post($_POST['rsc_email_template']));
    update_option('rsc_enable_customer_email', isset($_POST['rsc_enable_customer_email']) ? 1 : 0);
    update_option('rsc_customer_email_subject', sanitize_text_field($_POST['rsc_customer_email_subject']));
    update_option('rsc_customer_email_template', wp_kses_post($_POST['rsc_customer_email_template']));

    echo '<div class="notice notice-success"><p>' . __('Einstellungen gespeichert!', 'ramboeck-configurator') . '</p></div>';
}

// Get current settings
$admin_email = get_option('rsc_admin_email', get_option('admin_email'));
$currency = get_option('rsc_currency', 'EUR');
$primary_color = get_option('rsc_primary_color', '#F27024');
$secondary_color = get_option('rsc_secondary_color', '#36313E');
$email_subject = get_option('rsc_email_subject', __('Neue Anfrage über IT-Konfigurator', 'ramboeck-configurator'));
$email_template = get_option('rsc_email_template', "Neue Anfrage erhalten:\n\nName: {{name}}\nEmail: {{email}}\nTelefon: {{phone}}\nFirma: {{company}}\nBranche: {{industry}}\nMitarbeiter: {{company_size}}\n\nGewählte Services:\n{{services}}\n\nEinmalige Kosten: {{setup_cost}}\nMonatliche Kosten: {{monthly_cost}}");
$enable_customer_email = get_option('rsc_enable_customer_email', 0);
$customer_email_subject = get_option('rsc_customer_email_subject', __('Ihre Anfrage bei Ramböck IT', 'ramboeck-configurator'));
$customer_email_template = get_option('rsc_customer_email_template', "Hallo {{name}},\n\nvielen Dank für Ihre Anfrage! Wir haben Ihre Konfiguration erhalten und melden uns in Kürze bei Ihnen.\n\nIhre Auswahl:\n{{services}}\n\nEinmalige Kosten: {{setup_cost}}\nMonatliche Kosten: {{monthly_cost}}\n\nMit freundlichen Grüßen\nIhr Ramböck IT Team");
?>

<div class="wrap">
    <h1><?php _e('Einstellungen', 'ramboeck-configurator'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('rsc_settings_action', 'rsc_settings_nonce'); ?>

        <h2 class="nav-tab-wrapper" id="rsc-settings-tabs">
            <a href="#general" class="nav-tab nav-tab-active"><?php _e('Allgemein', 'ramboeck-configurator'); ?></a>
            <a href="#email" class="nav-tab"><?php _e('E-Mail Benachrichtigungen', 'ramboeck-configurator'); ?></a>
            <a href="#design" class="nav-tab"><?php _e('Design & Farben', 'ramboeck-configurator'); ?></a>
            <a href="#shortcode" class="nav-tab"><?php _e('Shortcode & Integration', 'ramboeck-configurator'); ?></a>
        </h2>

        <!-- General Settings -->
        <div id="general" class="rsc-tab-content">
            <div class="rsc-admin-card">
                <h2><?php _e('Allgemeine Einstellungen', 'ramboeck-configurator'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th><label for="rsc_admin_email"><?php _e('Admin E-Mail', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="email" id="rsc_admin_email" name="rsc_admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text" required>
                            <p class="description"><?php _e('Anfragen werden an diese E-Mail-Adresse gesendet.', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="rsc_currency"><?php _e('Währung', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <select id="rsc_currency" name="rsc_currency" class="regular-text">
                                <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR (€)</option>
                                <option value="USD" <?php selected($currency, 'USD'); ?>>USD ($)</option>
                                <option value="CHF" <?php selected($currency, 'CHF'); ?>>CHF (Fr.)</option>
                                <option value="GBP" <?php selected($currency, 'GBP'); ?>>GBP (£)</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Email Settings -->
        <div id="email" class="rsc-tab-content" style="display: none;">
            <div class="rsc-admin-card">
                <h2><?php _e('E-Mail an Administrator', 'ramboeck-configurator'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th><label for="rsc_email_subject"><?php _e('E-Mail Betreff', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="rsc_email_subject" name="rsc_email_subject" value="<?php echo esc_attr($email_subject); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="rsc_email_template"><?php _e('E-Mail Vorlage', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <textarea id="rsc_email_template" name="rsc_email_template" rows="12" class="large-text code"><?php echo esc_textarea($email_template); ?></textarea>
                            <p class="description">
                                <?php _e('Verfügbare Platzhalter:', 'ramboeck-configurator'); ?>
                                <code>{{name}}</code>, <code>{{email}}</code>, <code>{{phone}}</code>, <code>{{company}}</code>,
                                <code>{{industry}}</code>, <code>{{company_size}}</code>, <code>{{services}}</code>,
                                <code>{{setup_cost}}</code>, <code>{{monthly_cost}}</code>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="rsc-admin-card" style="margin-top: 20px;">
                <h2><?php _e('E-Mail an Kunden (Bestätigung)', 'ramboeck-configurator'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th><label for="rsc_enable_customer_email"><?php _e('Kunden-Email aktivieren', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <label>
                                <input type="checkbox" id="rsc_enable_customer_email" name="rsc_enable_customer_email" value="1" <?php checked($enable_customer_email, 1); ?>>
                                <?php _e('Automatische Bestätigungsmail an Kunden senden', 'ramboeck-configurator'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="rsc_customer_email_subject"><?php _e('E-Mail Betreff', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="rsc_customer_email_subject" name="rsc_customer_email_subject" value="<?php echo esc_attr($customer_email_subject); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="rsc_customer_email_template"><?php _e('E-Mail Vorlage', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <textarea id="rsc_customer_email_template" name="rsc_customer_email_template" rows="12" class="large-text code"><?php echo esc_textarea($customer_email_template); ?></textarea>
                            <p class="description">
                                <?php _e('Selbe Platzhalter wie bei der Admin-E-Mail verfügbar.', 'ramboeck-configurator'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Design Settings -->
        <div id="design" class="rsc-tab-content" style="display: none;">
            <div class="rsc-admin-card">
                <h2><?php _e('Farben & Branding', 'ramboeck-configurator'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th><label for="rsc_primary_color"><?php _e('Primärfarbe', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="rsc_primary_color" name="rsc_primary_color" value="<?php echo esc_attr($primary_color); ?>" class="color-picker" data-default-color="#F27024">
                            <p class="description"><?php _e('Wird für Buttons, Links und Akzente verwendet.', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="rsc_secondary_color"><?php _e('Sekundärfarbe', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="rsc_secondary_color" name="rsc_secondary_color" value="<?php echo esc_attr($secondary_color); ?>" class="color-picker" data-default-color="#36313E">
                            <p class="description"><?php _e('Wird für Überschriften und Textelemente verwendet.', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                </table>

                <h3><?php _e('Farb-Vorschau', 'ramboeck-configurator'); ?></h3>
                <div id="rsc-color-preview" style="padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
                    <h4 style="color: var(--rsc-secondary);"><?php _e('Beispiel Überschrift', 'ramboeck-configurator'); ?></h4>
                    <p><?php _e('Dies ist ein Beispieltext, um die Farbkombination zu testen.', 'ramboeck-configurator'); ?></p>
                    <button type="button" style="background: var(--rsc-primary); color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                        <?php _e('Beispiel Button', 'ramboeck-configurator'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Shortcode Integration -->
        <div id="shortcode" class="rsc-tab-content" style="display: none;">
            <div class="rsc-admin-card">
                <h2><?php _e('Shortcode & Integration', 'ramboeck-configurator'); ?></h2>

                <h3><?php _e('Verwendung', 'ramboeck-configurator'); ?></h3>
                <p><?php _e('Fügen Sie folgenden Shortcode in eine Seite oder einen Beitrag ein, um den Konfigurator anzuzeigen:', 'ramboeck-configurator'); ?></p>

                <div style="background: #f5f5f5; padding: 15px; border-left: 4px solid <?php echo esc_attr($primary_color); ?>; margin: 15px 0;">
                    <code style="font-size: 14px;">[ramboeck_configurator]</code>
                </div>

                <h3><?php _e('Optionale Parameter', 'ramboeck-configurator'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Parameter', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Beschreibung', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Beispiel', 'ramboeck-configurator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>title</code></td>
                            <td><?php _e('Überschrift des Konfigurators', 'ramboeck-configurator'); ?></td>
                            <td><code>[ramboeck_configurator title="Ihr IT-Service"]</code></td>
                        </tr>
                        <tr>
                            <td><code>subtitle</code></td>
                            <td><?php _e('Untertitel des Konfigurators', 'ramboeck-configurator'); ?></td>
                            <td><code>[ramboeck_configurator subtitle="Individuell konfigurierbar"]</code></td>
                        </tr>
                    </tbody>
                </table>

                <h3><?php _e('CSS Anpassungen', 'ramboeck-configurator'); ?></h3>
                <p><?php _e('Sie können das Aussehen mit Custom CSS in Ihrem Theme anpassen. Verwenden Sie folgende CSS-Klassen:', 'ramboeck-configurator'); ?></p>
                <ul style="list-style: disc; margin-left: 25px;">
                    <li><code>.rsc-wrapper</code> - Hauptcontainer</li>
                    <li><code>.rsc-step</code> - Einzelner Schritt</li>
                    <li><code>.rsc-service-card</code> - Service-Karten</li>
                    <li><code>.rsc-summary</code> - Preis-Zusammenfassung</li>
                    <li><code>.rsc-button</code> - Buttons</li>
                </ul>
            </div>
        </div>

        <p class="submit">
            <button type="submit" name="rsc_save_settings" class="button button-primary button-large">
                <?php _e('Einstellungen speichern', 'ramboeck-configurator'); ?>
            </button>
        </p>
    </form>
</div>

<style>
.rsc-admin-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
#rsc-color-preview {
    --rsc-primary: <?php echo esc_attr($primary_color); ?>;
    --rsc-secondary: <?php echo esc_attr($secondary_color); ?>;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.rsc-tab-content').hide();
        $(target).show();
    });

    // Color picker
    $('.color-picker').wpColorPicker({
        change: function(event, ui) {
            updateColorPreview();
        }
    });

    function updateColorPreview() {
        var primary = $('#rsc_primary_color').val();
        var secondary = $('#rsc_secondary_color').val();

        $('#rsc-color-preview').css({
            '--rsc-primary': primary,
            '--rsc-secondary': secondary
        });
    }

    updateColorPreview();
});
</script>
