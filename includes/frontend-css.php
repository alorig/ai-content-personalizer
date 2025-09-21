<?php
defined('ABSPATH') || exit;

add_action('wp_footer', 'igny8_output_custom_css');

function igny8_output_custom_css() {
    // Check if Content Engine is enabled and use Content Engine-specific settings if available
    $content_engine_status = get_option('igny8_content_engine_global_status', 'enabled');
    $post_type = get_post_type();
    $enabled_post_types = get_option('igny8_content_engine_enabled_post_types', []);
    
    if ($content_engine_status === 'enabled' && in_array($post_type, $enabled_post_types)) {
        // Use Content Engine-specific settings (simplified - no custom CSS or colors)
        $custom_css = '';
        $btn_color = '#0073aa'; // Default button color
        $bg_color = '#f9f9f9';  // Default background color
    } else {
        // Use global settings for backward compatibility
        $custom_css = trim(get_option('igny8_custom_css', ''));
        $btn_color = get_option('igny8_button_color', '#0073aa');
        $bg_color = get_option('igny8_content_bg', '#f9f9f9');
    }

    echo "<style id='igny8-custom-style'>
        .igny8-final-content {
            background-color: {$bg_color};
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        #igny8-form button.button {
            background-color: {$btn_color};
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .igny8-teaser {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .igny8-loading {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
        .igny8-content-container {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin: 20px 0;
            overflow: hidden;
        }
        .igny8-content-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .igny8-content-status {
            font-weight: bold;
            color: #495057;
        }
        .igny8-content-actions {
            display: flex;
            gap: 10px;
        }
        .igny8-save-btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .igny8-save-btn:hover {
            background: #005a87;
        }
        .igny8-save-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .igny8-final-content {
            padding: 20px;
            background: white;
        }
        {$custom_css}
    </style>";
}
