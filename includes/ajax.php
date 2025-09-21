<?php
defined('ABSPATH') || exit;

// Register AJAX endpoints
add_action('wp_ajax_igny8_get_fields', 'igny8_ajax_get_fields');
add_action('wp_ajax_nopriv_igny8_get_fields', 'igny8_ajax_get_fields');

add_action('wp_ajax_igny8_generate_custom', 'igny8_ajax_generate_custom');
add_action('wp_ajax_nopriv_igny8_generate_custom', 'igny8_ajax_generate_custom');

function igny8_ajax_get_fields() {
    // 🔹 Step 1: Load current field mode (fixed or dynamic)
    $post_id = intval($_GET['post_id'] ?? 0);
    
    // Check if Content Engine is enabled and use Content Engine-specific settings if available
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $post_type = get_post_type($post_id);
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    
    if ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
        // Use Content Engine-specific settings
        $mode = get_option('igny8_content_engine_field_mode', get_option('igny8_field_mode', 'dynamic'));
    } else {
        // Use global settings
    $mode = get_option('igny8_field_mode', 'dynamic');
    }

    // 🔹 Step 2: Fixed mode logic (render admin-defined fields)
    if ($mode === 'fixed') {
        // 🧱 Load saved field config from WP options table
        if ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
            // Use Content Engine-specific fixed fields
            $fields = get_option('igny8_content_engine_fixed_fields_config', get_option('igny8_fixed_fields_config', []));
        } else {
            // Use global fixed fields
        $fields = get_option('igny8_fixed_fields_config', []);
        }

        // 🎯 Get requested field IDs from shortcode (form_fields="1,2")
        $form_field_ids = array_map('intval', explode(',', sanitize_text_field($_GET['form_fields'] ?? '')));

        // 🧩 Track which fields are rendered (slugs)
        $visible_slugs = [];

        // 🚫 Bail out if config is missing or labels are empty
        if (empty($fields) || count(array_filter($fields, fn($f) => !empty($f['label']))) === 0) {
            echo '<div style="color:red;">❌ Incomplete data. Please personalize before proceeding.</div>';
            wp_die();
        }

        // 🔘 Begin form rendering
        echo '<form id="igny8-form">';

        // 🔁 Loop through fixed fields and render those matched by form_fields
        foreach ($fields as $index => $field) {
            if (!empty($form_field_ids) && !in_array($index + 1, $form_field_ids)) continue;

            $label = esc_html($field['label'] ?? '');
            $name = esc_attr($label);
            $slug = sanitize_title($label); // Slug used to match against shortcode keys
            $visible_slugs[] = $slug;

            $type = $field['type'] ?? 'text';

            // ✅ Override field options if values were passed via shortcode attribute (e.g. budget="Low,High")
            $shortcode_value = $_GET[$slug] ?? '';
            if (!empty($shortcode_value)) {
                $options = array_filter(array_map('trim', explode(',', $shortcode_value)));
            } else {
                $options = array_filter(array_map('trim', explode(',', $field['options'] ?? '')));
            }

            // 🎨 Render field based on type
            if ($type === 'select') {
                echo "<label for='$name'>$label:</label><select name='$name'>";
                foreach ($options as $option) {
                    echo "<option value='" . esc_attr($option) . "'>" . esc_html($option) . "</option>";
                }
                echo "</select>";

            } elseif ($type === 'radio') {
                echo "<label>$label:</label><br>";
                foreach ($options as $option) {
                    echo "<label><input type='radio' name='$name' value='" . esc_attr($option) . "'> " . esc_html($option) . "</label> ";
                }
                

            } else {
                // ✍️ Render text inputs with examples as placeholders
                $placeholder = '';
                
                // Use options/examples if available
                if (!empty($field['options'])) {
                    $placeholder = esc_attr($field['options']);
                } elseif (!empty($field['examples'])) {
                    $placeholder = implode(', ', array_filter($field['examples']));
                }
                
                // Fallback to default placeholder if examples are empty
                if (empty($placeholder)) {
                    $placeholder = 'Enter your ' . strtolower($label);
                }
                
                echo "<label for='$name'>$label:</label>";
                echo "<input type='text' name='$name' placeholder='" . esc_attr($placeholder) . "'>";
            }
        }

        // 🔒 Step 3: Inject hidden fields for context (shortcode fields not shown in form_fields)
        foreach ($fields as $field) {
            $label = $field['label'] ?? '';
            $slug  = sanitize_title($label);

            if (!in_array($slug, $visible_slugs) && isset($_GET[$slug]) && $_GET[$slug] !== '') {
    $raw_value = $_GET[$slug];
    $resolved_value = do_shortcode($raw_value);
    
	echo "<input type='hidden' name='" . esc_attr($label) . "' value='" . esc_attr($resolved_value) . "' />";

        }
		echo '<input type="hidden" name="PageContent" id="PageContent" value="">';
		

        // 🔘 Final submit button and output placeholder
        echo '<button type="submit" class="button">Personalize</button>';
        
        // Add save button for author/admin roles when auto-save is disabled
        if (current_user_can('edit_posts') && get_option('igny8_content_engine_save_variations', 1) !== '1') {
            echo '<button type="button" class="button igny8-save-btn" onclick="igny8_save_content_manual(this)" style="margin-left: 10px; background-color: #28a745; color: white;">💾 Save Content</button>';
        }
        
        echo '</form><div id="igny8-generated-content"></div>';

        wp_die(); // ✅ Kill AJAX properly
    }

    // 🟡 If mode is 'dynamic' (handled below this block), this logic does not run

    // 🔹 DYNAMIC MODE: GPT-based field detection and rendering
    // This block runs only if the admin has selected 'dynamic' mode (instead of 'fixed')

    $post_id = intval($_GET['post_id']); // 🎯 Post ID is passed via data-post-id for context
    $api_key = get_option('igny8_api_key');
    $model = get_option('igny8_model', 'gpt-4.1_standard');
    require_once plugin_dir_path(__FILE__) . 'utils.php';
    $model = igny8_normalize_model($model);

    // Check if Content Engine is enabled and use Content Engine-specific settings if available
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $post_type = get_post_type($post_id);
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);

    if ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
        // Use Content Engine-specific settings
        $scope = get_option('igny8_content_engine_input_scope', get_option('igny8_input_scope', '300'));
        $prompt_template = get_option('igny8_content_engine_detection_prompt', '');
    } else {
        // Use global settings
        $scope = get_option('igny8_input_scope', 'title');
        $prompt_template = get_option('igny8_content_engine_detection_prompt', '');
    }

    // 🔁 STEP 1: Load content from post based on scope
    $content = get_igny8_content_scope($post_id, $scope); // returns title/content/body with dynamic message

    // Add custom context if enabled
    $include_page_context = get_option('igny8_content_engine_include_page_context', 0) === '1';
    if ($include_page_context) {
        $custom_context = get_option('igny8_content_engine_context_source', '');
        if (!empty($custom_context)) {
            $content .= "\n\nAdditional Context:\n" . do_shortcode($custom_context);
        }
    }

    $prompt = str_replace('[CONTENT]', $content, $prompt_template); // inject content into prompt

    // 🔁 STEP 2: Check for existing field structure in cache
    $cached = get_post_meta($post_id, '_igny8_fields', true);

    if (is_array($cached)) {
        $fields = $cached; // 🧠 Use cached GPT result if available
    } else {
        // ⚙️ STEP 3: Call OpenAI API for dynamic field structure
        $response = igny8_call_openai($prompt, $api_key, $model);
        $fields = json_decode($response, true);

        // 🛑 Error handling for invalid GPT output
        if (!is_array($fields)) {
            echo "<strong>❌ Invalid GPT response:</strong><br><pre>" . esc_html($response) . "</pre>";
            wp_die();
        }

        // 🔧 Post-process fields to ensure text fields have meaningful examples
        if (isset($fields['fields']) && is_array($fields['fields'])) {
            foreach ($fields['fields'] as &$field) {
                if (($field['type'] ?? 'text') === 'text') {
                    // Ensure text fields have meaningful examples
                    if (empty($field['examples']) || !is_array($field['examples'])) {
                        $field['examples'] = ['Example 1', 'Example 2'];
                } else {
                    // Filter out empty examples and ensure we have at least 2
                    $field['examples'] = array_filter($field['examples']);
                    if (count($field['examples']) < 2) {
                        $field['examples'] = array_merge($field['examples'], ['Example 1', 'Example 2']);
                    }
                }
            }
        }
    }

    // 💾 Cache GPT result for this post
    update_post_meta($post_id, '_igny8_fields', $fields);

    // 🔢 STEP 4: Extract fields from GPT result
    $fieldset = $fields['fields'] ?? [];

    // 🔘 Start rendering the dynamic form
    echo '<form id="igny8-form">';

    // 🌐 STEP 5: Auto-detect user's city using IP API (client-side JS)
    echo "<script>
        async function getCity() {
            try {
                const res = await fetch('http://ip-api.com/json');
                const data = await res.json();
                return data.city || '';
            } catch { return ''; }
        }
        document.addEventListener('DOMContentLoaded', async () => {
            const city = await getCity();
            const cityInputs = document.querySelectorAll('[name=\"City\"], [name=\"Location\"]');
            cityInputs.forEach(el => el.value = city);
        });
    </script>";

    // 🔁 STEP 6: Render each field returned by GPT
    foreach ($fieldset as $field) {
        $label = esc_html($field['label'] ?? $field['name'] ?? '');
        $field_name = esc_attr($field['name'] ?? $field['label'] ?? ''); // Use consistent field name
        $field_id = esc_attr($field['id'] ?? $field_name); // Use field ID if available

        // 📦 Handle select fields
        if (($field['type'] ?? '') === 'select') {
            echo "<label for='$field_id'>" . ucwords($label) . ":</label><select name='$field_name' id='$field_id'>";
            foreach ($field['options'] ?? [] as $option) {
                echo "<option value='" . esc_attr($option) . "'>" . esc_html(ucwords($option)) . "</option>";
            }
            echo "</select><br><br>";

        } else {
            // ✍️ Render text inputs with examples as values
            $value = '';
            
            // Use examples if available
            if (isset($field['examples']) && !empty($field['examples'])) {
                $value = implode(', ', array_filter($field['examples']));
            }
            
            // Capitalize the value
            $value = ucwords($value);
        
            echo "<label for='$field_id'>" . ucwords($label) . ":</label><input type='text' name='$field_name' id='$field_id' value='" . esc_attr($value) . "' /><br><br>";
        }
    }

    // 🔘 Final submit button and container for GPT output
    echo '<button type="submit" class="button">Personalize</button>';

    // Add save button for author/admin roles when auto-save is disabled
    if (current_user_can('edit_posts') && get_option('igny8_content_engine_save_variations', 1) !== '1') {
        echo '<button type="button" class="button igny8-save-btn" onclick="igny8_save_content_manual(this)" style="margin-left: 10px; background-color: #28a745; color: white;">💾 Save Content</button>';
    }

    echo '</form><div id="igny8-generated-content"></div>';

    // ✅ End the AJAX response cleanly
    wp_die();
}

function igny8_ajax_generate_custom() {
    
    // 🔹 Step 1: Get context for the current post
    $post_id = intval($_GET['post_id']);

    // 🔹 Step 2: Sanitize user form input
    $field_inputs = [];
    foreach ($_POST as $k => $v) {
        if ($k !== 'submit') { // Skip submit button
            $field_inputs[$k] = sanitize_text_field($v);
        }
    }
    
    $auto_save_enabled = get_option('igny8_content_engine_save_variations', 1) === '1';
    
    // Check if we have any field inputs
    if (empty($field_inputs)) {
        echo '<div style="color:red;">⚠️ No form data received. Please check the form submission.</div>';
        wp_die();
    }
    
    // Debug logging for auto-save
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Igny8 Debug] AUTO-SAVE field inputs:');
        error_log('  Post ID: ' . $post_id);
        error_log('  Field inputs: ' . wp_json_encode($field_inputs));
        error_log('  Field count: ' . count($field_inputs));
    }
    
    // 🔹 Step 3: Use the new facade to check for existing content first
    $existing_variation = igny8_get_cached_variation($post_id, $field_inputs);
    
    if ($existing_variation) {
        // Content exists in cache - return it directly
        $result = [
            'success' => true,
            'content' => $existing_variation['content'],
            'variation_id' => $existing_variation['id'],
            'message' => 'Content retrieved from cache',
            'from_cache' => true
        ];
    } else {
        // No cached content - generate new
        $result = igny8_generate_content($post_id, $field_inputs, [
            'save_variation' => $auto_save_enabled, // Only save NEW content if auto-save is enabled
            'include_inputs' => true,
            'force_regenerate' => false // Always check cache first
        ]);
    }
    
    // 🔹 Step 4: Output result with role-based header
    if ($result['success']) {
        $variation_id = $result['variation_id'] ?? null;
        $from_cache = $result['from_cache'] ?? false;
        $auto_save_enabled = get_option('igny8_content_engine_save_variations', 1) === '1';
        
        // Check if current user can see the header (Author level or higher)
        $current_user = wp_get_current_user();
        $can_see_header = current_user_can('edit_posts'); // Author level or higher
        
        echo '<div class="igny8-content-container" data-variation-id="' . esc_attr($variation_id) . '" data-from-cache="' . ($from_cache ? 'true' : 'false') . '">';
        
        if ($can_see_header) {
            echo '<div class="igny8-content-header">';
            
            // Determine status message and button visibility
            if ($from_cache) {
                echo '<span class="igny8-content-status">📋 Content from local storage</span>';
                // No save button for retrieved content
            } else {
                if ($auto_save_enabled && $variation_id) {
                    echo '<span class="igny8-content-status">✅ New content saved</span>';
                    // No save button when auto-save is enabled and content was saved
                } else {
                    echo '<span class="igny8-content-status">✨ Freshly generated</span>';
                }
            }
            
            echo '</div>';
        }
        
        echo '<div class="igny8-final-content">' . wp_kses_post($result['content']) . '</div>';
        echo '</div>';
        
    } else {
        echo '<div style="color:red;">⚠️ ' . esc_html($result['message']) . '</div>';
    }
    
    wp_die(); // Clean AJAX exit
}
// ✅ Add missing Test API endpoint
add_action('wp_ajax_igny8_test_api', 'igny8_test_api_callback');

// ✅ Add manual save endpoint
add_action('wp_ajax_igny8_save_content_manual', 'igny8_ajax_save_content_manual');
add_action('wp_ajax_nopriv_igny8_save_content_manual', 'igny8_ajax_save_content_manual');

// ✅ Add simple test endpoint to verify AJAX is working
add_action('wp_ajax_igny8_test_ajax', 'igny8_test_ajax_callback');
add_action('wp_ajax_nopriv_igny8_test_ajax', 'igny8_test_ajax_callback');


function igny8_ajax_save_content_manual() {
    $correlation_id = igny8_generate_correlation_id();
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        igny8_send_ajax_error('Insufficient permissions', 'permission_denied', [], $correlation_id);
        return;
    }
    
    // Get the content and field data
    $content = wp_kses_post($_POST['content'] ?? ''); // Use wp_kses_post to preserve HTML markup
    $post_id = intval($_POST['post_id'] ?? 0);
    $field_inputs_raw = isset($_POST['field_inputs']) ? wp_unslash($_POST['field_inputs']) : '';
    
    if (empty($content) || !$post_id || empty($field_inputs_raw)) {
        igny8_send_ajax_error('Missing content, post ID, or field inputs', 'missing_data', [
            'content_length' => strlen($content),
            'post_id' => $post_id,
            'field_inputs_length' => strlen($field_inputs_raw)
        ], $correlation_id);
        return;
    }
    
    $field_inputs = json_decode($field_inputs_raw, true);
    
    if (!is_array($field_inputs)) {
        igny8_send_ajax_error('Invalid field inputs format', 'invalid_json', [
            'raw_input' => substr($field_inputs_raw, 0, 200),
            'json_error' => json_last_error_msg()
        ], $correlation_id);
        return;
    }
    
    try {
        // Debug logging
        igny8_log_error('Manual save attempt', $correlation_id, [
            'post_id' => $post_id,
            'field_inputs' => $field_inputs,
            'content_length' => strlen($content),
            'content_preview' => substr($content, 0, 100),
            'content_has_html' => strpos($content, '<') !== false ? 'YES' : 'NO'
        ]);
        
        // Use the new facade function - handles normalization and formatting internally
        $variation_id = igny8_save_variation($post_id, $field_inputs, $content);
        
        if ($variation_id) {
            wp_send_json_success([
                'message' => 'Content saved successfully!',
                'variation_id' => $variation_id,
                'correlation_id' => $correlation_id
            ]);
        } else {
            igny8_send_ajax_error('Failed to save content', 'save_failed', [
                'post_id' => $post_id,
                'field_count' => count($field_inputs)
            ], $correlation_id);
        }
    } catch (Exception $e) {
        igny8_log_error('Manual save exception: ' . $e->getMessage(), $correlation_id, [
            'post_id' => $post_id,
            'field_count' => count($field_inputs)
        ]);
        igny8_send_ajax_error('Unexpected error occurred', 'exception', [
            'error' => $e->getMessage()
        ], $correlation_id);
    }
}

function igny8_test_ajax_callback() {
    
    wp_send_json_success([
        'message' => 'AJAX is working!',
        'post_data' => $_POST,
        'get_data' => $_GET,
        'timestamp' => current_time('mysql')
    ]);
}

function igny8_test_api_callback() {
    $api_key = get_option('igny8_api_key');

    // Use your existing utility function from openai.php
    $result = igny8_test_connection($api_key);

    if ($result === true) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => $result]);
    }
}
