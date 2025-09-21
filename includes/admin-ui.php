<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 ADMIN PAGE UI
  Description: Admin pages for all Igny8 modules
==================================================*/

/**
 * Admin page loader function
 * Routes to appropriate page based on current submenu
 */
function igny8_admin_page_loader() {
    $current_page = $_GET['page'] ?? 'igny8';
    
    switch ($current_page) {
        case 'igny8':
            igny8_dashboard_page();
            break;

        case 'igny8-settings':
            igny8_settings_page();
            break;
        default:
            igny8_dashboard_page();
            break;
    }
}