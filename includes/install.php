<?php

// function to create defaults
function gm_options_install() {

	// Set Default Frequency
	add_site_option('gnumailman_update_frequency', 60*60); // 1 Hour

}

// run the install scripts upon plugin activation
register_activation_hook( GM_PLUGIN_FILE, 'gm_options_install' );