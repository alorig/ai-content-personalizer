<?php
defined('ABSPATH') || exit;

/*==================================================
  ## PERSONALIZE MODULE
  Description: Self-contained personalization module for content personalization
==================================================*/

// Module configuration
$igny8_personalize_options = [
    'status'                => ['default' => 'enabled', 'sanitize' => 'sanitize_text_field'],
    'enabled_post_types'    => ['default' => [], 'sanitize' => 'array_map_sanitize_text_field'],
    'insertion_position'    => ['default' => 'before', 'sanitize' => 'sanitize_text_field'],
    'display_mode'          => ['default' => 'always', 'sanitize' => 'sanitize_text_field'],
    'teaser_text'           => ['default' => 'Want to read this as if it was written exclusively about you?', 'sanitize' => 'sanitize_textarea_field'],
    'save_generated_content'=> ['default' => 1, 'sanitize' => 'intval'],
    'save_variations'       => ['default' => 0, 'sanitize' => 'intval'],
    'field_mode'            => ['default' => 'auto', 'sanitize' => 'sanitize_text_field'],
    'detection_prompt'      => ['default' => 'Extract personalization intelligence from the content below. Identify what information about the reader would make this content more relevant and valuable.

Return JSON with fields array. Each field should have:
- "label": field name
- "type": "text" or "select"
- "examples": [2 sample values] for text fields
- "options": [4-5 predefined values] for select fields

IMPORTANT: All text fields must have meaningful examples. Never leave text fields empty or with placeholder text. Provide real, useful examples that help personalize the content.

Content: [CONTENT]', 'sanitize' => 'sanitize_textarea_field'],
    'custom_context'        => ['default' => '', 'sanitize' => 'sanitize_textarea_field'],
    'include_page_context'  => ['default' => 1, 'sanitize' => 'intval'],
    'content_length'        => ['default' => 'medium', 'sanitize' => 'sanitize_text_field'],
    'tone'                  => ['default' => 'neutral', 'sanitize' => 'sanitize_text_field'],
    'style'                 => ['default' => '', 'sanitize' => 'sanitize_text_field'],
    'prompt'                => ['default' => '', 'sanitize' => 'sanitize_textarea_field'],
    'rewrite_prompt'        => ['default' => 'Rewrite the following content to be personalized for a reader with these characteristics:

[INPUTS]

Original content:
[CONTENT]

Make the content feel like it was written specifically for this person while maintaining the original message and tone.', 'sanitize' => 'sanitize_textarea_field'],
];

/**
 * Save Content Engine settings
 */
function igny8_content_engine_save_settings() {
    global $igny8_personalize_options;
    
    // Verify nonce
    if (!isset($_POST['igny8_content_engine_nonce']) || !wp_verify_nonce($_POST['igny8_content_engine_nonce'], 'igny8_content_engine_settings')) {
        wp_die('Security check failed');
    }
    
    // Loop through config and save each option
    foreach ($igny8_personalize_options as $key => $config) {
        $post_key = "igny8_{$key}";
        
        if (isset($_POST[$post_key])) {
            $value = $_POST[$post_key];
            
            // Apply sanitization based on config
            if (isset($config['sanitize']) && function_exists($config['sanitize'])) {
                if ($config['sanitize'] === 'array_map_sanitize_text_field') {
                    $value = array_map('sanitize_text_field', $value);
                } else {
                    $value = call_user_func($config['sanitize'], $value);
                }
            }
            
            igny8_option('personalize', $key, $value);
        } else {
            // Handle checkboxes and other fields that might not be present
            if (in_array($key, ['save_generated_content', 'save_variations', 'include_page_context'])) {
                igny8_option('personalize', $key, 0);
            }
        }
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
    $global_status = igny8_option('personalize', 'status');
    if ($global_status !== 'enabled') {
        return $content;
    }
    
    // Get current post type
    $post_type = get_post_type();
    if (!$post_type) {
        return $content;
    }
    
    // Check if this post type is enabled for personalization
    $enabled_post_types = igny8_option('personalize', 'enabled_post_types');
    if (!in_array($post_type, $enabled_post_types)) {
        return $content;
    }
    
    // Get insertion position
    $insertion_position = igny8_option('personalize', 'insertion_position');
    
    // Get display mode
    $display_mode = igny8_option('personalize', 'display_mode');
    
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
    
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Rewrite & Personalization', 'igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#global-settings" class="active"><?php esc_html_e('Global Settings', 'igny8'); ?></a></li>
            <li><a href="#display-settings"><?php esc_html_e('Display Settings', 'igny8'); ?></a></li>
            <li><a href="#content-generation"><?php esc_html_e('Content Generation', 'igny8'); ?></a></li>
            <li><a href="#advanced-settings"><?php esc_html_e('Advanced Settings', 'igny8'); ?></a></li>
        </ul>
        
        <form method="post" action="" class="igny8-settings-form">
            <?php wp_nonce_field('igny8_content_engine_settings', 'igny8_content_engine_nonce'); ?>
            
            <!-- Global Settings Tab -->
            <div id="global-settings" class="igny8-tab-content active">
                <div class="igny8-card-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="igny8-card igny8-card-blue" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-blue"></div>
                            <h3><?php esc_html_e('Module Status', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Status', 'igny8'); ?></label>
                                <select name="igny8_status">
                                    <option value="enabled" <?php selected(igny8_option('personalize', 'status'), 'enabled'); ?>><?php esc_html_e('Enabled', 'igny8'); ?></option>
                                    <option value="disabled" <?php selected(igny8_option('personalize', 'status'), 'disabled'); ?>><?php esc_html_e('Disabled', 'igny8'); ?></option>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('Enable or disable the personalization module globally.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-green" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-green"></div>
                            <h3><?php esc_html_e('Post Types', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Enabled Post Types', 'igny8'); ?></label>
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                $enabled_types = igny8_option('personalize', 'enabled_post_types');
                                foreach ($post_types as $post_type) {
                                    $checked = in_array($post_type->name, $enabled_types) ? 'checked' : '';
                                    echo '<label style="display: block; margin: 5px 0;"><input type="checkbox" name="igny8_enabled_post_types[]" value="' . esc_attr($post_type->name) . '" ' . $checked . '> ' . esc_html($post_type->label) . '</label>';
                                }
                                ?>
                                <p class="igny8-input-description"><?php esc_html_e('Select which post types should have personalization enabled.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Display Settings Tab -->
            <div id="display-settings" class="igny8-tab-content">
                <div class="igny8-card-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="igny8-card igny8-card-purple" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-purple"></div>
                            <h3><?php esc_html_e('Content Position', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Insertion Position', 'igny8'); ?></label>
                                <select name="igny8_insertion_position">
                                    <option value="before" <?php selected(igny8_option('personalize', 'insertion_position'), 'before'); ?>><?php esc_html_e('Before Content', 'igny8'); ?></option>
                                    <option value="after" <?php selected(igny8_option('personalize', 'insertion_position'), 'after'); ?>><?php esc_html_e('After Content', 'igny8'); ?></option>
                                    <option value="replace" <?php selected(igny8_option('personalize', 'insertion_position'), 'replace'); ?>><?php esc_html_e('Replace Content', 'igny8'); ?></option>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('Where to place the personalization content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-orange" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-orange"></div>
                            <h3><?php esc_html_e('Display Mode', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Display Mode', 'igny8'); ?></label>
                                <select name="igny8_display_mode">
                                    <option value="always" <?php selected(igny8_option('personalize', 'display_mode'), 'always'); ?>><?php esc_html_e('Always', 'igny8'); ?></option>
                                    <option value="logged_in" <?php selected(igny8_option('personalize', 'display_mode'), 'logged_in'); ?>><?php esc_html_e('Logged In Users Only', 'igny8'); ?></option>
                                    <option value="logged_out" <?php selected(igny8_option('personalize', 'display_mode'), 'logged_out'); ?>><?php esc_html_e('Logged Out Users Only', 'igny8'); ?></option>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('When to show personalization content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-teal" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-green"></div>
                            <h3><?php esc_html_e('Teaser Text', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Teaser Text', 'igny8'); ?></label>
                                <textarea name="igny8_teaser_text" rows="3" cols="50" placeholder="<?php esc_attr_e('Want to read this as if it was written exclusively about you?', 'igny8'); ?>"><?php echo esc_textarea(igny8_option('personalize', 'teaser_text')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Text shown to encourage users to personalize content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Generation Tab -->
            <div id="content-generation" class="igny8-tab-content">
                <div class="igny8-card-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="igny8-card igny8-card-blue" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-blue"></div>
                            <h3><?php esc_html_e('Content Length', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Content Length', 'igny8'); ?></label>
                                <select name="igny8_content_length">
                                    <option value="short" <?php selected(igny8_option('personalize', 'content_length'), 'short'); ?>><?php esc_html_e('Short (150 words)', 'igny8'); ?></option>
                                    <option value="medium" <?php selected(igny8_option('personalize', 'content_length'), 'medium'); ?>><?php esc_html_e('Medium (300 words)', 'igny8'); ?></option>
                                    <option value="long" <?php selected(igny8_option('personalize', 'content_length'), 'long'); ?>><?php esc_html_e('Long (500 words)', 'igny8'); ?></option>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('Length of generated personalized content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-green" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-green"></div>
                            <h3><?php esc_html_e('Content Style', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Tone', 'igny8'); ?></label>
                                <input type="text" name="igny8_tone" value="<?php echo esc_attr(igny8_option('personalize', 'tone')); ?>" placeholder="<?php esc_attr_e('neutral, friendly, professional', 'igny8'); ?>" />
                                <p class="igny8-input-description"><?php esc_html_e('Tone for generated content (e.g., neutral, friendly, professional).', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Style', 'igny8'); ?></label>
                                <input type="text" name="igny8_style" value="<?php echo esc_attr(igny8_option('personalize', 'style')); ?>" placeholder="<?php esc_attr_e('conversational, formal, casual', 'igny8'); ?>" />
                                <p class="igny8-input-description"><?php esc_html_e('Writing style for generated content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-purple" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-purple"></div>
                            <h3><?php esc_html_e('Custom Prompt', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Custom Prompt', 'igny8'); ?></label>
                                <textarea name="igny8_prompt" rows="5" cols="50" placeholder="<?php esc_attr_e('Enter custom prompt for content generation...', 'igny8'); ?>"><?php echo esc_textarea(igny8_option('personalize', 'prompt')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Custom prompt to override default content generation.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings Tab -->
            <div id="advanced-settings" class="igny8-tab-content">
                <div class="igny8-card-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="igny8-card igny8-card-orange" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-orange"></div>
                            <h3><?php esc_html_e('Field Detection', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Field Detection Mode', 'igny8'); ?></label>
                                <select name="igny8_field_mode">
                                    <option value="auto" <?php selected(igny8_option('personalize', 'field_mode'), 'auto'); ?>><?php esc_html_e('Auto Detect', 'igny8'); ?></option>
                                    <option value="manual" <?php selected(igny8_option('personalize', 'field_mode'), 'manual'); ?>><?php esc_html_e('Manual Configuration', 'igny8'); ?></option>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('How to detect personalization fields.', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Detection Prompt', 'igny8'); ?></label>
                                <textarea name="igny8_detection_prompt" rows="8" cols="50"><?php echo esc_textarea(igny8_option('personalize', 'detection_prompt')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Prompt used to detect personalization fields from content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-teal" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-blue"></div>
                            <h3><?php esc_html_e('Context Settings', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Custom Context', 'igny8'); ?></label>
                                <textarea name="igny8_custom_context" rows="4" cols="50" placeholder="<?php esc_attr_e('Additional context for personalization...', 'igny8'); ?>"><?php echo esc_textarea(igny8_option('personalize', 'custom_context')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Additional context to include in personalization.', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label>
                                    <input type="checkbox" name="igny8_include_page_context" value="1" <?php checked(igny8_option('personalize', 'include_page_context'), 1); ?> />
                                    <?php esc_html_e('Include Page Context', 'igny8'); ?>
                                </label>
                                <p class="igny8-input-description"><?php esc_html_e('Include page content as context for personalization.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-red" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-purple"></div>
                            <h3><?php esc_html_e('Content Storage', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label>
                                    <input type="checkbox" name="igny8_save_generated_content" value="1" <?php checked(igny8_option('personalize', 'save_generated_content'), 1); ?> />
                                    <?php esc_html_e('Save Generated Content', 'igny8'); ?>
                                </label>
                                <p class="igny8-input-description"><?php esc_html_e('Save generated content for future use.', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label>
                                    <input type="checkbox" name="igny8_save_variations" value="1" <?php checked(igny8_option('personalize', 'save_variations'), 1); ?> />
                                    <?php esc_html_e('Save Variations', 'igny8'); ?>
                                </label>
                                <p class="igny8-input-description"><?php esc_html_e('Save multiple variations of generated content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="igny8-data-table-header">
                <button type="submit" class="button igny8-add-new"><?php esc_html_e('Save Settings', 'igny8'); ?></button>
                <button type="button" class="button igny8-clear-filters"><?php esc_html_e('Reset to Defaults', 'igny8'); ?></button>
            </div>
        </form>
    </div>
    <?php
}

// Hook into the_content filter
add_filter('the_content', 'igny8_inject_shortcode_into_content');