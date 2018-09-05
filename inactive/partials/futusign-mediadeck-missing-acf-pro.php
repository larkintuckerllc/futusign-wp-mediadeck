<?php
/**
 * Missing acf-pro plugin partial.
 *
 * @link       https://bitbucket.org/futusign/futusign-wp-mediadeck
 * @since      0.1.0
 *
 * @package    futusign_mediadeck
 * @subpackage inactive/partials
 */
if ( ! defined( 'WPINC' ) ) {
  die;
}
/**
 * Show missing futusign
 *
 * @since    0.1.0
 */
function futusign_mediadeck_missing_acf_pro() {
	$is_installed = Futusign_MediaDeck::is_plugin_installed( 'acf-pro' );
	$target = false;
	$action = __('Install', 'futusign_mediadeck');
	if ( current_user_can( 'install_plugins' ) ) {
		if ( $is_installed ) {
			$action = __('Activate', 'futusign_mediadeck');
			$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $is_installed . '&plugin_status=active' ), 'activate-plugin_' . $is_installed );
		} else {
      $target = true;
			$url = 'https://www.futusign.com/downloads/';
		}
	} else {
		$target = true;
		$url = 'https://www.futusign.com/downloads/';
	}
	?>
	<div class="notice error is-dismissible">
		<p><strong>futusign MediaDeck</strong> <?php esc_html_e('depends on the last version of Advanced Custom Fields Pro to work!', 'futusign_mediadeck' ); ?></p>
		<p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary"<?php if ( $target ) : ?> target="_blank"<?php endif; ?>><?php echo esc_html( $action . ' Advanced Custom Fields Pro' ); ?></a></p>
	</div>
	<?php
}
futusign_mediadeck_missing_acf_pro();
