<?php
defined('ABSPATH') || exit;

/*==================================================
  ## IGNY8 DASHBOARD ADMIN
  Description: Dashboard page for Igny8 plugin
==================================================*/

/**
 * Dashboard page
 */
function igny8_dashboard_page() {
    // Get recent activity from all CPTs
    $recent_activities = [];
    
    // Recent keywords
    $recent_keywords = get_posts([
        'post_type' => 'igny8_keywords',
        'post_status' => 'publish',
        'numberposts' => 3,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    foreach ($recent_keywords as $keyword) {
        $recent_activities[] = [
            'id' => $keyword->ID,
            'activity' => sprintf(__('Keyword "%s" added', 'igny8'), $keyword->post_title),
            'module' => __('Keywords & Clusters', 'igny8'),
            'status' => 'completed',
            'date' => $keyword->post_date,
            'type' => 'keyword'
        ];
    }
    
    // Recent clusters
    $recent_clusters = get_posts([
        'post_type' => 'igny8_clusters',
        'post_status' => 'publish',
        'numberposts' => 2,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    foreach ($recent_clusters as $cluster) {
        $recent_activities[] = [
            'id' => $cluster->ID,
            'activity' => sprintf(__('Cluster "%s" created', 'igny8'), $cluster->post_title),
            'module' => __('Keywords & Clusters', 'igny8'),
            'status' => 'completed',
            'date' => $cluster->post_date,
            'type' => 'cluster'
        ];
    }
    
    // Recent tasks
    $recent_tasks = get_posts([
        'post_type' => 'igny8_content_planner',
        'post_status' => 'publish',
        'numberposts' => 2,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    foreach ($recent_tasks as $task) {
        $status = get_post_meta($task->ID, '_igny8_status', true) ?: 'Pending';
        $recent_activities[] = [
            'id' => $task->ID,
            'activity' => sprintf(__('Content task "%s" created', 'igny8'), $task->post_title),
            'module' => __('Content Engine', 'igny8'),
            'status' => strtolower($status),
            'date' => $task->post_date,
            'type' => 'task'
        ];
    }
    
    // Sort activities by date
    usort($recent_activities, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Limit to 5 most recent
    $recent_activities = array_slice($recent_activities, 0, 5);
    ?>
    <div class="wrap igny8-modern-admin">
        <h1><?php esc_html_e('Recent Activity','igny8'); ?></h1>

        <!-- Recent Activity -->
        <div class="igny8-data-table-container">
            <div class="igny8-table-header">
                <h3 class="igny8-table-title"><?php esc_html_e('Recent Activity','igny8'); ?></h3>
                <div class="igny8-table-controls">
                    <div class="igny8-table-show">
                        <label>Show</label>
                        <select>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                        </select>
                        <span>entries</span>
                    </div>
                    <div class="igny8-table-search">
                        <input type="search" placeholder="<?php esc_attr_e('Search activity...','igny8'); ?>">
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
                            <th class="igny8-sortable"><?php esc_html_e('ACTIVITY','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('MODULE','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('STATUS','igny8'); ?></th>
                            <th class="igny8-sortable"><?php esc_html_e('DATE','igny8'); ?></th>
                            <th class="igny8-actions-col"><?php esc_html_e('ACTIONS','igny8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_activities)): ?>
                        <tr>
                            <td colspan="7" class="igny8-no-data">
                                <?php esc_html_e('No recent activity found. Start by adding some keywords or creating content tasks!', 'igny8'); ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recent_activities as $index => $activity): ?>
                        <tr>
                            <td><input type="checkbox" class="igny8-table-checkbox"></td>
                            <td><?php echo sprintf('%06d', $index + 1); ?></td>
                            <td><?php echo esc_html($activity['activity']); ?></td>
                            <td><?php echo esc_html($activity['module']); ?></td>
                            <td>
                                <span class="igny8-badge <?php 
                                    echo $activity['status'] === 'completed' ? 'green' : 
                                        ($activity['status'] === 'pending' ? 'yellow' : 'blue'); 
                                ?>">
                                    <?php echo esc_html(ucfirst($activity['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date('Y-m-d', strtotime($activity['date']))); ?></td>
                            <td>
                                <button class="igny8-btn-icon igny8-edit-record" 
                                        data-type="<?php echo esc_attr($activity['type']); ?>" 
                                        data-id="<?php echo esc_attr($activity['id']); ?>"
                                        title="<?php esc_attr_e('Edit', 'igny8'); ?>">
                                    ✏️
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="igny8-table-footer">
                <div class="igny8-table-info">
                    <?php esc_html_e('Showing 1 to 3 of 15 entries','igny8'); ?>
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
