<?php
// 🔒 Exit if accessed directly from outside WordPress
defined('ABSPATH') || exit;

function igny8_build_combined_content($for_field_detection = false) {
    // Check if Content Engine is enabled and use Content Engine-specific settings
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $post_id = get_queried_object_id();
    $post_type = get_post_type($post_id);
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    
    if ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
        // Use Content Engine-specific settings
        $include_context = get_option('igny8_content_engine_include_page_context', '0') === '1';
        $input_scope = get_option('igny8_content_engine_input_scope', '300');
    } else {
        // Use global settings
        $include_context = get_option('igny8_include_page_context', '0') === '1';
        $input_scope = get_option('igny8_input_scope', '300');
    }

    $final_content = '';

    // ✅ Use PageContent from form if available
    if (!empty($_POST['PageContent'])) {
        $final_content .= "[SOURCE:PageContent from form]\n\n";
        $final_content .= trim(sanitize_text_field($_POST['PageContent']));
    } else {
        // ✅ Fallback to raw post content or term description
        $queried = get_queried_object();

        if ($queried instanceof WP_Post) {
            // 🎯 Post/page/product — use post content with proper scope
            $raw_content = get_post_field('post_content', $queried->ID);
            if (!empty($raw_content)) {
                $final_content .= "[SOURCE:Post Content]\n\n";
                
                // Apply scope logic - only add dynamic messages for field detection
                if ($for_field_detection) {
                    // Add dynamic messages for field detection
                    if ($input_scope === 'title') {
                        $final_content .= "Use this blog/page title to define the fields:\n\n";
                        $final_content .= get_the_title($queried->ID);
                    } elseif ($input_scope === '300') {
                        $final_content .= "Use these 300 words to define the fields:\n\n";
                        $final_content .= wp_trim_words(strip_tags($raw_content), 300, '...');
                    } elseif ($input_scope === '600') {
                        $final_content .= "Use these 600 words to define the fields:\n\n";
                        $final_content .= wp_trim_words(strip_tags($raw_content), 600, '...');
                    } else {
                        $final_content .= "Use this whole content to define the fields:\n\n";
                        $final_content .= strip_tags($raw_content);
                    }
                } else {
                    // For content generation, just add content without dynamic messages
                    if ($input_scope === 'title') {
                        $final_content .= get_the_title($queried->ID);
                    } elseif ($input_scope === '300') {
                        $final_content .= wp_trim_words(strip_tags($raw_content), 300, '...');
                    } elseif ($input_scope === '600') {
                        $final_content .= wp_trim_words(strip_tags($raw_content), 600, '...');
                    } else {
                        $final_content .= strip_tags($raw_content);
                    }
                }
            }

        } elseif (isset($queried->description) && !empty($queried->description)) {
            // 🏷️ Archive (term) — use term description
            $final_content .= "[SOURCE:Term Description]\n\n";
            $final_content .= wp_trim_words(strip_tags($queried->description), 300, '...');
        }
    }

    return trim($final_content) ?: 'No content available.';
}


/**
 * 🛡️ Checks content for moderation violations using OpenAI's moderation API
 *
 * @param string $text     The text to check for policy violations
 * @param string $api_key  Your OpenAI secret API key
 *
 * @return array {
 *     @type bool   $flagged    Whether the content was flagged
 *     @type array  $categories List of moderation categories (e.g. hate, violence)
 *     @type string $error      If error occurs, the message is returned in this key
 * }
 */
function igny8_check_moderation($text, $api_key) {
    $res = wp_remote_post('https://api.openai.com/v1/moderations', [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode(['input' => $text]),
        'timeout' => 20,
    ]);

    if (is_wp_error($res)) {
        return ['flagged' => false, 'error' => $res->get_error_message()];
    }

    $body = json_decode(wp_remote_retrieve_body($res), true);
    return [
        'flagged'    => $body['results'][0]['flagged'] ?? false,
        'categories' => $body['results'][0]['categories'] ?? [],
    ];
}

/**
 * 🔌 Tests whether the provided OpenAI API key is valid and working
 *
 * @param string $api_key  OpenAI secret API key
 * @return true|string     Returns true on success, or error message on failure
 */
function igny8_test_connection($api_key) {
    $res = wp_remote_post('https://api.openai.com/v1/moderations', [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode(['input' => 'test']),
        'timeout' => 10,
    ]);

    if (is_wp_error($res)) {
        return $res->get_error_message();
    }

    $code = wp_remote_retrieve_response_code($res);
    return ($code >= 200 && $code < 300)
        ? true
        : 'HTTP ' . $code . ' – ' . wp_remote_retrieve_body($res);
}

// igny8_format_generated_content function moved to includes/db.php for centralized formatting

function igny8_call_openai($prompt, $api_key, $model, $max_tokens = null) {
    $body_data = [
        'model' => $model,
        'messages' => [['role' => 'user', 'content' => $prompt]],
        'temperature' => 0.7,
    ];
    
    // Add max_tokens if specified for output length control
    if ($max_tokens !== null) {
        $body_data['max_tokens'] = $max_tokens;
    }
    
    $args = [
        'body' => json_encode($body_data),
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => 60,
    ];

    $res = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

    if (is_wp_error($res)) {
        return 'Error: ' . $res->get_error_message();
    }

    $body = json_decode(wp_remote_retrieve_body($res), true);
    return $body['choices'][0]['message']['content'] ?? 'No response.';
}
