<?php
/**
 * Igny8 – Content Engine (New) Admin UI
 *
 * This file renders the Content Engine module with modern SaaS layout.
 * It relies on assets/css/modern-admin.css for styling.
 * Keep business logic, queries and AJAX elsewhere; use the provided hooks to inject data.
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Page renderer
 */
function igny8_content_engine_new_admin_page() {
    // Get real data from CPTs
    $total_tasks = wp_count_posts('igny8_content_planner')->publish ?? 0;
    
    // Get tasks by status
    $pending_tasks = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Pending',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $pending_count = count($pending_tasks);
    
    $generated_tasks = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Generated',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $generated_count = count($generated_tasks);
    
    $scheduled_tasks = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Refresh Scheduled',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $scheduled_count = count($scheduled_tasks);
    
    // Context Builder data
    $context_profiles = wp_count_posts('igny8_context_profiles')->publish ?? 0;
    
    // Get distinct voice tones from taxonomy
    $voice_tone_terms = get_terms([
        'taxonomy' => 'igny8_voice_tone',
        'hide_empty' => false
    ]);
    $voice_tones = count($voice_tone_terms);
    
    $templates = $context_profiles; // Same as profiles for now
    
    // Content Generation data - get from variations table
    global $wpdb;
    $total_variations = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}igny8_variations") ?? 0;
    $cache_hits = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}igny8_variations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)") ?? 0;
    $api_calls_today = get_option('igny8_api_calls_today', 0);
    
    // Refresh Schedule data
    $scheduled_refreshes = $scheduled_count; // Same as scheduled tasks
    $running_refreshes = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Queued',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $running_refreshes = count($running_refreshes);
    
    // Get refreshed tasks from last 30 days
    $refreshed_30d = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Published',
                'compare' => '='
            ]
        ],
        'date_query' => [
            [
                'after' => '30 days ago'
            ]
        ],
        'fields' => 'ids'
    ]);
    $refreshed_30d = count($refreshed_30d);
    
    // Internal Linking data
    $suggested_links = get_posts([
        'post_type' => 'igny8_internal_links',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Suggested',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $suggested_links = count($suggested_links);
    
    $approved_links = get_posts([
        'post_type' => 'igny8_internal_links',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Approved',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $approved_links = count($approved_links);
    
    $inserted_links = get_posts([
        'post_type' => 'igny8_internal_links',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_status',
                'value' => 'Inserted',
                'compare' => '='
            ]
        ],
        'fields' => 'ids'
    ]);
    $inserted_links = count($inserted_links);
    
    // Performance data
    $cache_hit_rate = $total_variations > 0 ? round(($cache_hits / $total_variations) * 100) : 0;
    $personalized_posts = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->prefix}igny8_variations") ?? 0;
    $avg_generation_time = get_option('igny8_avg_generation_time', '2.1s');
    
    ?>
    <div class="wrap igny8-modern-admin">
        <h2><?php echo esc_html__('Content Engine', 'igny8'); ?></h2>

        <!-- Tabs -->
        <div class="igny8-tabs" id="igny8-ce-tabs" role="tablist" aria-label="Content Engine tabs">
            <ul class="igny8-tab-nav">
                <li><a href="#ce-planner" class="active"><?php esc_html_e('Planner', 'igny8'); ?></a></li>
                <li><a href="#ce-context"><?php esc_html_e('Context Builder', 'igny8'); ?></a></li>
                <li><a href="#ce-generation"><?php esc_html_e('Content Generation', 'igny8'); ?></a></li>
                <li><a href="#ce-refresh"><?php esc_html_e('Refresh Schedule', 'igny8'); ?></a></li>
                <li><a href="#ce-linking"><?php esc_html_e('Internal Linking', 'igny8'); ?></a></li>
                <li><a href="#ce-performance"><?php esc_html_e('Performance', 'igny8'); ?></a></li>
            </ul>
        </div>

        <div class="igny8-tab-panels">

            <!-- PLANNER -->
            <div id="ce-planner" class="igny8-tab-content active">
                <!-- Metric Cards -->
                <div class="igny8-metric-cards">
    <div class="igny8-metric-card">
        <div class="igny8-metric-value"><?php echo esc_html($total_tasks); ?></div>
        <div class="igny8-metric-label">Total Tasks</div>
        <div class="igny8-metric-change positive">+8%</div>
    </div>
    <div class="igny8-metric-card">
        <div class="igny8-metric-value"><?php echo esc_html($pending_count); ?></div>
        <div class="igny8-metric-label">Pending Tasks</div>
        <div class="igny8-metric-change negative">-5%</div>
    </div>
    <div class="igny8-metric-card">
        <div class="igny8-metric-value"><?php echo esc_html($generated_count); ?></div>
        <div class="igny8-metric-label">Generated Content</div>
        <div class="igny8-metric-change positive">+15%</div>
    </div>
    <div class="igny8-metric-card">
        <div class="igny8-metric-value"><?php echo esc_html($scheduled_count); ?></div>
        <div class="igny8-metric-label">Scheduled Tasks</div>
        <div class="igny8-metric-change positive">+3%</div>
    </div>
</div>


                <!-- Filter Bar -->
                <div class="igny8-filter-bar">
                    <div class="igny8-filter-group">
                        <label>Status</label>
                        <select>
                            <option><?php esc_html_e('All Status','igny8'); ?></option>
                            <option><?php esc_html_e('Pending','igny8'); ?></option>
                            <option><?php esc_html_e('Queued','igny8'); ?></option>
                            <option><?php esc_html_e('Generated','igny8'); ?></option>
                            <option><?php esc_html_e('Published','igny8'); ?></option>
                        </select>
                    </div>
                    <div class="igny8-filter-group">
                        <label>Content Type</label>
                        <select>
                            <option><?php esc_html_e('All Content Type','igny8'); ?></option>
                            <option><?php esc_html_e('Hub Page','igny8'); ?></option>
                            <option><?php esc_html_e('Sub Page','igny8'); ?></option>
                            <option><?php esc_html_e('Blog','igny8'); ?></option>
                            <option><?php esc_html_e('Product Page','igny8'); ?></option>
                            <option><?php esc_html_e('Service Page','igny8'); ?></option>
                        </select>
                    </div>
                    <div class="igny8-filter-group">
                        <label>Sector</label>
                        <select>
                            <option><?php esc_html_e('All Sector','igny8'); ?></option>
                        </select>
                    </div>
                    <div class="igny8-table-search">
                        <input type="search" placeholder="<?php esc_attr_e('Search tasks…','igny8'); ?>">
                    </div>
                    <button class="button igny8-apply-filters"><?php esc_html_e('Apply Filters','igny8'); ?></button>
                    <button class="button igny8-clear-filters"><?php esc_html_e('Clear','igny8'); ?></button>
                </div>

                <!-- Action Bar -->
                <div class="igny8-data-table-header">
                    <div class="igny8-filter-group">
                        <label>Bulk Actions</label>
                        <select>
                            <option>Bulk Actions</option>
                            <option>Delete Selected</option>
                            <option>Change Status</option>
                        </select>
                    </div>
                    <button class="button igny8-add-new" data-type="task"><?php esc_html_e('Add New Task','igny8'); ?></button>
                </div>

                <!-- Data Table -->
                <div class="igny8-data-table-container">
                    <div class="igny8-table-header">
                        <h3 class="igny8-table-title"><?php esc_html_e('Content Tasks','igny8'); ?></h3>
                        <div class="igny8-table-controls">
                            <div class="igny8-table-show">
                                <label>Show</label>
                                <select>
                                    <option value="8">8</option>
                                    <option value="16">16</option>
                                    <option value="32">32</option>
                                </select>
                                <span>entries</span>
                            </div>
                            <div class="igny8-table-search">
                                <input type="search" placeholder="<?php esc_attr_e('Search tasks...','igny8'); ?>">
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
                                    <th class="igny8-sortable"><?php esc_html_e('NO.','igny8'); ?></th>
                                    <th><?php esc_html_e('PLANNED TITLE','igny8'); ?></th>
                                    <th><?php esc_html_e('CONTENT TYPE','igny8'); ?></th>
                                    <th><?php esc_html_e('CLUSTER','igny8'); ?></th>
                                    <th><?php esc_html_e('KEYWORDS','igny8'); ?></th>
                                    <th><?php esc_html_e('STATUS','igny8'); ?></th>
                                    <th><?php esc_html_e('SCHEDULE DATE','igny8'); ?></th>
                                    <th><?php esc_html_e('LINKED PAGE','igny8'); ?></th>
                                    <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                /**
                                 * Hook: output your planner rows here.
                                 * Expect <tr>…</tr> with badges using .igny8-badge.{success|warning|error|info}
                                 */
                                do_action('igny8_ce_planner_rows');
                                ?>
                                <!-- Sample row (remove once real data prints via hook) -->
                                <tr>
                                    <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                    <td>001401</td>
                                    <td><?php esc_html_e('Complete AI Guide','igny8'); ?></td>
                                    <td><?php esc_html_e('Hub Page','igny8'); ?></td>
                                    <td><?php esc_html_e('AI & Machine Learning','igny8'); ?></td>
                                    <td>AI, Machine Learning, Deep Learning</td>
                                    <td>
                                        <span class="igny8-badge yellow"><?php esc_html_e('Pending','igny8'); ?></span>
                                    </td>
                                    <td>15 Dec 2024</td>
                                    <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('AI Guide','igny8'); ?></a></td>
                                    <td>
                                        <select class="igny8-table-select">
                                            <option>Actions</option>
                                            <option>Edit</option>
                                            <option>Delete</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                    <td>001402</td>
                                    <td><?php esc_html_e('SEO Best Practices','igny8'); ?></td>
                                    <td><?php esc_html_e('Blog Post','igny8'); ?></td>
                                    <td><?php esc_html_e('Digital Marketing','igny8'); ?></td>
                                    <td>SEO, Optimization, Rankings</td>
                                    <td>
                                        <span class="igny8-badge green"><?php esc_html_e('Published','igny8'); ?></span>
                                    </td>
                                    <td>10 Dec 2024</td>
                                    <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('SEO Guide','igny8'); ?></a></td>
                                    <td>
                                        <select class="igny8-table-select">
                                            <option>Actions</option>
                                            <option>Edit</option>
                                            <option>Delete</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                    <td>001403</td>
                                    <td><?php esc_html_e('UX Design Principles','igny8'); ?></td>
                                    <td><?php esc_html_e('Guide','igny8'); ?></td>
                                    <td><?php esc_html_e('Design & UX','igny8'); ?></td>
                                    <td>UX, Design, User Experience</td>
                                    <td>
                                        <span class="igny8-badge purple"><?php esc_html_e('Due in 2 Weeks','igny8'); ?></span>
                                    </td>
                                    <td>22 Dec 2024</td>
                                    <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('UX Guide','igny8'); ?></a></td>
                                    <td>
                                        <select class="igny8-table-select">
                                            <option>Actions</option>
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
                            <?php esc_html_e('Showing 1 to 3 of 16 entries','igny8'); ?>
                        </div>
                        <div class="igny8-table-pagination-controls">
                            <button class="igny8-table-page-btn" disabled><</button>
                            <span class="igny8-table-page-btn active">1</span>
                            <button class="igny8-table-page-btn">2</button>
                            <button class="igny8-table-page-btn">3</button>
                            <button class="igny8-table-page-btn">4</button>
                            <button class="igny8-table-page-btn">5</button>
                            <button class="igny8-table-page-btn">></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTEXT BUILDER -->
            <div id="ce-context" class="igny8-tab-content">
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($context_profiles); ?></div>
                        <div class="igny8-metric-label">Context Profiles</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($voice_tones); ?></div>
                        <div class="igny8-metric-label">Voice Tones</div>
                        <div class="igny8-metric-change positive">+2%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($templates); ?></div>
                        <div class="igny8-metric-label">Templates</div>
                        <div class="igny8-metric-change positive">+8%</div>
                    </div>
                </div>

                <!-- Profiles Grid -->
                <div class="igny8-filter-bar">
                    <div class="igny8-table-search">
                        <input type="search" placeholder="<?php esc_attr_e('Search profiles…','igny8'); ?>">
                    </div>
                    <button class="button igny8-add-new"><?php esc_html_e('Add Profile','igny8'); ?></button>
                </div>

                <div class="igny8-dashboard-section">
                    <div class="igny8-section-header">
                        <h3 class="igny8-section-title">Context Profiles</h3>
                    </div>
                    <div class="igny8-section-content">
                        <?php
                        /**
                         * Hook: print card items for context profiles
                         */
                        do_action('igny8_ce_context_cards');
                        ?>
                        <!-- Sample profile card -->
                        <div class="igny8-metric-card" style="margin-bottom: 16px;">
                            <div class="igny8-metric-card-header">
                                <div class="igny8-metric-card-icon blue">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 6h16M4 12h10M4 18h6" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="igny8-metric-card-value" style="font-size: 18px; margin-bottom: 8px;"><?php esc_html_e('Product Review – Conversational','igny8'); ?></div>
                            <div class="igny8-metric-card-label" style="margin-bottom: 8px;">
                                <span class="igny8-badge info"><?php esc_html_e('Friendly','igny8'); ?></span>
                                <span class="igny8-badge success"><?php esc_html_e('Product','igny8'); ?></span>
                            </div>
                            <div class="igny8-metric-card-label" style="margin-bottom: 12px; font-style: italic;"><?php esc_html_e('"Use a helpful tone, cite specs if available, keep paragraphs short."','igny8'); ?></div>
                            <div style="display: flex; gap: 8px;">
                                <button class="igny8-btn-icon" title="<?php esc_attr_e('Edit','igny8'); ?>">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </button>
                                <button class="igny8-btn-icon igny8-btn-danger" title="<?php esc_attr_e('Delete','igny8'); ?>">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENT GENERATION -->
            <div id="ce-generation" class="igny8-tab-content">
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($total_variations); ?></div>
                        <div class="igny8-metric-label">Total Variations</div>
                        <div class="igny8-metric-change positive">+12%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($cache_hits); ?></div>
                        <div class="igny8-metric-label">Cache Hits</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($api_calls_today); ?></div>
                        <div class="igny8-metric-label">API Calls Today</div>
                        <div class="igny8-metric-change negative">-3%</div>
                    </div>
                </div>

                <div class="igny8-filter-bar">
                    <div class="igny8-filter-group">
                        <label>Post Type</label>
                        <select>
                            <option><?php esc_html_e('All Post Types','igny8'); ?></option>
                        </select>
                    </div>
                    <div class="igny8-table-search">
                        <input type="search" placeholder="<?php esc_attr_e('Search by title/ID…','igny8'); ?>">
                    </div>
                    <button class="button igny8-apply-filters"><?php esc_html_e('Generate Selected','igny8'); ?></button>
                    <button class="button igny8-clear-filters"><?php esc_html_e('Clear','igny8'); ?></button>
                </div>

                <table class="igny8-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Post','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Type','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Status','igny8'); ?></th>
                            <th class="right"><?php esc_html_e('Last Run','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Actions','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('igny8_ce_generation_rows'); ?>
                        <tr>
                            <td><?php esc_html_e('AI in E-commerce: 2025 Outlook','igny8'); ?></td>
                            <td class="center"><?php esc_html_e('Post','igny8'); ?></td>
                            <td class="center"><span class="igny8-badge blue"><?php esc_html_e('Queued','igny8'); ?></span></td>
                            <td class="right">2024-12-10</td>
                            <td class="center">
                                <button class="igny8-btn-icon edit" title="<?php esc_attr_e('Run now','igny8'); ?>">▶</button>
                                <button class="igny8-btn-icon delete" title="<?php esc_attr_e('Remove','igny8'); ?>">🗑</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- REFRESH SCHEDULE -->
            <div id="ce-refresh" class="igny8-tab-content">
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($scheduled_refreshes); ?></div>
                        <div class="igny8-metric-label">Scheduled</div>
                        <div class="igny8-metric-change positive">+3%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($running_refreshes); ?></div>
                        <div class="igny8-metric-label">Running</div>
                        <div class="igny8-metric-change positive">+2%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($refreshed_30d); ?></div>
                        <div class="igny8-metric-label">Refreshed (30d)</div>
                        <div class="igny8-metric-change positive">+9%</div>
                    </div>
                </div>

                <table class="igny8-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Post','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Interval','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Status','igny8'); ?></th>
                            <th class="right"><?php esc_html_e('Next Run','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Actions','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('igny8_ce_refresh_rows'); ?>
                        <tr>
                            <td><?php esc_html_e('Complete AI Guide','igny8'); ?></td>
                            <td class="center"><?php esc_html_e('Every 30 days','igny8'); ?></td>
                            <td class="center"><span class="igny8-badge green"><?php esc_html_e('Active','igny8'); ?></span></td>
                            <td class="right">2025-01-12</td>
                            <td class="center">
                                <button class="igny8-btn-icon edit" title="<?php esc_attr_e('Edit schedule','igny8'); ?>">✏️</button>
                                <button class="igny8-btn-icon delete" title="<?php esc_attr_e('Disable','igny8'); ?>">🗑</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- INTERNAL LINKING -->
            <div id="ce-linking" class="igny8-tab-content">
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($suggested_links); ?></div>
                        <div class="igny8-metric-label">Suggested Links</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($approved_links); ?></div>
                        <div class="igny8-metric-label">Approved</div>
                        <div class="igny8-metric-change positive">+3%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($inserted_links); ?></div>
                        <div class="igny8-metric-label">Inserted</div>
                        <div class="igny8-metric-change positive">+4%</div>
                    </div>
                </div>

                <table class="igny8-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Source','igny8'); ?></th>
                            <th><?php esc_html_e('Target','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Anchor','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Type','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Status','igny8'); ?></th>
                            <th class="center"><?php esc_html_e('Actions','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('igny8_ce_link_rows'); ?>
                        <tr>
                            <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('AI Guide','igny8'); ?></a></td>
                            <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('Machine Learning Basics','igny8'); ?></a></td>
                            <td class="center"><?php esc_html_e('learn more about ML','igny8'); ?></td>
                            <td class="center"><span class="igny8-badge purple"><?php esc_html_e('Upward','igny8'); ?></span></td>
                            <td class="center"><span class="igny8-badge yellow"><?php esc_html_e('Suggested','igny8'); ?></span></td>
                            <td class="center">
                                <button class="igny8-btn-icon edit" title="<?php esc_attr_e('Approve','igny8'); ?>">✔</button>
                                <button class="igny8-btn-icon delete" title="<?php esc_attr_e('Reject','igny8'); ?>">✖</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Optional network graph placeholder -->
                <div style="margin-top:20px; background:#fff; border-radius:8px; padding:16px; box-shadow:0 2px 4px rgba(0,0,0,.05);">
                    <strong><?php esc_html_e('Link Graph (placeholder)','igny8'); ?></strong>
                    <div style="height:220px; background:#f9fafb; border:1px dashed #e5e7eb; border-radius:6px; margin-top:10px;"></div>
                </div>
            </section>

            <!-- PERFORMANCE -->
            <div id="ce-performance" class="igny8-tab-content">
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($cache_hit_rate); ?>%</div>
                        <div class="igny8-metric-label">Cache Hit Rate</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($personalized_posts); ?></div>
                        <div class="igny8-metric-label">Personalized Posts</div>
                        <div class="igny8-metric-change positive">+18%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value"><?php echo esc_html($avg_generation_time); ?></div>
                        <div class="igny8-metric-label">Avg Generation Time</div>
                        <div class="igny8-metric-change negative">-6%</div>
                    </div>
                </div>

                <table class="igny8-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Cluster','igny8'); ?></th>
                            <th class="right"><?php esc_html_e('Impressions','igny8'); ?></th>
                            <th class="right"><?php esc_html_e('CTR','igny8'); ?></th>
                            <th class="right"><?php esc_html_e('Avg Position','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('igny8_ce_performance_rows'); ?>
                        <tr>
                            <td><?php esc_html_e('AI & Machine Learning','igny8'); ?></td>
                            <td class="right">48,632</td>
                            <td class="right">3.9%</td>
                            <td class="right">14.2</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div><!-- /.igny8-tab-panels -->
        
        <!-- Add New Drawer -->
        <div id="igny8-add-drawer" class="igny8-drawer">
            <div class="igny8-drawer-overlay"></div>
            <div class="igny8-drawer-content">
                <div class="igny8-drawer-header">
                    <h3 id="igny8-drawer-title"><?php esc_html_e('Add New Record', 'igny8'); ?></h3>
                    <button class="igny8-drawer-close">&times;</button>
                </div>
                <div class="igny8-drawer-body">
                    <form id="igny8-add-form">
                        <div id="igny8-form-content">
                            <!-- Dynamic form content will be loaded here -->
                        </div>
                        <div class="igny8-drawer-footer">
                            <button type="button" class="button igny8-btn-secondary igny8-drawer-close"><?php esc_html_e('Cancel', 'igny8'); ?></button>
                            <button type="submit" class="button igny8-btn-primary"><?php esc_html_e('Save', 'igny8'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Edit Drawer -->
        <div id="igny8-edit-drawer" class="igny8-drawer">
            <div class="igny8-drawer-overlay"></div>
            <div class="igny8-drawer-content">
                <div class="igny8-drawer-header">
                    <h3 id="igny8-edit-drawer-title"><?php esc_html_e('Edit Record', 'igny8'); ?></h3>
                    <button class="igny8-drawer-close">&times;</button>
                </div>
                <div class="igny8-drawer-body">
                    <form id="igny8-edit-form">
                        <div id="igny8-edit-form-content">
                            <!-- Dynamic form content will be loaded here -->
                        </div>
                        <div class="igny8-drawer-footer">
                            <button type="button" class="button igny8-btn-secondary igny8-drawer-close"><?php esc_html_e('Cancel', 'igny8'); ?></button>
                            <button type="submit" class="button igny8-btn-primary"><?php esc_html_e('Update', 'igny8'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.wrap -->
  
    <?php
}
