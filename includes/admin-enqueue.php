<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN JAVASCRIPT FUNCTIONALITY
  Description: JavaScript for admin settings page interactions
==================================================*/

/**
 * Enqueue admin JavaScript for settings page functionality
 * This function adds the JavaScript that handles dynamic form interactions
 * including field management, validation, and API testing
 */
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on our Igny8 admin pages
    if (strpos($hook, 'igny8') === false) {
        return;
    }
    
    // Enqueue the admin dashboard CSS
    wp_enqueue_style(
        'igny8-admin-dashboard-css',
        plugins_url('../assets/css/admin-dashboard.css', __FILE__),
        [],
        '2.3',
        'all'
    );
    
    // Enqueue the admin dashboard JavaScript
    wp_enqueue_script(
        'igny8-admin-dashboard-js',
        plugins_url('../assets/js/admin-dashboard.js', __FILE__),
        ['jquery'],
        '2.3',
        true
    );
    
    // Enqueue the original admin JavaScript
    wp_enqueue_script(
        'igny8-admin-js',
        plugins_url('../assets/js/igny8-admin.js', __FILE__),
        ['jquery', 'igny8-admin-dashboard-js'],
        '2.2',
        true
    );
    
    // Localize script with AJAX URL
    wp_localize_script('igny8-admin-js', 'igny8Admin', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('igny8_admin_nonce')
    ]);
});

/**
 * Output inline JavaScript for admin settings page
 * This function provides the JavaScript functionality for:
 * - Dynamic field management (add/remove fixed fields)
 * - Form validation
 * - API connection testing
 */
function igny8_admin_inline_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        //==================================================
        // ## DOM REFERENCES
        //==================================================
        const addBtn     = document.getElementById('igny8-add-row');
        const tableBody  = document.querySelector('#igny8-content-engine-fixed-fields-table tbody');
        const form       = document.querySelector('form');
        const submitBtn  = form.querySelector('input[type="submit"]');
        const fieldMode  = document.querySelector('select[name="igny8_content_engine_field_mode"]');
        const errorBox   = document.getElementById('igny8-error-box');

        //==================================================
        // ## ADD NEW FIXED FIELD ROW
        //==================================================
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const rowCount = tableBody.querySelectorAll('tr').length;
                if (rowCount >= 6) return;

                const newIndex = rowCount;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="igny8_content_engine_fixed_fields_config[${newIndex}][label]"></td>
                    <td>
                        <select name="igny8_content_engine_fixed_fields_config[${newIndex}][type]">
                            <option value="text">Text</option>
                            <option value="select">Select</option>
                            <option value="radio">Radio</option>
                        </select>
                    </td>
                    <td><input type="text" name="igny8_content_engine_fixed_fields_config[${newIndex}][options]"></td>
                    <td><button type="button" class="button igny8-remove-row">Remove</button></td>
                `;
                tableBody.appendChild(row);
            });
        }

        //==================================================
        // ## REMOVE FIXED FIELD ROW
        //==================================================
        if (tableBody) {
            tableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('igny8-remove-row')) {
                    const row = e.target.closest('tr');
                    row.remove();
                }
            });
        }

        //==================================================
        // ## VALIDATE FORM BEFORE SUBMIT (IF FIXED MODE)
        //==================================================
        if (form && fieldMode) {
            form.addEventListener('submit', function (e) {
                if (fieldMode.value === 'fixed') {
                    const labels = tableBody.querySelectorAll('input[name^="igny8_content_engine_fixed_fields_config"][name$="[label]"]');
                    const hasAtLeastOne = Array.from(labels).some(input => input.value.trim() !== '');
                    if (!hasAtLeastOne) {
                        e.preventDefault();
                        errorBox.textContent = '⚠️ Please define at least one fixed field.';
                    } else {
                        errorBox.textContent = '';
                    }
                }
            });
        }

        //==================================================
        // ## TEST API CONNECTION
        //==================================================
        const testBtn   = document.getElementById('igny8-test-api');
        const statusBox = document.getElementById('igny8-api-status');

        if (testBtn && statusBox) {
            testBtn.addEventListener('click', () => {
                statusBox.textContent = '🔄 Testing...';

                fetch(ajaxurl + '?action=igny8_test_api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=igny8_test_api'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        statusBox.textContent = '✅ API connection successful.';
                        statusBox.style.color = 'green';
                    } else {
                        statusBox.textContent = '❌ API test failed: ' + (data.message || 'Unknown error');
                        statusBox.style.color = 'red';
                    }
                })
                .catch(() => {
                    statusBox.textContent = '❌ Request error.';
                    statusBox.style.color = 'red';
                });
            });
        }

    });
    </script>
    <?php
}

/**
 * Hook the inline script to the admin footer for all Igny8 pages
 * This ensures the JavaScript is loaded after the form is rendered
 */
add_action('admin_footer', function() {
    $current_page = $_GET['page'] ?? '';
    if (strpos($current_page, 'igny8') === 0) {
        igny8_admin_inline_script();
    }
}); 