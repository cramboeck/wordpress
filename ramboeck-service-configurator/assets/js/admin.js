/**
 * Admin JavaScript for Service Configurator
 *
 * @package RamboeckIT\ServiceConfigurator
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Color picker initialization
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.color-picker').wpColorPicker();
        }

        // Confirmation dialogs
        $('form[onsubmit*="confirm"]').on('submit', function() {
            return confirm($(this).attr('onsubmit').match(/'([^']+)'/)[1]);
        });
    });

})(jQuery);
