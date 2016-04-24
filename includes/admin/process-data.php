<?php
/*******************************************
 * GNU-Mailman Process GET and POST data
*******************************************/

/**
 * Process Data Submit (Admin)
 */
function gm_process_data() {
	if ( false === is_admin() ) {
		return;
	}

	if ( false === current_user_can( 'manage_options' ) ) {
		return;
	}

	$gm_post = ( !empty( $_POST['gm-action'] ) ) ? true : false;
	$gm_get = ( !empty( $_GET['gm-action'] ) ) ? true : false;

	if ( $gm_post ) {

		/****************************************
		 * Edit Mailing Lists
		****************************************/
		if ( true === isset( $_POST['gm-action'] ) && 'edit-lists' === $_POST['gm-action'] ) {

			// Get Current Mailing List Array.
			$list_array = gm_get_mailing_lists();

			// Loop Through each POST.
			foreach ( $_POST as $key => $value ) {
				$key = explode( '_', $key );
				$list_id = $key[0];
				$prop_name = $key[1];

				// If list id is 32 chars.
				if ( 32 === strlen( $list_id ) ) {
					switch ( $prop_name ) {
						case 'name':
						case 'url':
						case 'pass':
						case 'autosub':
							$list_array[ $list_id ][ $prop_name ] = $value;
							break;
					}
				}
			}

			// Store Updated Mailing List Array.
			gm_set_mailing_lists( $list_array );

			$url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_edit';
			wp_safe_redirect( $url );
			exit;
		}

		/****************************************
		 * Add Mailing List
		****************************************/
		if ( true === isset( $_POST['gm-action'] ) && 'add-list' === $_POST['gm-action'] ) {
		
			// Verify we can connect to the mailing list.
			$status = gm_connect_list( $_POST['url'], $_POST['pass'] );
			if ( false === $status['connected'] ) {
				$url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_error=' . urlencode( $status['error'] );
				wp_safe_redirect( $url );
				exit;
			}

			// Get Current Mailing List Array.
			$list_array = gm_get_mailing_lists();

			// Add New Mailing List.
			$unique_id = gm_create_unique_id();
			$list_array[$unique_id] = array(
					'id'		=> $unique_id,
					'name'		=> $_POST['name'],
					'url'		=> $_POST['url'],
					'pass'		=> $_POST['pass'],
					'autosub'	=> $_POST['autosub'],
			);

			// Store Updated Mailing List Array.
			gm_set_mailing_lists( $list_array );

			// Redirect.
			$url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_add';
			wp_safe_redirect( $url );
			exit;
		}

		/****************************************
		 * Update Admin Settings
		****************************************/
		if ( isset( $_POST['gm-action'] ) && $_POST['gm-action'] === 'edit-settings' ) {

			$update_frequency = (int) $_POST['update_frequency'];
			if ( is_int( $update_frequency ) && $update_frequency > 0 ) {
				update_site_option( 'gnumailman_update_frequency', $update_frequency );
			}

			$default_timeout = (int) $_POST['default_timeout'];
			if ( is_int( $default_timeout ) && $default_timeout > 0 ) {
				update_site_option( 'gnumailman_default_timeout', $default_timeout );
			}

			// Redirect.
			$url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gnu-mailman-settings&gm_message=updated';
			wp_safe_redirect( $url );
			exit;
		}
	}

	if ( $gm_get ) {

		/****************************************
		 * Delete Mailing List
		****************************************/
		if( true === isset( $_GET['gm-action'] ) && 'delete-list' === $_GET['gm-action'] ) {

			// Get Current Mailing List Array.
			$list_array = gm_get_mailing_lists();

			foreach ( $list_array as $key => $list ) {
				if ( $list['id'] === $_GET['id'] ) {
					unset( $list_array[ $key ] );
				}
			}

			// Store Updated Mailing List Array.
			gm_set_mailing_lists( $list_array );

			// Redirect.
			$url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gnu-mailman-lists&gm_message=list_removed';
			wp_safe_redirect( $url );
			exit;
		}
	}
}
add_action( 'admin_init', 'gm_process_data' );
