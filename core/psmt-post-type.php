<?php
/**
 * Post Type/taxonomy helper.
 *
 * @package psourcemediathek.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsourceMediathek Post type helper
 *
 * Helps creating gallery post types and taxonomies
 *
 * This class registers custom post type and taxonomies
 */
class PSMT_Post_Type_Helper {
	/**
	 * Singleton instance.
	 *
	 * @var PSMT_Post_Type_Helper
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'psmt_setup', array( $this, 'init' ), 0 );
	}

	/**
	 * Get singleton instance.
	 *
	 * @return PSMT_Post_Type_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup the process.
	 */
	public function init() {
		$this->register_post_types();
		$this->register_taxonomies();

	}

	/**
	 * Register our internal post type.
	 */
	private function register_post_types() {

		$label        = _x( 'Galerie', 'The Gallery Post Type Name', 'psourcemediathek' );
		$label_plural = _x( 'Galerien', 'The Gallery Post Type Plural Name', 'psourcemediathek' );

		$_labels = array(
			'name'               => $label_plural,
			'singular_name'      => $label,
			'menu_name'          => _x( 'PS-Mediathek', 'PsourceMediathek Admin menu name', 'psourcemediathek' ),
			'name_admin_bar'     => _x( 'PS-Mediathek', 'PsourceMediathek admin bar menu name', 'psourcemediathek' ),
			'all_items'          => _x( 'Alle Galerien', 'PsourceMediathek All galleries label', 'psourcemediathek' ),
			'add_new'            => _x( 'Galerie hinzufügen', 'admin add new gallery menu label', 'psourcemediathek' ),
			'add_new_item'       => _x( 'Galerie hinzufügen', 'admin add gallery label', 'psourcemediathek' ),
			'edit_item'          => _x( 'Galerie bearbeiten', 'admin edit gallery', 'psourcemediathek' ),
			'new_item'           => _x( 'Galerie hinzufügen', 'admin add new item label', 'psourcemediathek' ),
			'view_item'          => _x( 'Galerie ansehen', 'admin view gallery label', 'psourcemediathek' ),
			'search_items'       => _x( 'Galerien durchsuchen', 'admin search galleries label', 'psourcemediathek' ),
			'not_found'          => _x( 'Keine Galerien gefunden!', 'admin no galleries text', 'psourcemediathek' ),
			'not_found_in_trash' => _x( 'Keine Galerien im Müll gefunden!', 'admin no galleries text', 'psourcemediathek' ),
			'parent_item_colon'  => _x( 'Übergeordnete Galerie', 'admin gallery parent label', 'psourcemediathek' ),

		);// $this->_get_labels( $label, $label_plural );

		$has_archive = false;

		if ( psmt_get_option( 'enable_gallery_archive' ) ) {
			$has_archive = psmt_get_option( 'gallery_archive_slug' );
		}

		$args = array(
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'menu_position'       => 10,
			'menu_icon'           => 'dashicons-format-gallery',// sorry I don't have one.
			'show_in_admin_bar'   => true,
			'capability_type'     => 'post',
			'has_archive'         => $has_archive,
			'rewrite'             => array(
				'with_front' => false,
				'slug'       => psmt_get_gallery_post_type_rewrite_slug(),
			),
			'supports'            => array( 'title', 'comments', 'custom-fields' ),

		);

		$args['labels'] = $_labels;

		register_post_type( psmt_get_gallery_post_type(), $args );

		add_rewrite_endpoint( 'edit', EP_PAGES );

	}

	/**
	 * Register all internal taxonomies.
	 */
	private function register_taxonomies() {
		// register type taxonomy.
		$this->register_taxonomy( psmt_get_type_taxname(), array(
			'label'        => _x( 'Medientyp', 'Gallery Media Type', 'psourcemediathek' ),
			'labels'       => _x( 'Medientypen', 'Gallery Media Type Plural Name', 'psourcemediathek' ),
			'hierarchical' => false,
		) );

		// register component taxonomy.
		$this->register_taxonomy( psmt_get_component_taxname(), array(
			'label'        => _x( 'Komponente', 'Gallery Associated Type', 'psourcemediathek' ),
			'labels'       => _x( 'Komponenten', 'Gallery Associated Component Plural Name', 'psourcemediathek' ),
			'hierarchical' => false,
		) );

		// register status.
		$this->register_taxonomy( psmt_get_status_taxname(), array(
			'label'        => _x( 'Galeriestatus', 'Gallery privacy/status Type', 'psourcemediathek' ),
			'labels'       => _x( 'Galerien Status', 'Gallery Privacy Plural Name', 'psourcemediathek' ),
			'hierarchical' => false,
		) );

		$gallery_post_type = psmt_get_gallery_post_type();
		// associate taxonomy to gallery.
		register_taxonomy_for_object_type( psmt_get_type_taxname(), $gallery_post_type );
		register_taxonomy_for_object_type( psmt_get_component_taxname(), $gallery_post_type );
		register_taxonomy_for_object_type( psmt_get_status_taxname(), $gallery_post_type );

		$media_post_type = psmt_get_media_post_type();
		// associate taxonomies to media.
		register_taxonomy_for_object_type( psmt_get_type_taxname(), $media_post_type );
		register_taxonomy_for_object_type( psmt_get_component_taxname(), $media_post_type );
		register_taxonomy_for_object_type( psmt_get_status_taxname(), $media_post_type );

	}


	/**
	 * Register our internal taxonomies.
	 *
	 * @param string $taxonomy taxonomy name.
	 * @param array  $args taxonomy details params.
	 *
	 * @return bool
	 */
	private function register_taxonomy( $taxonomy, $args ) {

		if ( empty( $taxonomy ) ) {
			return false;
		}

		$labels = self::_get_tax_labels( $args['label'], $args['labels'] );

		if ( empty( $slug ) ) {
			$slug = $taxonomy;
		}

		register_taxonomy( $taxonomy, array( psmt_get_gallery_post_type(), psmt_get_media_post_type() ),
			array(
				'hierarchical'       => $args['hierarchical'],
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => true,
				'show_in_menu'       => false,
				'show_in_nav_menus'  => false,
				'show_ui'            => false,
				'show_tagcloud'      => false,
				'capabilities'       => array(
					'manage_terms' => 'manage_categories',
					'edit_terms'   => 'manage_categories',
					'delete_terms' => 'manage_categories',
					'assign_terms' => 'read', // allow subscribers to do it.
				),

				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array(
					//  'slug' => $slug,
					'with_front'   => true,
					'hierarchical' => $args['hierarchical'],
				),
			) );

		psourcemediathek()->taxonomies[ $taxonomy ] = $args;
	}

	/**
	 * Helper method to create labels.
	 *
	 * @param string $singular_name post type singular nam.
	 * @param string $plural_name post type plural name.
	 *
	 * @return array
	 */
	public function _get_tax_labels( $singular_name, $plural_name ) {

		$labels = array(
			'name'                       => $plural_name,
			'singular_name'              => $singular_name,
			'search_items'               => sprintf( __( 'Suche %s', 'psourcemediathek' ), $plural_name ),
			'popular_items'              => sprintf( __( 'Beliebte %s', 'psourcemediathek' ), $plural_name ),
			'all_items'                  => sprintf( __( 'Alle %s', 'psourcemediathek' ), $plural_name ),
			'parent_item'                => sprintf( __( 'Eltern %s', 'psourcemediathek' ), $singular_name ),
			'parent_item_colon'          => sprintf( __( 'Eltern %s:', 'psourcemediathek' ), $singular_name ),
			'edit_item'                  => sprintf( __( 'Bearbeite %s', 'psourcemediathek' ), $singular_name ),
			'update_item'                => sprintf( __( 'Aktualisiere %s', 'psourcemediathek' ), $singular_name ),
			'add_new_item'               => sprintf( __( 'Neue %s hinzufügen', 'psourcemediathek' ), $singular_name ),
			'new_item_name'              => sprintf( __( 'Neue %s Name', 'psourcemediathek' ), $singular_name ),
			'separate_items_with_commas' => sprintf( __( 'Trenne %s durch Kommas', 'psourcemediathek' ), $plural_name ),
			'add_or_remove_items'        => sprintf( __( '%s hinzufügen oder entfernen', 'psourcemediathek' ), $plural_name ),
			'choose_from_most_used'      => sprintf( __( 'Wähle aus den am häufigsten verwendeten %s', 'psourcemediathek' ), $plural_name ),

			//menu_name=>'' //nah let us leave it default
		);

		return $labels;
	}

}

PSMT_Post_Type_Helper::get_instance();
