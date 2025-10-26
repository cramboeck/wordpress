<?php
/**
 * Frontend Configurator Template - v5.0.0
 *
 * New 4-Step Flow:
 * 1. Company Profile (Extended with device/user counts)
 * 2. Package Selection (KERN-PAKET vs. Individual)
 * 3. Service Configuration (ADD-ONs or Individual Services)
 * 4. Contact & Summary (Enhanced with ROI calculator)
 *
 * @package RamboeckIT\ServiceConfigurator
 * @since 5.0.0
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
                <div class="rsc-progress-step-label"><?php _e('Unternehmen', 'ramboeck-configurator'); ?></div>
            </div>
            <div class="rsc-progress-step" data-step="2">
                <div class="rsc-progress-step-number">2</div>
                <div class="rsc-progress-step-label"><?php _e('Paket-Wahl', 'ramboeck-configurator'); ?></div>
            </div>
            <div class="rsc-progress-step" data-step="3">
                <div class="rsc-progress-step-number">3</div>
                <div class="rsc-progress-step-label"><?php _e('Services', 'ramboeck-configurator'); ?></div>
            </div>
            <div class="rsc-progress-step" data-step="4">
                <div class="rsc-progress-step-number">4</div>
                <div class="rsc-progress-step-label"><?php _e('Anfrage', 'ramboeck-configurator'); ?></div>
            </div>
        </div>
    </div>

    <!-- Main Configurator -->
    <div class="rsc-configurator" id="rsc-configurator">

        <!-- Step 1: Company Profile (Extended) -->
        <div class="rsc-step rsc-step-active" id="rsc-step-1">
            <div class="rsc-step-header">
                <h2><?php echo esc_html($atts['title'] ?? __('IT-Service Konfigurator', 'ramboeck-configurator')); ?></h2>
                <p class="rsc-step-subtitle"><?php echo esc_html($atts['subtitle'] ?? __('Ihre maßgeschneiderte IT-Lösung in 4 Schritten', 'ramboeck-configurator')); ?></p>
            </div>

            <div class="rsc-step-content">
                <h3><?php _e('Schritt 1: Ihr Unternehmensprofil', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-help-text"><?php _e('Damit wir Ihnen die passenden Services und Preise berechnen können:', 'ramboeck-configurator'); ?></p>

                <!-- Industry Selection -->
                <div class="rsc-form-group">
                    <label class="rsc-label"><?php _e('Ihre Branche', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
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

                <div class="rsc-form-row">
                    <!-- Company Size -->
                    <div class="rsc-form-group rsc-form-col-2">
                        <label class="rsc-label" for="rsc-company-size"><?php _e('Mitarbeiteranzahl', 'ramboeck-configurator'); ?> <span class="required">*</span></label>
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
                    <div class="rsc-form-group rsc-form-col-2">
                        <label class="rsc-label" for="rsc-locations"><?php _e('Standorte', 'ramboeck-configurator'); ?></label>
                        <input type="number" id="rsc-locations" name="locations" class="rsc-input" value="1" min="1" max="100">
                    </div>
                </div>

                <!-- NEW: Device and User Counts for Pricing -->
                <div class="rsc-pricing-inputs">
                    <h4><?php _e('Für die Preisberechnung:', 'ramboeck-configurator'); ?></h4>

                    <div class="rsc-form-row">
                        <div class="rsc-form-group rsc-form-col-2">
                            <label class="rsc-label" for="rsc-device-count">
                                <?php _e('Anzahl PC/Laptops', 'ramboeck-configurator'); ?> <span class="required">*</span>
                                <span class="rsc-help-icon" title="<?php _e('Alle Geräte die betreut werden sollen', 'ramboeck-configurator'); ?>">ℹ️</span>
                            </label>
                            <input type="number" id="rsc-device-count" name="device_count" class="rsc-input" value="5" min="1" max="999" required>
                            <small class="rsc-help-text"><?php _e('Bestimmt Staffelpreis & Onboarding-Kosten', 'ramboeck-configurator'); ?></small>
                        </div>

                        <div class="rsc-form-group rsc-form-col-2">
                            <label class="rsc-label" for="rsc-user-count">
                                <?php _e('Anzahl Benutzer (M365)', 'ramboeck-configurator'); ?> <span class="required">*</span>
                                <span class="rsc-help-icon" title="<?php _e('Anzahl Microsoft 365 Lizenzen', 'ramboeck-configurator'); ?>">ℹ️</span>
                            </label>
                            <input type="number" id="rsc-user-count" name="user_count" class="rsc-input" value="5" min="1" max="999" required>
                            <small class="rsc-help-text"><?php _e('Für Microsoft 365 Lizenzierung', 'ramboeck-configurator'); ?></small>
                        </div>
                    </div>

                    <div class="rsc-form-row">
                        <div class="rsc-form-group rsc-form-col-2">
                            <label class="rsc-label" for="rsc-server-count">
                                <?php _e('Anzahl Server', 'ramboeck-configurator'); ?>
                                <span class="rsc-help-icon" title="<?php _e('Lokale oder Cloud-Server', 'ramboeck-configurator'); ?>">ℹ️</span>
                            </label>
                            <input type="number" id="rsc-server-count" name="server_count" class="rsc-input" value="0" min="0" max="99">
                            <small class="rsc-help-text"><?php _e('Optional - für Server-Management ADD-ON', 'ramboeck-configurator'); ?></small>
                        </div>

                        <div class="rsc-form-group rsc-form-col-2">
                            <label class="rsc-label" for="rsc-mobile-count">
                                <?php _e('Mobile Geräte (Tablets/Phones)', 'ramboeck-configurator'); ?>
                                <span class="rsc-help-icon" title="<?php _e('Firmeneigene oder BYOD Geräte', 'ramboeck-configurator'); ?>">ℹ️</span>
                            </label>
                            <input type="number" id="rsc-mobile-count" name="mobile_count" class="rsc-input" value="0" min="0" max="999">
                            <small class="rsc-help-text"><?php _e('Optional - für MDM ADD-ON', 'ramboeck-configurator'); ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-primary rsc-button-next" data-next="2">
                    <?php _e('Weiter zur Paket-Auswahl', 'ramboeck-configurator'); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Step 2: Package Selection (KERN-PAKET vs Individual) -->
        <div class="rsc-step" id="rsc-step-2">
            <div class="rsc-step-header">
                <h3><?php _e('Schritt 2: Wählen Sie Ihr Modell', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-step-subtitle"><?php _e('All-Inclusive Paket oder individuelle Services?', 'ramboeck-configurator'); ?></p>
            </div>

            <div class="rsc-step-content">
                <!-- Loading State -->
                <div id="rsc-package-loading" class="rsc-loading">
                    <div class="rsc-spinner"></div>
                    <p><?php _e('Paket-Informationen werden geladen...', 'ramboeck-configurator'); ?></p>
                </div>

                <!-- Package Choice -->
                <div id="rsc-package-choice" class="rsc-package-choice" style="display: none;">

                    <!-- KERN-PAKET Card -->
                    <div class="rsc-package-card rsc-package-recommended" id="rsc-kern-paket-card">
                        <div class="rsc-package-badge"><?php _e('🌟 EMPFOHLEN', 'ramboeck-configurator'); ?></div>

                        <div class="rsc-package-header">
                            <h3 id="rsc-package-name"><?php _e('KERN-PAKET', 'ramboeck-configurator'); ?></h3>
                            <p class="rsc-package-tagline" id="rsc-package-tagline"></p>
                        </div>

                        <div class="rsc-package-pricing">
                            <div class="rsc-pricing-breakdown">
                                <div class="rsc-pricing-row">
                                    <span><?php _e('Managed Service', 'ramboeck-configurator'); ?></span>
                                    <strong id="rsc-managed-price">90,00 €</strong>
                                    <small id="rsc-tier-info"><?php _e('pro Gerät/Monat', 'ramboeck-configurator'); ?></small>
                                </div>
                                <div class="rsc-pricing-row">
                                    <span><?php _e('Microsoft 365', 'ramboeck-configurator'); ?></span>
                                    <strong>11,70 €</strong>
                                    <small><?php _e('pro Benutzer/Monat', 'ramboeck-configurator'); ?></small>
                                </div>
                                <div class="rsc-pricing-divider"></div>
                                <div class="rsc-pricing-total">
                                    <span><?php _e('Gesamt:', 'ramboeck-configurator'); ?></span>
                                    <strong id="rsc-package-total">0,00 €</strong>
                                    <small><?php _e('pro Monat', 'ramboeck-configurator'); ?></small>
                                </div>
                                <div class="rsc-pricing-onboarding">
                                    <span><?php _e('Onboarding:', 'ramboeck-configurator'); ?></span>
                                    <strong id="rsc-package-onboarding">0,00 €</strong>
                                    <small id="rsc-onboarding-info"></small>
                                </div>
                            </div>
                        </div>

                        <div class="rsc-package-features">
                            <h4><?php _e('✅ Alles inklusive:', 'ramboeck-configurator'); ?></h4>
                            <ul id="rsc-package-features-list"></ul>
                        </div>

                        <div class="rsc-package-guarantees">
                            <h4><?php _e('Unsere Garantien:', 'ramboeck-configurator'); ?></h4>
                            <ul id="rsc-package-guarantees-list"></ul>
                        </div>

                        <button type="button" class="rsc-button rsc-button-primary rsc-button-large rsc-package-select" data-config-type="package">
                            <?php _e('KERN-PAKET auswählen', 'ramboeck-configurator'); ?>
                        </button>
                    </div>

                    <!-- OR Divider -->
                    <div class="rsc-package-divider">
                        <span><?php _e('ODER', 'ramboeck-configurator'); ?></span>
                    </div>

                    <!-- Individual Services Card -->
                    <div class="rsc-package-card rsc-package-individual">
                        <div class="rsc-package-header">
                            <h3><?php _e('Individuelle Services', 'ramboeck-configurator'); ?></h3>
                            <p class="rsc-package-tagline"><?php _e('Stellen Sie sich Ihr eigenes Paket zusammen', 'ramboeck-configurator'); ?></p>
                        </div>

                        <div class="rsc-package-description">
                            <p><?php _e('Wählen Sie nur die Services aus, die Sie wirklich benötigen.', 'ramboeck-configurator'); ?></p>
                            <p class="rsc-warning-text"><?php _e('⚠️ Einzelpreise sind höher als im KERN-PAKET', 'ramboeck-configurator'); ?></p>
                        </div>

                        <div class="rsc-individual-benefits">
                            <ul>
                                <li>✓ <?php _e('Volle Flexibilität', 'ramboeck-configurator'); ?></li>
                                <li>✓ <?php _e('Nur gewünschte Services', 'ramboeck-configurator'); ?></li>
                                <li>✓ <?php _e('Jederzeit erweiterbar', 'ramboeck-configurator'); ?></li>
                            </ul>
                        </div>

                        <button type="button" class="rsc-button rsc-button-secondary rsc-button-large rsc-package-select" data-config-type="individual">
                            <?php _e('Individuelle Services wählen', 'ramboeck-configurator'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-secondary rsc-button-prev" data-prev="1">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <?php _e('Zurück', 'ramboeck-configurator'); ?>
                </button>
            </div>
        </div>

        <!-- Step 3: Service Configuration -->
        <div class="rsc-step" id="rsc-step-3">
            <div class="rsc-step-header">
                <h3 id="rsc-step3-title"><?php _e('Schritt 3: Services konfigurieren', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-step-subtitle" id="rsc-step3-subtitle"></p>
            </div>

            <div class="rsc-step-content">
                <!-- Configuration Type Indicator -->
                <div class="rsc-config-indicator" id="rsc-config-indicator">
                    <div class="rsc-config-badge" id="rsc-config-badge"></div>
                </div>

                <!-- Recommendation Banner (for individual configs) -->
                <div class="rsc-recommendation-banner" id="rsc-recommendation-banner" style="display: none;">
                    <div class="rsc-recommendation-icon">💡</div>
                    <div class="rsc-recommendation-content">
                        <h4><?php _e('Tipp: Wechseln Sie zum KERN-PAKET!', 'ramboeck-configurator'); ?></h4>
                        <p id="rsc-recommendation-text"></p>
                        <button type="button" class="rsc-button rsc-button-small rsc-button-primary" id="rsc-switch-to-package">
                            <?php _e('Zum KERN-PAKET wechseln', 'ramboeck-configurator'); ?>
                        </button>
                    </div>
                </div>

                <!-- Services Loading -->
                <div id="rsc-services-loading" class="rsc-loading">
                    <div class="rsc-spinner"></div>
                    <p><?php _e('Services werden geladen...', 'ramboeck-configurator'); ?></p>
                </div>

                <!-- Services Grid -->
                <div id="rsc-services-container" style="display: none;">
                    <!-- ADD-ONs (always shown) -->
                    <div class="rsc-services-section" id="rsc-addons-section">
                        <h4><?php _e('ADD-ONs (optional)', 'ramboeck-configurator'); ?></h4>
                        <p class="rsc-help-text"><?php _e('Erweitern Sie Ihre Lösung mit diesen optionalen Services:', 'ramboeck-configurator'); ?></p>
                        <div class="rsc-services-grid" id="rsc-addons-grid"></div>
                    </div>

                    <!-- Individual Services (only for individual config) -->
                    <div class="rsc-services-section" id="rsc-individual-section" style="display: none;">
                        <h4><?php _e('Verfügbare Services', 'ramboeck-configurator'); ?></h4>
                        <p class="rsc-help-text"><?php _e('Wählen Sie die Services aus, die Sie benötigen:', 'ramboeck-configurator'); ?></p>
                        <div class="rsc-services-grid" id="rsc-services-grid"></div>
                    </div>
                </div>

                <div class="rsc-no-services" id="rsc-no-services" style="display: none;">
                    <p><?php _e('Keine Services verfügbar.', 'ramboeck-configurator'); ?></p>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-secondary rsc-button-prev" data-prev="2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <?php _e('Zurück', 'ramboeck-configurator'); ?>
                </button>
                <button type="button" class="rsc-button rsc-button-primary rsc-button-next" data-next="4">
                    <?php _e('Weiter zur Anfrage', 'ramboeck-configurator'); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Step 4: Contact & Summary -->
        <div class="rsc-step" id="rsc-step-4">
            <div class="rsc-step-header">
                <h3><?php _e('Schritt 4: Angebot anfordern', 'ramboeck-configurator'); ?></h3>
                <p class="rsc-step-subtitle"><?php _e('Wir erstellen Ihnen ein unverbindliches Angebot', 'ramboeck-configurator'); ?></p>
            </div>

            <div class="rsc-step-content rsc-contact-grid">
                <!-- Contact Form -->
                <div class="rsc-contact-form">
                    <h4><?php _e('Ihre Kontaktdaten', 'ramboeck-configurator'); ?></h4>

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

                <!-- Enhanced Summary -->
                <div class="rsc-summary-card">
                    <h4><?php _e('Ihre Konfiguration', 'ramboeck-configurator'); ?></h4>

                    <!-- Company Profile Summary -->
                    <div class="rsc-summary-section">
                        <h5><?php _e('Unternehmensprofil', 'ramboeck-configurator'); ?></h5>
                        <div id="rsc-summary-profile"></div>
                    </div>

                    <!-- Configuration Type Summary -->
                    <div class="rsc-summary-section">
                        <h5><?php _e('Gewähltes Modell', 'ramboeck-configurator'); ?></h5>
                        <div id="rsc-summary-config-type"></div>
                    </div>

                    <!-- Services Summary -->
                    <div class="rsc-summary-section">
                        <h5><?php _e('Services', 'ramboeck-configurator'); ?></h5>
                        <div id="rsc-summary-services"></div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="rsc-summary-pricing">
                        <div class="rsc-pricing-breakdown" id="rsc-summary-breakdown"></div>

                        <div class="rsc-summary-total">
                            <div class="rsc-summary-total-row">
                                <span><?php _e('Onboarding (einmalig):', 'ramboeck-configurator'); ?></span>
                                <strong id="rsc-total-setup">0,00 €</strong>
                            </div>
                            <div class="rsc-summary-total-row rsc-summary-total-highlight">
                                <span><?php _e('Monatlich:', 'ramboeck-configurator'); ?></span>
                                <strong id="rsc-total-monthly">0,00 €</strong>
                            </div>
                        </div>
                    </div>

                    <!-- ROI Calculator -->
                    <div class="rsc-roi-calculator" id="rsc-roi-calculator">
                        <h5><?php _e('💰 ROI-Rechner', 'ramboeck-configurator'); ?></h5>
                        <div class="rsc-roi-content">
                            <div class="rsc-roi-row">
                                <span><?php _e('Zeitersparnis IT-Admin:', 'ramboeck-configurator'); ?></span>
                                <strong id="rsc-roi-time-saved">~20h/Monat</strong>
                            </div>
                            <div class="rsc-roi-row">
                                <span><?php _e('Vermiedene Ausfallkosten:', 'ramboeck-configurator'); ?></span>
                                <strong id="rsc-roi-downtime-saved">~500 €/Monat</strong>
                            </div>
                            <div class="rsc-roi-row rsc-roi-highlight">
                                <span><?php _e('ROI nach 12 Monaten:', 'ramboeck-configurator'); ?></span>
                                <strong id="rsc-roi-total">+250%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rsc-step-actions">
                <button type="button" class="rsc-button rsc-button-secondary rsc-button-prev" data-prev="3">
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
                <span class="rsc-sticky-summary-label"><?php _e('Onboarding:', 'ramboeck-configurator'); ?></span>
                <span class="rsc-sticky-summary-value" id="rsc-sticky-setup">0,00 €</span>
            </div>
            <div class="rsc-sticky-summary-item">
                <span class="rsc-sticky-summary-label"><?php _e('Monatlich:', 'ramboeck-configurator'); ?></span>
                <span class="rsc-sticky-summary-value" id="rsc-sticky-monthly">0,00 €</span>
            </div>
        </div>
    </div>
</div>
