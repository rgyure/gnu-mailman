<?php
/**
 * @package GM\Functions
 */

/**
 * Return the Update Frequency of Mailing List data
 *
 * @since   1.0.0
 * @return	int	Number of Seconds
 */
function gm_get_update_frequency() {
	return (int) get_site_option( 'gnumailman_update_frequency' );
}

/**
 * Return the Default Timeout of Mailing List Connection Attempt
 *
 * @since   1.0.3
 * @return	int	Number of Seconds
 */
function gm_get_default_timeout() {
	return (int) get_site_option( 'gnumailman_default_timeout' );
}

/**
 * Return an array of mailing lists and settings
 *
 * @since   1.0.0
 * @return array
 */
function gm_get_mailing_lists() {
	$list_array = unserialize( get_site_option( 'gnumailman_lists' ) );
	if ( false === is_array( $list_array ) ) {
		return array();
	} else {
		// Check to ensure each mailing list has a unique id.
		$is_update = false;

		foreach ( $list_array as $key => $list ) {
			if ( false === isset( $list['id'] ) ) {
				$is_update = true;
				// Add List Id.
				$unique_id = gm_create_unique_id();
				$list_array[ $key ]['id'] = $unique_id;

				$list_array[ $unique_id ] = $list_array[ $key ];
				unset( $list_array[ $key ] );
			}
			if ( 32 !== strlen( $key ) ) {
				unset( $list_array[ $key ] );
			}
		}

		if ( true === $is_update ) {
			gm_set_mailing_lists( $list_array );
			return gm_get_mailing_lists();
		}
	}

	return $list_array;
}

/**
 * Set an array of mailing lists to WP settings
 *
 * @since   1.0.0
 * @param	array $list_array Array of Mailing Lists.
 */
function gm_set_mailing_lists( $list_array ) {
	if ( false === is_array( $list_array ) ) {
		wp_die( '$listArray is NOT a valid array' );
	}

	return update_site_option( 'gnumailman_lists', serialize( $list_array ) );
}

/**
 * Return a single mailing list
 *
 * @since   1.0.0
 * @param	int	$list_id	Mailing List Id.
 * @return array
 */
function gm_get_mailing_list( $list_id ) {
	$list_array = gm_get_mailing_lists();

	foreach ( $list_array as $list ) {
		if ( $list['id'] === $list_id ) {
			return $list;
		}
	}

	wp_die( 'Invalid List Id (' . $list_id . ')' );
}

/**
 * Get list of mailing lists users is subscribed to.
 *
 * @since   1.0.0
 * @param	int	$user_id	WordPress User Id.
 * @return	array	Array of Lists Subscribed To (e.g. array(1, 2, 5) )
 */
function gm_get_user_subscriptions( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$current_subscriptions = unserialize( get_user_meta( $user_id, 'gm_subscriptions', true ) );

	// Check users subscriptions against active mailing list...list.
	$mailing_lists = gm_get_mailing_lists();
	$mailing_list_ids = array();
	foreach ( $mailing_lists as $list ) {
		$mailing_list_ids[] = $list['id'];
	}

	$data_stale = false;
	foreach ($current_subscriptions as $key => $list_id) {
		if ( false === in_array( $list_id, $mailing_list_ids ) ) {
			$data_stale = true;
			unset( $current_subscriptions[ $key ] );
		}
	}

	// Data is stale, need to update user's meta.
	if ( true === $data_stale ) {
		// Update User Metadata.
		update_user_meta( $user_id, 'gm_subscriptions', serialize( $current_subscriptions ) );
	}

	$last_update = get_user_meta( $user_id, 'gm_last_update', true );
	if ( ( time() - gm_get_update_frequency() ) > $last_update ) {
		// Cache time expired...Need to Update!
		$current_subscriptions = gm_update_user_subscriptions( $user_id );
	}

	return $current_subscriptions;
}

/**
 * Query Mailman for subscriptions and update local cache
 *
 * @since   1.0.0
 * @param	int	$user_id WordPress User Id (NULL will use current user).
 * @return	array
 */
function gm_update_user_subscriptions( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user = get_userdata( $user_id );
	$connection_failed = false;
	$current_subscriptions = array();

	// Loop through each list updating the current subscription list (primary email address).
	foreach ( gm_get_mailing_lists() as $list ) {
		$mailman = new Mailman( $list['url'], $list['pass'], $user->data->user_email, $user->data->display_name );

		// Make sure we can connect to the mailing list?
		$ml = $mailman->canConnect();
		if ( false === $ml['connected'] ) {
			echo '<div class="error"><p>' . $ml['error'] . '</p></div>';
			$connection_failed = true;
			continue; // Failed to connect!
		}

		if ( $mailman->isUserSubscribed() ) {
			// Subscribed.
			$current_subscriptions[] = $list['id'];
		}
	}

	// Update User Metadata.
	update_user_meta( $user_id, 'gm_subscriptions', serialize( $current_subscriptions ) );

	// Don't last update if there was a connection failure!
	if ( ! $connection_failed ) {
		update_user_meta( $user_id, 'gm_last_update', time() );
	}

	return $current_subscriptions;
}

/**
 * Subscribe a User to a List
 *
 * @since   1.0.0
 * @param	int	$list_id	Mailing List Id.
 * @param	int	$user_id	WordPress User Id (NULL will use current user).
 * @return	bool
 */
function gm_subscribe_user_list( $list_id, $user_id = null ) {

	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user = get_userdata( $user_id );

	$status = gm_subscribe( $list_id, $user->data->user_email, $user->data->display_name );


	if ( $status ) {
		$current_subscriptions = unserialize( get_user_meta( $user_id, 'gm_subscriptions', true ) );
		$current_subscriptions[] = $list_id;
		update_user_meta( $user_id, 'gm_subscriptions', serialize( $current_subscriptions ) );

		return true;
	}

	return false;
}

/**
 * Subscribe an Email to a List
 *
 * @since   1.0.5
 * @param	int	   $list_id Mailing List Id.
 * @param	string $email_address Email Address of Subscriber.
 * @param	string $display_name Display Name of Subscriber.
 * @return	bool
 */
function gm_subscribe( $list_id, $email_address, $display_name ) {
	$list = gm_get_mailing_list( $list_id );

	$mailman = new Mailman( $list['url'], $list['pass'], $email_address, $display_name );
	return $mailman->subscribe();
}

/**
 * Unsubscribe a User to a List
 *
 * @since   1.0.0
 * @param	int	$list_id	Mailing List Id.
 * @param	int	$user_id	WordPress User Id (NULL will use current user).
 * @return	bool
 */
function gm_unsubscribe_user_list( $list_id, $user_id = null ) {

	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user = get_userdata( $user_id );

	$status = gm_unsubscribe( $list_id, $user->data->user_email );

	if ( $status ) {
		$current_subscriptions = unserialize( get_user_meta( $user_id, 'gm_subscriptions', true ) );

		$key = array_search( $list_id, $current_subscriptions );
		unset( $current_subscriptions[ $key ] );

		update_user_meta( $user_id, 'gm_subscriptions', serialize( $current_subscriptions ) );
		return true;
	}

	return false;
}

/**
 * Unsubscribe an Email from a List
 *
 * @since   1.0.5
 * @param	int	   $list_id   Mailing List Id.
 * @param	string $email_address Email address.
 * @return	bool
 */
function gm_unsubscribe( $list_id, $email_address ) {
	$list = gm_get_mailing_list( $list_id );

	$mailman = new Mailman( $list['url'], $list['pass'], $email_address, '' );
	return $mailman->unsubscribe();
}

/**
 * Attempt to connect to a mailing list
 *
 * @since   1.0.3
 * @param	int	$list_url	Mailing List URL.
 * @param	int	$list_pass	Mailing List Password.
 * @return	array
 */
function gm_connect_list( $list_url, $list_pass ) {
	$mailman = new Mailman( $list_url, $list_pass );
	return $mailman->canConnect();
}

/**
 * Create a new Unique Id for a list
 *
 * @since   1.0.5
 * @return	string
 */
function gm_create_unique_id() {
	return md5( uniqid( mt_rand(), true ) );
}