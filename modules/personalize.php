<?php
defined('ABSPATH') || exit;

/*==================================================
  ## PERSONALIZE MODULE
  Description: Personalization module for content personalization and regeneration
==================================================*/

/**
 * Save Content Engine settings
 */
function igny8_content_engine_save_settings() {
    // Verify nonce
    if (!isset($_POST['igny8_content_engine_nonce']) || !wp_verify_nonce($_POST['igny8_content_engine_nonce'], 'igny8_content_engine_settings')) {
        wp_die('Security check failed');
    }
    
    // Save global status
    if (isset($_POST['igny8_content_engine_global_status'])) {
        igny8_update_content_engine_status(sanitize_text_field($_POST['igny8_content_engine_global_status']));
    }
    
    // Save enabled post types
    if (isset($_POST['igny8_content_engine_enabled_post_types'])) {
        $enabled_types = array_map('sanitize_text_field', $_POST['igny8_content_engine_enabled_post_types']);
        igny8_update_content_engine_enabled_post_types($enabled_types);
    } else {
        igny8_clear_content_engine_enabled_post_types();
    }
    
    // Save display settings
    if (isset($_POST['igny8_content_engine_insertion_position'])) {
        igny8_update_content_engine_insertion_position(sanitize_text_field($_POST['igny8_content_engine_insertion_position']));
    }
    
    if (isset($_POST['igny8_content_engine_display_mode'])) {
        igny8_update_content_engine_display_mode(sanitize_text_field($_POST['igny8_content_engine_display_mode']));
    }
    
    if (isset($_POST['igny8_content_engine_teaser_text'])) {
        igny8_update_content_engine_teaser_text(sanitize_text_field($_POST['igny8_content_engine_teaser_text']));
    }
    
    if (isset($_POST['igny8_content_engine_save_generated_content'])) {
        igny8_update_content_engine_save_generated_content(1);
    } else {
        igny8_update_content_engine_save_generated_content(0);
    }
    
    // Save variations setting
    if (isset($_POST['igny8_content_engine_save_variations'])) {
        igny8_update_content_engine_save_variations(1);
    } else {
        igny8_update_content_engine_save_variations(0);
    }
    
    // Save context settings
    if (isset($_POST['igny8_content_engine_field_mode'])) {
        igny8_update_content_engine_field_mode(sanitize_text_field($_POST['igny8_content_engine_field_mode']));
    }
    
    // Save variation settings
    if (isset($_POST['igny8_content_engine_detection_prompt'])) {
        igny8_update_content_engine_detection_prompt(sanitize_textarea_field($_POST['igny8_content_engine_detection_prompt']));
    }
    
    if (isset($_POST['igny8_content_engine_custom_context'])) {
        igny8_update_content_engine_custom_context(sanitize_textarea_field($_POST['igny8_content_engine_custom_context']));
    }
    
    if (isset($_POST['igny8_content_engine_include_page_context'])) {
        igny8_update_content_engine_include_page_context(1);
    } else {
        igny8_update_content_engine_include_page_context(0);
    }
    
    // Save content generation settings
    if (isset($_POST['igny8_content_engine_content_length'])) {
        igny8_update_content_engine_content_length(sanitize_text_field($_POST['igny8_content_engine_content_length']));
    }
    
    if (isset($_POST['igny8_content_engine_tone'])) {
        igny8_update_content_engine_tone(sanitize_text_field($_POST['igny8_content_engine_tone']));
    }
    
    if (isset($_POST['igny8_content_engine_style'])) {
        igny8_update_content_engine_style(sanitize_text_field($_POST['igny8_content_engine_style']));
    }
    
    // Save content generation prompt
    if (isset($_POST['igny8_content_engine_prompt'])) {
        igny8_update_content_engine_prompt(sanitize_textarea_field($_POST['igny8_content_engine_prompt']));
    }
    
    // Redirect to prevent resubmission
    $redirect_url = add_query_arg('settings-updated', 'true', admin_url('admin.php?page=' . $_GET['page']));
    wp_redirect($redirect_url);
    exit;
}

/**
 * Automatically inject Igny8 shortcode into content
 */
function igny8_inject_shortcode_into_content($content) {
    // Only run on frontend
    if (is_admin()) {
        return $content;
    }
    
    // Check if Content Engine is enabled globally
    $global_status = igny8_get_content_engine_status();
    if ($global_status !== 'enabled') {
        return $content;
    }
    
    // Get current post type
    $post_type = get_post_type();
    if (!$post_type) {
        return $content;
    }
    
    // Check if this post type is enabled for personalization
    $enabled_post_types = igny8_get_content_engine_enabled_post_types();
    if (!in_array($post_type, $enabled_post_types)) {
        return $content;
    }
    
    // Get insertion position
    $insertion_position = igny8_get_content_engine_insertion_position();
    
    // Get display mode
    $display_mode = igny8_get_content_engine_display_mode();
    
    // Check if we should show personalization based on display mode
    if ($display_mode === 'logged_in' && !is_user_logged_in()) {
        return $content;
    }
    
    if ($display_mode === 'logged_out' && is_user_logged_in()) {
        return $content;
    }
    
    // Inject shortcode based on position
    $shortcode = '[igny8]';
    
    switch ($insertion_position) {
        case 'before':
            return $shortcode . $content;
        case 'after':
            return $content . $shortcode;
        case 'replace':
            return $shortcode;
        default:
            return $shortcode . $content;
    }
}

// Hook into the_content filter
add_filter('the_content', 'igny8_inject_shortcode_into_content');

/**
 * Content Engine admin page renderer
 * Handles all Content Engine module admin interface and settings
 */
function igny8_content_engine_admin_page() {
    // Handle form submission
    $form_submitted = false;
    $nonce_valid = false;
    
    if (!empty($_POST)) {
        // Check for various submit indicators
        if (isset($_POST['submit']) || isset($_POST['save-content-engine-settings']) || isset($_POST['igny8_content_engine_nonce'])) {
            $form_submitted = true;
            
            // Verify nonce
            if (isset($_POST['igny8_content_engine_nonce']) && wp_verify_nonce($_POST['igny8_content_engine_nonce'], 'igny8_content_engine_settings')) {
                $nonce_valid = true;
                igny8_content_engine_save_settings();
            } else {
                // Show nonce error
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error is-dismissible"><p>Security check failed. Please try again.</p></div>';
                });
            }
        }
    }
    
    // Check if settings were just saved and show success message
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>Rewrite & Personalization settings saved successfully.</p></div>';
    }
    
    // Get current settings
    $enabled_post_types = igny8_get_content_engine_enabled_post_types();
    $content_engine_status = igny8_get_content_engine_status();
    
    // Include the UI renderer
    require_once plugin_dir_path(__FILE__) . '../globals/php/ui-render.php';
    igny8_render_content_engine_admin_page($enabled_post_types, $content_engine_status);
}
