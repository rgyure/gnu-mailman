<?php
/*******************************************
 * GNU-Mailman Defaul Admin  Page
*******************************************/

function gm_admin_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="updated"></div>

	<div class=wrap>
		<h2>Wordpress-Mailman Integration</h2>
		<h3>Welcome!</h3>
	</div>
<?php
}