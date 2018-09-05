<?php
/**
 * The file that defines the core plugin class
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
 * The core plugin class.
 *
 * @since      0.1.0
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/includes
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_MediaDeck {
	/**
	 * Static function to determine if dependant plugin(s) are active
	 *
	 * @since    0.1.0
	 * @var      string    $plugin    Indicates which plugin(s) to check for.
	 */
	public static function is_plugin_active( $plugin ) {
		if ( 'futusign' == $plugin ) {
			return class_exists( 'Futusign' );
		} elseif ('acf-pro' === $plugin) {
			$acf_pro_installed = Futusign_MediaDeck::is_plugin_installed('acf-pro');
			return $acf_pro_installed && class_exists( 'acf' );
		} elseif ( 'all' == $plugin ) {
			$acf_pro_installed = Futusign_MediaDeck::is_plugin_installed('acf-pro');
			return $acf_pro_installed && class_exists( 'acf' ) && class_exists( 'Futusign' );
		}
		return false;
	}
	/**
	 * Static function to determine if dependant plugin(s) are installed
	 *
	 * @since    0.1.0
	 * @var      string    $plugin    Indicates which plugin(s) to check for.
	 */
	public static function is_plugin_installed( $plugin ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$paths = false;
		if ( 'futusign' == $plugin ) {
			$paths = array( 'futusign/futusign.php' );
		} elseif ( 'acf-pro' == $plugin ) {
				$paths = array( 'advanced-custom-fields-pro/acf.php', 'acf-pro/acf.php');
		}
		if ( $paths ) {
			$plugins = get_plugins();
			if ( is_array( $plugins ) && count( $plugins ) > 0 ) {
				foreach ( $paths as $path ) {
					if ( isset( $plugins[$path] ) && ! empty( $plugins[$path] ) ) {
						return $path;
					}
				}
			}
		}
		return false;
	}
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Futusign_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->plugin_name = 'futusign-mediadeck';
		$this->version = '0.3.0';
		$this->load_dependencies();
		$this->set_locale();
		if (Futusign_MediaDeck::is_plugin_active('all')) {
			$this->define_common_hooks();
			if ( is_admin() ) {
				$this->define_admin_hooks();
			} else {
				$this->define_public_hooks();
			}
		} else {
			$this->define_inactive_hooks();
		}
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-mediadeck-loader.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-mediadeck-i18n.php';
		if (Futusign_MediaDeck::is_plugin_active('all')) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-futusign-mediadeck-common.php';
			if ( is_admin() ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-futusign-mediadeck-admin.php';
			} else {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-futusign-mediadeck-public.php';
			}
		} else {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inactive/class-futusign-mediadeck-inactive.php';
		}
		$this->loader = new Futusign_MediaDeck_Loader();
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Futusign_MediaDeck_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	/**
	 * Register all of the inactive hooks of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_inactive_hooks() {
		$plugin_inactive = new Futusign_MediaDeck_Inactive();
		$this->loader->add_action('admin_notices', $plugin_inactive, 'missing_plugins_notice' );
	}
	/**
	 * Register all of the common hooks of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_common_hooks() {
		$plugin_common = new Futusign_MediaDeck_Common();
		// MEDIA DECK
		$media_deck = $plugin_common->get_media_deck();
		$this->loader->add_action('init', $media_deck, 'register');
		$this->loader->add_action('init', $media_deck, 'register_field_group');
		$this->loader->add_action('manage_futusign_media_deck_posts_custom_column', $media_deck, 'manage_posts_custom_column', 10, 2 );
		$this->loader->add_filter('manage_futusign_media_deck_posts_columns', $media_deck, 'manage_posts_columns');
		$this->loader->add_action('restrict_manage_posts', $media_deck, 'restrict_manage_posts');
		$this->loader->add_action('parse_query', $media_deck, 'parse_query');
		// MEDIA DECK - OVERRIDE
		$this->loader->add_action('restrict_manage_posts', $media_deck, 'restrict_manage_posts_override');
		$this->loader->add_action('parse_query', $media_deck, 'parse_query_override');
	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Futusign_MediaDeck_Admin();
		$this->loader->add_filter( 'wp_link_query_args', $plugin_admin, 'wp_link_query_args' );
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Futusign_MediaDeck_Public();
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
