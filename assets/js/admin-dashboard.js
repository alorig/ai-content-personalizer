/*==================================================
  ## IGNY8 ADMIN DASHBOARD JAVASCRIPT
  Description: Tab switching and admin interface functionality
==================================================*/

(function($) {
    'use strict';

    // Initialize when DOM is ready
    $(document).ready(function() {
        Igny8AdminDashboard.init();
    });

    // Main Admin Dashboard Object
    var Igny8AdminDashboard = {
        
        /**
         * Initialize the admin dashboard
         */
        init: function() {
            this.initTabs();
            this.initNotifications();
            this.initFormEnhancements();
        },

        /**
         * Initialize tab functionality
         */
        initTabs: function() {
            var self = this;
            
            // Handle tab clicks
            $('.igny8-tab-nav a').on('click', function(e) {
                e.preventDefault();
                
                var targetTab = $(this).attr('href');
                var tabContainer = $(this).closest('.igny8-tabs');
                
                // Update active tab
                tabContainer.find('.igny8-tab-nav a').removeClass('active');
                $(this).addClass('active');
                
                // Show target content
                tabContainer.find('.igny8-tab-content').removeClass('active');
                $(targetTab).addClass('active');
                
                // Update URL hash for bookmarking
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, null, targetTab);
                }
                
                // Trigger custom event for other scripts
                $(document).trigger('igny8_tab_changed', [targetTab]);
            });
            
            // Handle browser back/forward
            $(window).on('popstate', function() {
                var hash = window.location.hash;
                if (hash) {
                    var tabLink = $('.igny8-tab-nav a[href="' + hash + '"]');
                    if (tabLink.length) {
                        tabLink.trigger('click');
                    }
                }
            });
            
            // Set initial active tab based on URL hash or first tab
            var initialHash = window.location.hash;
            if (initialHash) {
                var initialTab = $('.igny8-tab-nav a[href="' + initialHash + '"]');
                if (initialTab.length) {
                    initialTab.trigger('click');
                } else {
                    $('.igny8-tab-nav a:first').trigger('click');
                }
            } else {
                $('.igny8-tab-nav a:first').trigger('click');
            }
        },

        /**
         * Initialize notification system
         */
        initNotifications: function() {
            // Show notification function
            this.showNotification = function(message, type) {
                type = type || 'info';
                
                var notification = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
                
                // Insert after page title
                $('.wrap h1, .wrap h2').first().after(notification);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
                
                // Make dismissible
                notification.append('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>');
                notification.find('.notice-dismiss').on('click', function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                });
            };
        },

        /**
         * Initialize form enhancements
         */
        initFormEnhancements: function() {
            var self = this;
            
            // Handle form submissions with loading state
            $('.igny8-settings-form').on('submit', function() {
                var form = $(this);
                var submitBtn = form.find('input[type="submit"], button[type="submit"]');
                var originalText = submitBtn.val() || submitBtn.text();
                
                // Show loading state
                submitBtn.prop('disabled', true).val('Saving...').text('Saving...');
                
                // Re-enable after a delay (in case of errors)
                setTimeout(function() {
                    submitBtn.prop('disabled', false).val(originalText).text(originalText);
                }, 3000);
            });
            
            // Handle field changes for auto-save indicators
            $('.igny8-settings-form input, .igny8-settings-form select, .igny8-settings-form textarea').on('change', function() {
                var field = $(this);
                var form = field.closest('form');
                
                // Add visual indicator for changed fields
                if (!field.hasClass('igny8-changed')) {
                    field.addClass('igny8-changed');
                    form.addClass('igny8-has-changes');
                }
            });
            
            // Reset change indicators after successful save
            $(document).on('igny8_settings_saved', function() {
                $('.igny8-changed').removeClass('igny8-changed');
                $('.igny8-has-changes').removeClass('igny8-has-changes');
            });
        },

        /**
         * Utility function to show loading state
         */
        showLoading: function(element) {
            var $element = $(element);
            $element.addClass('igny8-loading');
            $element.prop('disabled', true);
        },

        /**
         * Utility function to hide loading state
         */
        hideLoading: function(element) {
            var $element = $(element);
            $element.removeClass('igny8-loading');
            $element.prop('disabled', false);
        },

        /**
         * Utility function to validate form fields
         */
        validateField: function(field) {
            var $field = $(field);
            var value = $field.val();
            var required = $field.prop('required');
            var pattern = $field.attr('pattern');
            
            // Remove existing error styling
            $field.removeClass('igny8-error');
            $field.siblings('.igny8-error-message').remove();
            
            // Check required fields
            if (required && !value.trim()) {
                this.showFieldError($field, 'This field is required.');
                return false;
            }
            
            // Check pattern validation
            if (pattern && value && !new RegExp(pattern).test(value)) {
                this.showFieldError($field, 'Please enter a valid value.');
                return false;
            }
            
            return true;
        },

        /**
         * Show field error
         */
        showFieldError: function(field, message) {
            var $field = $(field);
            $field.addClass('igny8-error');
            
            var errorMessage = $('<div class="igny8-error-message" style="color: #d63638; font-size: 12px; margin-top: 5px;">' + message + '</div>');
            $field.after(errorMessage);
        },

        /**
         * Clear field errors
         */
        clearFieldErrors: function() {
            $('.igny8-error').removeClass('igny8-error');
            $('.igny8-error-message').remove();
        }
    };

    // Make available globally
    window.Igny8AdminDashboard = Igny8AdminDashboard;

})(jQuery); 