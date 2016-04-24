<?php

/*******************************************
 * GNU-Mailman Admin Menu
*******************************************/

/**
 * Function to Setup Menu Links in WordPress
 *
 * @since   1.0.0
 */
function gm_settings_menu() {
	add_menu_page( __( 'Mailman Integration', 'gm' ), __( 'Mailman', 'gm' ), 'manage_options', 'gnu-mailman', 'gm_admin_page' );
	$gm_mailing_list_page		 = add_submenu_page( 'gnu-mailman', __( 'Mailing Lists', 'gm' ), __( 'Lists', 'gm' ), 'manage_options', 'gnu-mailman-lists', 'gm_mailing_lists_page' );
	$gm_settings_page			= add_submenu_page( 'gnu-mailman', __( 'Settings', 'gm' ), __( 'Settings', 'gm' ), 'manage_options', 'gnu-mailman-settings', 'gm_settings_page' );
}

add_action( 'admin_menu', 'gm_settings_menu' );
