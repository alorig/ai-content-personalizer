<?php
/**
 * Plugin Name: AI Content Personalizer & Rewriter
 * Plugin URI:  https://github.com/alorig/ai-content-personalizer
 * Description: Free IN this version we are updating to matsh Igny8 OS strcuture previous version as in 0.1 everythign is working fine.
 * Version:     1.0
 * Author:      Alorig Systems
 * Author URI:  https://alorig.com
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-personalizer
 */

defined('ABSPATH') || exit;

// Load all core modules
require_once plugin_dir_path(__FILE__) . 'install.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-enqueue.php';
// Load new modular structure
require_once plugin_dir_path(__FILE__) . 'modules/personalize.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/ui-render.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/options.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/actions.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/openai.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/db.php';
require_once plugin_dir_path(__FILE__) . 'globals/php/utils.php';


require_once plugin_dir_path(__FILE__) . 'includes/data-model.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui-framework.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/openai.php';
require_once plugin_dir_path(__FILE__) . 'includes/db.php';
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/content-generation-api.php';

// Load module-specific admin files
require_once plugin_dir_path(__FILE__) . 'includes/dashboard-admin.php';


// Load frontend styling and dynamic color logic
if (!is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/frontend-css.php';
}

// Register activation hook
register_activation_hook(__FILE__, 'igny8_install');
