<?php

defined('ABSPATH') || exit;



/**

 * Insert a row into wp_igny8_data.

 *

 * @param int $post_id

 * @param string $type e.g. 'prompt_log', 'cache', 'input_history'

 * @param array $data Associative array to store as JSON

 * @return int|false Insert ID or false on failure

 */

function igny8_db_insert($post_id, $type, $data) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';



    return $wpdb->insert($table, [

        'post_id'   => $post_id,

        'data_type' => $type,

        'data'      => wp_json_encode($data),

        'created_at' => current_time('mysql'),

        'updated_at' => current_time('mysql'),

    ]);

}



/**

 * Get records by post_id and data_type.

 *

 * @param int $post_id

 * @param string $type

 * @return array

 */

function igny8_db_get_by_type($post_id, $type) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';



    $rows = $wpdb->get_results($wpdb->prepare(

        "SELECT * FROM $table WHERE post_id = %d AND data_type = %s ORDER BY updated_at DESC",

        $post_id, $type

    ), ARRAY_A);



    foreach ($rows as &$row) {

        $row['data'] = json_decode($row['data'], true);

    }



    return $rows;

}



/**

 * Delete all Igny8 rows for a post (if needed).

 *

 * @param int $post_id

 * @return int Rows deleted

 */

function igny8_db_delete_post_data($post_id) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';

    return $wpdb->delete($table, ['post_id' => $post_id]);

}

/**
 * Variation caching functions for personalized content
 */

/**
 * Single normalization pipeline for field hashing
 * Resolves full set of expected field keys and ensures consistent normalization
 * 
 * @param int $post_id Post ID
 * @param array $raw_fields Raw field data from user input
 * @return array Array with 'normalized_fields', 'fields_json', 'fields_hash'
 */
function igny8_normalize_fields_for_hash($post_id, $raw_fields) {
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Igny8 Debug] normalize_fields_for_hash called with:');
        error_log('  Post ID: ' . $post_id);
        error_log('  Raw fields: ' . wp_json_encode($raw_fields));
        error_log('  Raw fields count: ' . count($raw_fields));
    }
    
    // Use only the submitted fields - this ensures consistency between auto-save and manual save
    $complete_fields = [];
    if (is_array($raw_fields)) {
        foreach ($raw_fields as $key => $value) {
            $complete_fields[$key] = $value;
        }
    }
    
    // Normalize field keys and values
    $normalized_fields = [];
    foreach ($complete_fields as $key => $value) {
        // Convert spaces to underscores, trim, and unify key format
        $safe_key = str_replace(' ', '_', trim($key));
        // Apply same sanitization as auto-save to ensure consistency
        $normalized_fields[$safe_key] = sanitize_text_field(trim((string)$value));
    }
    
    // Always sort keys alphabetically for consistent JSON sequence
    ksort($normalized_fields);
    
    // Generate stable JSON encoding
    $fields_json = json_encode($normalized_fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    // Generate hash from normalized fields
    $fields_hash = hash('sha256', $fields_json);
    
    // Debug logging for final result
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('  Normalized fields: ' . wp_json_encode($normalized_fields));
        error_log('  Fields JSON: ' . $fields_json);
        error_log('  Fields hash: ' . $fields_hash);
    }
    
    return [
        'normalized_fields' => $normalized_fields,
        'fields_json' => $fields_json,
        'fields_hash' => $fields_hash
    ];
}

/**
 * Get all detected fields for a post
 * @param int $post_id Post ID
 * @return array Array of field definitions
 */
function igny8_get_detected_fields($post_id) {
    // Try to get cached fields first
    $cached_fields = get_post_meta($post_id, '_igny8_fields', true);
    
    if (is_array($cached_fields) && !empty($cached_fields)) {
        return $cached_fields;
    }
    
    // If no cached fields, return empty array
    return [];
}

// Legacy function wrappers for backward compatibility
function igny8_normalize_fields($fields) {
    // This is now a simplified wrapper - should be replaced with normalize_fields_for_hash
    if (!is_array($fields)) {
        return [];
    }
    
    $normalized = [];
    foreach ($fields as $key => $value) {
        $safe_key = str_replace(' ', '_', trim($key));
        $normalized[$safe_key] = $value;
    }
    ksort($normalized);
    return $normalized;
}

function igny8_encode_fields_json($fields) {
    return json_encode($fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function igny8_generate_fields_hash($fields) {
    return hash('sha256', json_encode($fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

/**
 * Single content formatting contract
 * Normalizes headings, paragraphs, lists and appends CTA block exactly once
 * Is idempotent - can be applied multiple times without duplication
 * 
 * @param string $raw_html Raw HTML content from GPT
 * @return string Formatted HTML content with consistent structure
 */
function igny8_format_generated_content($raw_html) {
    $content = trim($raw_html);
    
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Igny8 Debug] format_generated_content called with:');
        error_log('  Content length: ' . strlen($content));
        error_log('  Content preview: ' . substr($content, 0, 200));
        error_log('  Has HTML tags: ' . (strpos($content, '<') !== false ? 'YES' : 'NO'));
    }
    
    // Define the standard CTA block
    $cta_block = '<p><strong>Start building with Igny8</strong> — your SEO systems can be live in days, not months. Every module works together to keep your growth structured, scalable, and future-proof.</p>';
    
    // Check if CTA already exists to ensure idempotency
    if (strpos($content, 'Start building with Igny8') !== false) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('  CTA already exists, returning as-is');
        }
        return $content; // Already formatted, return as-is
    }
    
    // If content already contains HTML tags, ensure proper formatting
    if (strpos($content, '<') !== false) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('  Content has HTML tags, ensuring proper formatting');
        }
        
        // Check if content already has proper HTML structure
        $has_proper_structure = (
            strpos($content, '<h2>') !== false || 
            strpos($content, '<p>') !== false || 
            strpos($content, '<ul>') !== false
        );
        
        if ($has_proper_structure) {
            // Content already has proper structure, just add CTA
            return $content . "\n" . $cta_block;
        } else {
            // Content has HTML but not proper structure, reformat it
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('  Content has HTML but not proper structure, reformatting');
            }
            // Strip HTML tags and reformat as plain text
            $plain_content = strip_tags($content);
            // Fall through to plain text formatting below
        }
    } else {
        // Content is plain text, use as-is
        $plain_content = $content;
    }
    
    // Format plain text content
    $paragraphs = preg_split('/\n\s*\n/', $plain_content);
    $formatted_paragraphs = [];
    
    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);
        if (empty($paragraph)) continue;
        
        // Check if it looks like a heading (short, no periods, or starts with numbers)
        if (strlen($paragraph) < 100 && 
            (!strpos($paragraph, '.') || preg_match('/^\d+\.?\s/', $paragraph))) {
            $formatted_paragraphs[] = '<h2>' . esc_html($paragraph) . '</h2>';
        } else {
            // Regular paragraph
            $formatted_paragraphs[] = '<p>' . esc_html($paragraph) . '</p>';
        }
    }
    
    // Join paragraphs
    $formatted_content = implode("\n", $formatted_paragraphs);
    
    // Add CTA block at the end
    $final_content = $formatted_content . "\n" . $cta_block;
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('  Final formatted content length: ' . strlen($final_content));
        error_log('  Final formatted content preview: ' . substr($final_content, 0, 200));
    }
    
    return $final_content;
}

/**
 * Variation facade - Get cached variation for post and raw fields
 * Uses normalize_fields_for_hash internally for consistent field handling
 * 
 * @param int $post_id Post ID
 * @param array $raw_fields Raw field data from user input
 * @return array|false Variation data or false if not found
 */
function igny8_get_cached_variation($post_id, $raw_fields) {
    global $wpdb;
    
    // Ensure table exists
    if (!igny8_ensure_variations_table()) {
        return false;
    }
    
    // Normalize fields to get consistent hash
    $normalization = igny8_normalize_fields_for_hash($post_id, $raw_fields);
    $fields_hash = $normalization['fields_hash'];
    
    // Debug logging for cache lookup
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Igny8 Debug] get_cached_variation lookup:');
        error_log('  Post ID: ' . $post_id);
        error_log('  Fields hash: ' . $fields_hash);
    }
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE post_id = %d AND fields_hash = %s",
        $post_id, $fields_hash
    ), ARRAY_A);
    
    if ($result) {
        $result['fields_json'] = json_decode($result['fields_json'], true);
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('  Cache HIT - found variation ID: ' . $result['id']);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('  Cache MISS - no variation found');
        }
    }
    
    return $result ?: false;
}

/**
 * Variation facade - Save variation for post and raw fields
 * Uses normalize_fields_for_hash and format_generated_content internally
 * 
 * @param int $post_id Post ID
 * @param array $raw_fields Raw field data from user input
 * @param string $raw_html Raw HTML content from GPT
 * @return int|false Insert ID or false on failure
 */
function igny8_save_variation($post_id, $raw_fields, $raw_html) {
    global $wpdb;
    
    // Ensure table exists
    if (!igny8_ensure_variations_table()) {
        return false;
    }
    
    // Normalize fields to get consistent hash and JSON
    $normalization = igny8_normalize_fields_for_hash($post_id, $raw_fields);
    $fields_hash = $normalization['fields_hash'];
    $fields_json = $normalization['fields_json'];
    
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Igny8 Debug] save_variation called with:');
        error_log('  Post ID: ' . $post_id);
        error_log('  Fields JSON: ' . $fields_json);
        error_log('  Fields hash: ' . $fields_hash);
        error_log('  Content length: ' . strlen($raw_html));
    }
    
    // Format content with consistent structure and CTA
    $formatted_content = igny8_format_generated_content($raw_html);
    
    // Debug logging for formatted content
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('  Formatted content length: ' . strlen($formatted_content));
        error_log('  Formatted content preview: ' . substr($formatted_content, 0, 200));
    }
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    $result = $wpdb->replace($table, [
        'post_id' => $post_id,
        'fields_hash' => $fields_hash,
        'fields_json' => $fields_json,
        'content' => $formatted_content,
        'created_at' => current_time('mysql')
    ]);
    
    // Return the insert ID or the existing ID if it was an update
    if ($result !== false) {
        // For REPLACE, if it was an insert, use insert_id; if it was an update, get the existing ID
        if ($wpdb->insert_id > 0) {
            return $wpdb->insert_id;
        } else {
            // It was an update, get the existing ID
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE post_id = %d AND fields_hash = %s",
                $post_id, $fields_hash
            ));
            return $existing ?: false;
        }
    }
    
    return false;
}

/**
 * Ensure the variations table exists
 * @return bool True if table exists or was created successfully
 */
function igny8_ensure_variations_table() {
    global $wpdb;
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    // Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    
    if (!$table_exists) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT UNSIGNED NOT NULL,
            fields_hash CHAR(64) NOT NULL,
            fields_json LONGTEXT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (post_id, fields_hash),
            UNIQUE KEY unique_variation (post_id, fields_hash)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Check if creation was successful
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    }
    
    return $table_exists;
}

/**
 * Delete all variations for a post
 * @param int $post_id Post ID
 * @return int Rows deleted
 */
function igny8_delete_post_variations($post_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'igny8_variations';
    
    return $wpdb->delete($table, ['post_id' => $post_id]);
}

