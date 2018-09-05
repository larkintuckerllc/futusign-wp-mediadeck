<?php
/**
 * The plugin bootstrap file
 *
 * @link             https://bitbucket.org/futusign/futusign-wp-mediadeck
 * @since            0.1.0
 * @package          futusign_mediadeck
 * @wordpress-plugin
 * Plugin Name:      futusign MediaDeck
 * Plugin URI:       https://www.futusign.com
 * Description:      Add futusign Media Decks feature
 * Version:          0.3.0
 * Author:           John Tucker
 * Author URI:       https://github.com/larkintuckerllc
 * License:          Custom
 * License URI:      https://www.futusign.com/license
 * Text Domain:      futusign-mediadeck
 * Domain Path:      /languages
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
//Use version 3.1 of the update checker.
require 'plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = new PluginUpdateChecker_3_1 (
	'http://futusign-wordpress.s3-website-us-east-1.amazonaws.com/futusignz-mediadeck.json',
	__FILE__
);
function activate_futusign_mediadeck() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-futusign-mediadeck-activator.php';
	Futusign_MediaDeck_Activator::activate();
}
function deactivate_futusign_mediadeck() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-futusign-mediadeck-deactivator.php';
	Futusign_MediaDeck_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_futusign_mediadeck' );
register_deactivation_hook( __FILE__, 'deactivate_futusign_mediadeck' );
require_once plugin_dir_path( __FILE__ ) . 'includes/class-futusign-mediadeck.php';
/**
 * Begins execution of the plugin.
 *
 * @since    0.1.0
 */
function run_futusign_mediadeck() {
	$plugin = new Futusign_MediaDeck();
	$plugin->run();
}
run_futusign_mediadeck();
