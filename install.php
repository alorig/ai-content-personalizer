<?php

defined('ABSPATH') || exit;



function igny8_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'igny8_data';
    $variations_table = $wpdb->prefix . 'igny8_variations';

    $charset_collate = $wpdb->get_charset_collate();

    // Main data table
    $sql = "CREATE TABLE $table_name (

        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

        post_id BIGINT UNSIGNED NOT NULL,

        data_type VARCHAR(50) NOT NULL,

        data JSON NOT NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        INDEX (post_id),

        INDEX (data_type)

    ) $charset_collate;";

    // Variations table for caching personalized content
    $variations_sql = "CREATE TABLE $variations_table (

        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

        post_id BIGINT UNSIGNED NOT NULL,

        fields_hash CHAR(64) NOT NULL,

        fields_json LONGTEXT NOT NULL,

        content LONGTEXT NOT NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        INDEX (post_id, fields_hash),

        UNIQUE KEY unique_variation (post_id, fields_hash)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
    dbDelta($variations_sql);

    // Create default taxonomy terms
    do_action('igny8_after_install');

}

