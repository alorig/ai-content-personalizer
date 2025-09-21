<?php
defined('ABSPATH') || exit;

/*==================================================
  ## UTILITY FUNCTIONS
  Description: Generic utility and helper functions
==================================================*/

/**
 * Global option handler for modules
 * @param string $module Module name (e.g., 'personalize')
 * @param string $key Option key
 * @param mixed $value Optional value to set
 * @return mixed Option value
 */
function igny8_option($module, $key, $value = null) {
    $option_name = "igny8_{$module}_{$key}";
    
    if ($value !== null) {
        // Setting a value
        update_option($option_name, $value);
        return $value;
    } else {
        // Getting a value
        return get_option($option_name, '');
    }
}

/**
 * Sanitize array of text fields
 * @param array $array Array to sanitize
 * @return array Sanitized array
 */
function array_map_sanitize_text_field($array) {
    return array_map('sanitize_text_field', $array);
}
