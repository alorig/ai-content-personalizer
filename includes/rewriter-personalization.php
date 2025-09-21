<?php
defined('ABSPATH') || exit;

/*==================================================
  ## REWRITER & PERSONALIZATION ADMIN MODULE
  Description: Rewriter & Personalization admin interface for content personalization and regeneration
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
        update_option('igny8_content_engine_global_status', sanitize_text_field($_POST['igny8_content_engine_global_status']));
    }
    
    // Save enabled post types
    if (isset($_POST['igny8_content_engine_enabled_post_types'])) {
        $enabled_types = array_map('sanitize_text_field', $_POST['igny8_content_engine_enabled_post_types']);
        update_option('igny8_content_engine_enabled_post_types', $enabled_types);
    } else {
        update_option('igny8_content_engine_enabled_post_types', []);
    }
    
    // Save display settings
    if (isset($_POST['igny8_content_engine_insertion_position'])) {
        update_option('igny8_content_engine_insertion_position', sanitize_text_field($_POST['igny8_content_engine_insertion_position']));
    }
    
    if (isset($_POST['igny8_content_engine_display_mode'])) {
        update_option('igny8_content_engine_display_mode', sanitize_text_field($_POST['igny8_content_engine_display_mode']));
    }
    
    if (isset($_POST['igny8_content_engine_teaser_text'])) {
        update_option('igny8_content_engine_teaser_text', sanitize_text_field($_POST['igny8_content_engine_teaser_text']));
    }
    
    if (isset($_POST['igny8_content_engine_save_generated_content'])) {
        update_option('igny8_content_engine_save_generated_content', 1);
    } else {
        update_option('igny8_content_engine_save_generated_content', 0);
    }
    
    // Save variations setting
    if (isset($_POST['igny8_content_engine_save_variations'])) {
        update_option('igny8_content_engine_save_variations', 1);
    } else {
        update_option('igny8_content_engine_save_variations', 0);
    }
    
    // Save context settings
    if (isset($_POST['igny8_content_engine_field_mode'])) {
        update_option('igny8_content_engine_field_mode', sanitize_text_field($_POST['igny8_content_engine_field_mode']));
    }
    
    // Save variation settings
    if (isset($_POST['igny8_content_engine_detection_prompt'])) {
        update_option('igny8_content_engine_detection_prompt', sanitize_textarea_field($_POST['igny8_content_engine_detection_prompt']));
    }
    
    if (isset($_POST['igny8_content_engine_custom_context'])) {
        update_option('igny8_content_engine_custom_context', sanitize_textarea_field($_POST['igny8_content_engine_custom_context']));
    }
    
    if (isset($_POST['igny8_content_engine_include_page_context'])) {
        update_option('igny8_content_engine_include_page_context', 1);
    } else {
        update_option('igny8_content_engine_include_page_context', 0);
    }
    
    // Save content generation settings
    if (isset($_POST['igny8_content_engine_content_length'])) {
        update_option('igny8_content_engine_content_length', sanitize_text_field($_POST['igny8_content_engine_content_length']));
    }
    
    if (isset($_POST['igny8_content_engine_tone'])) {
        update_option('igny8_content_engine_tone', sanitize_text_field($_POST['igny8_content_engine_tone']));
    }
    
    if (isset($_POST['igny8_content_engine_style'])) {
        update_option('igny8_content_engine_style', sanitize_text_field($_POST['igny8_content_engine_style']));
    }
    
    // Save content generation prompt
    if (isset($_POST['igny8_content_engine_prompt'])) {
        update_option('igny8_content_engine_prompt', sanitize_textarea_field($_POST['igny8_content_engine_prompt']));
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
    $global_status = get_option('igny8_content_engine_global_status', 'disabled');
    if ($global_status !== 'enabled') {
        return $content;
    }
    
    // Get current post type
    $post_type = get_post_type();
    if (!$post_type) {
        return $content;
    }
    
    // Check if this post type is enabled for personalization
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    if (!in_array($post_type, $enabled_post_types)) {
        return $content;
    }
    
    // Get insertion position
    $insertion_position = get_option('igny8_content_engine_insertion_position', 'before');
    
    // Get display mode
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
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Rewrite & Personalization','igny8'); ?></h1>
        
        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true'): ?>
            <div class="igny8-notice igny8-notice-success">
                <div class="igny8-notice-icon">✓</div>
                <div class="igny8-notice-content">
                    <h4><?php esc_html_e('Settings Saved Successfully!','igny8'); ?></h4>
                    <p><?php esc_html_e('Your personalization settings have been updated and are now active.','igny8'); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <ul class="igny8-tab-nav">
            <li><a href="#content-engine-overview" class="active">Overview</a></li>
            <li><a href="#content-engine-display">Display Settings</a></li>
            <li><a href="#content-engine-context">Context & Field Settings</a></li>
            <li><a href="#content-engine-variation">Variation Settings</a></li>
            <li><a href="#content-engine-regeneration">Content Regeneration</a></li>
            <li><a href="#content-engine-debug">Debug & Insights</a></li>
        </ul>
        
        <form method="post" action="" class="igny8-settings-form">
            <?php wp_nonce_field('igny8_content_engine_settings', 'igny8_content_engine_nonce'); ?>
            <input type="hidden" name="save-content-engine-settings" value="1">
            
            <!-- Overview Tab -->
            <div id="content-engine-overview" class="igny8-tab-content active">
                <!-- Metric Cards -->
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($content_engine_status === 'enabled' ? 'Active' : 'Inactive'); ?></div>
                        <div class="igny8-metric-label">Status</div>
                        <div class="igny8-metric-change positive">+2%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html(count($enabled_post_types)); ?></div>
                        <div class="igny8-metric-label">Enabled Post Types</div>
                        <div class="igny8-metric-change positive">+2%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">1,247</div>
                        <div class="igny8-metric-label">Personalized Posts</div>
                        <div class="igny8-metric-change positive">+18%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">89%</div>
                        <div class="igny8-metric-label">Cache Hit Rate</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                </div>
                
                <!-- Global Settings -->
                <div class="igny8-global-settings-section">
                    <div class="igny8-section-header">
                        <h3><?php esc_html_e('Personalization Control Center','igny8'); ?></h3>
                        <p><?php esc_html_e('Master controls for content personalization across your entire website. Enable or disable personalization globally and for specific content types.','igny8'); ?></p>
                    </div>
                    
                    <div class="igny8-global-settings-grid">
                        <!-- Global Content Engine Status Card -->
                        <div class="igny8-global-card <?php echo $content_engine_status === 'enabled' ? 'igny8-card-enabled' : 'igny8-card-disabled'; ?>">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon"><?php echo $content_engine_status === 'enabled' ? '✓' : '○'; ?></div>
                                <h4><?php esc_html_e('Global Content Engine Status','igny8'); ?></h4>
                                <span class="igny8-card-status"><?php echo $content_engine_status === 'enabled' ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                            <div class="igny8-card-content">
                                <p><?php esc_html_e('Master switch for personalization across all content types. When enabled, the [igny8] shortcode will be automatically injected into all enabled content.','igny8'); ?></p>
                                <div class="igny8-card-toggle">
                                    <label class="igny8-toggle-switch">
                                        <input type="checkbox" 
                                               name="igny8_content_engine_global_status_toggle" 
                                               value="1"
                                               <?php checked($content_engine_status === 'enabled'); ?>>
                                        <span class="igny8-toggle-slider"></span>
                                    </label>
                                    <input type="hidden" name="igny8_content_engine_global_status" value="<?php echo esc_attr($content_engine_status); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Post Type Cards (Posts and Pages only for 3-column layout) -->
                        <?php
                        // Get specific post types for the 3-column layout
                        $main_post_types = ['post', 'page'];
                        
                        foreach ($main_post_types as $post_type_name) {
                            $post_type_obj = get_post_type_object($post_type_name);
                            if (!$post_type_obj) continue;
                            
                            $post_type_label = $post_type_obj->label;
                            $is_enabled = in_array($post_type_name, $enabled_post_types);
                            $status_class = $is_enabled ? 'igny8-card-enabled' : 'igny8-card-disabled';
                            $status_text = $is_enabled ? 'Enabled' : 'Disabled';
                            $status_icon = $is_enabled ? '✓' : '○';
                            ?>
                            <div class="igny8-global-card <?php echo esc_attr($status_class); ?>">
                                <div class="igny8-card-header">
                                    <div class="igny8-card-icon"><?php echo esc_html($status_icon); ?></div>
                                    <h4><?php echo esc_html($post_type_label); ?></h4>
                                    <span class="igny8-card-status"><?php echo esc_html($status_text); ?></span>
                                </div>
                                <div class="igny8-card-content">
                                    <p><?php echo esc_html(sprintf('When enabled, the [igny8] shortcode will be automatically injected before %s content.', strtolower($post_type_label))); ?></p>
                                    <div class="igny8-card-toggle">
                                        <label class="igny8-toggle-switch">
                                            <input type="checkbox" 
                                                   name="igny8_content_engine_enabled_post_types[]" 
                                                   value="<?php echo esc_attr($post_type_name); ?>"
                                                   <?php checked($is_enabled); ?>>
                                            <span class="igny8-toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    
                        
                        <!-- Save Button -->
                        <div class="igny8-save-section">
                            <div class="igny8-save-container">
                                <button type="submit" class="igny8-btn igny8-btn-success igny8-btn-large">
                                    <span class="igny8-btn-icon">✓</span>
                                    <?php esc_html_e('Save Settings','igny8'); ?>
                                </button>
                                <p class="igny8-save-description"><?php esc_html_e('All settings will be saved and applied immediately.','igny8'); ?></p>
                            </div>
                        </div>
                        
                        <div class="igny8-placeholder">
                            <h4>Usage Snapshot</h4>
                            <p>This section will display usage statistics and performance metrics for the Content Engine.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Display Settings Tab -->
                <div id="content-engine-display" class="igny8-tab-content">
                    <div class="igny8-card-grid">
                        <!-- Insertion Position Card -->
                        <div class="igny8-card igny8-card-blue">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon igny8-icon-blue"></div>
                                <h3><?php esc_html_e('Insertion Position','igny8'); ?></h3>
                            </div>
                            <div class="igny8-card-content">
                                <div class="igny8-radio-group">
                                    <?php
                                    $position = get_option('igny8_content_engine_insertion_position', 'before');
                                    $positions = [
                                        'before' => ['label' => 'Before Content', 'desc' => 'Insert before main content'],
                                        'after' => ['label' => 'After Content', 'desc' => 'Insert after main content'],
                                        'replace' => ['label' => 'Replace Content', 'desc' => 'Replace entire content (experimental)']
                                    ];
                                    foreach ($positions as $value => $data) {
                                        $is_selected = ($position === $value);
                                        ?>
                                        <label class="igny8-radio-option <?php echo $is_selected ? 'selected' : ''; ?>">
                                            <input type="radio" name="igny8_content_engine_insertion_position" value="<?php echo esc_attr($value); ?>" <?php checked($position, $value); ?>>
                                            <div class="igny8-radio-content">
                                                <div class="igny8-radio-title"><?php echo esc_html($data['label']); ?></div>
                                                <div class="igny8-radio-desc"><?php echo esc_html($data['desc']); ?></div>
                                            </div>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Display Mode Card -->
                        <div class="igny8-card igny8-card-green">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon igny8-icon-green"></div>
                                <h3><?php esc_html_e('Display Mode','igny8'); ?></h3>
                            </div>
                            <div class="igny8-card-content">
                                <div class="igny8-radio-group">
                                    <?php
                                    $mode = get_option('igny8_content_engine_display_mode', 'button');
                                    $modes = [
                                        'button' => ['label' => 'Personalization Button', 'desc' => 'Show button for user interaction'],
                                        'inline' => ['label' => 'Inline Form', 'desc' => 'Show form directly in content'],
                                        'auto' => ['label' => 'Auto-Personalize', 'desc' => 'Automatically personalize content']
                                    ];
                                    foreach ($modes as $value => $data) {
                                        $is_selected = ($mode === $value);
                                        ?>
                                        <label class="igny8-radio-option <?php echo $is_selected ? 'selected' : ''; ?>">
                                            <input type="radio" name="igny8_content_engine_display_mode" value="<?php echo esc_attr($value); ?>" <?php checked($mode, $value); ?>>
                                            <div class="igny8-radio-content">
                                                <div class="igny8-radio-title"><?php echo esc_html($data['label']); ?></div>
                                                <div class="igny8-radio-desc"><?php echo esc_html($data['desc']); ?></div>
                                            </div>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Teaser Text Card -->
                        <div class="igny8-card igny8-card-purple">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon igny8-icon-purple"></div>
                                <h3><?php esc_html_e('Teaser Text','igny8'); ?></h3>
                            </div>
                            <div class="igny8-card-content">
                                <p><?php esc_html_e('Text displayed above the personalization button.','igny8'); ?></p>
                                <div class="igny8-input-group">
                                    <label><?php esc_html_e('Teaser Text','igny8'); ?></label>
                                    <input type="text" name="igny8_content_engine_teaser_text" value="<?php echo esc_attr(get_option('igny8_content_engine_teaser_text', 'Want to read this as if it was written exclusively about you?')); ?>" placeholder="<?php esc_attr_e('Enter teaser text...','igny8'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Save Generated Content Card -->
                        <div class="igny8-global-card <?php echo get_option('igny8_content_engine_save_variations', 1) ? 'igny8-card-enabled' : 'igny8-card-disabled'; ?>">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon igny8-icon-orange"></div>
                                <h4><?php esc_html_e('Save Generated Content','igny8'); ?></h4>
                                <span class="igny8-card-status"><?php echo get_option('igny8_content_engine_save_variations', 1) ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                            <div class="igny8-card-content">
                                <p><?php esc_html_e('When enabled, generated content is saved and reused for identical field combinations.','igny8'); ?></p>
                                <div class="igny8-card-toggle">
                                    <label class="igny8-toggle-switch">
                                        <input type="checkbox" name="igny8_content_engine_save_variations" value="1" <?php checked(get_option('igny8_content_engine_save_variations', 1), 1); ?>>
                                        <span class="igny8-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="igny8-save-section">
                        <div class="igny8-save-container">
                            <button type="submit" class="igny8-btn igny8-btn-success igny8-btn-large">
                                <span class="igny8-btn-icon">✓</span>
                                <?php esc_html_e('Save Settings','igny8'); ?>
                            </button>
                            <p class="igny8-save-description"><?php esc_html_e('All settings will be saved and applied immediately.','igny8'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Context & Field Settings Tab -->
                <div id="content-engine-context" class="igny8-tab-content">
                    <!-- Field Mode Section -->
                    <div class="igny8-field-mode-section">
                        <div class="igny8-field-mode-header">
                            <div class="igny8-field-mode-content">
                                <h3><?php esc_html_e('Field Mode','igny8'); ?></h3>
                                <p><?php esc_html_e('Choose how personalization fields should be generated.','igny8'); ?></p>
                            </div>
                            <div class="igny8-field-mode-control">
                                <label class="igny8-toggle-switch">
                                    <input type="checkbox" name="igny8_content_engine_field_mode" value="dynamic" <?php checked('dynamic', get_option('igny8_content_engine_field_mode', 'dynamic')); ?>>
                                    <span class="igny8-toggle-slider"></span>
                                </label>
                                <span class="igny8-toggle-label"><?php echo get_option('igny8_content_engine_field_mode', 'dynamic') === 'dynamic' ? 'Auto Detect (GPT)' : 'Fixed Fields'; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card-grid">

                        <!-- Fixed Fields Configuration Card -->
                        <div class="igny8-card igny8-card-full">
                            <div class="igny8-card-header">
                                <h3><?php esc_html_e('Fixed Fields Configuration','igny8'); ?></h3>
                                <p><?php esc_html_e('Configure fixed fields for personalization when not using auto-detection.','igny8'); ?></p>
                            </div>
                            <div class="igny8-card-content">
                                <div class="igny8-fixed-fields-container">
                                    <?php
                                    $fixed_fields = get_option('igny8_content_engine_fixed_fields_config', []);
                                    if (empty($fixed_fields)) {
                                        $fixed_fields = [['label' => '', 'type' => 'text', 'options' => '']];
                                    }
                                    foreach ($fixed_fields as $index => $field) {
                                        ?>
                                        <div class="igny8-field-row">
                                            <div class="igny8-field-group">
                                                <label><?php esc_html_e('Label','igny8'); ?></label>
                                                <input type="text" name="igny8_content_engine_fixed_fields_config[<?php echo $index; ?>][label]" value="<?php echo esc_attr($field['label'] ?? ''); ?>" placeholder="<?php esc_attr_e('Field label...','igny8'); ?>">
                                            </div>
                                            <div class="igny8-field-group">
                                                <label><?php esc_html_e('Type','igny8'); ?></label>
                                                <select name="igny8_content_engine_fixed_fields_config[<?php echo $index; ?>][type]">
                                                    <option value="text" <?php selected('text', $field['type'] ?? 'text'); ?>><?php esc_html_e('Text','igny8'); ?></option>
                                                    <option value="select" <?php selected('select', $field['type'] ?? ''); ?>><?php esc_html_e('Select','igny8'); ?></option>
                                                    <option value="radio" <?php selected('radio', $field['type'] ?? ''); ?>><?php esc_html_e('Radio','igny8'); ?></option>
                                                </select>
                                            </div>
                                            <div class="igny8-field-group">
                                                <label><?php esc_html_e('Options','igny8'); ?></label>
                                                <input type="text" name="igny8_content_engine_fixed_fields_config[<?php echo $index; ?>][options]" value="<?php echo esc_attr($field['options'] ?? ''); ?>" placeholder="<?php esc_attr_e('Comma-separated options...','igny8'); ?>">
                                            </div>
                                            <div class="igny8-field-actions">
                                                <button type="button" class="igny8-btn igny8-btn-danger igny8-remove-row"><?php esc_html_e('Remove','igny8'); ?></button>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="igny8-field-footer">
                                    <button type="button" class="igny8-btn igny8-btn-primary" id="igny8-add-row"><?php esc_html_e('Add Field','igny8'); ?></button>
                                    <p class="description"><?php esc_html_e('Define fixed fields for personalization forms.','igny8'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="igny8-save-section">
                        <div class="igny8-save-container">
                            <button type="submit" class="igny8-btn igny8-btn-success igny8-btn-large">
                                <span class="igny8-btn-icon">✓</span>
                                <?php esc_html_e('Save Settings','igny8'); ?>
                            </button>
                            <p class="igny8-save-description"><?php esc_html_e('All settings will be saved and applied immediately.','igny8'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Variation Settings Tab -->
                <div id="content-engine-variation" class="igny8-tab-content">
                    <div class="igny8-card-grid">
                        <!-- Detection Prompt Card -->
                        <div class="igny8-card igny8-card-full">
                            <div class="igny8-card-header">
                                <h3><?php esc_html_e('Detection Prompt','igny8'); ?></h3>
                                <p><?php esc_html_e('Prompt template for AI field detection.','igny8'); ?></p>
                            </div>
                            <div class="igny8-card-content">
                                <div class="igny8-filter-group">
                                    <label><?php esc_html_e('Detection Prompt','igny8'); ?></label>
                                    <textarea name="igny8_content_engine_detection_prompt" rows="8" placeholder="<?php esc_attr_e('Enter detection prompt...','igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_detection_prompt', 'Extract personalization intelligence from the content below. Identify what information about the reader would make this content more relevant and valuable.

Return JSON with fields array. Each field should have:
- "label": field name
- "type": "text" or "select"
- "examples": [2 sample values] for text fields
- "options": [4-5 predefined values] for select fields

IMPORTANT: All text fields must have meaningful examples. Never leave text fields empty or with placeholder text. Provide real, useful examples that help personalize the content.

Content: [CONTENT]')); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Context Card -->
                        <div class="igny8-card">
                            <div class="igny8-card-header">
                                <h3><?php esc_html_e('Custom Context','igny8'); ?></h3>
                                <p><?php esc_html_e('Additional text or shortcode to append to the detection prompt.','igny8'); ?></p>
                            </div>
                            <div class="igny8-card-content">
                                <div class="igny8-filter-group">
                                    <label><?php esc_html_e('Custom Context','igny8'); ?></label>
                                    <textarea name="igny8_content_engine_context_source" rows="4" placeholder="<?php esc_attr_e('Enter custom context...','igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_context_source', '')); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Include Page Context Card -->
                        <div class="igny8-global-card <?php echo get_option('igny8_content_engine_include_page_context', 0) ? 'igny8-card-enabled' : 'igny8-card-disabled'; ?>">
                            <div class="igny8-card-header">
                                <div class="igny8-card-icon"><?php echo get_option('igny8_content_engine_include_page_context', 0) ? '✓' : '○'; ?></div>
                                <h4><?php esc_html_e('Include Page Context','igny8'); ?></h4>
                                <span class="igny8-card-status"><?php echo get_option('igny8_content_engine_include_page_context', 0) ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                            <div class="igny8-card-content">
                                <p><?php esc_html_e('Include the Custom Context above in the detection prompt.','igny8'); ?></p>
                                <div class="igny8-card-toggle">
                                    <label class="igny8-toggle-switch">
                                        <input type="checkbox" name="igny8_content_engine_include_page_context" value="1" <?php checked(1, get_option('igny8_content_engine_include_page_context', 0)); ?>>
                                        <span class="igny8-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="igny8-save-section">
                        <div class="igny8-save-container">
                            <button type="submit" class="igny8-btn igny8-btn-success igny8-btn-large">
                                <span class="igny8-btn-icon">✓</span>
                                <?php esc_html_e('Save Settings','igny8'); ?>
                            </button>
                            <p class="igny8-save-description"><?php esc_html_e('All settings will be saved and applied immediately.','igny8'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Content Regeneration Tab -->
                <div id="content-engine-generation" class="igny8-tab-content">
                    <div class="igny8-data-table-container">
                        <div class="igny8-table-header">
                            <h3 class="igny8-table-title"><?php esc_html_e('Content Generation Settings','igny8'); ?></h3>
                        </div>
                        
                        <div class="igny8-table-wrapper">
                            <table class="igny8-data-table">
                                <thead>
                                    <tr>
                                        <th class="igny8-sortable"><?php esc_html_e('SETTING','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('VALUE','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('DESCRIPTION','igny8'); ?></th>
                                        <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php esc_html_e('Content Length','igny8'); ?></td>
                                        <td>
                                            <div class="igny8-filter-group">
                                                <select name="igny8_content_engine_content_length">
                                                    <?php
                                                    $length = get_option('igny8_content_engine_content_length', '300');
                                                    $lengths = [
                                                        '150' => '150 words (short)',
                                                        '300' => '300 words (medium)',
                                                        '600' => '600 words (long)',
                                                        'match' => 'Match original content length',
                                                        'full' => 'Full length (no limit)'
                                                    ];
                                                    foreach ($lengths as $value => $label) {
                                                        echo '<option value="' . esc_attr($value) . '" ' . selected($length, $value, false) . '>' . esc_html($label) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td><?php esc_html_e('Control the length of generated personalized content.','igny8'); ?></td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Test</option>
                                                <option>Reset</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Include User Inputs','igny8'); ?></td>
                                        <td>
                                            <label>
                                                <input type="checkbox" name="igny8_content_engine_include_inputs" value="1" <?php checked(1, get_option('igny8_content_engine_include_inputs', 1)); ?> class="igny8-table-checkbox">
                                                <?php esc_html_e('Include User Data','igny8'); ?>
                                            </label>
                                        </td>
                                        <td><?php esc_html_e('Include user form inputs in the rewrite prompt.','igny8'); ?></td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Test</option>
                                                <option>Reset</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Rewrite Prompt','igny8'); ?></td>
                                        <td>
                                            <div class="igny8-table-search">
                                                <textarea name="igny8_content_engine_rewrite_prompt" rows="8" cols="60" placeholder="<?php esc_attr_e('Enter rewrite prompt...','igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_rewrite_prompt', 'Rewrite the following content to be personalized for a reader with these characteristics:\n\n[INPUTS]\n\nOriginal content:\n[CONTENT]\n\nMake the content feel like it was written specifically for this person while maintaining the original message and tone.')); ?></textarea>
                                            </div>
                                        </td>
                                        <td><?php esc_html_e('Prompt template for content personalization. Use [INPUTS] for user data and [CONTENT] for original content.','igny8'); ?></td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Test</option>
                                                <option>Reset</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Content Rewriter Prompt','igny8'); ?></td>
                                        <td>
                                            <div class="igny8-table-search">
                                                <textarea name="igny8_content_engine_prompt" rows="8" cols="60" placeholder="<?php esc_attr_e('Enter content generation prompt...','igny8'); ?>"><?php echo esc_textarea(get_option('igny8_content_engine_prompt', '')); ?></textarea>
                                            </div>
                                        </td>
                                        <td><?php esc_html_e('Define the AI prompt template used when rewriting or personalizing content. Variables from persona/context will be appended automatically.','igny8'); ?></td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Test</option>
                                                <option>Reset</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Content Regeneration Tab -->
                <div id="content-engine-regeneration" class="igny8-tab-content">
                    <div class="igny8-metric-cards">
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">45</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Pending Regeneration','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+8%</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">23</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Recently Regenerated','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+15%</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">12</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Variation Generated','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+12%</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">89%</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Success Rate','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+3%</div>
                        </div>
                    </div>
                    
                    <div class="igny8-data-table-container">
                        <div class="igny8-table-header">
                            <h3 class="igny8-table-title"><?php esc_html_e('Content Regeneration Queue','igny8'); ?></h3>
                            <div class="igny8-table-controls">
                                <div class="igny8-table-show">
                                    <label><?php esc_html_e('Show:','igny8'); ?></label>
                                    <select>
                                        <option>10</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="igny8-table-search">
                                    <input type="text" placeholder="<?php esc_attr_e('Search posts...','igny8'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="igny8-table-wrapper">
                            <table class="igny8-data-table">
                                <thead>
                                    <tr>
                                        <th class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </th>
                                        <th class="igny8-sortable"><?php esc_html_e('POST TITLE','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('TYPE','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('LAST REGENERATED','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('VARIATIONS','igny8'); ?></th>
                                        <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>AI Guide</td>
                                        <td>Post</td>
                                        <td><span class="igny8-badge yellow">Pending</span></td>
                                        <td>2024-01-15</td>
                                        <td>3</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Regenerate</option>
                                                <option>Edit</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>Machine Learning</td>
                                        <td>Page</td>
                                        <td><span class="igny8-badge green">Regenerated</span></td>
                                        <td>2024-01-14</td>
                                        <td>5</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Regenerate</option>
                                                <option>Edit</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>Deep Learning</td>
                                        <td>Post</td>
                                        <td><span class="igny8-badge blue">Variation Generated</span></td>
                                        <td>2024-01-13</td>
                                        <td>2</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>Regenerate</option>
                                                <option>Edit</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="igny8-table-footer">
                            <div class="igny8-table-info">
                                <?php esc_html_e('Showing 1 to 3 of 3 entries','igny8'); ?>
                            </div>
                            <div class="igny8-table-pagination-controls">
                                <span class="igny8-table-page-btn"><?php esc_html_e('Previous','igny8'); ?></span>
                                <span class="igny8-table-page-btn active">1</span>
                                <span class="igny8-table-page-btn"><?php esc_html_e('Next','igny8'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Debug & Insights Tab -->
                <div id="content-engine-debug" class="igny8-tab-content">
                    <div class="igny8-metric-cards">
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">156</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Total Requests','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+12%</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">98.5%</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Success Rate','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+2%</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">2.3s</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Avg Response Time','igny8'); ?></div>
                            <div class="igny8-metric-change negative">-0.5s</div>
                        </div>
                        <div class="igny8-metric-card">
                            <div class="igny8-metric-value">45</div>
                            <div class="igny8-metric-label"><?php esc_html_e('Cache Hits','igny8'); ?></div>
                            <div class="igny8-metric-change positive">+8%</div>
                        </div>
                    </div>
                    
                    <div class="igny8-data-table-container">
                        <div class="igny8-table-header">
                            <h3 class="igny8-table-title"><?php esc_html_e('Debug Logs','igny8'); ?></h3>
                            <div class="igny8-table-controls">
                                <div class="igny8-table-show">
                                    <label><?php esc_html_e('Show:','igny8'); ?></label>
                                    <select>
                                        <option>10</option>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                                <div class="igny8-table-search">
                                    <input type="text" placeholder="<?php esc_attr_e('Search logs...','igny8'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="igny8-table-wrapper">
                            <table class="igny8-data-table">
                                <thead>
                                    <tr>
                                        <th class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </th>
                                        <th class="igny8-sortable"><?php esc_html_e('TIMESTAMP','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('LEVEL','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('MESSAGE','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('POST','igny8'); ?></th>
                                        <th class="igny8-sortable"><?php esc_html_e('RESPONSE TIME','igny8'); ?></th>
                                        <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>2024-01-15 14:30:25</td>
                                        <td><span class="igny8-badge green">INFO</span></td>
                                        <td>Content personalized successfully</td>
                                        <td>AI Guide</td>
                                        <td>2.1s</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>View Details</option>
                                                <option>Export</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>2024-01-15 14:28:15</td>
                                        <td><span class="igny8-badge yellow">WARNING</span></td>
                                        <td>Cache miss for user data</td>
                                        <td>Machine Learning</td>
                                        <td>3.2s</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>View Details</option>
                                                <option>Export</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="igny8-checkbox-col">
                                            <input type="checkbox" class="igny8-table-checkbox">
                                        </td>
                                        <td>2024-01-15 14:25:10</td>
                                        <td><span class="igny8-badge red">ERROR</span></td>
                                        <td>API timeout exceeded</td>
                                        <td>Deep Learning</td>
                                        <td>30.0s</td>
                                        <td>
                                            <select class="igny8-table-select">
                                                <option>Actions</option>
                                                <option>View Details</option>
                                                <option>Export</option>
                                                <option>Delete</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="igny8-table-footer">
                            <div class="igny8-table-info">
                                <?php esc_html_e('Showing 1 to 3 of 3 entries','igny8'); ?>
                            </div>
                            <div class="igny8-table-pagination-controls">
                                <span class="igny8-table-page-btn"><?php esc_html_e('Previous','igny8'); ?></span>
                                <span class="igny8-table-page-btn active">1</span>
                                <span class="igny8-table-page-btn"><?php esc_html_e('Next','igny8'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Debug Console -->
                    <div class="igny8-debug-console">
                        <div class="igny8-debug-header">
                            <h3><?php esc_html_e('Debug Console','igny8'); ?></h3>
                            <div class="igny8-debug-controls">
                                <button type="button" id="igny8-show-debug" class="button igny8-btn-primary"><?php esc_html_e('Show Debug Data','igny8'); ?></button>
                                <button type="button" id="igny8-hide-debug" class="button igny8-btn-secondary" style="display: none;"><?php esc_html_e('Hide Debug Data','igny8'); ?></button>
                            </div>
                        </div>
                        
                        <div id="igny8-debug-data" class="igny8-debug-content" style="display: none;">
                            <div class="igny8-debug-section">
                                <h4><?php esc_html_e('Field Data','igny8'); ?></h4>
                                <pre id="igny8-field-data"><?php echo esc_html(json_encode([
                                    'global_status' => get_option('igny8_content_engine_global_status', 'disabled'),
                                    'enabled_post_types' => get_option('igny8_content_engine_enabled_post_types', []),
                                    'insertion_position' => get_option('igny8_content_engine_insertion_position', 'before'),
                                    'display_mode' => get_option('igny8_content_engine_display_mode', 'button'),
                                    'teaser_text' => get_option('igny8_content_engine_teaser_text', ''),
                                    'field_mode' => get_option('igny8_content_engine_field_mode', 'dynamic'),
                                    'save_variations' => get_option('igny8_content_engine_save_variations', 1),
                                    'include_page_context' => get_option('igny8_content_engine_include_page_context', 1)
                                ], JSON_PRETTY_PRINT)); ?></pre>
                            </div>
                            
                            <div class="igny8-debug-section">
                                <h4><?php esc_html_e('System Information','igny8'); ?></h4>
                                <pre id="igny8-system-data"><?php echo esc_html(json_encode([
                                    'wordpress_version' => get_bloginfo('version'),
                                    'plugin_version' => '1.0.0',
                                    'php_version' => PHP_VERSION,
                                    'memory_limit' => ini_get('memory_limit'),
                                    'max_execution_time' => ini_get('max_execution_time'),
                                    'debug_mode' => defined('WP_DEBUG') && WP_DEBUG ? 'enabled' : 'disabled',
                                    'cache_status' => 'active',
                                    'api_key_status' => get_option('igny8_api_key') ? 'configured' : 'not configured'
                                ], JSON_PRETTY_PRINT)); ?></pre>
                            </div>
                            
                            <div class="igny8-debug-section">
                                <h4><?php esc_html_e('Recent Activity','igny8'); ?></h4>
                                <pre id="igny8-activity-data"><?php echo esc_html(json_encode([
                                    'last_save' => get_option('igny8_last_save_time', 'Never'),
                                    'total_requests' => get_option('igny8_total_requests', 0),
                                    'successful_requests' => get_option('igny8_successful_requests', 0),
                                    'failed_requests' => get_option('igny8_failed_requests', 0),
                                    'cache_hits' => get_option('igny8_cache_hits', 0),
                                    'cache_misses' => get_option('igny8_cache_misses', 0)
                                ], JSON_PRETTY_PRINT)); ?></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
}