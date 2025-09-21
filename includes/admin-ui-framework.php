<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN UI FRAMEWORK - PHASE 3
  Description: Modern SaaS-style admin interface components and layouts
==================================================*/

/**
 * Enqueue modern admin styles and scripts
 */
add_action('admin_enqueue_scripts', 'igny8_enqueue_modern_admin_assets');

function igny8_enqueue_modern_admin_assets($hook) {
    // Only load on Igny8 admin pages
    if (strpos($hook, 'igny8') === false) {
        return;
    }
    
    // Modern admin CSS
    wp_enqueue_style(
        'igny8-modern-admin',
        plugin_dir_url(dirname(__FILE__)) . 'assets/css/modern-admin.css',
        [],
        '1.0.0'
    );
    
    // Modern admin JS
    wp_enqueue_script(
        'igny8-modern-admin',
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/modern-admin.js',
        ['jquery'],
        '1.0.0',
        true
    );
}

/**
 * Render metric cards row
 */
function igny8_render_metric_cards($metrics) {
    echo '<div class="igny8-metric-cards">';
    foreach ($metrics as $metric) {
        echo '<div class="igny8-metric-card">';
        echo '<div class="igny8-metric-value">' . esc_html($metric['value']) . '</div>';
        echo '<div class="igny8-metric-label">' . esc_html($metric['label']) . '</div>';
        if (isset($metric['change'])) {
            $change_class = $metric['change'] > 0 ? 'positive' : 'negative';
            echo '<div class="igny8-metric-change ' . $change_class . '">' . esc_html($metric['change']) . '%</div>';
        }
        echo '</div>';
    }
    echo '</div>';
}

/**
 * Render filter bar
 */
function igny8_render_filter_bar($filters) {
    echo '<div class="igny8-filter-bar">';
    echo '<div class="igny8-filters-left">';
    
    foreach ($filters as $filter) {
        switch ($filter['type']) {
            case 'dropdown':
                echo '<div class="igny8-filter-group">';
                echo '<label>' . esc_html($filter['label']) . '</label>';
                echo '<select name="' . esc_attr($filter['name']) . '">';
                echo '<option value="">All ' . esc_html($filter['label']) . '</option>';
                foreach ($filter['options'] as $value => $label) {
                    echo '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
                }
                echo '</select>';
                echo '</div>';
                break;
                
            case 'search':
                echo '<div class="igny8-filter-group">';
                echo '<input type="search" name="' . esc_attr($filter['name']) . '" placeholder="' . esc_attr($filter['placeholder']) . '" />';
                echo '</div>';
                break;
                
            case 'range':
                echo '<div class="igny8-filter-group">';
                echo '<label>' . esc_html($filter['label']) . '</label>';
                echo '<input type="number" name="' . esc_attr($filter['name'] . '_min') . '" placeholder="Min" />';
                echo '<span>to</span>';
                echo '<input type="number" name="' . esc_attr($filter['name'] . '_max') . '" placeholder="Max" />';
                echo '</div>';
                break;
        }
    }
    
    echo '</div>';
    echo '<div class="igny8-filters-right">';
    echo '<button type="button" class="button button-primary igny8-apply-filters">Apply Filters</button>';
    echo '<button type="button" class="button igny8-clear-filters">Clear</button>';
    echo '</div>';
    echo '</div>';
}


/**
 * Render status badge
 */
function igny8_render_status_badge($status, $type = 'default') {
    $badge_class = 'igny8-badge igny8-badge-' . $type . ' igny8-status-' . strtolower(str_replace(' ', '-', $status));
    echo '<span class="' . esc_attr($badge_class) . '">' . esc_html($status) . '</span>';
}

/**
 * Render tooltip
 */
function igny8_render_tooltip($content, $icon = 'info') {
    echo '<span class="igny8-tooltip" data-tooltip="' . esc_attr($content) . '">';
    echo '<span class="igny8-tooltip-icon dashicons dashicons-' . esc_attr($icon) . '"></span>';
    echo '</span>';
}

