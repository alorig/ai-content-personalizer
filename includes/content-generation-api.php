<?php
defined('ABSPATH') || exit;

/**
 * Global Content Generation API
 * Provides reusable functions for generating and retrieving personalized content
 */

/**
 * Generate personalized content for a post with given field inputs
 * @param int $post_id WordPress post ID
 * @param array $field_inputs User input fields (e.g., ['field1' => 'value1', 'field2' => 'value2'])
 * @param array $options Optional settings (force_regenerate, model, etc.)
 * @return array Result array with 'success', 'content', 'variation_id', 'message'
 */
function igny8_generate_content($post_id, $field_inputs = [], $options = []) {
    global $wpdb;
    
    // Default options
    $defaults = [
        'force_regenerate' => false,
        'model' => null,
        'content_length' => null,
        'include_inputs' => true,
        'save_variation' => true
    ];
    $options = array_merge($defaults, $options);
    
    // Validate post exists
    if (!get_post($post_id)) {
        return [
            'success' => false,
            'content' => '',
            'variation_id' => null,
            'message' => 'Post not found'
        ];
    }
    
    // Check if Content Engine is enabled for this post type
    $post_type = get_post_type($post_id);
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    
    if ($content_engine_status !== 'enabled' || !in_array($post_type, $enabled_post_types)) {
        return [
            'success' => false,
            'content' => '',
            'variation_id' => null,
            'message' => 'Content Engine not enabled for this post type'
        ];
    }
    
    // Always check for existing variation (unless force regenerate)
    if (!$options['force_regenerate']) {
        $existing_variation = igny8_get_cached_variation($post_id, $field_inputs);
        if ($existing_variation) {
            return [
                'success' => true,
                'content' => $existing_variation['content'],
                'variation_id' => $existing_variation['id'],
                'message' => 'Content retrieved from existing variation',
                'from_cache' => true
            ];
        }
    }
    
    // Generate new content
    $generation_result = igny8_call_gpt_for_content($post_id, $field_inputs, $options);
    
    if (!$generation_result['success']) {
        return $generation_result;
    }
    
    // Save variation if requested
    $variation_id = null;
    if ($options['save_variation']) {
        $variation_id = igny8_save_variation($post_id, $field_inputs, $generation_result['content']);
    }
    
    return [
        'success' => true,
        'content' => $generation_result['content'],
        'variation_id' => $variation_id,
        'message' => 'Content generated successfully',
        'from_cache' => false
    ];
}



/**
 * Call GPT to generate content
 * @param int $post_id Post ID
 * @param array $field_inputs User input fields
 * @param array $options Generation options
 * @return array Result array with 'success', 'content', 'message'
 */
function igny8_call_gpt_for_content($post_id, $field_inputs, $options) {
    // Get OpenAI configuration
    $api_key = get_option('igny8_api_key');
    if (empty($api_key)) {
        return [
            'success' => false,
            'content' => '',
            'message' => 'OpenAI API key not configured'
        ];
    }
    
    // Get model
    $model = $options['model'] ?: get_option('igny8_model', 'gpt-4.1_standard');
    require_once plugin_dir_path(__FILE__) . 'utils.php';
    $model = igny8_normalize_model($model);
    
    // Get content generation settings
    $content_length = $options['content_length'] ?: get_option('igny8_content_engine_content_length', 'match');
    $rewrite_prompt = get_option('igny8_content_engine_rewrite_prompt', '');
    $include_inputs = $options['include_inputs'] ?: (get_option('igny8_content_engine_include_inputs', 1) === '1');
    
    // Build content context
    $post = get_post($post_id);
    if (!$post) {
        return [
            'success' => false,
            'content' => '',
            'message' => 'Post not found'
        ];
    }
    
    // Set up post context for content building
    global $wp_query;
    $original_query = $wp_query;
    $wp_query = new WP_Query(['p' => $post_id, 'post_type' => get_post_type($post_id)]);
    $GLOBALS['wp_query'] = $wp_query;
    
    // Build content using the existing function
    $content = igny8_build_combined_content(false); // false = for content generation
    
    // Build user inputs section
    $user_input = '';
    if ($include_inputs && !empty($field_inputs)) {
        $lines = [];
        foreach ($field_inputs as $key => $val) {
            if (!is_string($val)) continue;
            $cleaned = igny8_format_field($val);
            $label = ucwords(str_replace(['_', '-'], ' ', $key));
            $lines[] = "- {$label}: {$cleaned}";
        }
        $user_input = implode("\n", $lines);
    } else {
        $user_input = "No specific user characteristics provided.";
    }
    
    // Build final prompt
    $final_prompt = str_replace(['[INPUTS]', '[CONTENT]'], [$user_input, $content], $rewrite_prompt);
    
    // Calculate max_tokens
    $max_tokens = null;
    if ($content_length === 'match') {
        $original_word_count = str_word_count(strip_tags($content));
        $max_tokens = intval($original_word_count / 0.75);
    } elseif ($content_length !== 'full') {
        $target_words = intval($content_length);
        $max_tokens = intval($target_words / 0.75);
    }
    
    // Call OpenAI
    require_once plugin_dir_path(__FILE__) . 'openai.php';
    $response = igny8_call_openai($final_prompt, $api_key, $model, $max_tokens);
    
    // Restore original query
    $wp_query = $original_query;
    $GLOBALS['wp_query'] = $original_query;
    
    if (empty($response)) {
        return [
            'success' => false,
            'content' => '',
            'message' => 'Empty response from OpenAI'
        ];
    }
    
    // Format the content
    $formatted_content = igny8_format_generated_content($response);
    
    return [
        'success' => true,
        'content' => $formatted_content,
        'message' => 'Content generated successfully'
    ];
}

/**
 * Get all variations for a post
 * @param int $post_id Post ID
 * @return array Array of variation data
 */
function igny8_get_post_variations($post_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE post_id = %d ORDER BY created_at DESC",
        $post_id
    ), ARRAY_A);
    
    foreach ($results as &$result) {
        $result['fields_json'] = json_decode($result['fields_json'], true);
    }
    
    return $results;
}

/**
 * Delete a specific variation
 * @param int $variation_id Variation ID
 * @return bool Success status
 */
function igny8_delete_variation($variation_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    return $wpdb->delete($table, ['id' => $variation_id]) !== false;
}

/**
 * Get variation by ID
 * @param int $variation_id Variation ID
 * @return array|false Variation data or false if not found
 */
function igny8_get_variation_by_id($variation_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE id = %d",
        $variation_id
    ), ARRAY_A);
    
    if ($result) {
        $result['fields_json'] = json_decode($result['fields_json'], true);
    }
    
    return $result ?: false;
}

