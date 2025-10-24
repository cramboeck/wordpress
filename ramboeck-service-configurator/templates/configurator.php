<?php
/**
 * Frontend Configurator Template
 *
 * @package RamboeckIT\ServiceConfigurator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$industries_table = $wpdb->prefix . 'rsc_industry_presets';
$industries = $wpdb->get_results("SELECT * FROM $industries_table ORDER BY sort_order ASC");

$primary_color = get_option('rsc_primary_color', '#F27024');
$secondary_color = get_option('rsc_secondary_color', '#36313E');
?>

<div class="rsc-wrapper" data-primary-color="<?php echo esc_attr($primary_color); ?>" data-secondary-color="<?php echo esc_attr($secondary_color); ?>">
    <!-- Progress Indicator -->
    <div class="rsc-progress">
        <div class="rsc-progress-bar">
            <div class="rsc-progress-fill" id="rsc-progress-fill"></div>
        </div>
        <div class="rsc-progress-steps">
            <div class="rsc-progress-step active" data-step="1">
                <div class="rsc-progress-step-number">1</div>
                <div class="rsc-progress-step-label"><?php _e('Ihr Unternehmen', 'ramboeck-configurator'); ?></div>
            </div>
            <div class="rsc-progress-step" data-step="2">
                <div class="rsc-progress-step-number">2</div>
                <div class="rsc-progress-step-label"><?php _e('Services wählen', 'ramboeck-configurator'); ?></div>
            </div>
            <div class="rsc-progress-step" data-step="3">
                <div class="rsc-progress-step-number">3</div>
                <div class="rsc-progress-step-label"><?php _e('Kontakt', 'ramboeck-configurator'); ?></div>
            </div>
        </div>
    </div>

    <!-- Main Configurator -->
    <div class="rsc-configurator" id="rsc-configurator">

        <!-- Step 1: Company Profile -->
        <div class="rsc-step rsc-step-active" id="rsc-step-1">
            <div class="rsc-step-header">
                <h2><?php echo esc_html($atts['title'] ?? __('IT-Service Konfigurator', 'ramboeck-configurator')); ?></h2>
                <p class="rsc-step-subtitle"><?php echo esc_html($atts['subtitle'] ?? __('Stellen Sie Ihre IT-Lösung individuell zusammen', 'ramboeck-configurator')); ?></p>
            </div>

            <div class="rsc-step-content">
                <h3><?php _e('Schritt 1: Erzählen Sie uns von Ihrem Unternehmen', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-help-text"><?php _e('Damit wir Ihnen die passenden Services empfehlen können, benötigen wir einige Informationen:', 'ramboeck-configurator'); ?></p>

                <!-- Industry Selection -->
                <div class="rsc-form-group">
                    <label class="rsc-label"><?php _e('In welcher Branche sind Sie tätig?', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
                    <div class="rsc-industry-grid">
                        <?php foreach ($industries as $industry): ?>
                            <div class="rsc-industry-card" data-industry="<?php echo esc_attr($industry->industry_key); ?>">
                                <div class="rsc-industry-icon">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="9" y1="9" x2="15" y2="9"></line>
                                        <line x1="9" y1="15" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                                <h4><?php echo esc_html($industry->industry_name); ?></h4>
                                <?php if ($industry->description): ?>
                                    <p><?php echo esc_html($industry->description); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="rsc-industry" name="industry" required>
                </div>

                <!-- Company Size -->
                <div class="rsc-form-group">
                    <label class="rsc-label" for="rsc-company-size"><?php _e('Wie viele Mitarbeiter hat Ihr Unternehmen?', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
                    <select id="rsc-company-size" name="company_size" class="rsc-select" required>
                        <option value=""><?php _e('Bitte wählen...', 'ramboeck-configurator'); ?></option>
                        <option value="1-5"><?php _e('1-5 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                        <option value="6-10"><?php _e('6-10 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                        <option value="11-25"><?php _e('11-25 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                        <option value="26-50"><?php _e('26-50 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                        <option value="51-100"><?php _e('51-100 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                        <option value="100+"><?php _e('Über 100 Mitarbeiter', 'ramboeck-configurator'); ?></option>
                    </select>
                </div>

                <!-- Locations -->
                <div class="rsc-form-group">
                    <label class="rsc-label" for="rsc-locations"><?php _e('Anzahl Standorte', 'ramboeck-configurator'); ?></label>
                    <input type="number" id="rsc-locations" name="locations" class="rsc-input" value="1" min="1" max="100">
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-primary rsc-button-next" data-next="2">
                    <?php _e('Weiter zu den Services', 'ramboeck-configurator'); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Step 2: Service Selection -->
        <div class="rsc-step" id="rsc-step-2">
            <div class="rsc-step-header">
                <h3><?php _e('Schritt 2: Wählen Sie Ihre Services', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-step-subtitle" id="rsc-recommendation-text"></p>
            </div>

            <div class="rsc-step-content">
                <div id="rsc-services-loading" class="rsc-loading">
                    <div class="rsc-spinner"></div>
                    <p><?php _e('Services werden geladen...', 'ramboeck-configurator'); ?></p>
                </div>

                <div id="rsc-services-grid" class="rsc-services-grid" style="display: none;">
                    <!-- Services will be loaded dynamically -->
                </div>

                <div class="rsc-no-services" id="rsc-no-services" style="display: none;">
                    <p><?php _e('Keine Services verfügbar.', 'ramboeck-configurator'); ?></p>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-secondary rsc-button-prev" data-prev="1">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <?php _e('Zurück', 'ramboeck-configurator'); ?>
                </button>
                <button type="button" class="rsc-button rsc-button-primary rsc-button-next" data-next="3">
                    <?php _e('Weiter zur Anfrage', 'ramboeck-configurator'); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Step 3: Contact Form -->
        <div class="rsc-step" id="rsc-step-3">
            <div class="rsc-step-header">
                <h3><?php _e('Schritt 3: Ihre Kontaktdaten', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-step-subtitle"><?php _e('Wir erstellen Ihnen ein unverbindliches Angebot', 'ramboeck-configurator'); ?></p>
            </div>

            <div class="rsc-step-content rsc-contact-grid">
                <div class="rsc-contact-form">
                    <div class="rsc-form-group">
                        <label class="rsc-label" for="rsc-name"><?php _e('Ihr Name', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
                        <input type="text" id="rsc-name" name="name" class="rsc-input" required>
                    </div>

                    <div class="rsc-form-group">
                        <label class="rsc-label" for="rsc-email"><?php _e('E-Mail-Adresse', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
                        <input type="email" id="rsc-email" name="email" class="rsc-input" required>
                    </div>

                    <div class="rsc-form-group">
                        <label class="rsc-label" for="rsc-phone"><?php _e('Telefonnummer', 'ramboeck-configurator'); ?></label>
                        <input type="tel" id="rsc-phone" name="phone" class="rsc-input">
                    </div>

                    <div class="rsc-form-group">
                        <label class="rsc-label" for="rsc-company"><?php _e('Firma', 'ramboeck-configurator'); ?></label>
                        <input type="text" id="rsc-company" name="company" class="rsc-input">
                    </div>

                    <div class="rsc-form-group rsc-form-checkbox">
                        <label>
                            <input type="checkbox" id="rsc-privacy" name="privacy" required>
                            <span><?php _e('Ich akzeptiere die Datenschutzerklärung', 'ramboeck-configurator'); ?> <span class="required">*</span></span>
                        </label>
                    </div>
                </div>

                <!-- Summary -->
                <div class="rsc-summary-card">
                    <h4><?php _e('Ihre Konfiguration', 'ramboeck-configurator'); ?></h4>

                    <div class="rsc-summary-section">
                        <h5><?php _e('Unternehmensprofil', 'ramboeck-configurator'); ?></h5>
                        <div id="rsc-summary-profile"></div>
                    </div>

                    <div class="rsc-summary-section">
                        <h5><?php _e('Gewählte Services', 'ramboeck-configurator'); ?></h5>
                        <div id="rsc-summary-services"></div>
                    </div>

                    <div class="rsc-summary-total">
                        <div class="rsc-summary-total-row">
                            <span><?php _e('Einmalige Kosten:', 'ramboeck-configurator'); ?></span>
                            <strong id="rsc-total-setup">0,00 €</strong>
                        </div>
                        <div class="rsc-summary-total-row rsc-summary-total-highlight">
                            <span><?php _e('Monatliche Kosten:', 'ramboeck-configurator'); ?></span>
                            <strong id="rsc-total-monthly">0,00 €</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-secondary rsc-button-prev" data-prev="2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <?php _e('Zurück', 'ramboeck-configurator'); ?>
                </button>
                <button type="button" class="rsc-button rsc-button-primary rsc-button-submit" id="rsc-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <?php _e('Angebot anfordern', 'ramboeck-configurator'); ?>
                </button>
            </div>
        </div>

        <!-- Success Message -->
        <div class="rsc-step" id="rsc-step-success" style="display: none;">
            <div class="rsc-success-message">
                <div class="rsc-success-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                </div>
                <h2><?php _e('Vielen Dank für Ihre Anfrage!', 'ramboeck-configurator'); ?></h2>
                <p><?php _e('Wir haben Ihre Konfiguration erhalten und melden uns in Kürze bei Ihnen.', 'ramboeck-configurator'); ?></p>
                <button type="button" class="rsc-button rsc-button-primary" onclick="location.reload()">
                    <?php _e('Neue Konfiguration', 'ramboeck-configurator'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Price Summary Sticky -->
    <div class="rsc-sticky-summary" id="rsc-sticky-summary" style="display: none;">
        <div class="rsc-sticky-summary-content">
            <div class="rsc-sticky-summary-item">
                <span class="rsc-sticky-summary-label"><?php _e('Einmalig:', 'ramboeck-configurator'); ?></span>
                <span class="rsc-sticky-summary-value" id="rsc-sticky-setup">0,00 €</span>
            </div>
            <div class="rsc-sticky-summary-item">
                <span class="rsc-sticky-summary-label"><?php _e('Monatlich:', 'ramboeck-configurator'); ?></span>
                <span class="rsc-sticky-summary-value" id="rsc-sticky-monthly">0,00 €</span>
            </div>
        </div>
    </div>
</div>
