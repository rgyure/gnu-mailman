<?php
/*******************************************
 * GNU-Mailman User Profile Page
*******************************************/

function gm_user_profile_page() {
?>
	<h3>Mailing Lists</h3>
	<table class="form-table">
		<?php
		// Get Current Subscriptions
		$mailingListSubscriptions = gm_get_user_subscriptions();

		// Get Current Mailing Lists and Loop
		$mailingLists = gm_get_mailing_lists();
		foreach ($mailingLists as $listId => $list){
			$checked = '';
			if (in_array($listId, $mailingListSubscriptions))
				$checked = ' checked="checked"';
			?>
			<tr>
				<th><label for="subscribe_<?php echo $listId;?>"><?php echo $list['name'];?></label></th>
				<td><input name="subscribe[]" type="checkbox" id="subscribe_<?php echo $listId;?>" value="<?php echo $listId;?>" <?php echo $checked;?> /> Subscribe to <?php echo $list['name'];?> list</td>
			</tr>
		<?php } ?>
	</table>
<?php
}

add_action('show_user_profile', 'gm_user_profile_page');
add_action('edit_user_profile', 'gm_user_profile_page');

function gm_user_profile_update($userId, $oldUserData) {
	$user_info = get_userdata($userId);

	if (isset($_POST['submit']))
	{
		// Get Current Subscriptions
		$mailingListSubscriptions = gm_get_user_subscriptions();

		// If Email address changed, remove from all active lists
		if ($oldUserData->data->user_email != $user_info->data->user_email){
			foreach ($mailingListSubscriptions as $listId => $list) {
				gm_unsubscribe_user_list($listId, $userId);
			}
		}

		if (isset($_POST['subscribe']))
		{
			$subscriptions = $_POST['subscribe'];
		} else {
			$subscriptions = array();
		}

		// Loop Through POST Values
		foreach ($subscriptions as $key => $listId) {
			if (!in_array($listId, $mailingListSubscriptions)) {
				// Subscribe to this Mailing List
				gm_subscribe_user_list($listId, $userId);
			}
		}

		// Loop Through Current Mailing List
		foreach ($mailingListSubscriptions as $listId => $list) {
			if (!in_array($listId, $subscriptions)) {
				// Key doesnt exist, unsubscribe to this Mailing List
				gm_unsubscribe_user_list($listId, $userId);
			}
		}
	}
}

add_action('profile_update', 'gm_user_profile_update', 10, 2);