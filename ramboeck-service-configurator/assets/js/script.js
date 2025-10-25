/**
 * Frontend JavaScript for Service Configurator
 *
 * @package RamboeckIT\ServiceConfigurator
 */

(function($) {
    'use strict';

    class ServiceConfigurator {
        constructor() {
            this.currentStep = 1;
            this.selectedIndustry = null;
            this.selectedServices = [];
            this.services = [];
            this.formData = {};

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

            // Industry selection with better visual feedback
            $(document).on('click', '.rsc-industry-card', function(e) {
                e.preventDefault();

                // Remove previous selection
                $('.rsc-industry-card').removeClass('selected');

                // Add selection to clicked card with animation
                $(this).addClass('selected');

                // Store selection
                self.selectedIndustry = $(this).data('industry');
                $('#rsc-industry').val(self.selectedIndustry);

                // Visual feedback - pulse animation
                $(this).addClass('pulse-once');
                setTimeout(() => {
                    $(this).removeClass('pulse-once');
                }, 600);

                // Enable next button
                $('.rsc-button-next[data-next="2"]').prop('disabled', false).removeClass('disabled');
            });

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
                if (self.validateStep(3)) {
                    self.submitConfiguration();
                }
            });

            // Show sticky summary on scroll (step 2+)
            $(window).on('scroll', function() {
                if (self.currentStep >= 2) {
                    if ($(window).scrollTop() > 300) {
                        $('#rsc-sticky-summary').fadeIn();
                    } else {
                        $('#rsc-sticky-summary').fadeOut();
                    }
                }
            });
        }

        goToStep(step) {
            // Save current step data
            this.saveStepData(this.currentStep);

            // Hide current step
            $('.rsc-step').removeClass('rsc-step-active');
            $('.rsc-progress-step').removeClass('active completed');

            // Show new step
            $('#rsc-step-' + step).addClass('rsc-step-active');

            // Update progress
            for (let i = 1; i <= 3; i++) {
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

            // Load services when entering step 2
            if (step === 2) {
                this.loadServices();
            }

            // Update summary when entering step 3
            if (step === 3) {
                this.updateSummary();
            }
        }

        updateProgress() {
            const progress = ((this.currentStep - 1) / 2) * 100;
            $('#rsc-progress-fill').css('width', progress + '%');
        }

        validateStep(step) {
            if (step === 1) {
                // Check industry and company size
                if (!this.selectedIndustry) {
                    this.showError(rscData.i18n.step1Title || 'Schritt 1', 'Bitte wählen Sie Ihre Branche aus.');
                    return false;
                }

                const companySize = $('#rsc-company-size').val();
                if (!companySize) {
                    this.showError(rscData.i18n.step1Title || 'Schritt 1', 'Bitte geben Sie die Anzahl Ihrer Mitarbeiter an.');
                    return false;
                }
            } else if (step === 2) {
                // Check if at least one service is selected
                if (this.selectedServices.length === 0) {
                    this.showError(rscData.i18n.step2Title || 'Schritt 2', 'Bitte wählen Sie mindestens einen Service aus.');
                    return false;
                }
            } else if (step === 3) {
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
            } else if (step === 3) {
                this.formData.name = $('#rsc-name').val().trim();
                this.formData.email = $('#rsc-email').val().trim();
                this.formData.phone = $('#rsc-phone').val().trim();
                this.formData.company = $('#rsc-company').val().trim();
            }
        }

        loadServices() {
            const self = this;

            console.log('Loading services for industry:', this.selectedIndustry);

            $('#rsc-services-loading').show();
            $('#rsc-services-grid').hide();
            $('#rsc-no-services').hide();

            // Check if rscData exists
            if (typeof rscData === 'undefined') {
                console.error('rscData is not defined!');
                this.showError('Fehler', 'Plugin-Konfiguration fehlt. Bitte Seite neu laden.');
                $('#rsc-services-loading').hide();
                return;
            }

            console.log('AJAX Request:', {
                url: rscData.ajaxUrl,
                action: 'rsc_get_services',
                nonce: rscData.nonce,
                industry: this.selectedIndustry
            });

            $.ajax({
                url: rscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rsc_get_services',
                    nonce: rscData.nonce,
                    industry: this.selectedIndustry
                },
                success: function(response) {
                    console.log('AJAX Response:', response);

                    $('#rsc-services-loading').hide();

                    if (response.success && response.data && response.data.length > 0) {
                        console.log('Services loaded:', response.data.length);
                        self.services = response.data;
                        self.renderServices();

                        // Update recommendation text
                        const recommendedCount = response.data.filter(s => s.is_recommended).length;
                        if (recommendedCount > 0) {
                            $('#rsc-recommendation-text').html(
                                '<strong>' + recommendedCount + ' Services</strong> sind speziell für Ihre Branche empfohlen.'
                            );
                        }
                    } else {
                        console.warn('No services in response or response.success is false');
                        if (response.data && response.data.message) {
                            console.error('Error message:', response.data.message);
                            self.showError('Fehler', response.data.message);
                        } else {
                            console.log('Empty services array');
                        }
                        $('#rsc-no-services').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    console.error('Response Text:', xhr.responseText);

                    let errorMessage = 'Services konnten nicht geladen werden. ';

                    if (xhr.status === 0) {
                        errorMessage += 'Keine Verbindung zum Server. Bitte prüfen Sie Ihre Internetverbindung.';
                    } else if (xhr.status === 404) {
                        errorMessage += 'AJAX-Endpunkt nicht gefunden. Plugin korrekt installiert?';
                    } else if (xhr.status === 403) {
                        errorMessage += 'Zugriff verweigert. Bitte neu anmelden.';
                    } else if (xhr.status === 500) {
                        errorMessage += 'Server-Fehler. Bitte überprüfen Sie die PHP-Fehler-Logs.';
                    } else {
                        errorMessage += 'Fehler ' + xhr.status + ': ' + error;
                    }

                    self.showError('Fehler', errorMessage);
                    $('#rsc-services-loading').hide();
                }
            });
        }

        renderServices() {
            const self = this;
            const $grid = $('#rsc-services-grid');
            $grid.empty();

            // Sort: recommended first
            const sortedServices = this.services.sort((a, b) => {
                if (a.is_recommended && !b.is_recommended) return -1;
                if (!a.is_recommended && b.is_recommended) return 1;
                return parseInt(a.sort_order) - parseInt(b.sort_order);
            });

            sortedServices.forEach(function(service) {
                const isSelected = self.selectedServices.some(s => s.id === service.id);
                const recommendedBadge = service.is_recommended ?
                    '<span class="rsc-service-badge">Empfohlen</span>' : '';

                const $card = $(`
                    <div class="rsc-service-card ${isSelected ? 'selected' : ''} ${service.is_recommended ? 'recommended' : ''}"
                         data-service-id="${service.id}">
                        ${recommendedBadge}
                        <div class="rsc-service-header">
                            <h4>${self.escapeHtml(service.name)}</h4>
                            <div class="rsc-service-checkbox">
                                <input type="checkbox" ${isSelected ? 'checked' : ''}>
                            </div>
                        </div>
                        <p class="rsc-service-description">${self.escapeHtml(service.description)}</p>
                        ${service.tooltip ? `<p class="rsc-service-tooltip">${self.escapeHtml(service.tooltip)}</p>` : ''}
                        <div class="rsc-service-pricing">
                            <div class="rsc-service-price">
                                <span class="rsc-price-label">Einmalig:</span>
                                <span class="rsc-price-value">${self.formatPrice(service.setup_price)}</span>
                            </div>
                            <div class="rsc-service-price">
                                <span class="rsc-price-label">Monatlich:</span>
                                <span class="rsc-price-value">${self.formatPrice(service.monthly_price)}</span>
                            </div>
                        </div>
                    </div>
                `);

                $card.on('click', function() {
                    self.toggleService(service, $card);
                });

                $grid.append($card);
            });

            $grid.fadeIn();
        }

        toggleService(service, $card) {
            const index = this.selectedServices.findIndex(s => s.id === service.id);

            if (index > -1) {
                // Remove service
                this.selectedServices.splice(index, 1);
                $card.removeClass('selected');
                $card.find('input[type="checkbox"]').prop('checked', false);
            } else {
                // Add service
                this.selectedServices.push({
                    id: service.id,
                    name: service.name,
                    description: service.description,
                    setup_price: parseFloat(service.setup_price),
                    monthly_price: parseFloat(service.monthly_price)
                });
                $card.addClass('selected');
                $card.find('input[type="checkbox"]').prop('checked', true);
            }

            this.updatePriceSummary();
        }

        updatePriceSummary() {
            let totalSetup = 0;
            let totalMonthly = 0;

            this.selectedServices.forEach(service => {
                totalSetup += service.setup_price;
                totalMonthly += service.monthly_price;
            });

            // Update sticky summary
            $('#rsc-sticky-setup').text(this.formatPrice(totalSetup));
            $('#rsc-sticky-monthly').text(this.formatPrice(totalMonthly));

            // Update main summary
            $('#rsc-total-setup').text(this.formatPrice(totalSetup));
            $('#rsc-total-monthly').text(this.formatPrice(totalMonthly));

            // Show sticky on step 2
            if (this.currentStep === 2 && this.selectedServices.length > 0) {
                $('#rsc-sticky-summary').show();
            }
        }

        updateSummary() {
            const self = this;

            // Profile summary
            const industryName = $('.rsc-industry-card.selected h4').text();
            $('#rsc-summary-profile').html(`
                <p><strong>Branche:</strong> ${industryName}</p>
                <p><strong>Mitarbeiter:</strong> ${this.formData.company_size}</p>
                <p><strong>Standorte:</strong> ${this.formData.locations}</p>
            `);

            // Services summary
            if (this.selectedServices.length === 0) {
                $('#rsc-summary-services').html('<p>Keine Services ausgewählt</p>');
            } else {
                let html = '<ul class="rsc-summary-list">';
                this.selectedServices.forEach(service => {
                    html += `
                        <li>
                            <span class="rsc-summary-service-name">${self.escapeHtml(service.name)}</span>
                            <span class="rsc-summary-service-prices">
                                ${self.formatPrice(service.setup_price)} / ${self.formatPrice(service.monthly_price)}
                            </span>
                        </li>
                    `;
                });
                html += '</ul>';
                $('#rsc-summary-services').html(html);
            }

            this.updatePriceSummary();
        }

        submitConfiguration() {
            const self = this;
            const $submitBtn = $('#rsc-submit');

            // Disable button
            $submitBtn.prop('disabled', true).html('<div class="rsc-spinner-small"></div> Wird gesendet...');

            // Calculate totals
            let totalSetup = 0;
            let totalMonthly = 0;
            this.selectedServices.forEach(service => {
                totalSetup += service.setup_price;
                totalMonthly += service.monthly_price;
            });

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
                services: JSON.stringify(this.selectedServices),
                total_setup: totalSetup,
                total_monthly: totalMonthly
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

        showError(title, message) {
            alert(title + '\n\n' + message);
        }

        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        formatPrice(price) {
            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: rscData.currency || 'EUR'
            }).format(price);
        }

        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        if ($('.rsc-wrapper').length) {
            new ServiceConfigurator();
        }
    });

})(jQuery);
