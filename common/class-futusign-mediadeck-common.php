<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://bitbucket.org/futusign/futusign-wp-mediadeck
 * @since      0.1.0
 *
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/common
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The common functionality of the plugin.
 *
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/common
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_MediaDeck_Common {
	/**
	 * The media deck.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      Futusign_Media_Deck_Type    $media_deck    The media deck.
	 */
	private $media_deck;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->media_deck = new Futusign_Media_Deck_Type();
	}
	/**
	 * Load the required dependencies for module.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-mediadeck-type.php';
	}
	/**
	 * Retrieve the media deck.
	 *
	 * @since     0.1.0
	 * @return    Futusign_MediaDeck_Type    The media deck functionality.
	 */
	public function get_media_deck() {
		return $this->media_deck;
	}
}
