<?php
/*******************************************
 * GNU-Mailman Admin Settings Page
*******************************************/

/**
 * Admin Area - Settings Page HTML
 *
 * @since   1.0.0
 */
function gm_settings_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="updated"></div>

	<div class=wrap>
		<h2>Wordpress-Mailman Integration</h2>
		<h3>Settings</h3>

		<?php
		// POST Update Messages.
		if( $_GET['gm_message'] == 'updated')
			echo '<div class="updated"><p>Settings Updated</p></div>';
		?>

		<form method="post">
			<input type="hidden" name="gm-action" value="edit-settings"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Update Frequency</th>
					<td>
						<input type="text" name="update_frequency" class="regular-text code" value="<?php echo get_site_option( 'gnumailman_update_frequency' ); ?>" /> seconds
					</td>
					<td>How often the script should refresh data from the mailing lists.</td>
				</tr>
				<tr valign="top">
					<th scope="row">Default Timeout</th>
					<td>
						<input type="text" name="default_timeout" class="regular-text code" value="<?php echo get_site_option( 'gnumailman_default_timeout' ); ?>" /> seconds
					</td>
					<td>How long the script should attempt to connect to a mailing list.</td>
				</tr>
			</table>

			<div class="submit"><input type="submit" name="info_update" class="button-primary" value="Update" /></div>
		</form>
	</div>

<?php
}