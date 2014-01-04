<?php
/*******************************************
 * GNU-Mailman Automatic Functions
*******************************************/

/**
 * On Wordpress User Registration, auto subscribe user to
 * all lists that are set to be autosubscribed.
 *
 * @since   1.0.0
 * @param	int	$userId	Wordpress User Id
 */
function gm_on_register($userId) {
	foreach (gm_get_mailing_lists() as $listId => $list) {
		if ($list['autosub']) {
			// Subscribe User to List
			gm_subscribe_user_list($listId, $userId);
		}
	}
}

add_action('user_register', 'gm_on_register');


/**
 * On Wordpress User Delete, unsubscribe user to all the mailing
 * lists they are current subscribed to.
 *
 * @since   1.0.0
 * @param	int	$userId	Wordpress User Id
 */
function gm_on_delete($userId) {
	foreach (gm_get_user_subscriptions($userId) as $listId => $list) {
		// Unsubscribe User to List
		gm_unsubscribe_user_list($listId, $userId);
	}
}

add_action('delete_user', 'gm_on_delete');