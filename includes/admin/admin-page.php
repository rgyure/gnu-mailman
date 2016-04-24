<?php
/*******************************************
 * GNU-Mailman Defaul Admin  Page
*******************************************/

/**
 * Admin Area - Main Page HTML
 *
 * @since   1.0.0
 */
function gm_admin_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="updated"></div>

	<div class=wrap>
		<h2>WordPress-Mailman Integration</h2>
		<h3>Welcome!</h3>
		<p>You are currently running version <?php echo GM_PLUGIN_VERSION; ?>. For support, please visit the <a href="http://wordpress.org/support/plugin/gnu-mailman-integration">Wordpress Plugin page</a></p>
	</div>
<?php
}