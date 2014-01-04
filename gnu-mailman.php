<?php
/**
 * GNU Wordpress Mailman Integration
 *
 *
 * @package   Mailman
 * @author    Ryan Gyure <me@ryan.gy>
 * @license   GPL-2.0+
 * @link      http://blog.ryan.gy/applications/wordpress/gnu-mailman/
 * @copyright 2014 Ryan Gyure
 *
 * @wordpress-plugin
 * Plugin Name:       GNU-Mailman Integration
 * Plugin URI:        http://blog.ryan.gy/applications/wordpress/gnu-mailman/
 * Description:       GNU-Mailman integration with Wordpress
 * Version:           1.0.0
 * Author:            Ryan Gyure
 * Author URI:        http://www.ryangyure.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/rgyure/gnu-mailman
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'GM_PLUGIN_DIR' ) ) {
	define( 'GM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'GM_PLUGIN_VERSION' ) ) {
	define( 'GM_PLUGIN_VERSION', '1.0' );
}

if ( ! defined( 'GM_PLUGIN_FILE' ) ) {
	define( 'GM_PLUGIN_FILE', __FILE__ );
}

load_plugin_textdomain('gnumailman', 'wp-content/plugins/gnu-mailman');

/*******************************************
 * File Includes
*******************************************/
require_once( GM_PLUGIN_DIR . 'includes/install.php' );
require_once( GM_PLUGIN_DIR . 'includes/Mailman.php' );
require_once( GM_PLUGIN_DIR . 'includes/functions.php' );
require_once( GM_PLUGIN_DIR . 'includes/user-forms.php' );
require_once( GM_PLUGIN_DIR . 'includes/auto-functions.php' );

// Admin Only Includes
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( GM_PLUGIN_DIR . 'includes/admin/menu-links.php' ); // first
	require_once( GM_PLUGIN_DIR . 'includes/admin/process-data.php' );
	require_once( GM_PLUGIN_DIR . 'includes/admin/settings-page.php' );
	require_once( GM_PLUGIN_DIR . 'includes/admin/mailing-lists-page.php' );
	require_once( GM_PLUGIN_DIR . 'includes/admin/admin-page.php' );
}
?>