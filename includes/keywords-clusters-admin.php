<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 KEYWORDS & CLUSTERS ADMIN
  Description: Keywords & Clusters page for Igny8 plugin
==================================================*/

/**
 * Keywords & Clusters module page
 */
function igny8_keywords_clusters_page() {
    // Get real data from CPTs
    $total_keywords = wp_count_posts('igny8_keywords')->publish ?? 0;
    $total_clusters = wp_count_posts('igny8_clusters')->publish ?? 0;
    
    // Get keywords with cluster relations
    $keywords_with_clusters = get_posts([
        'post_type' => 'igny8_keywords',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_cluster_relation',
                'compare' => 'EXISTS'
            ]
        ],
        'fields' => 'ids'
    ]);
    
    $mapped_keywords_count = count($keywords_with_clusters);
    $mapped_percentage = $total_keywords > 0 ? round(($mapped_keywords_count / $total_keywords) * 100) : 0;
    
    // Get high volume keywords (search volume > 1000)
    $high_volume_keywords = get_posts([
        'post_type' => 'igny8_keywords',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_igny8_search_volume',
                'value' => 1000,
                'compare' => '>',
                'type' => 'NUMERIC'
            ]
        ],
        'fields' => 'ids'
    ]);
    
    $high_volume = count($high_volume_keywords);
    $unmapped_keywords = $total_keywords - $mapped_keywords_count;
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Keywords & Clusters','igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#keywords-clusters-keywords" class="active">Keywords</a></li>
            <li><a href="#keywords-clusters-clusters">Clusters</a></li>
            <li><a href="#keywords-clusters-insights">Insights</a></li>
        </ul>
        
        <!-- Keywords Tab -->
        <div id="keywords-clusters-keywords" class="igny8-tab-content active">
            <!-- Metric Cards -->
            <div class="igny8-metric-cards">
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($total_keywords); ?></div>
                    <div class="igny8-metric-label">Total Keywords</div>
                    <div class="igny8-metric-change positive">+12%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($mapped_percentage); ?>%</div>
                    <div class="igny8-metric-label">Mapped to Clusters</div>
                    <div class="igny8-metric-change positive">+5%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($high_volume); ?></div>
                    <div class="igny8-metric-label">High Volume Keywords</div>
                    <div class="igny8-metric-change positive">+8%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($unmapped_keywords); ?></div>
                    <div class="igny8-metric-label">Unmapped Keywords</div>
                    <div class="igny8-metric-change negative">-15%</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="igny8-filter-bar">
                <div class="igny8-filter-group">
                    <label>Sector</label>
                    <select>
                        <option><?php esc_html_e('All Sectors','igny8'); ?></option>
                        <option><?php esc_html_e('Technology','igny8'); ?></option>
                        <option><?php esc_html_e('Healthcare','igny8'); ?></option>
                        <option><?php esc_html_e('Finance','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-filter-group">
                    <label>Intent</label>
                    <select>
                        <option><?php esc_html_e('All Intent','igny8'); ?></option>
                        <option><?php esc_html_e('Informational','igny8'); ?></option>
                        <option><?php esc_html_e('Transactional','igny8'); ?></option>
                        <option><?php esc_html_e('Commercial','igny8'); ?></option>
                        <option><?php esc_html_e('Navigational','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-filter-group">
                    <label>Volume Range</label>
                    <select>
                        <option><?php esc_html_e('All Volumes','igny8'); ?></option>
                        <option><?php esc_html_e('High (1000+)','igny8'); ?></option>
                        <option><?php esc_html_e('Medium (100-999)','igny8'); ?></option>
                        <option><?php esc_html_e('Low (1-99)','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-table-search">
                    <input type="search" placeholder="<?php esc_attr_e('Search keywords...','igny8'); ?>">
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
                        <option>Assign to Cluster</option>
                        <option>Export Selected</option>
                        <option>Delete Selected</option>
                    </select>
                </div>
                <button class="button igny8-add-new" data-type="keyword"><?php esc_html_e('Add Keyword','igny8'); ?></button>
            </div>

            <!-- Data Table -->
            <div class="igny8-data-table-container">
                <div class="igny8-table-header">
                    <h3 class="igny8-table-title"><?php esc_html_e('Keywords','igny8'); ?></h3>
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
                            <input type="search" placeholder="<?php esc_attr_e('Search keywords...','igny8'); ?>">
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
                                <th class="igny8-sortable"><?php esc_html_e('KEYWORD','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('SEARCH VOLUME','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('KD','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('CPC','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('INTENT','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('SECTOR','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('CLUSTER','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('LINKED PAGE','igny8'); ?></th>
                                <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                <td>000001</td>
                                <td><?php esc_html_e('AI Marketing','igny8'); ?></td>
                                <td>1,250</td>
                                <td>45</td>
                                <td>$2.50</td>
                                <td><span class="igny8-badge blue"><?php esc_html_e('Informational','igny8'); ?></span></td>
                                <td><?php esc_html_e('Technology','igny8'); ?></td>
                                <td><?php esc_html_e('AI & Machine Learning','igny8'); ?></td>
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
                                <td>000002</td>
                                <td><?php esc_html_e('SEO Best Practices','igny8'); ?></td>
                                <td>890</td>
                                <td>38</td>
                                <td>$1.80</td>
                                <td><span class="igny8-badge green"><?php esc_html_e('Commercial','igny8'); ?></span></td>
                                <td><?php esc_html_e('Marketing','igny8'); ?></td>
                                <td><?php esc_html_e('Digital Marketing','igny8'); ?></td>
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
                                <td>000003</td>
                                <td><?php esc_html_e('UX Design Principles','igny8'); ?></td>
                                <td>650</td>
                                <td>42</td>
                                <td>$2.20</td>
                                <td><span class="igny8-badge purple"><?php esc_html_e('Navigational','igny8'); ?></span></td>
                                <td><?php esc_html_e('Design','igny8'); ?></td>
                                <td><?php esc_html_e('Design & UX','igny8'); ?></td>
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
                        <?php esc_html_e('Showing 1 to 3 of 25 entries','igny8'); ?>
                    </div>
                    <div class="igny8-table-pagination-controls">
                        <button class="igny8-table-page-btn" disabled><</button>
                        <span class="igny8-table-page-btn active">1</span>
                        <button class="igny8-table-page-btn">2</button>
                        <button class="igny8-table-page-btn">3</button>
                        <button class="igny8-table-page-btn">></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clusters Tab -->
        <div id="keywords-clusters-clusters" class="igny8-tab-content">
            <!-- Metric Cards -->
            <div class="igny8-metric-cards">
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($total_clusters); ?></div>
                    <div class="igny8-metric-label">Total Clusters</div>
                    <div class="igny8-metric-change positive">+3%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($mapped_percentage); ?>%</div>
                    <div class="igny8-metric-label">Coverage Rate</div>
                    <div class="igny8-metric-change positive">+5%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($total_clusters > 0 ? round($mapped_keywords_count / $total_clusters) : 0); ?></div>
                    <div class="igny8-metric-label">Avg Keywords per Cluster</div>
                    <div class="igny8-metric-change positive">+8%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php 
                        $high_priority_clusters = get_posts([
                            'post_type' => 'igny8_clusters',
                            'post_status' => 'publish',
                            'meta_query' => [
                                [
                                    'key' => '_igny8_priority',
                                    'value' => 'high',
                                    'compare' => '='
                                ]
                            ],
                            'fields' => 'ids'
                        ]);
                        echo esc_html(count($high_priority_clusters));
                    ?></div>
                    <div class="igny8-metric-label">High Priority Clusters</div>
                    <div class="igny8-metric-change negative">-2%</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="igny8-filter-bar">
                <div class="igny8-filter-group">
                    <label>Sector</label>
                    <select>
                        <option><?php esc_html_e('All Sectors','igny8'); ?></option>
                        <option><?php esc_html_e('Technology','igny8'); ?></option>
                        <option><?php esc_html_e('Healthcare','igny8'); ?></option>
                        <option><?php esc_html_e('Finance','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-filter-group">
                    <label>Priority</label>
                    <select>
                        <option><?php esc_html_e('All Priorities','igny8'); ?></option>
                        <option><?php esc_html_e('High','igny8'); ?></option>
                        <option><?php esc_html_e('Medium','igny8'); ?></option>
                        <option><?php esc_html_e('Low','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-table-search">
                    <input type="search" placeholder="<?php esc_attr_e('Search clusters...','igny8'); ?>">
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
                        <option>Change Priority</option>
                        <option>Export Selected</option>
                        <option>Delete Selected</option>
                    </select>
                </div>
                <button class="button igny8-add-new"><?php esc_html_e('Add Cluster','igny8'); ?></button>
            </div>

            <!-- Data Table -->
            <div class="igny8-data-table-container">
                <div class="igny8-table-header">
                    <h3 class="igny8-table-title"><?php esc_html_e('Clusters','igny8'); ?></h3>
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
                            <input type="search" placeholder="<?php esc_attr_e('Search clusters...','igny8'); ?>">
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
                                <th class="igny8-sortable"><?php esc_html_e('CLUSTER NAME','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('CLUSTER PAGE TITLE','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('SECTOR','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('KEYWORD COUNT','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('PRIORITY','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('TARGET URL','igny8'); ?></th>
                                <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                <td>000001</td>
                                <td><?php esc_html_e('AI & Machine Learning','igny8'); ?></td>
                                <td><?php esc_html_e('Complete AI Guide','igny8'); ?></td>
                                <td><?php esc_html_e('Technology','igny8'); ?></td>
                                <td>45</td>
                                <td><span class="igny8-badge red"><?php esc_html_e('High','igny8'); ?></span></td>
                                <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('/ai-guide','igny8'); ?></a></td>
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
                                <td>000002</td>
                                <td><?php esc_html_e('Digital Marketing','igny8'); ?></td>
                                <td><?php esc_html_e('SEO Best Practices','igny8'); ?></td>
                                <td><?php esc_html_e('Marketing','igny8'); ?></td>
                                <td>32</td>
                                <td><span class="igny8-badge yellow"><?php esc_html_e('Medium','igny8'); ?></span></td>
                                <td><a href="#" target="_blank" rel="noopener"><?php esc_html_e('/seo-guide','igny8'); ?></a></td>
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
                        <?php esc_html_e('Showing 1 to 2 of 23 entries','igny8'); ?>
                    </div>
                    <div class="igny8-table-pagination-controls">
                        <button class="igny8-table-page-btn" disabled><</button>
                        <span class="igny8-table-page-btn active">1</span>
                        <button class="igny8-table-page-btn">2</button>
                        <button class="igny8-table-page-btn">3</button>
                        <button class="igny8-table-page-btn">></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Tab -->
        <div id="keywords-clusters-insights" class="igny8-tab-content">
            <!-- Metric Cards -->
            <div class="igny8-metric-cards">
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value">89%</div>
                    <div class="igny8-metric-label">Coverage %</div>
                    <div class="igny8-metric-change positive">+5%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value">12.5</div>
                    <div class="igny8-metric-label">Avg Rank per Cluster</div>
                    <div class="igny8-metric-change positive">+2.3</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value">23</div>
                    <div class="igny8-metric-label">Content Gaps</div>
                    <div class="igny8-metric-change negative">-8%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value">156K</div>
                    <div class="igny8-metric-label">Est. Monthly Traffic</div>
                    <div class="igny8-metric-change positive">+15%</div>
                </div>
            </div>

            <!-- Charts Placeholder -->
            <div class="igny8-dashboard-charts">
                <div class="igny8-chart-container">
                    <div class="igny8-chart-title"><?php esc_html_e('Traffic Estimate by Cluster','igny8'); ?></div>
                    <div class="igny8-chart-placeholder"><?php esc_html_e('Chart visualization will be displayed here','igny8'); ?></div>
                </div>
                <div class="igny8-chart-container">
                    <div class="igny8-chart-title"><?php esc_html_e('Ranking Distribution','igny8'); ?></div>
                    <div class="igny8-chart-placeholder"><?php esc_html_e('Chart visualization will be displayed here','igny8'); ?></div>
                </div>
            </div>

            <!-- Content Gaps Table -->
            <div class="igny8-data-table-container">
                <div class="igny8-table-header">
                    <h3 class="igny8-table-title"><?php esc_html_e('Content Gap Analysis','igny8'); ?></h3>
                </div>

                <div class="igny8-table-wrapper">
                    <table class="igny8-data-table">
                        <thead>
                            <tr>
                                <th class="igny8-sortable"><?php esc_html_e('UNMAPPED KEYWORD','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('SEARCH VOLUME','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('DIFFICULTY','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('SUGGESTED CLUSTER','igny8'); ?></th>
                                <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e('Machine Learning Tutorial','igny8'); ?></td>
                                <td>2,100</td>
                                <td>35</td>
                                <td><?php esc_html_e('AI & Machine Learning','igny8'); ?></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>Map to Cluster</option>
                                        <option>Create New Cluster</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('SEO Tools Comparison','igny8'); ?></td>
                                <td>1,850</td>
                                <td>42</td>
                                <td><?php esc_html_e('Digital Marketing','igny8'); ?></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>Map to Cluster</option>
                                        <option>Create New Cluster</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
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
    </div>
    <?php
}
