<?php
/*******************************************
 * GNU-Mailman Add Mailing List - Partial Page
*******************************************/
?>
<form method="post">
	<input type="hidden" name="gm-action" value="add-list"/>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Mailing List Name</th>
			<td>
				<input type="text" name="name" class="regular-text code" value="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Mailing List URL</th>
			<td>
				<input type="text" name="url" class="regular-text code" value="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Mailing Password</th>
			<td>
				<input type="text" name="pass" class="regular-text code" value="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Auto Subscribe on User Register?</th>
			<td>
				<select name="autosub">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
			</td>
		</tr>
	</table>

	<div class="submit"><input type="submit" name="add" class="button-primary" value="Add Mailing List" /></div>
</form>
