<?php
/**
 * Plugin Name: AI Content Personalizer & Rewriter
 * Plugin URI:  https://github.com/alorig/ai-content-personalizer
 * Description: Free plugin to personalize or rewrite WordPress content for each visitor using AI. Frontend personalization, unlimited variations, OpenAI-powered.
 * Version:     0.1.0
 * Author:      Alorig Systems
 * Author URI:  https://alorig.com
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-personalizer
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Simple test hook so plugin loads without errors.
 */
function ai_content_personalizer_init() {
    // This just confirms the plugin is active in the WP debug log.
    error_log('AI Content Personalizer & Rewriter plugin loaded.');
}
add_action('plugins_loaded', 'ai_content_personalizer_init');
