<?php
/*******************************************
 * GNU-Mailman Process GET and POST data
*******************************************/

function gm_process_data() {
	if( ! is_admin() )
		return;

	if( ! current_user_can( 'manage_options' ) )
		return;

	$gm_post = ( !empty( $_POST['gm-action'] ) ) ? true : false;
	$gm_get = ( !empty( $_GET['gm-action'] ) ) ? true : false;

	if( $gm_post ) {

		/****************************************
		 * Edit Mailing Lists
		****************************************/
		if( $_POST['gm-action'] == 'edit-lists' ) {

			// Get Current Mailing List Array
			$listArray = gm_get_mailing_lists();

			// Loop Through each POST
			foreach ($_POST as $key => $value) {
				$key = explode('_', $key);
				$listId = (int) $key[0];
				$propName = $key[1];

				// If listId is an INT, continue
				if (is_int($listId)) {
					switch($propName){
						case 'name':
						case 'url':
						case 'pass':
						case 'autosub':
							$listArray[$listId][$propName] = $value;
							break;
					}
				}
			}

			// Store Updated Mailing List Array
			gm_set_mailing_lists($listArray);

			$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_edit';
			wp_safe_redirect( $url ); exit;
		}

		/****************************************
		 * Add Mailing List
		****************************************/
		if( $_POST['gm-action'] == 'add-list' ) {

			// Get Current Mailing List Array
			$listArray = gm_get_mailing_lists();

			// Add New Mailing List
			$listArray[] = array(
					'name'		=> $_POST['name'],
					'url'		=> $_POST['url'],
					'pass'		=> $_POST['pass'],
					'autosub'	=> $_POST['autosub'],
			);

			// Store Updated Mailing List Array
			gm_set_mailing_lists($listArray);

			// Redirect
			$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_add';
			wp_safe_redirect( $url ); exit;
		}

		/****************************************
		 * Update Admin Settings
		****************************************/
		if( $_POST['gm-action'] == 'edit-settings' ) {

			$updateFrequency = (int) $_POST['update_frequency'];
			if (is_int($updateFrequency) AND $updateFrequency > 0) {
				update_site_option('gnumailman_update_frequency', $updateFrequency);
			}

			// Redirect
			$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gnu-mailman-settings&gm_message=updated';
			wp_safe_redirect( $url ); exit;
		}
	}

	if( $gm_get ) {

		/****************************************
		 * Delete Mailing List
		****************************************/
		if( $_GET['gm-action'] == 'delete-list' ) {

			// Get Current Mailing List Array
			$listArray = gm_get_mailing_lists();

			// Delete Mailing List By Name
			unset($listArray[$_GET['id']]);

			// Store Updated Mailing List Array
			gm_set_mailing_lists($listArray);

			// Redirect
			$url = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_removed';
			wp_safe_redirect( $url ); exit;
		}

	}
}
add_action( 'admin_init', 'gm_process_data' );