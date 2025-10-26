/**
 * Frontend JavaScript for Service Configurator v5.0.0
 *
 * New 4-Step Flow:
 * 1. Company Profile (extended)
 * 2. Package Selection (KERN-PAKET vs Individual)
 * 3. Service Configuration (ADD-ONs or Individual Services)
 * 4. Contact & Summary (enhanced with ROI)
 *
 * @package RamboeckIT\ServiceConfigurator
 * @since 5.0.0
 */

(function($) {
    'use strict';

    class ServiceConfigurator {
        constructor() {
            // Step tracking
            this.currentStep = 1;
            this.totalSteps = 4; // NEW: 4 steps instead of 3

            // Step 1 data
            this.selectedIndustry = null;
            this.deviceCount = 5;
            this.userCount = 5;
            this.serverCount = 0;
            this.mobileCount = 0;

            // Step 2 data (NEW)
            this.configType = null; // 'package' or 'individual'
            this.packageInfo = null;

            // Step 3 data
            this.services = [];
            this.selectedServices = []; // For individual config
            this.selectedAddons = []; // Always available

            // Step 4 data
            this.formData = {};
            this.pricingBreakdown = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.updateProgress();
            this.initializeAnimations();
        }

        initializeAnimations() {
            // Add fade-in animation to cards
            $('.rsc-industry-card').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.05) + 's'
                }).addClass('fade-in');
            });
        }

        bindEvents() {
            const self = this;

            // === STEP 1: Company Profile ===
            // Industry selection
            $(document).on('click', '.rsc-industry-card', function(e) {
                console.log('Industry card clicked:', $(this).data('industry'));

                $('.rsc-industry-card').removeClass('selected');
                $(this).addClass('selected');

                self.selectedIndustry = $(this).data('industry');
                $('#rsc-industry').val(self.selectedIndustry);

                console.log('Selected industry:', self.selectedIndustry);

                // Visual feedback
                $(this).addClass('pulse-once');
                setTimeout(() => $(this).removeClass('pulse-once'), 600);

                // Enable next button
                $('.rsc-button-next[data-next="2"]').prop('disabled', false).removeClass('disabled');
            });

            // Device count change
            $('#rsc-device-count').on('change', function() {
                self.deviceCount = parseInt($(this).val()) || 1;
                console.log('Device count changed:', self.deviceCount);
            });

            // User count change
            $('#rsc-user-count').on('change', function() {
                self.userCount = parseInt($(this).val()) || 1;
                console.log('User count changed:', self.userCount);
            });

            // === STEP 2: Package Selection (NEW) ===
            $('.rsc-package-select').on('click', function() {
                const configType = $(this).data('config-type');
                self.selectConfigType(configType);
            });

            // === STEP 3: Service Configuration ===
            // Switch to package button
            $('#rsc-switch-to-package').on('click', function() {
                self.switchToPackage();
            });

            // === NAVIGATION ===
            // Next button
            $('.rsc-button-next').on('click', function() {
                const nextStep = $(this).data('next');
                if (self.validateStep(self.currentStep)) {
                    self.goToStep(nextStep);
                }
            });

            // Previous button
            $('.rsc-button-prev').on('click', function() {
                const prevStep = $(this).data('prev');
                self.goToStep(prevStep);
            });

            // Progress step click
            $('.rsc-progress-step').on('click', function() {
                const step = $(this).data('step');
                if (step < self.currentStep) {
                    self.goToStep(step);
                }
            });

            // Submit button
            $('#rsc-submit').on('click', function() {
                if (self.validateStep(4)) {
                    self.submitConfiguration();
                }
            });

            // Show sticky summary on scroll (step 3+)
            $(window).on('scroll', function() {
                if (self.currentStep >= 3) {
                    if ($(window).scrollTop() > 300) {
                        $('#rsc-sticky-summary').fadeIn();
                    } else {
                        $('#rsc-sticky-summary').fadeOut();
                    }
                }
            });
        }

        // ==========================================
        // NAVIGATION
        // ==========================================

        goToStep(step) {
            console.log('Going to step:', step);

            // Save current step data
            this.saveStepData(this.currentStep);

            // Hide current step
            $('.rsc-step').removeClass('rsc-step-active');
            $('.rsc-progress-step').removeClass('active completed');

            // Show new step
            $('#rsc-step-' + step).addClass('rsc-step-active');

            // Update progress indicators
            for (let i = 1; i <= this.totalSteps; i++) {
                const $step = $('.rsc-progress-step[data-step="' + i + '"]');
                if (i < step) {
                    $step.addClass('completed');
                } else if (i === step) {
                    $step.addClass('active');
                }
            }

            this.currentStep = step;
            this.updateProgress();

            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.rsc-wrapper').offset().top - 50
            }, 300);

            // Step-specific actions
            if (step === 2) {
                this.loadPackageInfo();
            } else if (step === 3) {
                this.loadServicesForStep3();
            } else if (step === 4) {
                this.updateSummary();
            }
        }

        updateProgress() {
            const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            $('#rsc-progress-fill').css('width', progress + '%');
        }

        validateStep(step) {
            if (step === 1) {
                // Check industry
                if (!this.selectedIndustry) {
                    this.showError('Schritt 1', 'Bitte wählen Sie Ihre Branche aus.');
                    return false;
                }

                // Check company size
                const companySize = $('#rsc-company-size').val();
                if (!companySize) {
                    this.showError('Schritt 1', 'Bitte geben Sie die Anzahl Ihrer Mitarbeiter an.');
                    return false;
                }

                // Check device count (NEW)
                const deviceCount = parseInt($('#rsc-device-count').val());
                if (!deviceCount || deviceCount < 1) {
                    this.showError('Schritt 1', 'Bitte geben Sie die Anzahl Ihrer Geräte an.');
                    $('#rsc-device-count').focus();
                    return false;
                }

                // Check user count (NEW)
                const userCount = parseInt($('#rsc-user-count').val());
                if (!userCount || userCount < 1) {
                    this.showError('Schritt 1', 'Bitte geben Sie die Anzahl Ihrer Benutzer an.');
                    $('#rsc-user-count').focus();
                    return false;
                }

            } else if (step === 2) {
                // Check if config type is selected (NEW)
                if (!this.configType) {
                    this.showError('Schritt 2', 'Bitte wählen Sie zwischen KERN-PAKET und individuellen Services.');
                    return false;
                }

            } else if (step === 3) {
                // For package: No validation needed (ADD-ONs are optional)
                // For individual: At least one service required
                if (this.configType === 'individual' && this.selectedServices.length === 0) {
                    this.showError('Schritt 3', 'Bitte wählen Sie mindestens einen Service aus.');
                    return false;
                }

            } else if (step === 4) {
                // Validate contact form
                const name = $('#rsc-name').val().trim();
                const email = $('#rsc-email').val().trim();
                const privacy = $('#rsc-privacy').is(':checked');

                if (!name) {
                    this.showError('Kontaktdaten', 'Bitte geben Sie Ihren Namen ein.');
                    $('#rsc-name').focus();
                    return false;
                }

                if (!email || !this.validateEmail(email)) {
                    this.showError('Kontaktdaten', 'Bitte geben Sie eine gültige E-Mail-Adresse ein.');
                    $('#rsc-email').focus();
                    return false;
                }

                if (!privacy) {
                    this.showError('Datenschutz', 'Bitte akzeptieren Sie die Datenschutzerklärung.');
                    return false;
                }
            }

            return true;
        }

        saveStepData(step) {
            if (step === 1) {
                this.formData.industry = this.selectedIndustry;
                this.formData.company_size = $('#rsc-company-size').val();
                this.formData.locations = $('#rsc-locations').val();
                this.deviceCount = parseInt($('#rsc-device-count').val()) || 1;
                this.userCount = parseInt($('#rsc-user-count').val()) || 1;
                this.serverCount = parseInt($('#rsc-server-count').val()) || 0;
                this.mobileCount = parseInt($('#rsc-mobile-count').val()) || 0;

                this.formData.device_count = this.deviceCount;
                this.formData.user_count = this.userCount;
                this.formData.server_count = this.serverCount;
                this.formData.mobile_count = this.mobileCount;

            } else if (step === 4) {
                this.formData.name = $('#rsc-name').val().trim();
                this.formData.email = $('#rsc-email').val().trim();
                this.formData.phone = $('#rsc-phone').val().trim();
                this.formData.company = $('#rsc-company').val().trim();
            }
        }

        // ==========================================
        // STEP 2: PACKAGE SELECTION (NEW)
        // ==========================================

        loadPackageInfo() {
            const self = this;

            console.log('Loading KERN-PAKET info for:', {
                deviceCount: this.deviceCount,
                userCount: this.userCount
            });

            $('#rsc-package-loading').show();
            $('#rsc-package-choice').hide();

            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rsc_get_package_info',
                    nonce: rscData.nonce,
                    device_count: this.deviceCount,
                    user_count: this.userCount
                },
                success: function(response) {
                    console.log('Package info response:', response);

                    $('#rsc-package-loading').hide();

                    if (response.success && response.data) {
                        self.packageInfo = response.data;
                        self.renderPackageInfo();
                        $('#rsc-package-choice').fadeIn();
                    } else {
                        self.showError('Fehler', response.data?.message || 'KERN-PAKET konnte nicht geladen werden.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Package info error:', {xhr, status, error});
                    self.showError('Fehler', 'Paket-Informationen konnten nicht geladen werden.');
                    $('#rsc-package-loading').hide();
                }
            });
        }

        renderPackageInfo() {
            if (!this.packageInfo) return;

            const pricing = this.packageInfo.pricing;

            // Set tagline
            $('#rsc-package-tagline').text(this.packageInfo.tagline || '');

            // Set pricing
            $('#rsc-managed-price').text(this.formatPrice(pricing.managed_price));
            $('#rsc-tier-info').text(pricing.tier_info);
            $('#rsc-package-total').text(this.formatPrice(pricing.monthly_total));
            $('#rsc-package-onboarding').text(this.formatPrice(pricing.onboarding));

            // Onboarding info text
            let onboardingInfo = '';
            if (this.deviceCount <= 3) {
                onboardingInfo = '✅ Kostenlos (1-3 Geräte)';
            } else if (this.deviceCount >= 10) {
                onboardingInfo = '✅ Kostenlos (10+ Geräte)';
            } else {
                onboardingInfo = '💶 99€ pro Gerät (4-9 Geräte)';
            }
            $('#rsc-onboarding-info').text(onboardingInfo);

            // Features list
            const featuresList = $('#rsc-package-features-list');
            featuresList.empty();
            if (this.packageInfo.features && Array.isArray(this.packageInfo.features)) {
                this.packageInfo.features.forEach(feature => {
                    featuresList.append(`<li>${this.escapeHtml(feature)}</li>`);
                });
            }

            // Guarantees list
            const guaranteesList = $('#rsc-package-guarantees-list');
            guaranteesList.empty();
            if (this.packageInfo.guarantees && Array.isArray(this.packageInfo.guarantees)) {
                this.packageInfo.guarantees.forEach(guarantee => {
                    guaranteesList.append(`<li>${this.escapeHtml(guarantee)}</li>`);
                });
            }
        }

        selectConfigType(type) {
            console.log('Selected config type:', type);

            this.configType = type;

            // Provide visual feedback
            $('.rsc-package-select').prop('disabled', true);
            const $selectedBtn = $('.rsc-package-select[data-config-type="' + type + '"]');
            $selectedBtn.html('<div class="rsc-spinner-small" style="display:inline-block;margin-right:8px"></div> Wird geladen...');

            // Short delay for UX
            setTimeout(() => {
                this.goToStep(3);
                // Re-enable buttons
                $('.rsc-package-select').prop('disabled', false);
                if (type === 'package') {
                    $('.rsc-package-select[data-config-type="package"]').html('KERN-PAKET auswählen');
                } else {
                    $('.rsc-package-select[data-config-type="individual"]').html('Individuelle Services wählen');
                }
            }, 500);
        }

        switchToPackage() {
            // User clicked "switch to package" from individual config
            console.log('Switching from individual to package');

            // Reset selected services
            this.selectedServices = [];

            // Go back to step 2 and auto-select package
            this.configType = 'package';
            this.goToStep(3);

            // Update Step 3 UI
            this.loadServicesForStep3();
        }

        // ==========================================
        // STEP 3: SERVICE CONFIGURATION
        // ==========================================

        loadServicesForStep3() {
            const self = this;

            console.log('Loading services for Step 3, config type:', this.configType);

            // Update step 3 title based on config type
            if (this.configType === 'package') {
                $('#rsc-step3-title').text('Schritt 3: ADD-ONs konfigurieren (optional)');
                $('#rsc-step3-subtitle').text('Erweitern Sie Ihr KERN-PAKET mit optionalen Services');
                $('#rsc-config-badge').html('🌟 KERN-PAKET + ADD-ONs').removeClass('badge-individual').addClass('badge-package');
            } else {
                $('#rsc-step3-title').text('Schritt 3: Services auswählen');
                $('#rsc-step3-subtitle').text('Wählen Sie die benötigten Services einzeln aus');
                $('#rsc-config-badge').html('📋 Individuelle Konfiguration').removeClass('badge-package').addClass('badge-individual');
            }

            $('#rsc-services-loading').show();
            $('#rsc-services-container').hide();
            $('#rsc-recommendation-banner').hide();

            // Load services via AJAX
            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rsc_get_services',
                    nonce: rscData.nonce,
                    industry: this.selectedIndustry
                },
                success: function(response) {
                    console.log('Services response:', response);

                    $('#rsc-services-loading').hide();

                    if (response.success && response.data && response.data.length > 0) {
                        self.services = response.data;
                        self.renderServicesForStep3();
                        $('#rsc-services-container').fadeIn();

                        // Check if we should show recommendation (for individual config)
                        if (self.configType === 'individual') {
                            self.checkRecommendation();
                        }
                    } else {
                        self.showError('Fehler', response.data?.message || 'Keine Services gefunden.');
                        $('#rsc-no-services').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Services loading error:', {xhr, status, error});
                    self.showError('Fehler', 'Services konnten nicht geladen werden.');
                    $('#rsc-services-loading').hide();
                }
            });
        }

        renderServicesForStep3() {
            // Separate services into ADD-ONs and regular services
            const addons = this.services.filter(s => s.service_type === 'addon');
            const regularServices = this.services.filter(s => s.service_type !== 'addon');

            console.log('Rendering services - ADD-ONs:', addons.length, 'Regular:', regularServices.length);

            // Always render ADD-ONs
            this.renderServiceGrid(addons, $('#rsc-addons-grid'), 'addon');

            // Show individual services section only if config type is individual
            if (this.configType === 'individual') {
                $('#rsc-individual-section').show();
                this.renderServiceGrid(regularServices, $('#rsc-services-grid'), 'service');
            } else {
                $('#rsc-individual-section').hide();
            }
        }

        renderServiceGrid(services, $container, type) {
            const self = this;
            $container.empty();

            if (services.length === 0) {
                $container.html('<p>Keine Services verfügbar.</p>');
                return;
            }

            // Sort: recommended first
            const sortedServices = services.sort((a, b) => {
                if (a.is_recommended && !b.is_recommended) return -1;
                if (!a.is_recommended && b.is_recommended) return 1;
                return parseInt(a.sort_order) - parseInt(b.sort_order);
            });

            sortedServices.forEach(function(service) {
                const isSelected = type === 'addon' ?
                    self.selectedAddons.some(s => s.id === service.id) :
                    self.selectedServices.some(s => s.id === service.id);

                const recommendedBadge = service.is_recommended ?
                    '<span class="rsc-service-badge">Empfohlen</span>' : '';

                // Get price to display
                let displayPrice = service.monthly_price;
                if (type === 'service' && service.standalone_price) {
                    displayPrice = service.standalone_price;
                }

                // Build features HTML if available
                let featuresHtml = '';
                if (service.features && Array.isArray(service.features) && service.features.length > 0) {
                    featuresHtml = '<div class="rsc-service-features"><ul>';
                    service.features.slice(0, 3).forEach(function(feature) {
                        if (typeof feature === 'object') {
                            featuresHtml += `<li>${feature.icon || '✓'} ${self.escapeHtml(feature.title || '')}</li>`;
                        } else {
                            featuresHtml += `<li>${self.escapeHtml(feature)}</li>`;
                        }
                    });
                    featuresHtml += '</ul></div>';
                }

                const $card = $(`
                    <div class="rsc-service-card ${isSelected ? 'selected' : ''} ${service.is_recommended ? 'recommended' : ''}"
                         data-service-id="${service.id}"
                         data-service-type="${type}">
                        ${recommendedBadge}
                        <div class="rsc-service-header">
                            <h4>${self.escapeHtml(service.name)}</h4>
                            <div class="rsc-service-checkbox">
                                <input type="checkbox" ${isSelected ? 'checked' : ''}>
                            </div>
                        </div>
                        <p class="rsc-service-description">${self.escapeHtml(service.description)}</p>
                        ${featuresHtml}
                        <div class="rsc-service-pricing">
                            <div class="rsc-service-price">
                                <span class="rsc-price-label">Monatlich:</span>
                                <span class="rsc-price-value">${self.formatPrice(displayPrice)}</span>
                            </div>
                        </div>
                    </div>
                `);

                $card.on('click', function() {
                    self.toggleService(service, $card, type);
                });

                $container.append($card);
            });
        }

        toggleService(service, $card, type) {
            const targetArray = type === 'addon' ? this.selectedAddons : this.selectedServices;
            const index = targetArray.findIndex(s => s.id === service.id);

            if (index > -1) {
                // Remove service
                targetArray.splice(index, 1);
                $card.removeClass('selected');
                $card.find('input[type="checkbox"]').prop('checked', false);
            } else {
                // Add service
                const price = (type === 'service' && service.standalone_price) ?
                    parseFloat(service.standalone_price) :
                    parseFloat(service.monthly_price);

                targetArray.push({
                    id: service.id,
                    name: service.name,
                    description: service.description,
                    monthly_price: price
                });
                $card.addClass('selected');
                $card.find('input[type="checkbox"]').prop('checked', true);
            }

            // Update pricing
            this.updatePriceSummary();

            // Re-check recommendation if individual config
            if (this.configType === 'individual' && type === 'service') {
                this.checkRecommendation();
            }
        }

        checkRecommendation() {
            const self = this;

            if (this.configType !== 'individual' || this.selectedServices.length === 0) {
                $('#rsc-recommendation-banner').hide();
                return;
            }

            const serviceIds = this.selectedServices.map(s => s.id);

            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rsc_check_recommendation',
                    nonce: rscData.nonce,
                    service_ids: serviceIds,
                    device_count: this.deviceCount
                },
                success: function(response) {
                    console.log('Recommendation response:', response);

                    if (response.success && response.data.recommend) {
                        const savings = response.data.savings;
                        const savingsPercent = response.data.savings_percent;

                        $('#rsc-recommendation-text').html(
                            `Sie sparen <strong>${self.formatPrice(savings)}</strong> (${savingsPercent}%) ` +
                            `mit dem KERN-PAKET statt einzelner Services!`
                        );

                        $('#rsc-recommendation-banner').slideDown();
                    } else {
                        $('#rsc-recommendation-banner').slideUp();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Recommendation check error:', {xhr, status, error});
                }
            });
        }

        updatePriceSummary() {
            // This will be calculated via AJAX in updateSummary()
            // For now, just show approximate values
            let totalMonthly = 0;

            if (this.configType === 'package' && this.packageInfo) {
                totalMonthly = this.packageInfo.pricing.monthly_total;
            } else if (this.configType === 'individual') {
                this.selectedServices.forEach(s => {
                    totalMonthly += s.monthly_price * this.deviceCount;
                });
            }

            // Add ADD-ONs
            this.selectedAddons.forEach(s => {
                totalMonthly += s.monthly_price;
            });

            // Update sticky summary
            $('#rsc-sticky-setup').text(this.formatPrice(0)); // Will be calculated properly later
            $('#rsc-sticky-monthly').text(this.formatPrice(totalMonthly));

            if (this.currentStep >= 3) {
                $('#rsc-sticky-summary').show();
            }
        }

        // ==========================================
        // STEP 4: SUMMARY & CONTACT
        // ==========================================

        updateSummary() {
            const self = this;

            // Profile summary
            const industryName = $('.rsc-industry-card.selected h4').text() || this.formData.industry;
            $('#rsc-summary-profile').html(`
                <p><strong>Branche:</strong> ${industryName}</p>
                <p><strong>Mitarbeiter:</strong> ${this.formData.company_size}</p>
                <p><strong>Geräte:</strong> ${this.deviceCount}</p>
                <p><strong>Benutzer (M365):</strong> ${this.userCount}</p>
                ${this.serverCount > 0 ? `<p><strong>Server:</strong> ${this.serverCount}</p>` : ''}
                ${this.mobileCount > 0 ? `<p><strong>Mobile Geräte:</strong> ${this.mobileCount}</p>` : ''}
            `);

            // Config type summary
            if (this.configType === 'package') {
                $('#rsc-summary-config-type').html(`
                    <div class="rsc-config-badge badge-package">🌟 KERN-PAKET</div>
                    <p>All-Inclusive Rundum-Sorglos-Betreuung</p>
                `);
            } else {
                $('#rsc-summary-config-type').html(`
                    <div class="rsc-config-badge badge-individual">📋 Individuelle Services</div>
                    <p>Maßgeschneiderte Service-Auswahl</p>
                `);
            }

            // Calculate detailed pricing via AJAX
            this.calculatePricing();
        }

        calculatePricing() {
            const self = this;

            const serviceIds = this.selectedServices.map(s => s.id);
            const addonConfig = this.selectedAddons.map(s => ({
                service_id: s.id,
                quantity: 1 // For now, quantity is always 1
            }));

            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rsc_calculate_pricing',
                    nonce: rscData.nonce,
                    config_type: this.configType,
                    device_count: this.deviceCount,
                    user_count: this.userCount,
                    service_ids: serviceIds,
                    addon_config: JSON.stringify(addonConfig)
                },
                success: function(response) {
                    console.log('Pricing response:', response);

                    if (response.success && response.data) {
                        self.pricingBreakdown = response.data;
                        self.renderPricingBreakdown();
                        self.renderROI();
                    } else {
                        console.error('Pricing calculation failed');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Pricing calculation error:', {xhr, status, error});
                }
            });
        }

        renderPricingBreakdown() {
            if (!this.pricingBreakdown) return;

            const breakdown = this.pricingBreakdown;
            let html = '';

            // Base pricing
            if (this.configType === 'package') {
                html += `
                    <div class="rsc-pricing-row">
                        <span>Managed Service (${breakdown.base.tier})</span>
                        <strong>${this.formatPrice(breakdown.base.managed_price_per_device)} × ${this.deviceCount}</strong>
                    </div>
                    <div class="rsc-pricing-row">
                        <span>Microsoft 365 Business Standard</span>
                        <strong>${this.formatPrice(breakdown.base.m365_price_per_user)} × ${this.userCount}</strong>
                    </div>
                    <div class="rsc-pricing-subtotal">
                        <span>KERN-PAKET Gesamt:</span>
                        <strong>${this.formatPrice(breakdown.base.monthly_total)}</strong>
                    </div>
                `;
            } else {
                // Individual services
                if (breakdown.base.services && breakdown.base.services.length > 0) {
                    breakdown.base.services.forEach(s => {
                        html += `
                            <div class="rsc-pricing-row">
                                <span>${this.escapeHtml(s.name)}</span>
                                <strong>${this.formatPrice(s.total)}</strong>
                            </div>
                        `;
                    });
                    html += `
                        <div class="rsc-pricing-subtotal">
                            <span>Services Gesamt:</span>
                            <strong>${this.formatPrice(breakdown.base.monthly_total)}</strong>
                        </div>
                    `;
                }
            }

            // ADD-ONs
            if (breakdown.addons && breakdown.addons.services.length > 0) {
                html += '<div class="rsc-pricing-section-title">ADD-ONs:</div>';
                breakdown.addons.services.forEach(addon => {
                    html += `
                        <div class="rsc-pricing-row">
                            <span>${this.escapeHtml(addon.name)}</span>
                            <strong>${this.formatPrice(addon.total)}</strong>
                        </div>
                    `;
                });
            }

            $('#rsc-summary-breakdown').html(html);

            // Update totals
            $('#rsc-total-setup').text(this.formatPrice(breakdown.onboarding));
            $('#rsc-total-monthly').text(this.formatPrice(breakdown.total.monthly));
            $('#rsc-sticky-setup').text(this.formatPrice(breakdown.onboarding));
            $('#rsc-sticky-monthly').text(this.formatPrice(breakdown.total.monthly));

            // Services list
            this.renderServicesList();
        }

        renderServicesList() {
            let html = '<ul class="rsc-summary-list">';

            if (this.configType === 'package') {
                html += '<li><strong>KERN-PAKET</strong> (Alles inklusive)</li>';
            } else {
                this.selectedServices.forEach(s => {
                    html += `<li>${this.escapeHtml(s.name)}</li>`;
                });
            }

            this.selectedAddons.forEach(s => {
                html += `<li>${this.escapeHtml(s.name)} <span class="badge-addon">ADD-ON</span></li>`;
            });

            html += '</ul>';
            $('#rsc-summary-services').html(html);
        }

        renderROI() {
            // Simple ROI calculation
            const deviceCount = this.deviceCount;
            const monthlyTotal = this.pricingBreakdown?.total.monthly || 0;

            // Estimates:
            // - Time saved: ~4h per device/month for IT admin
            // - Downtime prevention: ~2% of business hours = ~3.5h/month per device
            // - Average hourly business cost: 50€

            const timeSavedHours = deviceCount * 4;
            const downtimeSavedHours = deviceCount * 3.5;
            const totalValuePerMonth = (timeSavedHours + downtimeSavedHours) * 50;

            const roi = ((totalValuePerMonth - monthlyTotal) / monthlyTotal * 100).toFixed(0);

            $('#rsc-roi-time-saved').text(`~${timeSavedHours}h/Monat`);
            $('#rsc-roi-downtime-saved').text(`~${this.formatPrice(downtimeSavedHours * 50)}/Monat`);
            $('#rsc-roi-total').text(`+${roi}%`);
        }

        // ==========================================
        // FORM SUBMISSION
        // ==========================================

        submitConfiguration() {
            const self = this;
            const $submitBtn = $('#rsc-submit');

            // Disable button
            $submitBtn.prop('disabled', true).html('<div class="rsc-spinner-small"></div> Wird gesendet...');

            // Prepare service data
            let servicesData = [];
            if (this.configType === 'package') {
                servicesData.push({
                    type: 'package',
                    name: 'KERN-PAKET',
                    monthly_total: this.packageInfo.pricing.monthly_total
                });
            } else {
                servicesData = this.selectedServices.map(s => ({
                    type: 'service',
                    id: s.id,
                    name: s.name,
                    monthly_price: s.monthly_price
                }));
            }

            // Add ADD-ONs
            this.selectedAddons.forEach(s => {
                servicesData.push({
                    type: 'addon',
                    id: s.id,
                    name: s.name,
                    monthly_price: s.monthly_price
                });
            });

            // Calculate totals
            const onboarding = this.pricingBreakdown?.onboarding || 0;
            const monthly = this.pricingBreakdown?.total.monthly || 0;

            // Prepare data
            const data = {
                action: 'rsc_submit_configuration',
                nonce: rscData.nonce,
                name: this.formData.name,
                email: this.formData.email,
                phone: this.formData.phone,
                company: this.formData.company,
                industry: this.formData.industry,
                company_size: this.formData.company_size,
                locations: this.formData.locations,
                device_count: this.deviceCount,
                user_count: this.userCount,
                server_count: this.serverCount,
                mobile_count: this.mobileCount,
                config_type: this.configType,
                services: JSON.stringify(servicesData),
                total_setup: onboarding,
                total_monthly: monthly
            };

            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        self.showSuccess();
                    } else {
                        self.showError('Fehler', response.data.message || 'Ein Fehler ist aufgetreten.');
                        $submitBtn.prop('disabled', false).html(`
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Angebot anfordern
                        `);
                    }
                },
                error: function() {
                    self.showError('Fehler', 'Die Anfrage konnte nicht gesendet werden. Bitte versuchen Sie es erneut.');
                    $submitBtn.prop('disabled', false).html(`
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Angebot anfordern
                    `);
                }
            });
        }

        showSuccess() {
            $('.rsc-step').removeClass('rsc-step-active');
            $('#rsc-step-success').fadeIn().addClass('rsc-step-active');
            $('#rsc-sticky-summary').hide();
            $('.rsc-progress').hide();

            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.rsc-wrapper').offset().top - 50
            }, 300);
        }

        // ==========================================
        // UTILITY FUNCTIONS
        // ==========================================

        showError(title, message) {
            alert(title + '\n\n' + message);
        }

        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        formatPrice(price) {
            // Map currency symbols to ISO codes
            const currencyMap = {
                '€': 'EUR',
                '$': 'USD',
                '£': 'GBP',
                '¥': 'JPY',
                'CHF': 'CHF'
            };

            let currency = 'EUR';
            if (typeof rscData !== 'undefined' && rscData.currency) {
                currency = currencyMap[rscData.currency] || rscData.currency;
            }

            // Ensure currency is a valid ISO code
            if (!/^[A-Z]{3}$/.test(currency)) {
                currency = 'EUR';
            }

            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: currency
            }).format(price);
        }

        escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        if ($('.rsc-wrapper').length) {
            new ServiceConfigurator();
        }
    });

})(jQuery);
