<?php
defined('ABSPATH') || exit;

/*==================================================
  ## PERSONALIZE MODULE
  Description: Self-contained personalization module for content personalization
==================================================*/

// Module configuration - using existing working field names that are connected to the API
$igny8_personalize_options = [
    'global_status'         => ['default' => 'enabled', 'sanitize' => 'sanitize_text_field'],
    'enabled_post_types'    => ['default' => [], 'sanitize' => 'array_map_sanitize_text_field'],
    'insertion_position'    => ['default' => 'before', 'sanitize' => 'sanitize_text_field'],
    'display_mode'          => ['default' => 'always', 'sanitize' => 'sanitize_text_field'],
    'teaser_text'           => ['default' => 'Want to read this as if it was written exclusively about you?', 'sanitize' => 'sanitize_textarea_field'],
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
    'context_source'        => ['default' => '', 'sanitize' => 'sanitize_textarea_field'],
    'include_page_context'  => ['default' => 1, 'sanitize' => 'intval'],
    'content_length'        => ['default' => 'medium', 'sanitize' => 'sanitize_text_field'],
    'input_scope'           => ['default' => '300', 'sanitize' => 'sanitize_text_field'],
    'include_inputs'        => ['default' => 1, 'sanitize' => 'intval'],
    'rewrite_prompt'        => ['default' => 'Rewrite the following content to be personalized for a reader with these characteristics:

[INPUTS]

Original content:
[CONTENT]

Make the content feel like it was written specifically for this person while maintaining the original message and tone.', 'sanitize' => 'sanitize_textarea_field'],
];


/**
 * Automatically inject Igny8 shortcode into content
 */
function igny8_inject_shortcode_into_content($content) {
    // Only run on frontend
    if (is_admin()) {
        return $content;
    }
    
    // Check if Content Engine is enabled globally using existing working field name
    $global_status = get_option('igny8_content_engine_global_status', 'enabled');
    if ($global_status !== 'enabled') {
        return $content;
    }
    
    // Get current post type
    $post_type = get_post_type();
    if (!$post_type) {
        return $content;
    }
    
    // Check if this post type is enabled for personalization using existing working field name
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    if (!in_array($post_type, $enabled_post_types)) {
        return $content;
    }
    
    // Get insertion position using existing working field name
    $insertion_position = get_option('igny8_content_engine_insertion_position', 'before');
    
    // Get display mode using existing working field name
    $display_mode = get_option('igny8_content_engine_display_mode', 'always');
    
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
        
        <form method="post" action="options.php" class="igny8-settings-form">
            <?php
            settings_fields('igny8_settings_group');
            do_settings_sections('igny8_settings_group');
            ?>
            
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
                                <div class="igny8-toggle-switch">
                                    <input type="checkbox" name="igny8_content_engine_global_status" <?php checked(get_option('igny8_content_engine_global_status', 'enabled'), 'enabled'); ?>>
                                    <span class="igny8-toggle-slider"></span>
                                </div>
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
                                $allowed_post_types = ['post', 'page'];
                                $enabled_types = get_option('igny8_content_engine_enabled_post_types', []);
                                foreach ($allowed_post_types as $post_type_name) {
                                    $post_type = get_post_type_object($post_type_name);
                                    if ($post_type) {
                                        $checked = in_array($post_type_name, $enabled_types) ? 'checked' : '';
                                        echo '<label class="igny8-checkbox-label" style="display: block; margin: 8px 0;">
                                            <input type="checkbox" name="igny8_content_engine_enabled_post_types[]" value="' . esc_attr($post_type_name) . '" ' . $checked . '>
                                            <span class="igny8-checkbox-text">' . esc_html($post_type->label) . '</span>
                                        </label>';
                                    }
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
                            <div class="igny8-radio-group">
                                <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_insertion_position', 'before') === 'before' ? 'selected' : ''; ?>">
                                    <input type="radio" name="igny8_content_engine_insertion_position" value="before" <?php checked(get_option('igny8_content_engine_insertion_position', 'before'), 'before'); ?>>
                                    <div class="igny8-radio-content">
                                        <div class="igny8-radio-title"><?php esc_html_e('Before Content', 'igny8'); ?></div>
                                        <div class="igny8-radio-desc"><?php esc_html_e('Show personalization before the main content', 'igny8'); ?></div>
                                    </div>
                                </div>
                                <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_insertion_position', 'before') === 'after' ? 'selected' : ''; ?>">
                                    <input type="radio" name="igny8_content_engine_insertion_position" value="after" <?php checked(get_option('igny8_content_engine_insertion_position', 'before'), 'after'); ?>>
                                    <div class="igny8-radio-content">
                                        <div class="igny8-radio-title"><?php esc_html_e('After Content', 'igny8'); ?></div>
                                        <div class="igny8-radio-desc"><?php esc_html_e('Show personalization after the main content', 'igny8'); ?></div>
                                    </div>
                                </div>
                                <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_insertion_position', 'before') === 'replace' ? 'selected' : ''; ?>">
                                    <input type="radio" name="igny8_content_engine_insertion_position" value="replace" <?php checked(get_option('igny8_content_engine_insertion_position', 'before'), 'replace'); ?>>
                                    <div class="igny8-radio-content">
                                        <div class="igny8-radio-title"><?php esc_html_e('Replace Content', 'igny8'); ?></div>
                                        <div class="igny8-radio-desc"><?php esc_html_e('Replace the main content with personalization', 'igny8'); ?></div>
                                    </div>
                                </div>
                            </div>
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
                                <div class="igny8-radio-group">
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_display_mode', 'always') === 'always' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_display_mode" value="always" <?php checked(get_option('igny8_content_engine_display_mode', 'always'), 'always'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Always', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Show personalization to all visitors', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_display_mode', 'always') === 'logged_in' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_display_mode" value="logged_in" <?php checked(get_option('igny8_content_engine_display_mode', 'always'), 'logged_in'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Logged In Users Only', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Show only to authenticated users', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_display_mode', 'always') === 'logged_out' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_display_mode" value="logged_out" <?php checked(get_option('igny8_content_engine_display_mode', 'always'), 'logged_out'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Logged Out Users Only', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Show only to anonymous visitors', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                </div>
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
                                <textarea name="igny8_content_engine_teaser_text" rows="3" cols="50" placeholder="<?php esc_attr_e('Want to read this as if it was written exclusively about you?', 'igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_teaser_text', 'Want to read this as if it was written exclusively about you?')); ?></textarea>
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
                                <div class="igny8-radio-group">
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_content_length', '300') === '300' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_content_length" value="300" <?php checked(get_option('igny8_content_engine_content_length', '300'), '300'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('300 Words', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Short personalized content', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_content_length', '300') === '600' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_content_length" value="600" <?php checked(get_option('igny8_content_engine_content_length', '300'), '600'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('600 Words', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Medium personalized content', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_content_length', '300') === 'full' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_content_length" value="full" <?php checked(get_option('igny8_content_engine_content_length', '300'), 'full'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Full Content Length', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Match original content length', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="igny8-input-description"><?php esc_html_e('Length of generated personalized content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-purple" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-purple"></div>
                            <h3><?php esc_html_e('Rewrite Prompt', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Rewrite Prompt', 'igny8'); ?></label>
                                <textarea name="igny8_content_engine_rewrite_prompt" rows="8" cols="50" placeholder="<?php esc_attr_e('Enter rewrite prompt for content generation...', 'igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_rewrite_prompt', 'Rewrite the following content to be personalized for a reader with these characteristics:

[INPUTS]

Original content:
[CONTENT]

Make the content feel like it was written specifically for this person while maintaining the original message and tone.')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Prompt used to rewrite content for personalization.', 'igny8'); ?></p>
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
                                <div class="igny8-radio-group">
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_field_mode', 'auto') === 'auto' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_field_mode" value="auto" <?php checked(get_option('igny8_content_engine_field_mode', 'auto'), 'auto'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Auto Detect', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Automatically detect fields using AI', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="igny8-radio-option <?php echo get_option('igny8_content_engine_field_mode', 'auto') === 'manual' ? 'selected' : ''; ?>">
                                        <input type="radio" name="igny8_content_engine_field_mode" value="manual" <?php checked(get_option('igny8_content_engine_field_mode', 'auto'), 'manual'); ?>>
                                        <div class="igny8-radio-content">
                                            <div class="igny8-radio-title"><?php esc_html_e('Manual Configuration', 'igny8'); ?></div>
                                            <div class="igny8-radio-desc"><?php esc_html_e('Use predefined field configuration', 'igny8'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="igny8-input-description"><?php esc_html_e('How to detect personalization fields.', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Detection Prompt', 'igny8'); ?></label>
                                <textarea name="igny8_content_engine_detection_prompt" rows="8" cols="50"><?php echo esc_textarea(get_option('igny8_content_engine_detection_prompt', 'Extract personalization intelligence from the content below. Identify what information about the reader would make this content more relevant and valuable.

Return JSON with fields array. Each field should have:
- "label": field name
- "type": "text" or "select"
- "examples": [2 sample values] for text fields
- "options": [4-5 predefined values] for select fields

IMPORTANT: All text fields must have meaningful examples. Never leave text fields empty or with placeholder text. Provide real, useful examples that help personalize the content.

Content: [CONTENT]')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Prompt used to detect personalization fields from content.', 'igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual Fields Table -->
                    <div class="igny8-card igny8-card-orange" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-orange"></div>
                            <h3><?php esc_html_e('Manual Fields Configuration', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Fixed Fields', 'igny8'); ?></label>
                                <p class="igny8-input-description"><?php esc_html_e('Configure fields manually when Field Detection Mode is set to Manual.', 'igny8'); ?></p>
                                
                                <table class="wp-list-table widefat fixed striped" id="igny8-fixed-fields-table">
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Label', 'igny8'); ?></th>
                                            <th><?php esc_html_e('Type', 'igny8'); ?></th>
                                            <th><?php esc_html_e('Options/Examples', 'igny8'); ?></th>
                                            <th><?php esc_html_e('Actions', 'igny8'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="igny8-fixed-fields-tbody">
                                        <?php
                                        $fixed_fields = get_option('igny8_content_engine_fixed_fields_config', []);
                                        if (empty($fixed_fields)) {
                                            $fixed_fields = [
                                                ['label' => 'Name', 'type' => 'text', 'options' => 'John, Sarah'],
                                                ['label' => 'Location', 'type' => 'text', 'options' => 'New York, London'],
                                                ['label' => 'Age', 'type' => 'select', 'options' => '18-25,26-35,36-45,46-55,55+']
                                            ];
                                        }
                                        foreach ($fixed_fields as $index => $field) {
                                            echo '<tr>';
                                            echo '<td><input type="text" name="igny8_content_engine_fixed_fields_config[' . $index . '][label]" value="' . esc_attr($field['label'] ?? '') . '"></td>';
                                            echo '<td>';
                                            echo '<select name="igny8_content_engine_fixed_fields_config[' . $index . '][type]">';
                                            echo '<option value="text" ' . selected($field['type'] ?? 'text', 'text', false) . '>' . esc_html__('Text', 'igny8') . '</option>';
                                            echo '<option value="select" ' . selected($field['type'] ?? 'text', 'select', false) . '>' . esc_html__('Select', 'igny8') . '</option>';
                                            echo '</select>';
                                            echo '</td>';
                                            echo '<td><input type="text" name="igny8_content_engine_fixed_fields_config[' . $index . '][options]" value="' . esc_attr($field['options'] ?? '') . '" placeholder="Comma-separated values"></td>';
                                            echo '<td><button type="button" class="button igny8-remove-field">' . esc_html__('Remove', 'igny8') . '</button></td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                
                                <button type="button" class="button igny8-add-field" style="margin-top: 10px;"><?php esc_html_e('Add Field', 'igny8'); ?></button>
                            </div>
                        </div>
                    </div>
                                    </div>
                                    <div class="igny8-card-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="igny8-card igny8-card-teal" style="flex: 1; min-width: 300px;">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-blue"></div>
                            <h3><?php esc_html_e('Context Settings', 'igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('Custom Context', 'igny8'); ?></label>
                                <textarea name="igny8_content_engine_context_source" rows="4" cols="50" placeholder="<?php esc_attr_e('Additional context for personalization...', 'igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_context_source', '')); ?></textarea>
                                <p class="igny8-input-description"><?php esc_html_e('Additional context to include in personalization.', 'igny8'); ?></p>
                            </div>
                            <div class="igny8-input-group">
                                <label class="igny8-checkbox-label">
                                    <input type="checkbox" name="igny8_content_engine_include_page_context" value="1" <?php checked(get_option('igny8_content_engine_include_page_context', 1), 1); ?> />
                                    <span class="igny8-checkbox-text"><?php esc_html_e('Include Page Context', 'igny8'); ?></span>
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
                                <label class="igny8-checkbox-label">
                                    <input type="checkbox" name="igny8_content_engine_save_variations" value="1" <?php checked(get_option('igny8_content_engine_save_variations', 1), 1); ?> />
                                    <span class="igny8-checkbox-text"><?php esc_html_e('Save Content Variants', 'igny8'); ?></span>
                                </label>
                                <p class="igny8-input-description"><?php esc_html_e('Save multiple variants automatically to be served from cache in future.', 'igny8'); ?></p>
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