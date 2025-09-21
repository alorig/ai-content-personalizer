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

        case 'igny8-keywords-clusters':
            igny8_keywords_clusters_page();
            break;
        case 'igny8-trust-signals':
            igny8_trust_signals_page();
            break;
        case 'igny8-theme':
            igny8_theme_page();
            break;
        case 'igny8-settings':
            igny8_settings_page();
            break;
        case 'igny8-reports':
            igny8_reports_page();
            break;
        case 'igny8-help':
            igny8_help_page();
            break;
        default:
            igny8_dashboard_page();
            break;
    }
}



/**
 * Trust Signals module page
 */
function igny8_trust_signals_page() {
    // Get metric values
    $total_campaigns = 12;
    $active_campaigns = 8;
    $completed_campaigns = 4;
    $pending_campaigns = 2;
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Trust Signals','igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#trust-signals-campaigns" class="active">Campaigns</a></li>
            <li><a href="#trust-signals-authority">Authority Builder</a></li>
            <li><a href="#trust-signals-social">Social Proof</a></li>
            <li><a href="#trust-signals-analytics">Analytics</a></li>
        </ul>
        
        <!-- Campaigns Tab -->
        <div id="trust-signals-campaigns" class="igny8-tab-content active">
            <!-- Metric Cards -->
            <div class="igny8-metric-cards">
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($total_campaigns); ?></div>
                    <div class="igny8-metric-label">Total Campaigns</div>
                    <div class="igny8-metric-change positive">+15%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($active_campaigns); ?></div>
                    <div class="igny8-metric-label">Active Campaigns</div>
                    <div class="igny8-metric-change positive">+8%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($completed_campaigns); ?></div>
                    <div class="igny8-metric-label">Completed</div>
                    <div class="igny8-metric-change positive">+12%</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($pending_campaigns); ?></div>
                    <div class="igny8-metric-label">Pending</div>
                    <div class="igny8-metric-change negative">-5%</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="igny8-filter-bar">
                <div class="igny8-filter-group">
                    <label>Status</label>
                    <select>
                        <option><?php esc_html_e('All Status','igny8'); ?></option>
                        <option><?php esc_html_e('Active','igny8'); ?></option>
                        <option><?php esc_html_e('Completed','igny8'); ?></option>
                        <option><?php esc_html_e('Pending','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-filter-group">
                    <label>Campaign Type</label>
                    <select>
                        <option><?php esc_html_e('All Types','igny8'); ?></option>
                        <option><?php esc_html_e('Backlink Building','igny8'); ?></option>
                        <option><?php esc_html_e('Social Proof','igny8'); ?></option>
                        <option><?php esc_html_e('Authority Building','igny8'); ?></option>
                    </select>
                </div>
                <div class="igny8-table-search">
                    <input type="search" placeholder="<?php esc_attr_e('Search campaigns...','igny8'); ?>">
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
                        <option>Activate</option>
                        <option>Pause</option>
                        <option>Delete</option>
                    </select>
                </div>
                <button class="button igny8-add-new"><?php esc_html_e('New Campaign','igny8'); ?></button>
            </div>

            <!-- Data Table -->
            <div class="igny8-data-table-container">
                <div class="igny8-table-header">
                    <h3 class="igny8-table-title"><?php esc_html_e('Trust Signal Campaigns','igny8'); ?></h3>
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
                            <input type="search" placeholder="<?php esc_attr_e('Search campaigns...','igny8'); ?>">
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
                                <th class="igny8-sortable"><?php esc_html_e('CAMPAIGN NAME','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('TYPE','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('PROGRESS','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('START DATE','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('END DATE','igny8'); ?></th>
                                <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                <td>000001</td>
                                <td><?php esc_html_e('Tech Blog Outreach','igny8'); ?></td>
                                <td><span class="igny8-badge blue"><?php esc_html_e('Backlink Building','igny8'); ?></span></td>
                                <td><span class="igny8-badge green"><?php esc_html_e('Active','igny8'); ?></span></td>
                                <td>75%</td>
                                <td><?php esc_html_e('2024-12-01','igny8'); ?></td>
                                <td><?php esc_html_e('2024-12-31','igny8'); ?></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>View</option>
                                        <option>Edit</option>
                                        <option>Pause</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="igny8-table-checkbox"></td>
                                <td>000002</td>
                                <td><?php esc_html_e('Industry Awards','igny8'); ?></td>
                                <td><span class="igny8-badge purple"><?php esc_html_e('Authority Building','igny8'); ?></span></td>
                                <td><span class="igny8-badge yellow"><?php esc_html_e('Pending','igny8'); ?></span></td>
                                <td>25%</td>
                                <td><?php esc_html_e('2024-12-10','igny8'); ?></td>
                                <td><?php esc_html_e('2025-01-15','igny8'); ?></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>View</option>
                                        <option>Edit</option>
                                        <option>Activate</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="igny8-table-footer">
                    <div class="igny8-table-info">
                        <?php esc_html_e('Showing 1 to 2 of 12 entries','igny8'); ?>
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
    </div>
    <?php
}

/**
 * Theme module page
 */
function igny8_theme_page() {
    // Get metric values
    $total_themes = 5;
    $active_themes = 1;
    $custom_themes = 3;
    $theme_variations = 12;
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Theme','igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#theme-overview" class="active">Overview</a></li>
            <li><a href="#theme-customization">Customization</a></li>
            <li><a href="#theme-templates">Templates</a></li>
            <li><a href="#theme-advanced">Advanced</a></li>
        </ul>
        
        <!-- Overview Tab -->
        <div id="theme-overview" class="igny8-tab-content active">
            <!-- Metric Cards -->
            <div class="igny8-metric-cards">
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($total_themes); ?></div>
                    <div class="igny8-metric-label">Total Themes</div>
                    <div class="igny8-metric-change positive">+2</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($active_themes); ?></div>
                    <div class="igny8-metric-label">Active Theme</div>
                    <div class="igny8-metric-change positive">+1</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($custom_themes); ?></div>
                    <div class="igny8-metric-label">Custom Themes</div>
                    <div class="igny8-metric-change positive">+1</div>
                </div>
                <div class="igny8-metric-card">
                    <div class="igny8-metric-value"><?php echo esc_html($theme_variations); ?></div>
                    <div class="igny8-metric-label">Theme Variations</div>
                    <div class="igny8-metric-change positive">+3</div>
                </div>
            </div>

            <!-- Current Theme Info -->
            <div class="igny8-data-table-container">
                <div class="igny8-table-header">
                    <h3 class="igny8-table-title"><?php esc_html_e('Current Theme Configuration','igny8'); ?></h3>
                </div>

                <div class="igny8-table-wrapper">
                    <table class="igny8-data-table">
                        <thead>
                            <tr>
                                <th class="igny8-sortable"><?php esc_html_e('SETTING','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('VALUE','igny8'); ?></th>
                                <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                                <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e('Primary Color','igny8'); ?></td>
                                <td>#3b82f6</td>
                                <td><span class="igny8-badge green"><?php esc_html_e('Active','igny8'); ?></span></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>Edit</option>
                                        <option>Reset</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Typography','igny8'); ?></td>
                                <td>Inter, sans-serif</td>
                                <td><span class="igny8-badge green"><?php esc_html_e('Active','igny8'); ?></span></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>Edit</option>
                                        <option>Reset</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Layout Style','igny8'); ?></td>
                                <td>Modern Grid</td>
                                <td><span class="igny8-badge green"><?php esc_html_e('Active','igny8'); ?></span></td>
                                <td>
                                    <select class="igny8-table-select">
                                        <option>Actions</option>
                                        <option>Edit</option>
                                        <option>Reset</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customization Tab -->
        <div id="theme-customization" class="igny8-tab-content">
            <!-- Theme Customization Tools -->
            <div class="igny8-card-grid">
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Color Palette','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Customize your theme colors and create a consistent brand experience.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Customize','igny8'); ?></button>
                    </div>
                </div>
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Typography','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Choose fonts and typography settings that match your brand.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Customize','igny8'); ?></button>
                    </div>
                </div>
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Layout','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Adjust spacing, borders, and overall layout structure.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Customize','igny8'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Tab -->
        <div id="theme-templates" class="igny8-tab-content">
            <!-- Template Gallery -->
            <div class="igny8-card-grid">
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Modern SaaS','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Clean, professional design perfect for SaaS applications.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Preview','igny8'); ?></button>
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Apply','igny8'); ?></button>
                    </div>
                </div>
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Corporate','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Professional corporate theme with traditional styling.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Preview','igny8'); ?></button>
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Apply','igny8'); ?></button>
                    </div>
                </div>
                <div class="igny8-card">
                    <div class="igny8-card-header">
                        <h3 class="igny8-card-title"><?php esc_html_e('Creative','igny8'); ?></h3>
                    </div>
                    <p><?php esc_html_e('Bold, creative design for innovative brands.','igny8'); ?></p>
                    <div class="igny8-card-actions">
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Preview','igny8'); ?></button>
                        <button class="igny8-btn-icon edit"><?php esc_html_e('Apply','igny8'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Tab -->
        <div id="theme-advanced" class="igny8-tab-content">
            <div class="igny8-help-section">
                <div class="igny8-help-title"><?php esc_html_e('Advanced Theme Configuration','igny8'); ?></div>
                <div class="igny8-help-content">
                    <p><?php esc_html_e('Advanced theme settings allow you to customize CSS, JavaScript, and other technical aspects of your theme.','igny8'); ?></p>
                    <p><?php esc_html_e('Use these settings with caution as they can affect the overall functionality of your admin interface.','igny8'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Reports page
 */
function igny8_reports_page() {
    // Get metric values
    $total_reports = 25;
    $scheduled_reports = 8;
    $completed_reports = 15;
    $failed_reports = 2;
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Reports','igny8'); ?></h1>
        
        <!-- Metric Cards -->
        <div class="igny8-metric-cards">
            <div class="igny8-metric-card">
                <div class="igny8-metric-value"><?php echo esc_html($total_reports); ?></div>
                <div class="igny8-metric-label">Total Reports</div>
                <div class="igny8-metric-change positive">+5</div>
            </div>
            <div class="igny8-metric-card">
                <div class="igny8-metric-value"><?php echo esc_html($scheduled_reports); ?></div>
                <div class="igny8-metric-label">Scheduled</div>
                <div class="igny8-metric-change positive">+2</div>
            </div>
            <div class="igny8-metric-card">
                <div class="igny8-metric-value"><?php echo esc_html($completed_reports); ?></div>
                <div class="igny8-metric-label">Completed</div>
                <div class="igny8-metric-change positive">+3</div>
            </div>
            <div class="igny8-metric-card">
                <div class="igny8-metric-value"><?php echo esc_html($failed_reports); ?></div>
                <div class="igny8-metric-label">Failed</div>
                <div class="igny8-metric-change negative">-1</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="igny8-filter-bar">
            <div class="igny8-filter-group">
                <label>Report Type</label>
                <select>
                    <option><?php esc_html_e('All Types','igny8'); ?></option>
                    <option><?php esc_html_e('SEO Performance','igny8'); ?></option>
                    <option><?php esc_html_e('Content Analysis','igny8'); ?></option>
                    <option><?php esc_html_e('Keyword Tracking','igny8'); ?></option>
                </select>
            </div>
            <div class="igny8-filter-group">
                <label>Status</label>
                <select>
                    <option><?php esc_html_e('All Status','igny8'); ?></option>
                    <option><?php esc_html_e('Completed','igny8'); ?></option>
                    <option><?php esc_html_e('Scheduled','igny8'); ?></option>
                    <option><?php esc_html_e('Failed','igny8'); ?></option>
                </select>
            </div>
            <div class="igny8-table-search">
                <input type="search" placeholder="<?php esc_attr_e('Search reports...','igny8'); ?>">
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
                    <option>Export</option>
                    <option>Schedule</option>
                    <option>Delete</option>
                </select>
            </div>
            <button class="button igny8-add-new"><?php esc_html_e('Generate Report','igny8'); ?></button>
        </div>

        <!-- Data Table -->
        <div class="igny8-data-table-container">
            <div class="igny8-table-header">
                <h3 class="igny8-table-title"><?php esc_html_e('Reports','igny8'); ?></h3>
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
                        <input type="search" placeholder="<?php esc_attr_e('Search reports...','igny8'); ?>">
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
                            <th class="igny8-sortable"><?php esc_html_e('REPORT NAME','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('TYPE','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('GENERATED','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('SIZE','igny8'); ?></th>
                            <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="igny8-table-checkbox"></td>
                            <td>000001</td>
                            <td><?php esc_html_e('Monthly SEO Performance','igny8'); ?></td>
                            <td><span class="igny8-badge blue"><?php esc_html_e('SEO Performance','igny8'); ?></span></td>
                            <td><span class="igny8-badge green"><?php esc_html_e('Completed','igny8'); ?></span></td>
                            <td><?php esc_html_e('2024-12-15','igny8'); ?></td>
                            <td>2.3 MB</td>
                            <td>
                                <select class="igny8-table-select">
                                    <option>Actions</option>
                                    <option>Download</option>
                                    <option>View</option>
                                    <option>Schedule</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="igny8-table-checkbox"></td>
                            <td>000002</td>
                            <td><?php esc_html_e('Keyword Ranking Report','igny8'); ?></td>
                            <td><span class="igny8-badge purple"><?php esc_html_e('Keyword Tracking','igny8'); ?></span></td>
                            <td><span class="igny8-badge yellow"><?php esc_html_e('Scheduled','igny8'); ?></span></td>
                            <td><?php esc_html_e('2024-12-16','igny8'); ?></td>
                            <td>-</td>
                            <td>
                                <select class="igny8-table-select">
                                    <option>Actions</option>
                                    <option>Run Now</option>
                                    <option>Edit Schedule</option>
                                    <option>Cancel</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="igny8-table-footer">
                <div class="igny8-table-info">
                    <?php esc_html_e('Showing 1 to 2 of 25 entries','igny8'); ?>
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
    <?php
}

/**
 * Help page
 */
function igny8_help_page() {
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Help','igny8'); ?></h1>
        
        <!-- Help Sections -->
        <div class="igny8-card-grid">
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Getting Started','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Learn the basics of Igny8 and how to set up your first SEO campaign.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Keywords & Clusters','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Master keyword research and cluster organization for better SEO results.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Content Engine','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Generate and optimize content using AI-powered tools and templates.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Trust Signals','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Build authority and trust through strategic backlink and social proof campaigns.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Reports & Analytics','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Track performance and generate comprehensive SEO reports.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
            <div class="igny8-card">
                <div class="igny8-card-header">
                    <h3 class="igny8-card-title"><?php esc_html_e('Troubleshooting','igny8'); ?></h3>
                </div>
                <p><?php esc_html_e('Common issues and solutions to help you resolve problems quickly.','igny8'); ?></p>
                <div class="igny8-card-actions">
                    <button class="igny8-btn-icon edit"><?php esc_html_e('Read Guide','igny8'); ?></button>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="igny8-data-table-container">
            <div class="igny8-table-header">
                <h3 class="igny8-table-title"><?php esc_html_e('Frequently Asked Questions','igny8'); ?></h3>
            </div>

            <div class="igny8-table-wrapper">
                <table class="igny8-data-table">
                    <thead>
                        <tr>
                            <th class="igny8-sortable"><?php esc_html_e('QUESTION','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('CATEGORY','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('ANSWER','igny8'); ?></th>
                            <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('How do I add keywords?','igny8'); ?></td>
                            <td><span class="igny8-badge blue"><?php esc_html_e('Keywords','igny8'); ?></span></td>
                            <td><?php esc_html_e('Use the Keywords & Clusters module to add and organize your keywords...','igny8'); ?></td>
                            <td>
                                <select class="igny8-table-select">
                                    <option>Actions</option>
                                    <option>View Full Answer</option>
                                    <option>Edit</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('How does content generation work?','igny8'); ?></td>
                            <td><span class="igny8-badge green"><?php esc_html_e('Content','igny8'); ?></span></td>
                            <td><?php esc_html_e('The Content Engine uses AI to generate optimized content based on your keywords...','igny8'); ?></td>
                            <td>
                                <select class="igny8-table-select">
                                    <option>Actions</option>
                                    <option>View Full Answer</option>
                                    <option>Edit</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('How to set up trust signals?','igny8'); ?></td>
                            <td><span class="igny8-badge purple"><?php esc_html_e('Trust Signals','igny8'); ?></span></td>
                            <td><?php esc_html_e('Trust signals help build authority through backlinks and social proof...','igny8'); ?></td>
                            <td>
                                <select class="igny8-table-select">
                                    <option>Actions</option>
                                    <option>View Full Answer</option>
                                    <option>Edit</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="igny8-help-section">
            <div class="igny8-help-title"><?php esc_html_e('Need More Help?','igny8'); ?></div>
            <div class="igny8-help-content">
                <p><?php esc_html_e('If you can\'t find the answer you\'re looking for, our support team is here to help.','igny8'); ?></p>
                <p>
                    <a href="mailto:support@igny8.com" class="button igny8-add-new"><?php esc_html_e('Contact Support','igny8'); ?></a>
                    <a href="#" class="button igny8-apply-filters"><?php esc_html_e('Live Chat','igny8'); ?></a>
                </p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render the main Igny8 settings page with tabbed interface
 */
function igny8_settings_page() {
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Igny8 Settings','igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#connections-api" class="active">Connections & API Keys</a></li>
            <li><a href="#styling-ui">Styling & UI Preferences</a></li>
            <li><a href="#diagnostics-dev">Diagnostics & Developer Tools</a></li>
        </ul>
        
        <form method="post" action="options.php" class="igny8-settings-form">
            <?php
            settings_fields('igny8_settings_group');
            do_settings_sections('igny8_settings_group');
            ?>

            <!-- Connections & API Keys Tab -->
            <div id="connections-api" class="igny8-tab-content active">
                <!-- Metric Cards -->
                <div class="igny8-metric-cards">
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">3</div>
                        <div class="igny8-metric-label">Connected APIs</div>
                        <div class="igny8-metric-change positive">+1</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">89%</div>
                        <div class="igny8-metric-label">API Health</div>
                        <div class="igny8-metric-change positive">+5%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">156</div>
                        <div class="igny8-metric-label">Requests Today</div>
                        <div class="igny8-metric-change positive">+12%</div>
                    </div>
                    <div class="igny8-metric-card">
                        <div class="igny8-metric-value">2</div>
                        <div class="igny8-metric-label">Failed Requests</div>
                        <div class="igny8-metric-change negative">-1</div>
                    </div>
                </div>

                <!-- API Configuration Table -->
                <div class="igny8-data-table-container">
                    <div class="igny8-table-header">
                        <h3 class="igny8-table-title"><?php esc_html_e('API Configuration','igny8'); ?></h3>
                    </div>

                    <div class="igny8-table-wrapper">
                        <table class="igny8-data-table">
                            <thead>
                                <tr>
                                    <th class="igny8-sortable"><?php esc_html_e('SERVICE','igny8'); ?></th>
                                    <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                                    <th class="igny8-sortable"><?php esc_html_e('LAST TESTED','igny8'); ?></th>
                                    <th class="igny8-sortable"><?php esc_html_e('REQUESTS TODAY','igny8'); ?></th>
                                    <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php esc_html_e('OpenAI API','igny8'); ?></td>
                                    <td><span class="igny8-badge green"><?php esc_html_e('Connected','igny8'); ?></span></td>
                                    <td><?php esc_html_e('2024-12-15 14:30','igny8'); ?></td>
                                    <td>45</td>
                                    <td>
                                        <select class="igny8-table-select">
                                            <option>Actions</option>
                                            <option>Test Connection</option>
                                            <option>Configure</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e('Google Search Console','igny8'); ?></td>
                                    <td><span class="igny8-badge green"><?php esc_html_e('Connected','igny8'); ?></span></td>
                                    <td><?php esc_html_e('2024-12-15 14:25','igny8'); ?></td>
                                    <td>23</td>
                                    <td>
                                        <select class="igny8-table-select">
                                            <option>Actions</option>
                                            <option>Test Connection</option>
                                            <option>Configure</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- API Key Configuration -->
                <div class="igny8-card-grid">
                    <div class="igny8-card igny8-card-blue">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-blue"></div>
                            <h3><?php esc_html_e('OpenAI API Key','igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-input-group">
                                <label><?php esc_html_e('API Key','igny8'); ?></label>
                                <input type="password" name="igny8_api_key" value="<?php echo esc_attr(get_option('igny8_api_key', '')); ?>" placeholder="<?php esc_attr_e('sk-...','igny8'); ?>">
                                <p class="igny8-input-description"><?php esc_html_e('Enter your OpenAI API key. This will be used for all AI operations.','igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="igny8-card igny8-card-green">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-green"></div>
                            <h3><?php esc_html_e('AI Model Selection','igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-filter-group">
                                <label><?php esc_html_e('Select Model','igny8'); ?></label>
                                <select name="igny8_model">
                                    <optgroup label="<?php esc_attr_e('GPT-5 Models','igny8'); ?>">
                                        <option value="gpt-5_standard" <?php selected('gpt-5_standard', get_option('igny8_model', 'gpt-4.1_standard')); ?>><?php esc_html_e('GPT-5 Standard ($50/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-5_flex" <?php selected('gpt-5_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-5 Flex ($25/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-5-mini_standard" <?php selected('gpt-5-mini_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-5 Mini Standard ($20/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-5-mini_flex" <?php selected('gpt-5-mini_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-5 Mini Flex ($10/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-5-nano_standard" <?php selected('gpt-5-nano_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-5 Nano Standard ($15/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-5-nano_flex" <?php selected('gpt-5-nano_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-5 Nano Flex ($8/1M tokens)','igny8'); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php esc_attr_e('GPT-4.1 Models','igny8'); ?>">
                                        <option value="gpt-4.1_standard" <?php selected('gpt-4.1_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Standard ($30/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4.1_flex" <?php selected('gpt-4.1_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Flex ($15/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4.1-mini_standard" <?php selected('gpt-4.1-mini_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Mini Standard ($12/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4.1-mini_flex" <?php selected('gpt-4.1-mini_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Mini Flex ($6/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4.1-nano_standard" <?php selected('gpt-4.1-nano_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Nano Standard ($8/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4.1-nano_flex" <?php selected('gpt-4.1-nano_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4.1 Nano Flex ($4/1M tokens)','igny8'); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php esc_attr_e('GPT-4o Models','igny8'); ?>">
                                        <option value="gpt-4o_standard" <?php selected('gpt-4o_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4o Standard ($25/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4o-mini_standard" <?php selected('gpt-4o-mini_standard', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4o Mini Standard ($10/1M tokens)','igny8'); ?></option>
                                        <option value="gpt-4o-mini_flex" <?php selected('gpt-4o-mini_flex', get_option('igny8_model')); ?>><?php esc_html_e('GPT-4o Mini Flex ($5/1M tokens)','igny8'); ?></option>
                                    </optgroup>
                                </select>
                                <p class="igny8-input-description"><?php esc_html_e('Choose the AI model for content generation. Standard models are faster, Flex models are more cost-effective.','igny8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- API Testing -->
                <div class="igny8-card-grid">
                    <div class="igny8-card igny8-card-purple">
                        <div class="igny8-card-header">
                            <div class="igny8-card-icon igny8-icon-purple"></div>
                            <h3><?php esc_html_e('API Testing','igny8'); ?></h3>
                        </div>
                        <div class="igny8-card-content">
                            <div class="igny8-api-test-section">
                                <div class="igny8-api-test-controls">
                                    <button type="button" id="igny8-test-api" class="button igny8-btn-primary"><?php esc_html_e('Test API Connection','igny8'); ?></button>
                                    <div id="igny8-api-status" class="igny8-api-status"><?php esc_html_e('Ready to test','igny8'); ?></div>
                                </div>
                                <div class="igny8-api-test-results" id="igny8-api-results" style="display: none;">
                                    <h4><?php esc_html_e('Test Results','igny8'); ?></h4>
                                    <div class="igny8-test-metrics">
                                        <div class="igny8-test-metric">
                                            <span class="igny8-test-label"><?php esc_html_e('Response Time:','igny8'); ?></span>
                                            <span class="igny8-test-value" id="igny8-response-time">-</span>
                                        </div>
                                        <div class="igny8-test-metric">
                                            <span class="igny8-test-label"><?php esc_html_e('Status Code:','igny8'); ?></span>
                                            <span class="igny8-test-value" id="igny8-status-code">-</span>
                                        </div>
                                        <div class="igny8-test-metric">
                                            <span class="igny8-test-label"><?php esc_html_e('Model Used:','igny8'); ?></span>
                                            <span class="igny8-test-value" id="igny8-model-used">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other tabs content would go here -->
            <div id="styling-ui" class="igny8-tab-content">
                <div class="igny8-help-section">
                    <div class="igny8-help-title"><?php esc_html_e('Styling & UI Preferences','igny8'); ?></div>
                    <div class="igny8-help-content">
                        <p><?php esc_html_e('Customize the appearance and behavior of your Igny8 admin interface.','igny8'); ?></p>
                    </div>
                </div>
            </div>

            <div id="diagnostics-dev" class="igny8-tab-content">
                <div class="igny8-help-section">
                    <div class="igny8-help-title"><?php esc_html_e('Diagnostics & Developer Tools','igny8'); ?></div>
                    <div class="igny8-help-content">
                        <p><?php esc_html_e('Debug tools and system information for troubleshooting.','igny8'); ?></p>
                    </div>
                </div>
            </div>

            <div class="igny8-data-table-header">
                <button type="submit" class="button igny8-add-new"><?php esc_html_e('Save Settings','igny8'); ?></button>
                <button type="button" class="button igny8-clear-filters"><?php esc_html_e('Reset to Defaults','igny8'); ?></button>
            </div>
        </form>
    </div>
    <?php
}