<?php
/**
 * Define the media deck functionality
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
 * Define the media deck functionality.
 *
 * @since      0.1.0
 * @package    futusign_mediadeck
 * @subpackage futusign_mediadeck/common
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_Media_Deck_Type {
/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
	}
	/**
	 * Register the media deck post type.
	 *
	 * @since    0.1.0
	 */
	public function register() {
		$labels = array(
			'name' => __( 'Media Decks', 'futusign' ),
			'singular_name' => __( 'Media Deck', 'futusign' ),
			'add_new' => __( 'Add New' , 'futusign' ),
			'add_new_item' => __( 'Add New Media Deck' , 'futusign' ),
			'edit_item' =>  __( 'Edit Media Deck' , 'futusign' ),
			'new_item' => __( 'New Media Deck' , 'futusign' ),
			'view_item' => __('View Media Deck', 'futusign'),
			'search_items' => __('Search Media Decks', 'futusign'),
			'not_found' =>  __('No Media Decks found', 'futusign'),
			'not_found_in_trash' => __('No Media Decks found in Trash', 'futusign'),
		);
		register_post_type( 'futusign_media_deck',
			array(
			'labels' => $labels,
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'has_archive' => false,
			'show_in_rest' => true,
			'rest_base' => 'fs-media-decks',
			'menu_icon' => plugins_url( 'img/media_deck.png', __FILE__ )
			)
		);
	}
	/**
	 * Values for playlist column.
	 *
	 * @since    0.1.0
	 * @param     string       $column     Column name.
	 * @param     string       $post_id    Post id.
	 */
	public static function manage_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'playlists':
				$playlists = get_the_terms( $post_id, 'futusign_playlist' );
				if ($playlists == false) {
					echo '';
				} else {
					echo join( ', ', wp_list_pluck( $playlists, 'name' ) );
				}
				break;
			case 'overrides':
				$overrides = get_the_terms( $post_id, 'futusign_override' );
				if ($overrides == false) {
					echo '';
				} else {
					echo join( ', ', wp_list_pluck( $overrides, 'name' ) );
				}
				break;
		}
	}
	/**
	 * Add playlist column
	 *
	 * @since    0.1.0
	 * @param     array       $columns     Columns.
	 */
	public static function manage_posts_columns($columns) {
		$i = array_search( 'title', array_keys( $columns ) ) + 1;
		$columns_before = array_slice( $columns, 0, $i );
		$columns_after = array_slice( $columns, $i );
		$overrides = array();
		if (class_exists( 'Futusign_Override' )) {
			$overrides = array(
				'overrides' => __('On Overrides', 'futusign')
			);
		}
		return array_merge(
			$columns_before,
			array(
				'playlists' => __('On Playlists', 'futusign')
			),
			$overrides,
			$columns_after
		);
	}
	/**
	 * Add playlist filter to admin.
	 *
	 * @since    0.1.0
	 */
	public static function restrict_manage_posts() {
		global $typenow;
		$post_type = 'futusign_media_deck';
		$taxonomy_id = 'futusign_playlist';
		if ($typenow != $post_type) {
			return;
		}
		$selected = isset( $_GET[$taxonomy_id] ) ? $_GET[$taxonomy_id] : '';
		$taxonomy = get_taxonomy( $taxonomy_id );
		wp_dropdown_categories( array(
			'show_option_all' =>  __( 'Show All', 'futusign' ) . ' ' . $taxonomy->label,
			'taxonomy' => $taxonomy_id,
			'name' => $taxonomy_id,
			'orderby' => 'name',
			'selected' => $selected,
			'show_count' => false,
			'hide_empty' => false,
			'hide_if_empty' => true,
		) );
	}
	/**
	 * Build filter admin selection for overide
	 *
	 * @since    0.1.0
	 */
	public function restrict_manage_posts_override() {
		if (! class_exists( 'Futusign_Override' )) {
			return;
		}
		global $typenow;
		$post_type = 'futusign_media_deck';
		$taxonomy_id = 'futusign_override';
		if ($typenow != $post_type) {
			return;
		}
		$selected = isset( $_GET[$taxonomy_id] ) ? $_GET[$taxonomy_id] : '';
		$taxonomy = get_taxonomy( $taxonomy_id );
		wp_dropdown_categories( array(
			'show_option_all' =>  __( 'Show All', 'futusign' ) . ' ' . $taxonomy->label,
			'taxonomy' => $taxonomy_id,
			'name' => $taxonomy_id,
			'orderby' => 'name',
			'selected' => $selected,
			'show_count' => false,
			'hide_empty' => false,
			'hide_if_empty' => true,
		) );
	}
	/**
	 * Convert query for playlists from id to slug.
	 *
	 * @since    0.1.0
	 */
	public static function parse_query($wp_query) {
		global $pagenow;
		$post_type = 'futusign_media_deck';
		$taxonomy_id = 'futusign_playlist';
		$q_vars = &$wp_query->query_vars;
		if (
			$pagenow != 'edit.php' ||
			!isset( $q_vars['post_type'] ) ||
			$q_vars['post_type'] !== $post_type ||
			!isset( $q_vars[$taxonomy_id] ) ||
			!is_numeric( $q_vars[$taxonomy_id] ) ||
			$q_vars[$taxonomy_id] == 0
		) {
			return;
		}
		$term = get_term_by( 'id', $q_vars[$taxonomy_id], $taxonomy_id );
		$q_vars[$taxonomy_id] = $term->slug;
	}
	/**
	 * Convert query playlists variables from ids to slugs - override
	 *
	 * @since    0.1.0
	 */
	public function parse_query_override($wp_query) {
		if (! class_exists( 'Futusign_Override' )) {
			return;
		}
		global $pagenow;
		$post_type = 'futusign_media_deck';
		$taxonomy_id = 'futusign_override';
		$q_vars = &$wp_query->query_vars;
		if (
			$pagenow != 'edit.php' ||
			!isset( $q_vars['post_type'] ) ||
			$q_vars['post_type'] !== $post_type ||
			!isset( $q_vars[$taxonomy_id] ) ||
			!is_numeric( $q_vars[$taxonomy_id] ) ||
			$q_vars[$taxonomy_id] == 0
		) {
			return;
		}
		$term = get_term_by( 'id', $q_vars[$taxonomy_id], $taxonomy_id );
		$q_vars[$taxonomy_id] = $term->slug;
	}
	/**
	 * Define advanced custom fields for media deck.
	 *
	 * @since    0.1.0
	 */
	public function register_field_group() {
		if( function_exists('acf_add_local_field_group') ) {
			acf_add_local_field_group(array (
				'key' => 'acf_futusign_media_decks',
				'title' => 'futusign Media Decks',
				'fields' => array (
					array (
						'key' => 'field_acf_fs_md_instructions',
						'label' => __('Instructions', 'futusign_media_deck'),
						'name' => '',
						'type' => 'message',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => wp_kses(__( 'In addition to setting the <i>Media</i>, etc., add the <i>Media Deck</i> to one or more <i>list</i> below.', 'futusign_web' ), array( 'i' => array() ) ),
						'new_lines' => 'wpautop',
						'esc_html' => 0,
					),
					array (
						'key' => 'field_acf_fs_md_media',
						'label' => __('Media', 'futusign_media_deck'),
						'name' => 'media',
						'type' => 'flexible_content',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'button_label' => __('Add Media', 'futusign_media_deck'),
						'min' => 1,
						'max' => '',
						'layouts' => array (
							array (
								'key' => 'field_acf_fs_md_image',
								'name' => 'image',
								'label' => __('Image', 'futusign_media_deck'),
								'display' => 'block',
								'sub_fields' => array (
									array (
										'key' => 'field_acf_fs_md_im_file',
										'label' => __('File', 'futusign_media_deck'),
										'name' => 'file',
										'type' => 'image',
										'instructions' => __('Upload an image file.', 'futusign_media_deck'),
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'return_format' => 'url',
										'preview_size' => 'thumbnail',
										'library' => 'all',
										'min_width' => '',
										'min_height' => '',
										'min_size' => '',
										'max_width' => '',
										'max_height' => '',
										'max_size' => '',
										'mime_types' => '',
									),
									array (
										'key' => 'field_acf_fs_md_im_image_duration',
										'label' => __('Image Duration', 'futusign-wp-mediadeck'),
										'name' => 'image_duration',
										'type' => 'number',
										'instructions' => __('The number of seconds to show image.', 'futusign-wp-mediadeck'),
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => 10,
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'min' => 2,
										'max' => '',
										'step' => 1,
									),
								),
								'min' => '',
								'max' => '',
							),
							array (
								'key' => 'field_acf_fs_md_web',
								'name' => 'web',
								'label' => __('Web', 'futusign-wp-mediadeck'),
								'display' => 'block',
								'sub_fields' => array (
									array (
										'key' => 'field_acf_fs_md_wb_url',
										'label' => __('URL', 'futusign-wp-mediadeck'),
										'name' => 'url',
										'type' => 'text',
										'instructions' => __('Provide a URL.', 'futusign-wp-mediadeck'),
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array (
										'key' => 'field_acf_fs_md_wb_web_duration',
										'label' => __('Web Duration', 'futusign-wp-mediadeck'),
										'name' => 'web_duration',
										'type' => 'number',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => 10,
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'min' => 2,
										'max' => '',
										'step' => 1,
									),
								),
								'min' => '',
								'max' => '',
							),
							array (
								'key' => 'field_acf_fs_md_youtube',
								'name' => 'youtube',
								'label' => __('YouTube Video', 'futusign_media_deck'),
								'display' => 'block',
								'sub_fields' => array (
									array (
										'key' => 'field_acf_fs_md_yt_url',
										'label' => __('URL', 'futusign_media_deck'),
										'name' => 'url',
										'type' => 'text',
										'instructions' => __('The share Uniform Resource Locator (URL) or address of the YouTube video, e.g., https://youtu.be/cmdFne7LnuA.', 'futusign-wp-mediadeck'),
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array (
										'key' => 'field_acf_fs_md_yt_suggested_quality',
										'label' => __('Suggested Quality', 'futusign-wp-mediadeck'),
										'name' => 'suggested_quality',
										'type' => 'select',
										'instructions' => __('The highest quality that the YouTube Video will play at; default will auto-select the quality.', 'futusign-wp-mediadeck'),
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array (
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'choices' => array (
											'default' => 'default',
											'highres' => 'highres',
											'hd1080' => 'hd1080',
											'hd720' => 'hd720',
											'large' => 'large',
											'medium' => 'medium',
											'small' => 'small',
										),
										'default_value' => array (
											0 => 'default',
										),
										'allow_null' => 0,
										'multiple' => 0,
										'ui' => 0,
										'ajax' => 0,
										'return_format' => 'value',
										'placeholder' => '',
									),
								),
								'min' => '',
								'max' => '',
							),
						),
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'futusign_media_deck',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => array (
					0 => 'permalink',
					1 => 'the_content',
					2 => 'excerpt',
					3 => 'discussion',
					4 => 'comments',
					5 => 'revisions',
					6 => 'slug',
					7 => 'author',
					8 => 'format',
					9 => 'page_attributes',
					10 => 'featured_image',
					11 => 'categories',
					12 => 'tags',
					13 => 'send-trackbacks',
				),
				'active' => 1,
				'description' => '',
			));

		}
	}
}
