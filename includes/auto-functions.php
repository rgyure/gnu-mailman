<?php
/*******************************************
 * GNU-Mailman Automatic Functions
*******************************************/

function gm_on_register($userId) {
	foreach (gm_get_mailing_lists() as $listId => $list) {
		if ($list['autosub']) {
			// Subscribe User to List
			gm_subscribe_user_list($listId, $userId);
		}
	}
}

add_action('user_register', 'gm_on_register');

function gm_on_delete($userId) {
	foreach (gm_get_user_subscriptions($userId) as $listId => $list) {
		// Unsubscribe User to List
		gm_unsubscribe_user_list($listId, $userId);
	}
}

add_action('delete_user', 'gm_on_delete');