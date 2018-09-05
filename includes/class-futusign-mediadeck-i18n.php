<?php
/**
 * Define the internationalization functionality
 *
 * @link       https://bitbucket.org/futusign/futusign-wp-mediadeck
 * @since      0.1.0
 *
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/includes
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define the internationalization functionality.
 *
 * @since      0.1.0
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/includes
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_MediaDeck_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'futusign_mediadeck',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
