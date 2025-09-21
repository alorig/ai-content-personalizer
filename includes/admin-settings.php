<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN SETTINGS REGISTRATION
  Description: Registers all configurable plugin options 
  Group: igny8_settings_group
==================================================*/

// Include the settings page
require_once plugin_dir_path(__FILE__) . 'settings-page.php';

/**
 * Register all plugin settings on admin_init hook
 * This function handles the registration of all WordPress options
 * that the plugin uses for configuration
 */
add_action('admin_init', function () {

    // == Core OpenAI settings
    register_setting('igny8_settings_group', 'igny8_api_key');
    register_setting('igny8_settings_group', 'igny8_model', [
        'sanitize_callback' => function ($raw) {
            if (!is_string($raw) || $raw === '') {
                return 'gpt-4.1';
            }
            $allowed = [
                'gpt-5',
                'gpt-4.1',
                'gpt-5-mini',
                'gpt-4.1-mini',
                'gpt-5-nano',
                'gpt-3.5-turbo',
                'gpt-4o-mini',
            ];
            return in_array($raw, $allowed, true) ? $raw : 'gpt-4.1';
        }
    ]);
    register_setting('igny8_settings_group', 'igny8_use_moderation');

    // == Global UI settings (if not FLUX-specific)
    register_setting('igny8_settings_group', 'igny8_teaser_text', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    // == Global UI: Custom CSS injection
    register_setting('igny8_settings_group', 'igny8_custom_css', [
        'sanitize_callback' => 'wp_kses_post',
    ]);

    // == Global UI: Button and content background colors
    register_setting('igny8_settings_group', 'igny8_button_color', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_bg', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    // == Personalize Module Settings - Register all content engine fields
    register_setting('igny8_settings_group', 'igny8_content_engine_global_status', [
        'sanitize_callback' => function($raw) {
            return isset($_POST['igny8_content_engine_global_status']) ? 'enabled' : 'disabled';
        }
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_enabled_post_types', [
        'sanitize_callback' => function($raw) {
            return is_array($raw) ? array_map('sanitize_text_field', $raw) : [];
        }
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_insertion_position', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_display_mode', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_teaser_text', [
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_save_generated_content', [
        'sanitize_callback' => 'intval',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_save_variations', [
        'sanitize_callback' => 'intval',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_field_mode', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_detection_prompt', [
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_context_source', [
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_include_page_context', [
        'sanitize_callback' => 'intval',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_content_length', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_rewrite_prompt', [
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    register_setting('igny8_settings_group', 'igny8_content_engine_fixed_fields_config', [
        'sanitize_callback' => function($raw) {
            if (!is_array($raw)) return [];
            $sanitized = [];
            foreach ($raw as $index => $field) {
                $sanitized[$index] = [
                    'label' => sanitize_text_field($field['label'] ?? ''),
                    'type' => sanitize_text_field($field['type'] ?? 'text'),
                    'options' => sanitize_text_field($field['options'] ?? '')
                ];
            }
            return $sanitized;
        }
    ]);
});


/*==================================================
  ## IGNY8 TOP-LEVEL ADMIN MENU
  Description: Creates top-level menu with submenus for all Igny8 modules
==================================================*/

/**
 * Add Igny8 top-level menu and submenus to WordPress admin
 * Creates a complete admin menu structure for all Igny8 modules
 */
add_action('admin_menu', function () {
    // Add top-level menu
    add_menu_page(
        'Igny8 Dashboard',  // Page title
        'Igny8',            // Menu title
        'manage_options',   // Capability required
        'igny8',            // Menu slug
        'igny8_admin_page_loader', // Callback function to render the page
        'dashicons-admin-generic', // Icon
        30                  // Position
    );

    // Add submenus
    add_submenu_page(
        'igny8',            // Parent slug
        'Dashboard',        // Page title
        'Dashboard',        // Menu title
        'manage_options',   // Capability required
        'igny8',            // Menu slug (same as parent for first submenu)
        'igny8_admin_page_loader' // Callback function
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'Rewrite & Personalization',   // Page title
        'Rewrite & Personalization',   // Menu title
        'manage_options',   // Capability required
        'igny8-content-engine', // Menu slug (keep same for compatibility)
        'igny8_content_engine_admin_page' // Callback function (keep same for compatibility)
    );

    add_submenu_page(
        'igny8',            // Parent slug
        'Settings',         // Page title
        'Settings',         // Menu title
        'manage_options',   // Capability required
        'igny8-settings',   // Menu slug
        'igny8_admin_page_loader' // Callback function
    );

}); 