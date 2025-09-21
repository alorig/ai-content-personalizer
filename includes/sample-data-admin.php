<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 SAMPLE DATA ADMIN
  Description: Admin page for inserting sample data
==================================================*/

/**
 * Add sample data admin page
 */
function igny8_add_sample_data_page() {
    add_submenu_page(
        'igny8-dashboard',
        'Sample Data',
        'Sample Data',
        'manage_options',
        'igny8-sample-data',
        'igny8_sample_data_page'
    );
}
add_action('admin_menu', 'igny8_add_sample_data_page');

/**
 * Sample data page
 */
function igny8_sample_data_page() {
    if (isset($_POST['insert_sample_data']) && wp_verify_nonce($_POST['igny8_sample_data_nonce'], 'igny8_sample_data')) {
        insert_sample_data();
    }
    
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Sample Data Management', 'igny8'); ?></h1>
        
        <div class="igny8-card">
            <div class="igny8-card-header">
                <h3><?php esc_html_e('Insert Sample Data', 'igny8'); ?></h3>
            </div>
            <div class="igny8-card-content">
                <p><?php esc_html_e('This will insert sample data into all CPTs and taxonomies for testing purposes.', 'igny8'); ?></p>
                
                <form method="post">
                    <?php wp_nonce_field('igny8_sample_data', 'igny8_sample_data_nonce'); ?>
                    <button type="submit" name="insert_sample_data" class="button button-primary">
                        <?php esc_html_e('Insert Sample Data', 'igny8'); ?>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="igny8-card">
            <div class="igny8-card-header">
                <h3><?php esc_html_e('Current Data Status', 'igny8'); ?></h3>
            </div>
            <div class="igny8-card-content">
                <?php display_data_status(); ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Insert sample data
 */
function insert_sample_data() {
    echo '<div class="notice notice-info"><p><strong>Inserting sample data...</strong></p></div>';
    
    // Create taxonomy terms
    create_taxonomy_terms();
    
    // Create sample posts
    create_sample_keywords();
    create_sample_clusters();
    create_sample_tasks();
    create_sample_profiles();
    create_sample_links();
    create_sample_performance_logs();
    
    echo '<div class="notice notice-success"><p><strong>✅ Sample data insertion complete!</strong></p></div>';
}

/**
 * Create taxonomy terms
 */
function create_taxonomy_terms() {
    echo '<h3>Creating Taxonomy Terms</h3>';
    
    // Create Sector terms
    $sectors = [
        'technology' => 'Technology',
        'healthcare' => 'Healthcare', 
        'finance' => 'Finance',
        'education' => 'Education',
        'marketing' => 'Marketing'
    ];
    
    foreach ($sectors as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_sector', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created sector: $name<br>";
        }
    }
    
    // Create Intent terms
    $intents = [
        'informational' => 'Informational',
        'transactional' => 'Transactional',
        'commercial' => 'Commercial',
        'navigational' => 'Navigational'
    ];
    
    foreach ($intents as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_intent', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created intent: $name<br>";
        }
    }
    
    // Create Content Type terms
    $content_types = [
        'hub-page' => 'Hub Page',
        'sub-page' => 'Sub Page',
        'blog' => 'Blog',
        'product-page' => 'Product Page',
        'service-page' => 'Service Page'
    ];
    
    foreach ($content_types as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_content_type', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created content type: $name<br>";
        }
    }
    
    // Create Voice Tone terms
    $voice_tones = [
        'professional' => 'Professional',
        'casual' => 'Casual',
        'friendly' => 'Friendly',
        'authoritative' => 'Authoritative',
        'conversational' => 'Conversational'
    ];
    
    foreach ($voice_tones as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_voice_tone', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created voice tone: $name<br>";
        }
    }
    
    // Create Tags terms
    $tags = [
        'product' => 'Product',
        'service' => 'Service',
        'faq' => 'FAQ',
        'schema' => 'Schema',
        'blog' => 'Blog',
        'guide' => 'Guide'
    ];
    
    foreach ($tags as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_tags', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created tag: $name<br>";
        }
    }
    
    // Create Link Type terms
    $link_types = [
        'upward' => 'Upward',
        'downward' => 'Downward',
        'horizontal' => 'Horizontal'
    ];
    
    foreach ($link_types as $slug => $name) {
        $term = wp_insert_term($name, 'igny8_link_type', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            echo "✓ Created link type: $name<br>";
        }
    }
}

/**
 * Create sample keywords
 */
function create_sample_keywords() {
    echo '<h3>Creating Sample Keywords</h3>';
    
    $keywords_data = [
        [
            'title' => 'AI Marketing Tools',
            'search_volume' => 1250,
            'difficulty' => 45,
            'cpc' => 2.50,
            'intent' => 'informational',
            'sector' => 'technology'
        ],
        [
            'title' => 'SEO Best Practices 2024',
            'search_volume' => 890,
            'difficulty' => 38,
            'cpc' => 1.80,
            'intent' => 'informational',
            'sector' => 'marketing'
        ],
        [
            'title' => 'Digital Marketing Agency',
            'search_volume' => 2100,
            'difficulty' => 52,
            'cpc' => 3.20,
            'intent' => 'commercial',
            'sector' => 'marketing'
        ],
        [
            'title' => 'Content Marketing Strategy',
            'search_volume' => 1560,
            'difficulty' => 41,
            'cpc' => 2.10,
            'intent' => 'informational',
            'sector' => 'marketing'
        ]
    ];
    
    foreach ($keywords_data as $keyword_data) {
        $post_id = wp_insert_post([
            'post_title' => $keyword_data['title'],
            'post_type' => 'igny8_keywords',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_search_volume', $keyword_data['search_volume']);
            update_post_meta($post_id, '_igny8_difficulty_level', $keyword_data['difficulty']);
            update_post_meta($post_id, '_igny8_cpc', $keyword_data['cpc']);
            
            // Add taxonomy terms
            wp_set_post_terms($post_id, [$keyword_data['intent']], 'igny8_intent');
            wp_set_post_terms($post_id, [$keyword_data['sector']], 'igny8_sector');
            
            echo "✓ Created keyword: {$keyword_data['title']}<br>";
        }
    }
}

/**
 * Create sample clusters
 */
function create_sample_clusters() {
    echo '<h3>Creating Sample Clusters</h3>';
    
    $clusters_data = [
        [
            'title' => 'AI & Machine Learning',
            'page_title' => 'Complete AI Guide for Marketers',
            'target_url' => 'https://example.com/ai-guide',
            'priority' => 'high',
            'sector' => 'technology'
        ],
        [
            'title' => 'Digital Marketing Fundamentals',
            'page_title' => 'Digital Marketing 101',
            'target_url' => 'https://example.com/digital-marketing',
            'priority' => 'medium',
            'sector' => 'marketing'
        ],
        [
            'title' => 'Content Strategy Hub',
            'page_title' => 'Content Marketing Mastery',
            'target_url' => 'https://example.com/content-strategy',
            'priority' => 'high',
            'sector' => 'marketing'
        ],
        [
            'title' => 'SEO Optimization Center',
            'page_title' => 'Advanced SEO Techniques',
            'target_url' => 'https://example.com/seo-optimization',
            'priority' => 'medium',
            'sector' => 'marketing'
        ]
    ];
    
    foreach ($clusters_data as $cluster_data) {
        $post_id = wp_insert_post([
            'post_title' => $cluster_data['title'],
            'post_type' => 'igny8_clusters',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_cluster_page_title', $cluster_data['page_title']);
            update_post_meta($post_id, '_igny8_target_url', $cluster_data['target_url']);
            update_post_meta($post_id, '_igny8_priority', $cluster_data['priority']);
            
            // Add taxonomy terms
            wp_set_post_terms($post_id, [$cluster_data['sector']], 'igny8_sector');
            
            echo "✓ Created cluster: {$cluster_data['title']}<br>";
        }
    }
}

/**
 * Create sample content planner tasks
 */
function create_sample_tasks() {
    echo '<h3>Creating Sample Content Tasks</h3>';
    
    $tasks_data = [
        [
            'title' => 'AI Marketing Tools Comparison Guide',
            'content_type' => 'hub-page',
            'status' => 'pending',
            'schedule_date' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'refresh_days' => 30
        ],
        [
            'title' => 'SEO Checklist for 2024',
            'content_type' => 'blog',
            'status' => 'generated',
            'schedule_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'refresh_days' => 45
        ],
        [
            'title' => 'Digital Marketing ROI Calculator',
            'content_type' => 'product-page',
            'status' => 'queued',
            'schedule_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'refresh_days' => 60
        ],
        [
            'title' => 'Content Marketing Trends Report',
            'content_type' => 'sub-page',
            'status' => 'refresh-scheduled',
            'schedule_date' => date('Y-m-d H:i:s', strtotime('+14 days')),
            'refresh_days' => 90
        ]
    ];
    
    foreach ($tasks_data as $task_data) {
        $post_id = wp_insert_post([
            'post_title' => $task_data['title'],
            'post_type' => 'igny8_content_planner',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_status', $task_data['status']);
            update_post_meta($post_id, '_igny8_schedule_date', $task_data['schedule_date']);
            update_post_meta($post_id, '_igny8_refresh_after_days', $task_data['refresh_days']);
            
            // Add taxonomy terms
            wp_set_post_terms($post_id, [$task_data['content_type']], 'igny8_content_type');
            
            echo "✓ Created task: {$task_data['title']}<br>";
        }
    }
}

/**
 * Create sample context profiles
 */
function create_sample_profiles() {
    echo '<h3>Creating Sample Context Profiles</h3>';
    
    $profiles_data = [
        [
            'title' => 'Professional Marketing Guide',
            'content' => 'Create comprehensive, professional content that demonstrates expertise in marketing strategies and best practices.',
            'schema_hints' => '{"@type": "HowTo", "name": "Marketing Guide"}',
            'voice_tone' => 'professional',
            'tags' => ['guide', 'marketing']
        ],
        [
            'title' => 'Casual Tech Blog',
            'content' => 'Write in a casual, friendly tone that makes complex technology topics accessible to everyday users.',
            'schema_hints' => '{"@type": "Article", "headline": "Tech Blog Post"}',
            'voice_tone' => 'casual',
            'tags' => ['blog', 'technology']
        ],
        [
            'title' => 'Authoritative SEO Content',
            'content' => 'Produce authoritative content that establishes thought leadership in SEO and digital marketing.',
            'schema_hints' => '{"@type": "Article", "author": "SEO Expert"}',
            'voice_tone' => 'authoritative',
            'tags' => ['seo', 'guide']
        ],
        [
            'title' => 'Friendly Product Descriptions',
            'content' => 'Write product descriptions that are warm, approachable, and highlight benefits in a conversational way.',
            'schema_hints' => '{"@type": "Product", "description": "Product Info"}',
            'voice_tone' => 'friendly',
            'tags' => ['product', 'service']
        ]
    ];
    
    foreach ($profiles_data as $profile_data) {
        $post_id = wp_insert_post([
            'post_title' => $profile_data['title'],
            'post_content' => $profile_data['content'],
            'post_type' => 'igny8_context_profiles',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_schema_hints', $profile_data['schema_hints']);
            
            // Add taxonomy terms
            wp_set_post_terms($post_id, [$profile_data['voice_tone']], 'igny8_voice_tone');
            wp_set_post_terms($post_id, $profile_data['tags'], 'igny8_tags');
            
            echo "✓ Created profile: {$profile_data['title']}<br>";
        }
    }
}

/**
 * Create sample internal links
 */
function create_sample_links() {
    echo '<h3>Creating Sample Internal Links</h3>';
    
    $links_data = [
        [
            'title' => 'AI Guide → Marketing Tools',
            'anchor_text' => 'best AI marketing tools',
            'link_type' => 'downward',
            'status' => 'suggested',
            'priority' => 8
        ],
        [
            'title' => 'SEO Guide → Content Strategy',
            'anchor_text' => 'content optimization',
            'link_type' => 'horizontal',
            'status' => 'approved',
            'priority' => 6
        ],
        [
            'title' => 'Marketing Hub → AI Guide',
            'anchor_text' => 'artificial intelligence',
            'link_type' => 'upward',
            'status' => 'inserted',
            'priority' => 9
        ],
        [
            'title' => 'Blog Post → Product Page',
            'anchor_text' => 'try our tool',
            'link_type' => 'downward',
            'status' => 'suggested',
            'priority' => 7
        ]
    ];
    
    foreach ($links_data as $link_data) {
        $post_id = wp_insert_post([
            'post_title' => $link_data['title'],
            'post_type' => 'igny8_internal_links',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_anchor_text', $link_data['anchor_text']);
            update_post_meta($post_id, '_igny8_status', $link_data['status']);
            update_post_meta($post_id, '_igny8_priority', $link_data['priority']);
            
            // Add taxonomy terms
            wp_set_post_terms($post_id, [$link_data['link_type']], 'igny8_link_type');
            
            echo "✓ Created link: {$link_data['title']}<br>";
        }
    }
}

/**
 * Create sample performance logs
 */
function create_sample_performance_logs() {
    echo '<h3>Creating Sample Performance Logs</h3>';
    
    $logs_data = [
        [
            'title' => 'AI & Machine Learning - 2024-12-15',
            'generation_count' => 15,
            'refresh_count' => 3,
            'linking_density' => 0.85
        ],
        [
            'title' => 'Digital Marketing Fundamentals - 2024-12-14',
            'generation_count' => 22,
            'refresh_count' => 5,
            'linking_density' => 0.92
        ],
        [
            'title' => 'Content Strategy Hub - 2024-12-13',
            'generation_count' => 18,
            'refresh_count' => 2,
            'linking_density' => 0.78
        ],
        [
            'title' => 'SEO Optimization Center - 2024-12-12',
            'generation_count' => 12,
            'refresh_count' => 4,
            'linking_density' => 0.88
        ]
    ];
    
    foreach ($logs_data as $log_data) {
        $post_id = wp_insert_post([
            'post_title' => $log_data['title'],
            'post_type' => 'igny8_performance_logs',
            'post_status' => 'publish'
        ]);
        
        if ($post_id) {
            // Add meta fields
            update_post_meta($post_id, '_igny8_generation_count', $log_data['generation_count']);
            update_post_meta($post_id, '_igny8_refresh_count', $log_data['refresh_count']);
            update_post_meta($post_id, '_igny8_linking_density', $log_data['linking_density']);
            
            // Add sample search console data
            $search_console_data = [
                'impressions' => rand(1000, 5000),
                'ctr' => rand(2, 8) / 100,
                'avg_position' => rand(5, 25)
            ];
            update_post_meta($post_id, '_igny8_search_console_data', json_encode($search_console_data));
            
            echo "✓ Created performance log: {$log_data['title']}<br>";
        }
    }
}

/**
 * Display current data status
 */
function display_data_status() {
    $cpts = [
        'igny8_keywords' => 'Keywords',
        'igny8_clusters' => 'Clusters',
        'igny8_content_planner' => 'Content Tasks',
        'igny8_context_profiles' => 'Context Profiles',
        'igny8_internal_links' => 'Internal Links',
        'igny8_performance_logs' => 'Performance Logs'
    ];
    
    $taxonomies = [
        'igny8_sector' => 'Sectors',
        'igny8_intent' => 'Intents',
        'igny8_content_type' => 'Content Types',
        'igny8_voice_tone' => 'Voice Tones',
        'igny8_tags' => 'Tags',
        'igny8_link_type' => 'Link Types'
    ];
    
    echo '<h4>CPT Counts:</h4>';
    foreach ($cpts as $cpt => $name) {
        $count = wp_count_posts($cpt)->publish ?? 0;
        echo "<strong>$name:</strong> $count posts<br>";
    }
    
    echo '<h4>Taxonomy Counts:</h4>';
    foreach ($taxonomies as $taxonomy => $name) {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        $count = is_wp_error($terms) ? 0 : count($terms);
        echo "<strong>$name:</strong> $count terms<br>";
    }
}
