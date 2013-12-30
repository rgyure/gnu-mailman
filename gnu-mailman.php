<?php
/*
* Plugin Name: Wordpress Mailman Integration
* Plugin URI: http://www.fxhinc.com/
* Description: Mailman integration
* Version: 1.0
* Author: Ryan Gyure
* Author URI: http://www.ryangyure.com/
*/

if ( !defined( 'GM_PLUGIN_DIR' ) ) {
	define( 'GM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'GM_PLUGIN_VERSION' ) ) {
	define( 'GM_PLUGIN_VERSION', '1.0' );
}

if ( !defined( 'GM_PLUGIN_FILE' ) ) {
	define( 'GM_PLUGIN_FILE', __FILE__ );
}

load_plugin_textdomain('gnumailman', 'wp-content/plugins/gnu-mailman');

/*******************************************
 * File Includes
*******************************************/
include( GM_PLUGIN_DIR . 'includes/install.php' );
include( GM_PLUGIN_DIR . 'includes/Mailman.php' );
include( GM_PLUGIN_DIR . 'includes/functions.php' );
include( GM_PLUGIN_DIR . 'includes/user-forms.php' );
include( GM_PLUGIN_DIR . 'includes/auto-functions.php' );

// Admin Only Includes
if( is_admin() ) {
	include( GM_PLUGIN_DIR . 'includes/admin/menu-links.php' ); // first
	include( GM_PLUGIN_DIR . 'includes/admin/process-data.php' );
	include( GM_PLUGIN_DIR . 'includes/admin/settings-page.php' );
	include( GM_PLUGIN_DIR . 'includes/admin/mailing-lists-page.php' );
	include( GM_PLUGIN_DIR . 'includes/admin/admin-page.php' );
}
?>