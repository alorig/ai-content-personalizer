<?php
defined('ABSPATH') || exit;

/*==================================================
  ## OPTIONS MANAGEMENT
  Description: WordPress options management functions for Content Engine
==================================================*/

/**
 * Get API key status
 * @return string API key status ('configured' or 'not configured')
 */
function igny8_get_api_key_status() {
    return get_option('igny8_api_key') ? 'configured' : 'not configured';
}

/**
 * Get last save time
 * @return string Last save time
 */
function igny8_get_last_save_time() {
    return get_option('igny8_last_save_time', 'Never');
}

/**
 * Get total requests count
 * @return int Total requests count
 */
function igny8_get_total_requests() {
    return get_option('igny8_total_requests', 0);
}

/**
 * Get successful requests count
 * @return int Successful requests count
 */
function igny8_get_successful_requests() {
    return get_option('igny8_successful_requests', 0);
}

/**
 * Get failed requests count
 * @return int Failed requests count
 */
function igny8_get_failed_requests() {
    return get_option('igny8_failed_requests', 0);
}

/**
 * Get cache hits count
 * @return int Cache hits count
 */
function igny8_get_cache_hits() {
    return get_option('igny8_cache_hits', 0);
}

/**
 * Get cache misses count
 * @return int Cache misses count
 */
function igny8_get_cache_misses() {
    return get_option('igny8_cache_misses', 0);
}

// ==================================================
// ADDITIONAL OPTIONS FROM OTHER FILES
// ==================================================

/**
 * Get API key - Called from: includes/settings-page.php:88, includes/ajax.php:145,465, includes/content-generation-api.php:100
 * @return string API key
 */
function igny8_get_api_key() {
    return get_option('igny8_api_key', '');
}

/**
 * Update API key - Called from: includes/settings-page.php (form processing)
 * @param string $key API key
 */
function igny8_update_api_key($key) {
    update_option('igny8_api_key', sanitize_text_field($key));
}

/**
 * Get AI model - Called from: includes/settings-page.php:104-122, includes/ajax.php:146, includes/content-generation-api.php:110
 * @return string AI model
 */
function igny8_get_model() {
    return get_option('igny8_model', 'gpt-4.1_standard');
}

/**
 * Update AI model - Called from: includes/settings-page.php (form processing)
 * @param string $model AI model
 */
function igny8_update_model($model) {
    update_option('igny8_model', sanitize_text_field($model));
}

/**
 * Get custom CSS - Called from: includes/frontend-css.php:19
 * @return string Custom CSS
 */
function igny8_get_custom_css() {
    return trim(get_option('igny8_custom_css', ''));
}

/**
 * Update custom CSS - Called from: includes/frontend-css.php (form processing)
 * @param string $css Custom CSS
 */
function igny8_update_custom_css($css) {
    update_option('igny8_custom_css', $css);
}

/**
 * Get button color - Called from: includes/frontend-css.php:20
 * @return string Button color
 */
function igny8_get_button_color() {
    return get_option('igny8_button_color', '#0073aa');
}

/**
 * Update button color - Called from: includes/frontend-css.php (form processing)
 * @param string $color Button color
 */
function igny8_update_button_color($color) {
    update_option('igny8_button_color', sanitize_text_field($color));
}

/**
 * Get content background color - Called from: includes/frontend-css.php:21
 * @return string Background color
 */
function igny8_get_content_bg() {
    return get_option('igny8_content_bg', '#f9f9f9');
}

/**
 * Update content background color - Called from: includes/frontend-css.php (form processing)
 * @param string $color Background color
 */
function igny8_update_content_bg($color) {
    update_option('igny8_content_bg', sanitize_text_field($color));
}

/**
 * Get field mode (legacy) - Called from: includes/ajax.php:22,25
 * @return string Field mode
 */
function igny8_get_field_mode() {
    return get_option('igny8_field_mode', 'dynamic');
}

/**
 * Update field mode (legacy) - Called from: includes/ajax.php (form processing)
 * @param string $mode Field mode
 */
function igny8_update_field_mode($mode) {
    update_option('igny8_field_mode', sanitize_text_field($mode));
}

/**
 * Get fixed fields config (legacy) - Called from: includes/ajax.php:33,36
 * @return array Fixed fields configuration
 */
function igny8_get_fixed_fields_config() {
    return get_option('igny8_fixed_fields_config', []);
}

/**
 * Update fixed fields config (legacy) - Called from: includes/ajax.php (form processing)
 * @param array $config Fixed fields configuration
 */
function igny8_update_fixed_fields_config($config) {
    update_option('igny8_fixed_fields_config', $config);
}

/**
 * Get input scope (legacy) - Called from: includes/ajax.php:157,161, includes/openai.php:15,19
 * @return string Input scope
 */
function igny8_get_input_scope() {
    return get_option('igny8_input_scope', '300');
}

/**
 * Update input scope (legacy) - Called from: includes/ajax.php (form processing)
 * @param string $scope Input scope
 */
function igny8_update_input_scope($scope) {
    update_option('igny8_input_scope', sanitize_text_field($scope));
}

/**
 * Get detection prompt (legacy) - Called from: includes/ajax.php:158,162
 * @return string Detection prompt
 */
function igny8_get_detection_prompt() {
    return get_option('igny8_content_engine_detection_prompt', '');
}

/**
 * Update detection prompt (legacy) - Called from: includes/ajax.php (form processing)
 * @param string $prompt Detection prompt
 */
function igny8_update_detection_prompt($prompt) {
    update_option('igny8_content_engine_detection_prompt', sanitize_textarea_field($prompt));
}

/**
 * Get include page context (legacy) - Called from: includes/openai.php:14,18
 * @return string Include page context setting
 */
function igny8_get_include_page_context() {
    return get_option('igny8_include_page_context', '0');
}

/**
 * Update include page context (legacy) - Called from: includes/openai.php (form processing)
 * @param string $enabled Include page context setting
 */
function igny8_update_include_page_context($enabled) {
    update_option('igny8_include_page_context', sanitize_text_field($enabled));
}

/**
 * Get teaser text (legacy) - Called from: includes/shortcode.php:60,64
 * @return string Teaser text
 */
function igny8_get_teaser_text() {
    return get_option('igny8_teaser_text', 'Want to read this as if it was written exclusively about you?');
}

/**
 * Update teaser text (legacy) - Called from: includes/shortcode.php (form processing)
 * @param string $text Teaser text
 */
function igny8_update_teaser_text($text) {
    update_option('igny8_teaser_text', sanitize_text_field($text));
}

/**
 * Get context source (legacy) - Called from: includes/shortcode.php:160,162
 * @return string Context source
 */
function igny8_get_context_source() {
    return get_option('igny8_context_source', '');
}

/**
 * Update context source (legacy) - Called from: includes/shortcode.php (form processing)
 * @param string $source Context source
 */
function igny8_update_context_source($source) {
    update_option('igny8_context_source', sanitize_textarea_field($source));
}

/**
 * Get content engine input scope - Called from: includes/ajax.php:157, includes/openai.php:15
 * @return string Content engine input scope
 */
function igny8_get_content_engine_input_scope() {
    return get_option('igny8_content_engine_input_scope', '300');
}

/**
 * Update content engine input scope - Called from: includes/ajax.php (form processing)
 * @param string $scope Content engine input scope
 */
function igny8_update_content_engine_input_scope($scope) {
    update_option('igny8_content_engine_input_scope', sanitize_text_field($scope));
}

/**
 * Get generic option with fallback - Called from: includes/utils.php:108,115
 * @param string $key Option key
 * @param mixed $default Default value
 * @return mixed Option value
 */
function igny8_get_generic_option($key, $default = '') {
    return get_option($key, $default);
}

/**
 * Update generic option - Called from: includes/utils.php (form processing)
 * @param string $key Option key
 * @param mixed $value Option value
 */
function igny8_update_generic_option($key, $value) {
    update_option($key, $value);
}