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
 * Render modern data table
 */
/*
function igny8_render_data_table($table_config) {
    echo '<div class="igny8-data-table-container">';
    
    // Modern table header with controls
    echo '<div class="igny8-table-header">';
    echo '<h3 class="igny8-table-title">' . esc_html($table_config['title']) . '</h3>';
    echo '<div class="igny8-table-controls">';
    echo '<div class="igny8-table-show">';
    echo '<span>Show</span>';
    echo '<select>';
    echo '<option value="8">8</option>';
    echo '<option value="16">16</option>';
    echo '<option value="32">32</option>';
    echo '</select>';
    echo '<span>entries</span>';
    echo '</div>';
    echo '<div class="igny8-table-search">';
    echo '<span>Search:</span>';
    echo '<input type="text" placeholder="Search...">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="igny8-table-wrapper">';
    echo '<table class="igny8-data-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="igny8-checkbox-col"><input type="checkbox" class="igny8-table-checkbox" /></th>';
    echo '<th class="igny8-sortable">NO.</th>';
    
    foreach ($table_config['columns'] as $column) {
        $sortable = isset($column['sortable']) && $column['sortable'] ? 'igny8-sortable' : '';
        echo '<th class="' . $sortable . '" data-column="' . esc_attr($column['key']) . '">' . strtoupper(esc_html($column['label'])) . '</th>';
    }
    
    echo '<th class="igny8-actions-col">ACTIONS</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Placeholder rows with modern styling
    for ($i = 1; $i <= 5; $i++) {
        echo '<tr>';
        echo '<td><input type="checkbox" class="igny8-table-checkbox" /></td>';
        echo '<td>' . str_pad($i, 6, '0', STR_PAD_LEFT) . '</td>';
        
        foreach ($table_config['columns'] as $column) {
            echo '<td data-column="' . esc_attr($column['key']) . '">';
            
            // Special handling for status columns
            if (strpos(strtolower($column['key']), 'status') !== false) {
                $statuses = ['Pending', 'Published', 'Draft', 'Scheduled'];
                $status = $statuses[array_rand($statuses)];
                $colors = ['#f59e0b', '#10b981', '#6b7280', '#8b5cf6'];
                $color = $colors[array_rand($colors)];
                echo '<span style="display: inline-flex; align-items: center; gap: 6px;">';
                echo '<span style="width: 8px; height: 8px; border-radius: 50%; background: ' . $color . ';"></span>';
                echo esc_html($status);
                echo '</span>';
            } else {
                echo '<span class="igny8-placeholder">' . esc_html($column['placeholder'] ?? 'Sample Data') . '</span>';
            }
            echo '</td>';
        }
        
        echo '<td>';
        echo '<button class="igny8-table-page-btn" style="padding: 4px 8px; font-size: 12px;">Actions ↓</button>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Modern pagination footer
    echo '<div class="igny8-table-footer">';
    echo '<div class="igny8-table-info">Showing 1 to 5 of 25 entries</div>';
    echo '<div class="igny8-table-pagination-controls">';
    echo '<button class="igny8-table-page-btn" disabled><</button>';
    echo '<button class="igny8-table-page-btn active">1</button>';
    echo '<button class="igny8-table-page-btn">2</button>';
    echo '<button class="igny8-table-page-btn">3</button>';
    echo '<button class="igny8-table-page-btn">4</button>';
    echo '<button class="igny8-table-page-btn">5</button>';
    echo '<button class="igny8-table-page-btn">></button>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
}*/

/**
 * Render card grid
 */
function igny8_render_card_grid($grid_config) {
    echo '<div class="igny8-card-grid">';
    
    for ($i = 1; $i <= 6; $i++) {
        echo '<div class="igny8-card">';
        echo '<div class="igny8-card-header">';
        echo '<h3 class="igny8-card-title">Sample Card ' . $i . '</h3>';
        echo '<div class="igny8-card-badges">';
        echo '<span class="igny8-badge igny8-badge-primary">Primary</span>';
        echo '<span class="igny8-badge igny8-badge-secondary">Secondary</span>';
        echo '</div>';
        echo '</div>';
        echo '<div class="igny8-card-content">';
        echo '<p>This is a sample card content with placeholder data for the ' . esc_html($grid_config['title']) . ' grid.</p>';
        echo '</div>';
        echo '<div class="igny8-card-footer">';
        echo '<button type="button" class="button button-primary igny8-edit-card">Edit</button>';
        echo '<button type="button" class="button igny8-delete-card">Delete</button>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Render dashboard charts
 */
function igny8_render_dashboard_charts($charts) {
    echo '<div class="igny8-dashboard-charts">';
    
    foreach ($charts as $chart) {
        echo '<div class="igny8-chart-container">';
        echo '<div class="igny8-chart-header">';
        echo '<h3>' . esc_html($chart['title']) . '</h3>';
        echo '<div class="igny8-chart-controls">';
        echo '<select class="igny8-chart-period">';
        echo '<option value="7d">Last 7 days</option>';
        echo '<option value="30d">Last 30 days</option>';
        echo '<option value="90d">Last 90 days</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '<div class="igny8-chart-placeholder">';
        echo '<div class="igny8-chart-mock">';
        echo '<div class="igny8-chart-bars">';
        for ($i = 1; $i <= 7; $i++) {
            $height = rand(20, 100);
            echo '<div class="igny8-chart-bar" style="height: ' . $height . '%"></div>';
        }
        echo '</div>';
        echo '<div class="igny8-chart-labels">Mon Tue Wed Thu Fri Sat Sun</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
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

/**
 * Render collapsible help section
 */
function igny8_render_help_section($title, $content) {
    echo '<div class="igny8-help-section">';
    echo '<button type="button" class="igny8-help-toggle">';
    echo '<span class="dashicons dashicons-editor-help"></span>';
    echo esc_html($title);
    echo '<span class="dashicons dashicons-arrow-down-alt2"></span>';
    echo '</button>';
    echo '<div class="igny8-help-content" style="display: none;">';
    echo '<p>' . esc_html($content) . '</p>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render modal structure
 */
function igny8_render_modal($modal_id, $title, $content) {
    echo '<div id="' . esc_attr($modal_id) . '" class="igny8-modal" style="display: none;">';
    echo '<div class="igny8-modal-overlay"></div>';
    echo '<div class="igny8-modal-content">';
    echo '<div class="igny8-modal-header">';
    echo '<h2>' . esc_html($title) . '</h2>';
    echo '<button type="button" class="igny8-modal-close">&times;</button>';
    echo '</div>';
    echo '<div class="igny8-modal-body">';
    echo $content;
    echo '</div>';
    echo '<div class="igny8-modal-footer">';
    echo '<button type="button" class="button igny8-modal-cancel">Cancel</button>';
    echo '<button type="button" class="button button-primary igny8-modal-save">Save</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render side drawer
 */
function igny8_render_side_drawer($drawer_id, $title, $content) {
    echo '<div id="' . esc_attr($drawer_id) . '" class="igny8-side-drawer" style="display: none;">';
    echo '<div class="igny8-drawer-overlay"></div>';
    echo '<div class="igny8-drawer-content">';
    echo '<div class="igny8-drawer-header">';
    echo '<h2>' . esc_html($title) . '</h2>';
    echo '<button type="button" class="igny8-drawer-close">&times;</button>';
    echo '</div>';
    echo '<div class="igny8-drawer-body">';
    echo $content;
    echo '</div>';
    echo '<div class="igny8-drawer-footer">';
    echo '<button type="button" class="button igny8-drawer-cancel">Cancel</button>';
    echo '<button type="button" class="button button-primary igny8-drawer-save">Save</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
