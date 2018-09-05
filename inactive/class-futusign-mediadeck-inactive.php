<?php
/**
 * The inactive functionality of the plugin.
 *
 * @link       https://bitbucket.org/futusign/futusign-wp-mediadeck
 * @since      0.1.0
 *
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/inactive
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The inactive functionality of the plugin.
 *
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/inactive
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_MediaDeck_Inactive {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
	}
	/**
	 * Display missing plugin dependency notices.
	 *
	 * @since    0.1.0
	 */
	public function missing_plugins_notice() {
		if ( ! Futusign_MediaDeck::is_plugin_active( 'futusign' ) ) {
			include plugin_dir_path( __FILE__ ) . 'partials/futusign-mediadeck-missing-futusign.php';
		}
		if ( ! Futusign_MediaDeck::is_plugin_active( 'acf-pro' ) ) {
			include plugin_dir_path( __FILE__ ) . 'partials/futusign-mediadeck-missing-acf-pro.php';
		}
	}
}
