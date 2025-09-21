<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 DATA MODEL - PHASE 2
  Description: Custom Post Types and Taxonomies for Keywords, Clusters, Content Planning, Context Profiles, Internal Links, and Performance Logs
==================================================*/

/**
 * Initialize all custom post types and taxonomies
 */
add_action('init', 'igny8_init_data_model');

function igny8_init_data_model() {
    igny8_create_taxonomies();
    igny8_create_post_types();
}

/**
 * Create all taxonomies
 */
function igny8_create_taxonomies() {
    // 1. Sector (hierarchical: Sector → Sub-sector)
    register_taxonomy('igny8_sector', ['igny8_keywords', 'igny8_clusters'], [
        'hierarchical' => true,
        'labels' => [
            'name' => 'Sectors',
            'singular_name' => 'Sector',
            'search_items' => 'Search Sectors',
            'all_items' => 'All Sectors',
            'parent_item' => 'Parent Sector',
            'parent_item_colon' => 'Parent Sector:',
            'edit_item' => 'Edit Sector',
            'update_item' => 'Update Sector',
            'add_new_item' => 'Add New Sector',
            'new_item_name' => 'New Sector Name',
            'menu_name' => 'Sectors',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'sector'],
    ]);

    // 2. Intent (flat taxonomy)
    register_taxonomy('igny8_intent', ['igny8_keywords'], [
        'hierarchical' => false,
        'labels' => [
            'name' => 'Intents',
            'singular_name' => 'Intent',
            'search_items' => 'Search Intents',
            'all_items' => 'All Intents',
            'edit_item' => 'Edit Intent',
            'update_item' => 'Update Intent',
            'add_new_item' => 'Add New Intent',
            'new_item_name' => 'New Intent Name',
            'menu_name' => 'Intents',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'intent'],
    ]);

    // 3. Content Type (flat taxonomy)
    register_taxonomy('igny8_content_type', ['igny8_content_planner'], [
        'hierarchical' => false,
        'labels' => [
            'name' => 'Content Types',
            'singular_name' => 'Content Type',
            'search_items' => 'Search Content Types',
            'all_items' => 'All Content Types',
            'edit_item' => 'Edit Content Type',
            'update_item' => 'Update Content Type',
            'add_new_item' => 'Add New Content Type',
            'new_item_name' => 'New Content Type Name',
            'menu_name' => 'Content Types',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'content-type'],
    ]);

    // 4. Voice Tone (flat taxonomy)
    register_taxonomy('igny8_voice_tone', ['igny8_context_profiles'], [
        'hierarchical' => false,
        'labels' => [
            'name' => 'Voice Tones',
            'singular_name' => 'Voice Tone',
            'search_items' => 'Search Voice Tones',
            'all_items' => 'All Voice Tones',
            'edit_item' => 'Edit Voice Tone',
            'update_item' => 'Update Voice Tone',
            'add_new_item' => 'Add New Voice Tone',
            'new_item_name' => 'New Voice Tone Name',
            'menu_name' => 'Voice Tones',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'voice-tone'],
    ]);

    // 5. Tags (flat taxonomy)
    register_taxonomy('igny8_tags', ['igny8_context_profiles'], [
        'hierarchical' => false,
        'labels' => [
            'name' => 'Profile Tags',
            'singular_name' => 'Profile Tag',
            'search_items' => 'Search Tags',
            'all_items' => 'All Tags',
            'edit_item' => 'Edit Tag',
            'update_item' => 'Update Tag',
            'add_new_item' => 'Add New Tag',
            'new_item_name' => 'New Tag Name',
            'menu_name' => 'Profile Tags',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'profile-tag'],
    ]);

    // 6. Link Type (flat taxonomy)
    register_taxonomy('igny8_link_type', ['igny8_internal_links'], [
        'hierarchical' => false,
        'labels' => [
            'name' => 'Link Types',
            'singular_name' => 'Link Type',
            'search_items' => 'Search Link Types',
            'all_items' => 'All Link Types',
            'edit_item' => 'Edit Link Type',
            'update_item' => 'Update Link Type',
            'add_new_item' => 'Add New Link Type',
            'new_item_name' => 'New Link Type Name',
            'menu_name' => 'Link Types',
        ],
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'link-type'],
    ]);
}

/**
 * Create all custom post types
 */
function igny8_create_post_types() {
    // 1. Keywords CPT
    register_post_type('igny8_keywords', [
        'labels' => [
            'name' => 'Keywords',
            'singular_name' => 'Keyword',
            'menu_name' => 'Keywords',
            'add_new' => 'Add New Keyword',
            'add_new_item' => 'Add New Keyword',
            'edit_item' => 'Edit Keyword',
            'new_item' => 'New Keyword',
            'view_item' => 'View Keyword',
            'search_items' => 'Search Keywords',
            'not_found' => 'No keywords found',
            'not_found_in_trash' => 'No keywords found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Keywords & Clusters submenu
        'capability_type' => 'post',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);

    // 2. Clusters CPT
    register_post_type('igny8_clusters', [
        'labels' => [
            'name' => 'Clusters',
            'singular_name' => 'Cluster',
            'menu_name' => 'Clusters',
            'add_new' => 'Add New Cluster',
            'add_new_item' => 'Add New Cluster',
            'edit_item' => 'Edit Cluster',
            'new_item' => 'New Cluster',
            'view_item' => 'View Cluster',
            'search_items' => 'Search Clusters',
            'not_found' => 'No clusters found',
            'not_found_in_trash' => 'No clusters found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Keywords & Clusters submenu
        'capability_type' => 'post',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);

    // 3. Content Planner CPT
    register_post_type('igny8_content_planner', [
        'labels' => [
            'name' => 'Content Planner',
            'singular_name' => 'Content Plan',
            'menu_name' => 'Content Planner',
            'add_new' => 'Add New Plan',
            'add_new_item' => 'Add New Content Plan',
            'edit_item' => 'Edit Content Plan',
            'new_item' => 'New Content Plan',
            'view_item' => 'View Content Plan',
            'search_items' => 'Search Content Plans',
            'not_found' => 'No content plans found',
            'not_found_in_trash' => 'No content plans found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Content Engine submenu
        'capability_type' => 'post',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);

    // 4. Context Profiles CPT
    register_post_type('igny8_context_profiles', [
        'labels' => [
            'name' => 'Context Profiles',
            'singular_name' => 'Context Profile',
            'menu_name' => 'Context Profiles',
            'add_new' => 'Add New Profile',
            'add_new_item' => 'Add New Context Profile',
            'edit_item' => 'Edit Context Profile',
            'new_item' => 'New Context Profile',
            'view_item' => 'View Context Profile',
            'search_items' => 'Search Context Profiles',
            'not_found' => 'No context profiles found',
            'not_found_in_trash' => 'No context profiles found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Content Engine submenu
        'capability_type' => 'post',
        'supports' => ['title', 'editor'],
        'has_archive' => false,
        'rewrite' => false,
    ]);

    // 5. Internal Links CPT
    register_post_type('igny8_internal_links', [
        'labels' => [
            'name' => 'Internal Links',
            'singular_name' => 'Internal Link',
            'menu_name' => 'Internal Links',
            'add_new' => 'Add New Link',
            'add_new_item' => 'Add New Internal Link',
            'edit_item' => 'Edit Internal Link',
            'new_item' => 'New Internal Link',
            'view_item' => 'View Internal Link',
            'search_items' => 'Search Internal Links',
            'not_found' => 'No internal links found',
            'not_found_in_trash' => 'No internal links found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Content Engine submenu
        'capability_type' => 'post',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);

    // 6. Performance Logs CPT
    register_post_type('igny8_performance_logs', [
        'labels' => [
            'name' => 'Performance Logs',
            'singular_name' => 'Performance Log',
            'menu_name' => 'Performance Logs',
            'add_new' => 'Add New Log',
            'add_new_item' => 'Add New Performance Log',
            'edit_item' => 'Edit Performance Log',
            'new_item' => 'New Performance Log',
            'view_item' => 'View Performance Log',
            'search_items' => 'Search Performance Logs',
            'not_found' => 'No performance logs found',
            'not_found_in_trash' => 'No performance logs found in trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Will be added to Content Engine submenu
        'capability_type' => 'post',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}

/**
 * Add default taxonomy terms on plugin activation
 */
add_action('igny8_after_install', 'igny8_create_default_taxonomy_terms');

function igny8_create_default_taxonomy_terms() {
    // Create default Intent terms
    $intent_terms = ['Informational', 'Transactional', 'Commercial', 'Navigational'];
    foreach ($intent_terms as $term) {
        if (!term_exists($term, 'igny8_intent')) {
            wp_insert_term($term, 'igny8_intent');
        }
    }

    // Create default Content Type terms
    $content_type_terms = ['Hub Page', 'Sub Page', 'Blog', 'Product Page', 'Service Page', 'Attribute Page'];
    foreach ($content_type_terms as $term) {
        if (!term_exists($term, 'igny8_content_type')) {
            wp_insert_term($term, 'igny8_content_type');
        }
    }

    // Create default Voice Tone terms
    $voice_tone_terms = ['Friendly', 'Technical', 'Authoritative'];
    foreach ($voice_tone_terms as $term) {
        if (!term_exists($term, 'igny8_voice_tone')) {
            wp_insert_term($term, 'igny8_voice_tone');
        }
    }

    // Create default Tags terms
    $tags_terms = ['Product', 'Service', 'FAQ', 'Schema', 'Blog'];
    foreach ($tags_terms as $term) {
        if (!term_exists($term, 'igny8_tags')) {
            wp_insert_term($term, 'igny8_tags');
        }
    }

    // Create default Link Type terms
    $link_type_terms = ['Upward', 'Downward', 'Horizontal'];
    foreach ($link_type_terms as $term) {
        if (!term_exists($term, 'igny8_link_type')) {
            wp_insert_term($term, 'igny8_link_type');
        }
    }
}

/**
 * Add CPTs to admin menus
 */
add_action('admin_menu', 'igny8_add_cpts_to_menus');

function igny8_add_cpts_to_menus() {
    // Add Keywords and Clusters to Keywords & Clusters submenu
    add_submenu_page(
        'igny8-keywords-clusters',
        'Keywords',
        'Keywords',
        'manage_options',
        'edit.php?post_type=igny8_keywords'
    );

    add_submenu_page(
        'igny8-keywords-clusters',
        'Clusters',
        'Clusters',
        'manage_options',
        'edit.php?post_type=igny8_clusters'
    );

    // Add Content Engine CPTs to Content Engine submenu
    add_submenu_page(
        'igny8-content-engine-new',
        'Content Planner',
        'Content Planner',
        'manage_options',
        'edit.php?post_type=igny8_content_planner'
    );

    add_submenu_page(
        'igny8-content-engine-new',
        'Context Profiles',
        'Context Profiles',
        'manage_options',
        'edit.php?post_type=igny8_context_profiles'
    );

    add_submenu_page(
        'igny8-content-engine-new',
        'Internal Links',
        'Internal Links',
        'manage_options',
        'edit.php?post_type=igny8_internal_links'
    );

    add_submenu_page(
        'igny8-content-engine-new',
        'Performance Logs',
        'Performance Logs',
        'manage_options',
        'edit.php?post_type=igny8_performance_logs'
    );
}

/**
 * Custom meta boxes for CPTs
 */
add_action('add_meta_boxes', 'igny8_add_meta_boxes');

function igny8_add_meta_boxes() {
    // Keywords meta box
    add_meta_box(
        'igny8_keywords_meta',
        'Keyword Data',
        'igny8_keywords_meta_box_callback',
        'igny8_keywords',
        'normal',
        'high'
    );

    // Clusters meta box
    add_meta_box(
        'igny8_clusters_meta',
        'Cluster Data',
        'igny8_clusters_meta_box_callback',
        'igny8_clusters',
        'normal',
        'high'
    );

    // Content Planner meta box
    add_meta_box(
        'igny8_content_planner_meta',
        'Content Plan Data',
        'igny8_content_planner_meta_box_callback',
        'igny8_content_planner',
        'normal',
        'high'
    );

    // Context Profiles meta box
    add_meta_box(
        'igny8_context_profiles_meta',
        'Profile Data',
        'igny8_context_profiles_meta_box_callback',
        'igny8_context_profiles',
        'normal',
        'high'
    );

    // Internal Links meta box
    add_meta_box(
        'igny8_internal_links_meta',
        'Link Data',
        'igny8_internal_links_meta_box_callback',
        'igny8_internal_links',
        'normal',
        'high'
    );

    // Performance Logs meta box
    add_meta_box(
        'igny8_performance_logs_meta',
        'Performance Data',
        'igny8_performance_logs_meta_box_callback',
        'igny8_performance_logs',
        'normal',
        'high'
    );
}

/**
 * Meta box callbacks
 */
function igny8_keywords_meta_box_callback($post) {
    wp_nonce_field('igny8_keywords_meta_box', 'igny8_keywords_meta_box_nonce');
    
    $search_volume = get_post_meta($post->ID, '_igny8_search_volume', true);
    $difficulty = get_post_meta($post->ID, '_igny8_difficulty', true);
    $cpc = get_post_meta($post->ID, '_igny8_cpc', true);
    $cluster_relation = get_post_meta($post->ID, '_igny8_cluster_relation', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_search_volume">Search Volume:</label></th>';
    echo '<td><input type="number" id="igny8_search_volume" name="igny8_search_volume" value="' . esc_attr($search_volume) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_difficulty">Difficulty Level:</label></th>';
    echo '<td><input type="number" id="igny8_difficulty" name="igny8_difficulty" value="' . esc_attr($difficulty) . '" min="1" max="100" /></td></tr>';
    
    echo '<tr><th><label for="igny8_cpc">CPC:</label></th>';
    echo '<td><input type="number" id="igny8_cpc" name="igny8_cpc" value="' . esc_attr($cpc) . '" step="0.01" /></td></tr>';
    
    echo '<tr><th><label for="igny8_cluster_relation">Cluster Relation:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => 'igny8_clusters',
        'name' => 'igny8_cluster_relation',
        'selected' => $cluster_relation,
        'show_option_none' => 'Select Cluster',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    echo '</table>';
}

function igny8_clusters_meta_box_callback($post) {
    wp_nonce_field('igny8_clusters_meta_box', 'igny8_clusters_meta_box_nonce');
    
    $cluster_page_title = get_post_meta($post->ID, '_igny8_cluster_page_title', true);
    $target_url = get_post_meta($post->ID, '_igny8_target_url', true);
    $priority = get_post_meta($post->ID, '_igny8_priority', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_cluster_page_title">Cluster Page Title:</label></th>';
    echo '<td><input type="text" id="igny8_cluster_page_title" name="igny8_cluster_page_title" value="' . esc_attr($cluster_page_title) . '" style="width: 100%;" /></td></tr>';
    
    echo '<tr><th><label for="igny8_target_url">Target URL:</label></th>';
    echo '<td><input type="url" id="igny8_target_url" name="igny8_target_url" value="' . esc_attr($target_url) . '" style="width: 100%;" /></td></tr>';
    
    echo '<tr><th><label for="igny8_priority">Priority:</label></th>';
    echo '<td><select id="igny8_priority" name="igny8_priority">';
    echo '<option value="">Select Priority</option>';
    echo '<option value="high"' . selected($priority, 'high', false) . '>High</option>';
    echo '<option value="medium"' . selected($priority, 'medium', false) . '>Medium</option>';
    echo '<option value="low"' . selected($priority, 'low', false) . '>Low</option>';
    echo '</select></td></tr>';
    echo '</table>';
}

function igny8_content_planner_meta_box_callback($post) {
    wp_nonce_field('igny8_content_planner_meta_box', 'igny8_content_planner_meta_box_nonce');
    
    $related_cluster = get_post_meta($post->ID, '_igny8_related_cluster', true);
    $related_keywords = get_post_meta($post->ID, '_igny8_related_keywords', true);
    $status = get_post_meta($post->ID, '_igny8_status', true);
    $schedule_date = get_post_meta($post->ID, '_igny8_schedule_date', true);
    $refresh_after_days = get_post_meta($post->ID, '_igny8_refresh_after_days', true);
    $linked_wp_post = get_post_meta($post->ID, '_igny8_linked_wp_post', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_related_cluster">Related Cluster:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => 'igny8_clusters',
        'name' => 'igny8_related_cluster',
        'selected' => $related_cluster,
        'show_option_none' => 'Select Cluster',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    
    echo '<tr><th><label for="igny8_status">Status:</label></th>';
    echo '<td><select id="igny8_status" name="igny8_status">';
    $statuses = ['Pending', 'Queued', 'Generated', 'Published', 'Refresh Scheduled'];
    foreach ($statuses as $status_option) {
        echo '<option value="' . esc_attr($status_option) . '"' . selected($status, $status_option, false) . '>' . esc_html($status_option) . '</option>';
    }
    echo '</select></td></tr>';
    
    echo '<tr><th><label for="igny8_schedule_date">Schedule Date:</label></th>';
    echo '<td><input type="datetime-local" id="igny8_schedule_date" name="igny8_schedule_date" value="' . esc_attr($schedule_date) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_refresh_after_days">Refresh After Days:</label></th>';
    echo '<td><input type="number" id="igny8_refresh_after_days" name="igny8_refresh_after_days" value="' . esc_attr($refresh_after_days) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_linked_wp_post">Linked WP Post/Page:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => ['post', 'page'],
        'name' => 'igny8_linked_wp_post',
        'selected' => $linked_wp_post,
        'show_option_none' => 'Select Post/Page',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    echo '</table>';
}

function igny8_context_profiles_meta_box_callback($post) {
    wp_nonce_field('igny8_context_profiles_meta_box', 'igny8_context_profiles_meta_box_nonce');
    
    $schema_hints = get_post_meta($post->ID, '_igny8_schema_hints', true);
    $product_facts = get_post_meta($post->ID, '_igny8_product_facts', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_schema_hints">Schema Hints:</label></th>';
    echo '<td><textarea id="igny8_schema_hints" name="igny8_schema_hints" rows="4" style="width: 100%;">' . esc_textarea($schema_hints) . '</textarea></td></tr>';
    
    echo '<tr><th><label for="igny8_product_facts">Product Facts:</label></th>';
    echo '<td><textarea id="igny8_product_facts" name="igny8_product_facts" rows="4" style="width: 100%;">' . esc_textarea($product_facts) . '</textarea></td></tr>';
    echo '</table>';
}

function igny8_internal_links_meta_box_callback($post) {
    wp_nonce_field('igny8_internal_links_meta_box', 'igny8_internal_links_meta_box_nonce');
    
    $source_page = get_post_meta($post->ID, '_igny8_source_page', true);
    $target_page = get_post_meta($post->ID, '_igny8_target_page', true);
    $anchor_text = get_post_meta($post->ID, '_igny8_anchor_text', true);
    $cluster_reference = get_post_meta($post->ID, '_igny8_cluster_reference', true);
    $keywords_used = get_post_meta($post->ID, '_igny8_keywords_used', true);
    $priority = get_post_meta($post->ID, '_igny8_priority', true);
    $status = get_post_meta($post->ID, '_igny8_status', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_source_page">Source Page:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => ['post', 'page'],
        'name' => 'igny8_source_page',
        'selected' => $source_page,
        'show_option_none' => 'Select Source Page',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    
    echo '<tr><th><label for="igny8_target_page">Target Page:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => ['post', 'page'],
        'name' => 'igny8_target_page',
        'selected' => $target_page,
        'show_option_none' => 'Select Target Page',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    
    echo '<tr><th><label for="igny8_anchor_text">Anchor Text:</label></th>';
    echo '<td><input type="text" id="igny8_anchor_text" name="igny8_anchor_text" value="' . esc_attr($anchor_text) . '" style="width: 100%;" /></td></tr>';
    
    echo '<tr><th><label for="igny8_cluster_reference">Cluster Reference:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => 'igny8_clusters',
        'name' => 'igny8_cluster_reference',
        'selected' => $cluster_reference,
        'show_option_none' => 'Select Cluster',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    
    echo '<tr><th><label for="igny8_priority">Priority:</label></th>';
    echo '<td><input type="number" id="igny8_priority" name="igny8_priority" value="' . esc_attr($priority) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_status">Status:</label></th>';
    echo '<td><select id="igny8_status" name="igny8_status">';
    $statuses = ['Suggested', 'Approved', 'Inserted'];
    foreach ($statuses as $status_option) {
        echo '<option value="' . esc_attr($status_option) . '"' . selected($status, $status_option, false) . '>' . esc_html($status_option) . '</option>';
    }
    echo '</select></td></tr>';
    echo '</table>';
}

function igny8_performance_logs_meta_box_callback($post) {
    wp_nonce_field('igny8_performance_logs_meta_box', 'igny8_performance_logs_meta_box_nonce');
    
    $cluster_reference = get_post_meta($post->ID, '_igny8_cluster_reference', true);
    $generation_count = get_post_meta($post->ID, '_igny8_generation_count', true);
    $refresh_count = get_post_meta($post->ID, '_igny8_refresh_count', true);
    $linking_density = get_post_meta($post->ID, '_igny8_linking_density', true);
    $search_console_data = get_post_meta($post->ID, '_igny8_search_console_data', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="igny8_cluster_reference">Cluster Reference:</label></th>';
    echo '<td>';
    wp_dropdown_posts([
        'post_type' => 'igny8_clusters',
        'name' => 'igny8_cluster_reference',
        'selected' => $cluster_reference,
        'show_option_none' => 'Select Cluster',
        'option_none_value' => ''
    ]);
    echo '</td></tr>';
    
    echo '<tr><th><label for="igny8_generation_count">Generation Count:</label></th>';
    echo '<td><input type="number" id="igny8_generation_count" name="igny8_generation_count" value="' . esc_attr($generation_count) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_refresh_count">Refresh Count:</label></th>';
    echo '<td><input type="number" id="igny8_refresh_count" name="igny8_refresh_count" value="' . esc_attr($refresh_count) . '" /></td></tr>';
    
    echo '<tr><th><label for="igny8_linking_density">Linking Density:</label></th>';
    echo '<td><input type="number" id="igny8_linking_density" name="igny8_linking_density" value="' . esc_attr($linking_density) . '" step="0.01" /></td></tr>';
    
    echo '<tr><th><label for="igny8_search_console_data">Search Console Data:</label></th>';
    echo '<td><textarea id="igny8_search_console_data" name="igny8_search_console_data" rows="4" style="width: 100%;">' . esc_textarea($search_console_data) . '</textarea></td></tr>';
    echo '</table>';
}

/**
 * Save meta box data
 */
add_action('save_post', 'igny8_save_meta_boxes');

function igny8_save_meta_boxes($post_id) {
    // Keywords meta box
    if (isset($_POST['igny8_keywords_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_keywords_meta_box_nonce'], 'igny8_keywords_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_search_volume', sanitize_text_field($_POST['igny8_search_volume']));
        update_post_meta($post_id, '_igny8_difficulty', sanitize_text_field($_POST['igny8_difficulty']));
        update_post_meta($post_id, '_igny8_cpc', sanitize_text_field($_POST['igny8_cpc']));
        update_post_meta($post_id, '_igny8_cluster_relation', sanitize_text_field($_POST['igny8_cluster_relation']));
    }
    
    // Clusters meta box
    if (isset($_POST['igny8_clusters_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_clusters_meta_box_nonce'], 'igny8_clusters_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_cluster_page_title', sanitize_text_field($_POST['igny8_cluster_page_title']));
        update_post_meta($post_id, '_igny8_target_url', esc_url_raw($_POST['igny8_target_url']));
        update_post_meta($post_id, '_igny8_priority', sanitize_text_field($_POST['igny8_priority']));
    }
    
    // Content Planner meta box
    if (isset($_POST['igny8_content_planner_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_content_planner_meta_box_nonce'], 'igny8_content_planner_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_related_cluster', sanitize_text_field($_POST['igny8_related_cluster']));
        update_post_meta($post_id, '_igny8_status', sanitize_text_field($_POST['igny8_status']));
        update_post_meta($post_id, '_igny8_schedule_date', sanitize_text_field($_POST['igny8_schedule_date']));
        update_post_meta($post_id, '_igny8_refresh_after_days', sanitize_text_field($_POST['igny8_refresh_after_days']));
        update_post_meta($post_id, '_igny8_linked_wp_post', sanitize_text_field($_POST['igny8_linked_wp_post']));
    }
    
    // Context Profiles meta box
    if (isset($_POST['igny8_context_profiles_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_context_profiles_meta_box_nonce'], 'igny8_context_profiles_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_schema_hints', sanitize_textarea_field($_POST['igny8_schema_hints']));
        update_post_meta($post_id, '_igny8_product_facts', sanitize_textarea_field($_POST['igny8_product_facts']));
    }
    
    // Internal Links meta box
    if (isset($_POST['igny8_internal_links_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_internal_links_meta_box_nonce'], 'igny8_internal_links_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_source_page', sanitize_text_field($_POST['igny8_source_page']));
        update_post_meta($post_id, '_igny8_target_page', sanitize_text_field($_POST['igny8_target_page']));
        update_post_meta($post_id, '_igny8_anchor_text', sanitize_text_field($_POST['igny8_anchor_text']));
        update_post_meta($post_id, '_igny8_cluster_reference', sanitize_text_field($_POST['igny8_cluster_reference']));
        update_post_meta($post_id, '_igny8_priority', sanitize_text_field($_POST['igny8_priority']));
        update_post_meta($post_id, '_igny8_status', sanitize_text_field($_POST['igny8_status']));
    }
    
    // Performance Logs meta box
    if (isset($_POST['igny8_performance_logs_meta_box_nonce']) && wp_verify_nonce($_POST['igny8_performance_logs_meta_box_nonce'], 'igny8_performance_logs_meta_box')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        update_post_meta($post_id, '_igny8_cluster_reference', sanitize_text_field($_POST['igny8_cluster_reference']));
        update_post_meta($post_id, '_igny8_generation_count', sanitize_text_field($_POST['igny8_generation_count']));
        update_post_meta($post_id, '_igny8_refresh_count', sanitize_text_field($_POST['igny8_refresh_count']));
        update_post_meta($post_id, '_igny8_linking_density', sanitize_text_field($_POST['igny8_linking_density']));
        update_post_meta($post_id, '_igny8_search_console_data', sanitize_textarea_field($_POST['igny8_search_console_data']));
    }
}
