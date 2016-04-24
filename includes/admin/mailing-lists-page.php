<?php
/*******************************************
 * GNU-Mailman Admin Mailing Lists Page
*******************************************/

/**
 * Admin Area - Mailing Lists Page HTML
 *
 * @since   1.0.0
 */
function gm_mailing_lists_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="updated"></div>

	<div class=wrap>
		<form method="post">
			<input type="hidden" name="gm-action" value="edit-lists" />
			<h2>Wordpress-Mailman Integration</h2>

			<?php
			// POST Update Messages.
			if( true === isset( $_GET[ 'gm_message' ] ) &&
			    $_GET[ 'gm_message' ] === 'list_edit' ) {
				echo '<div class="updated"><p>Mailing Lists Updated</p></div>';
			}

			if( true === isset($_GET[ 'gm_message' ] ) &&
			    $_GET[ 'gm_message' ] === 'list_add' ) {
				echo '<div class="updated"><p>Mailing List Added</p></div>';
			}

			if( true === isset($_GET[ 'gm_message' ] ) &&
			    $_GET[ 'gm_message' ] == 'list_removed' ) {
				echo '<div class="updated"><p>Mailing List Removed</p></div>';
			}
			
			if( true === isset($_GET[ 'gm_error' ] ) ) {
				echo '<div class="error"><p>' . urldecode( $_GET['gm_error'] ) . '</p></div>';
			}
			?>

			<h3><?php _e('Mailing Lists', 'gm') ?></h3>
			<table class="form-table">
				<?php
				$mailing_lists = gm_get_mailing_lists();
				foreach ( $mailing_lists as $list ) {
				?>
				<tr valign="top">
					<th scope="row">Mailing List Name</th>
					<td>
						<input type="text" name="<?php echo $list['id']; ?>_name" class="regular-text code" value="<?php echo $list['name']; ?>" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Mailing List URL</th>
					<td>
						<input type="text" name="<?php echo $list['id']; ?>_url" class="regular-text code" value="<?php echo $list['url']; ?>" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Mailing Password</th>
					<td>
						<input type="text" name="<?php echo $list['id']; ?>_pass" class="regular-text code" value="<?php echo $list['pass']; ?>" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Auto Subscribe on User Register?</th>
					<td>
						<select name="<?php echo $list['id']; ?>_autosub">
							<option value="0" <?php if ($list['autosub'] == 0){ echo 'selected="selected"'; }?>>No</option>
							<option value="1" <?php if ($list['autosub'] == 1){ echo 'selected="selected"'; }?>>Yes</option>
						</select>
					</td>
				</tr>

				<tr>
					<td colspan="2"><input type="button" name="<?php echo $list['id']; ?>_delete" class="button-primary" onClick="return confirm_delete('<?php echo $list['id']; ?>');" value="Delete <?php echo $list['name']; ?>" /></td>
				</tr>

				<tr>
					<td colspan="2"><hr/></td>
				</tr>
				<?php } ?>
			</table>

			<?php if (count($mailing_lists) > 0){ ?>
			<div class="submit"><input type="submit" name="info_update" class="button-primary" value="Update All Mailing Lists" /></div>
			<?php } ?>
		</form>

		<h3><?php _e('Add Mailing List', 'gm') ?></h3>
		<?php include('add-mailing-list.php'); ?>
	</div>
	<script type="text/javascript">
	function confirm_delete(id) {
		if (confirm('Are you sure?')) {
			window.location='<?php echo get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=gnu-mailman&gm-action=delete-list&id=' + id
		}

		return false;
	}
	</script>
<?php
}