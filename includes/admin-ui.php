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




/**
 * Render the main Igny8 settings page with tabbed interface
 */
function igny8_settings_page() {
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Igny8 Settings','igny8'); ?></h1>
        
        <ul class="igny8-tab-nav">
            <li><a href="#connections-api" class="active">Connections & API Keys</a></li>
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

            <div class="igny8-data-table-header">
                <button type="submit" class="button igny8-add-new"><?php esc_html_e('Save Settings','igny8'); ?></button>
                <button type="button" class="button igny8-clear-filters"><?php esc_html_e('Reset to Defaults','igny8'); ?></button>
            </div>
        </form>
    </div>
    <?php
}