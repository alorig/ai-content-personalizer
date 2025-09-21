<?php
defined('ABSPATH') || exit;

/*==================================================
  ## OPTIONS MANAGEMENT
  Description: WordPress options management functions for Content Engine
==================================================*/

/**
 * Get Content Engine global status
 * @return string Global status ('enabled' or 'disabled')
 */
function igny8_get_content_engine_status() {
    return get_option('igny8_content_engine_global_status', 'disabled');
}

/**
 * Update Content Engine global status
 * @param string $status Global status ('enabled' or 'disabled')
 */
function igny8_update_content_engine_status($status) {
    update_option('igny8_content_engine_global_status', sanitize_text_field($status));
}

/**
 * Get enabled post types for Content Engine
 * @return array Array of enabled post types
 */
function igny8_get_content_engine_enabled_post_types() {
    return get_option('igny8_content_engine_enabled_post_types', []);
}

/**
 * Update enabled post types for Content Engine
 * @param array $post_types Array of post types to enable
 */
function igny8_update_content_engine_enabled_post_types($post_types) {
    $enabled_types = array_map('sanitize_text_field', $post_types);
    update_option('igny8_content_engine_enabled_post_types', $enabled_types);
}

/**
 * Clear enabled post types for Content Engine
 */
function igny8_clear_content_engine_enabled_post_types() {
    update_option('igny8_content_engine_enabled_post_types', []);
}

/**
 * Get Content Engine insertion position
 * @return string Insertion position ('before', 'after', 'replace')
 */
function igny8_get_content_engine_insertion_position() {
    return get_option('igny8_content_engine_insertion_position', 'before');
}

/**
 * Update Content Engine insertion position
 * @param string $position Insertion position
 */
function igny8_update_content_engine_insertion_position($position) {
    update_option('igny8_content_engine_insertion_position', sanitize_text_field($position));
}

/**
 * Get Content Engine display mode
 * @return string Display mode ('button', 'inline', 'auto', 'always', 'logged_in', 'logged_out')
 */
function igny8_get_content_engine_display_mode() {
    return get_option('igny8_content_engine_display_mode', 'always');
}

/**
 * Update Content Engine display mode
 * @param string $mode Display mode
 */
function igny8_update_content_engine_display_mode($mode) {
    update_option('igny8_content_engine_display_mode', sanitize_text_field($mode));
}

/**
 * Get Content Engine teaser text
 * @return string Teaser text
 */
function igny8_get_content_engine_teaser_text() {
    return get_option('igny8_content_engine_teaser_text', 'Want to read this as if it was written exclusively about you?');
}

/**
 * Update Content Engine teaser text
 * @param string $text Teaser text
 */
function igny8_update_content_engine_teaser_text($text) {
    update_option('igny8_content_engine_teaser_text', sanitize_text_field($text));
}

/**
 * Get Content Engine save generated content setting
 * @return int 1 if enabled, 0 if disabled
 */
function igny8_get_content_engine_save_generated_content() {
    return get_option('igny8_content_engine_save_generated_content', 0);
}

/**
 * Update Content Engine save generated content setting
 * @param int $enabled 1 to enable, 0 to disable
 */
function igny8_update_content_engine_save_generated_content($enabled) {
    update_option('igny8_content_engine_save_generated_content', $enabled);
}

/**
 * Get Content Engine save variations setting
 * @return int 1 if enabled, 0 if disabled
 */
function igny8_get_content_engine_save_variations() {
    return get_option('igny8_content_engine_save_variations', 1);
}

/**
 * Update Content Engine save variations setting
 * @param int $enabled 1 to enable, 0 to disable
 */
function igny8_update_content_engine_save_variations($enabled) {
    update_option('igny8_content_engine_save_variations', $enabled);
}

/**
 * Get Content Engine field mode
 * @return string Field mode ('dynamic' or 'fixed')
 */
function igny8_get_content_engine_field_mode() {
    return get_option('igny8_content_engine_field_mode', 'dynamic');
}

/**
 * Update Content Engine field mode
 * @param string $mode Field mode
 */
function igny8_update_content_engine_field_mode($mode) {
    update_option('igny8_content_engine_field_mode', sanitize_text_field($mode));
}

/**
 * Get Content Engine detection prompt
 * @return string Detection prompt
 */
function igny8_get_content_engine_detection_prompt() {
    return get_option('igny8_content_engine_detection_prompt', 'Extract personalization intelligence from the content below. Identify what information about the reader would make this content more relevant and valuable.

Return JSON with fields array. Each field should have:
- "label": field name
- "type": "text" or "select"
- "examples": [2 sample values] for text fields
- "options": [4-5 predefined values] for select fields

IMPORTANT: All text fields must have meaningful examples. Never leave text fields empty or with placeholder text. Provide real, useful examples that help personalize the content.

Content: [CONTENT]');
}

/**
 * Update Content Engine detection prompt
 * @param string $prompt Detection prompt
 */
function igny8_update_content_engine_detection_prompt($prompt) {
    update_option('igny8_content_engine_detection_prompt', sanitize_textarea_field($prompt));
}

/**
 * Get Content Engine custom context
 * @return string Custom context
 */
function igny8_get_content_engine_custom_context() {
    return get_option('igny8_content_engine_custom_context', '');
}

/**
 * Update Content Engine custom context
 * @param string $context Custom context
 */
function igny8_update_content_engine_custom_context($context) {
    update_option('igny8_content_engine_custom_context', sanitize_textarea_field($context));
}

/**
 * Get Content Engine context source
 * @return string Context source
 */
function igny8_get_content_engine_context_source() {
    return get_option('igny8_content_engine_context_source', '');
}

/**
 * Update Content Engine context source
 * @param string $source Context source
 */
function igny8_update_content_engine_context_source($source) {
    update_option('igny8_content_engine_context_source', sanitize_textarea_field($source));
}

/**
 * Get Content Engine include page context setting
 * @return int 1 if enabled, 0 if disabled
 */
function igny8_get_content_engine_include_page_context() {
    return get_option('igny8_content_engine_include_page_context', 0);
}

/**
 * Update Content Engine include page context setting
 * @param int $enabled 1 to enable, 0 to disable
 */
function igny8_update_content_engine_include_page_context($enabled) {
    update_option('igny8_content_engine_include_page_context', $enabled);
}

/**
 * Get Content Engine content length
 * @return string Content length setting
 */
function igny8_get_content_engine_content_length() {
    return get_option('igny8_content_engine_content_length', '300');
}

/**
 * Update Content Engine content length
 * @param string $length Content length setting
 */
function igny8_update_content_engine_content_length($length) {
    update_option('igny8_content_engine_content_length', sanitize_text_field($length));
}

/**
 * Get Content Engine tone
 * @return string Content tone
 */
function igny8_get_content_engine_tone() {
    return get_option('igny8_content_engine_tone', '');
}

/**
 * Update Content Engine tone
 * @param string $tone Content tone
 */
function igny8_update_content_engine_tone($tone) {
    update_option('igny8_content_engine_tone', sanitize_text_field($tone));
}

/**
 * Get Content Engine style
 * @return string Content style
 */
function igny8_get_content_engine_style() {
    return get_option('igny8_content_engine_style', '');
}

/**
 * Update Content Engine style
 * @param string $style Content style
 */
function igny8_update_content_engine_style($style) {
    update_option('igny8_content_engine_style', sanitize_text_field($style));
}

/**
 * Get Content Engine prompt
 * @return string Content generation prompt
 */
function igny8_get_content_engine_prompt() {
    return get_option('igny8_content_engine_prompt', '');
}

/**
 * Update Content Engine prompt
 * @param string $prompt Content generation prompt
 */
function igny8_update_content_engine_prompt($prompt) {
    update_option('igny8_content_engine_prompt', sanitize_textarea_field($prompt));
}

/**
 * Get Content Engine fixed fields configuration
 * @return array Fixed fields configuration
 */
function igny8_get_content_engine_fixed_fields_config() {
    return get_option('igny8_content_engine_fixed_fields_config', []);
}

/**
 * Update Content Engine fixed fields configuration
 * @param array $config Fixed fields configuration
 */
function igny8_update_content_engine_fixed_fields_config($config) {
    update_option('igny8_content_engine_fixed_fields_config', $config);
}

/**
 * Get Content Engine include inputs setting
 * @return int 1 if enabled, 0 if disabled
 */
function igny8_get_content_engine_include_inputs() {
    return get_option('igny8_content_engine_include_inputs', 1);
}

/**
 * Update Content Engine include inputs setting
 * @param int $enabled 1 to enable, 0 to disable
 */
function igny8_update_content_engine_include_inputs($enabled) {
    update_option('igny8_content_engine_include_inputs', $enabled);
}

/**
 * Get Content Engine rewrite prompt
 * @return string Rewrite prompt
 */
function igny8_get_content_engine_rewrite_prompt() {
    return get_option('igny8_content_engine_rewrite_prompt', 'Rewrite the following content to be personalized for a reader with these characteristics:\n\n[INPUTS]\n\nOriginal content:\n[CONTENT]\n\nMake the content feel like it was written specifically for this person while maintaining the original message and tone.');
}

/**
 * Update Content Engine rewrite prompt
 * @param string $prompt Rewrite prompt
 */
function igny8_update_content_engine_rewrite_prompt($prompt) {
    update_option('igny8_content_engine_rewrite_prompt', sanitize_textarea_field($prompt));
}

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