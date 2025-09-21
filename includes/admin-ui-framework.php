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
