<?php

/*******************************************
 * GNU-Mailman General Functions
*******************************************/

/**
 * Return the Update Frequency of Mailing List data
 *
 * @since   1.0.0
 * @return	int	Number of Seconds
 */
function gm_get_update_frequency(){
	return (int) get_site_option('gnumailman_update_frequency');
}

/**
 * Return an array of mailing lists and settings
 *
 * @since   1.0.0
 * @return array
 */
function gm_get_mailing_lists(){
	$listArray = unserialize(get_site_option('gnumailman_lists'));
	if (!is_array($listArray))
		return array();

	return $listArray;
}

/**
 * Set an array of mailing lists to WP settings
 *
 * @since   1.0.0
 * @param	array	$listArray	Array of Mailing Lists
 */
function gm_set_mailing_lists($listArray){
	if (!is_array($listArray))
		die('$listArray is NOT a valid array');

	return update_site_option('gnumailman_lists', serialize($listArray));
}

/**
 * Return a single mailing list
 *
 * @since   1.0.0
 * @param	int	$listId	Mailing List Id
 * @return array
 */
function gm_get_mailing_list($listId){
	$listArray = gm_get_mailing_lists();
	$list = $listArray[$listId];

	if (is_array($list)){
		return $list;
	}else{
		die('Invalid List Id (' . $listId . ')');
	}
}

/**
 * Get list of mailing lists users is subscribed to.
 *
 * @since   1.0.0
 * @param	int	$userId	Wordpress User Id
 * @return	array	Array of Lists Subscribed To (e.g. array(1, 2, 5) )
 */
function gm_get_user_subscriptions($userId = NULL){
	if ($userId == NULL)
		$userId = get_current_user_id();

	$currentSubscriptions = unserialize(get_user_meta( $userId, 'gm_subscriptions', true ));

	$lastUpdate = get_user_meta( $userId, 'gm_last_update', true );
	if ((time() - gm_get_update_frequency()) > $lastUpdate){
		// Cache time expired...Need to Update
		$currentSubscriptions = gm_update_user_subscriptions($userId);
	}

	return $currentSubscriptions;
}

/**
 * Query Mailman for subscriptions and update local cache
 *
 * @since   1.0.0
 * @param	int	$userId	Wordpress User Id (NULL will use current user)
 * @return	array
 */
function gm_update_user_subscriptions($userId = NULL){
	if ($userId == NULL)
		$userId = get_current_user_id();

	$user = get_userdata($userId);
	$currentSubscriptions = array();

	// Loop through each list updating the current subscription list
	foreach (gm_get_mailing_lists() as $listId => $list) {
		$mailman = new Mailman($list['url'], $list['pass'], $user->data->user_email, $user->data->display_name);

		if ($mailman->isUserSubscribed()){
			// Subscribed
			$currentSubscriptions[] = $listId;
		}
	}

	// Update User Metadata
	update_user_meta( $userId, 'gm_subscriptions', serialize($currentSubscriptions) );
	update_user_meta( $userId, 'gm_last_update', time() );

	return $currentSubscriptions;
}

/**
 * Subscribe a User to a List
 *
 * @since   1.0.0
 * @param	int	$listId	Mailing List Id
 * @param	int	$userId	Wordpress User Id (NULL will use current user)
 * @return	bool
 */
function gm_subscribe_user_list($listId, $userId = NULL){
	$list = gm_get_mailing_list($listId);

	if ($userId == NULL)
		$userId = get_current_user_id();

	$user = get_userdata($userId);

	$mailman = new Mailman($list['url'], $list['pass'], $user->data->user_email, $user->data->display_name);
	$status = $mailman->subscribe();

	if ($status){
		$currentSubscriptions = unserialize(get_user_meta( $userId, 'gm_subscriptions', true ));
		$currentSubscriptions[] = $listId;
		update_user_meta( $userId, 'gm_subscriptions', serialize($currentSubscriptions ));
		return TRUE;
	}

	return FALSE;
}

/**
 * Unsubscribe a User to a List
 *
 * @since   1.0.0
 * @param	int	$listId	Mailing List Id
 * @param	int	$userId	Wordpress User Id (NULL will use current user)
 * @return	bool
 */
function gm_unsubscribe_user_list($listId, $userId = NULL){
	$list = gm_get_mailing_list($listId);

	if ($userId == NULL)
		$userId = get_current_user_id();

	$user = get_userdata($userId);

	$mailman = new Mailman($list['url'], $list['pass'], $user->data->user_email, $user->data->display_name);
	$status = $mailman->unsubscribe();

	if ($status){
		$currentSubscriptions = unserialize(get_user_meta( $userId, 'gm_subscriptions', true ));

		$key = array_search($listId, $currentSubscriptions);
		unset($currentSubscriptions[$key]);

		update_user_meta( $userId, 'gm_subscriptions', serialize($currentSubscriptions ));
		return TRUE;
	}

	return FALSE;
}