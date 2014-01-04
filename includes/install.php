<?php

/*******************************************
 * GNU-Mailman Installation Functions
*******************************************/

/**
 * Function to setup plugin defaults on plugin activation
 *
 * @since   1.0.0
 */
function gm_options_install() {

	// Set Default Frequency
	add_site_option('gnumailman_update_frequency', 60*60); // 1 Hour

}

register_activation_hook( GM_PLUGIN_FILE, 'gm_options_install' );