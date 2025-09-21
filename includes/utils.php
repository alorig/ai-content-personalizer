<?php
defined('ABSPATH') || exit;

/**
 * Get content from a post based on selected scope.
 *
 * @param int $post_id
 * @param string $scope 'title', '300', or 'full'
 * @return string
 */
function get_igny8_content_scope($post_id, $scope = '300') {
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && isset($term->description) && !empty($term->description)) {
            return strip_tags($term->description);
        }
    }
    
    if ($scope === 'title') {
        return "Use this blog/page title to define the fields:\n\n" . get_the_title($post_id);
    }
    
    $post = get_post($post_id);
    if (!$post instanceof WP_Post) return '';
    
    $post_type = get_post_type($post);
    if (in_array($post_type, ['product', 'post', 'page'])) {
        $content = strip_tags($post->post_content);
    } else {
        return '';
    }
    
    if ($scope === '300') {
        return "Use these 300 words to define the fields:\n\n" . wp_trim_words($content, 300, '');
    } elseif ($scope === '600') {
        return "Use these 600 words to define the fields:\n\n" . wp_trim_words($content, 600, '');
    } else {
        return "Use this whole content to define the fields:\n\n" . $content;
    }
}

/**
 * Sanitize all form inputs before passing to GPT.
 *
 * @param array $raw_data
 * @return array
 */
function igny8_sanitize_inputs($raw_data) {
    $clean = [];
    foreach ($raw_data as $key => $value) {
        $clean[sanitize_text_field($key)] = sanitize_text_field($value);
    }
    return $clean;
}

/**
 * Inject inputs and content into the prompt template.
 *
 * @param string $template
 * @param array $inputs
 * @param string $content
 * @return string
 */
function igny8_build_prompt($template, $inputs, $content) {
    $json_inputs = json_encode($inputs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return str_replace(['[INPUTS]', '[CONTENT]'], [$json_inputs, $content], $template);
}
function igny8_format_field($value) {    if (is_array($value)) {        return array_map('igny8_format_field', $value);    }    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');    $value = str_replace(["\r\n", "\r"], "\n", $value);    $value = preg_replace("/\n{2,}/", "\n", $value);    return trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));}

/**
 * Normalize stored model values (e.g., 'gpt-5_standard') into valid OpenAI model IDs (e.g., 'gpt-5').
 * Accepts legacy values without suffix and returns as-is.
 */
function igny8_normalize_model($storedModel) {
    if (!is_string($storedModel) || $storedModel === '') {
        return 'gpt-4.1';
    }
    // If new format with tier suffix, strip it
    if (strpos($storedModel, '_standard') !== false) {
        return str_replace('_standard', '', $storedModel);
    }
    if (strpos($storedModel, '_flex') !== false) {
        return str_replace('_flex', '', $storedModel);
    }
    // Already a base model (legacy)
    return $storedModel;
}

/**
 * Resolve Content Engine setting with fallback hierarchy
 * Returns module-specific value if set, else global value if set, else default
 * 
 * @param string $key Setting key (without prefix)
 * @param mixed $default Default value if neither module nor global setting exists
 * @return mixed Resolved setting value
 */
function igny8_resolve_content_engine_setting($key, $default = null) {
    // Check if Content Engine is enabled for current post type
    $post_id = get_queried_object_id();
    $post_type = get_post_type($post_id);
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    
    $use_content_engine = ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types));
    
    if ($use_content_engine) {
        // Try Content Engine-specific setting first
        $module_value = get_option('igny8_content_engine_' . $key);
        if ($module_value !== false && $module_value !== '') {
            return $module_value;
        }
    }
    
    // Fall back to global setting
    $global_value = get_option('igny8_' . $key);
    if ($global_value !== false && $global_value !== '') {
        return $global_value;
    }
    
    // Return default
    return $default;
}

/**
 * Generate correlation ID for error tracking
 * @return string Unique correlation ID
 */
function igny8_generate_correlation_id() {
    return 'igny8_' . uniqid() . '_' . time();
}

/**
 * Standardized AJAX error response
 * @param string $message Error message
 * @param string $code Error code
 * @param array $details Additional error details
 * @param string $correlation_id Correlation ID for tracking
 * @return void
 */
function igny8_send_ajax_error($message, $code = 'error', $details = [], $correlation_id = null) {
    if (!$correlation_id) {
        $correlation_id = igny8_generate_correlation_id();
    }
    
    wp_send_json_error([
        'success' => false,
        'code' => $code,
        'message' => $message,
        'details' => $details,
        'correlation_id' => $correlation_id
    ]);
}

/**
 * Log error with correlation ID when debug is enabled
 * @param string $message Error message
 * @param string $correlation_id Correlation ID
 * @param array $context Additional context
 * @return void
 */
function igny8_log_error($message, $correlation_id = null, $context = []) {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    if (!$correlation_id) {
        $correlation_id = igny8_generate_correlation_id();
    }
    
    $log_message = sprintf('[Igny8 Error] %s (Correlation ID: %s)', $message, $correlation_id);
    if (!empty($context)) {
        $log_message .= ' Context: ' . wp_json_encode($context);
    }
    
    error_log($log_message);
}

/**
 * Helper functions for CPT data queries
 */

/**
 * Get count of CPT posts by meta field value
 */
function igny8_get_cpt_count_by_meta($post_type, $meta_key, $meta_value, $compare = '=') {
    $posts = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => $meta_key,
                'value' => $meta_value,
                'compare' => $compare
            ]
        ],
        'fields' => 'ids'
    ]);
    return count($posts);
}

/**
 * Get percentage of mapped keywords
 */
function igny8_get_mapped_keywords_percentage() {
    $total_keywords = wp_count_posts('igny8_keywords')->publish ?? 0;
    if ($total_keywords === 0) return 0;
    
    $mapped_keywords = igny8_get_cpt_count_by_meta('igny8_keywords', '_igny8_cluster_relation', '', 'EXISTS');
    return round(($mapped_keywords / $total_keywords) * 100);
}

/**
 * Get cache hit rate percentage
 */
function igny8_get_cache_hit_rate() {
    global $wpdb;
    $total_variations = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}igny8_variations") ?? 0;
    if ($total_variations === 0) return 0;
    
    $cache_hits = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}igny8_variations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)") ?? 0;
    return round(($cache_hits / $total_variations) * 100);
}

/**
 * Get personalized posts count
 */
function igny8_get_personalized_posts_count() {
    global $wpdb;
    return $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->prefix}igny8_variations") ?? 0;
}