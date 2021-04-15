<?php
/**
 * BuddyPress Component loader etc.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsourceMediathek Core Component for BuddyPress
 * Adds support for the Media upload/display to various bp component
 */
class PSMT_BuddyPress_Component extends BP_Component {

	/**
	 * Singleton instance.
	 *
	 * @var PSMT_BuddyPress_Component
	 */
	private static $instance;

	/**
	 * Get the singleton instance
	 *
	 * @return PSMT_BuddyPress_Component
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Everything starts here
	 */
	private function __construct() {

		parent::start(
			'psourcemediathek', // unique id.
			__( 'Galerie', 'psourcemediathek' ),
			untrailingslashit( psourcemediathek()->get_path() ) // base path.
		);
		// mark it as active component, otherwise notifications will not be rendered.
		buddypress()->active_components[ $this->id ] = 1;

	}

	/**
	 * Include files, we don't as we are using the psourcemediathek->core_init to do it
	 *
	 * @param array $args files to be included.
	 */
	public function includes( $args = array() ) {
	}

	/**
	 * Setup everything for BuddyPress Specific installation
	 *
	 * @param array $args global args.
	 */
	public function setup_globals( $args = array() ) {

		$bp = buddypress();

		$globals = array(
			'slug'                  => PSMT_GALLERY_SLUG,
			'root_slug'             => isset( $bp->pages->psourcemediathek->slug ) ? $bp->pages->psourcemediathek->slug : PSMT_GALLERY_SLUG,
			'notification_callback' => 'psmt_format_notifications',
			'has_directory'         => psmt_get_option( 'has_gallery_directory' ),
			'search_string'         => __( 'Galerien durchsuchen...', 'psourcemediathek' ),
			'directory_title'       => isset( $bp->pages->psourcemediathek->id ) ? get_the_title( $bp->pages->psourcemediathek->id ) : __( 'Galerieverzeichnis', 'psourcemediathek' ),
		);

		parent::setup_globals( $globals );
	}

	/**
	 * Setup nav.
	 *
	 * @param array $main main nav items array.
	 * @param array $sub sub nav items array.
	 *
	 * @return bool
	 */
	public function setup_nav( $main = array(), $sub = array() ) {

		$component    = 'members';
		$component_id = psmt_get_current_component_id();

		if ( ! psmt_is_enabled( $component, $component_id ) ) {
			// allow to disable user galleries in case they don't want it.
			return false;
		}

		$view_helper = PSMT_Gallery_Screens::get_instance();

		// Add 'Gallery' to the user's main navigation.
		$main_nav = array(
			'name'                => sprintf( __( 'Galerie <span>%d</span>', 'psourcemediathek' ), psmt_get_total_gallery_for_user() ),
			'slug'                => $this->slug,
			'position'            => 86,
			'screen_function'     => array( $view_helper, 'render' ),
			'default_subnav_slug' => 'my-galleries',
			'item_css_id'         => $this->id,
		);

		if ( bp_is_user() ) {
			$user_domain = bp_displayed_user_domain();
		} else {
			$user_domain = bp_loggedin_user_domain();
		}

		$gallery_link = trailingslashit( $user_domain . $this->slug ); // with a trailing slash.

		// Add the My Gallery nav item.
		$sub_nav[] = array(
			'name'            => __( 'Meine Galerie', 'psourcemediathek' ),
			'slug'            => 'my-galleries',
			'parent_url'      => $gallery_link,
			'parent_slug'     => $this->slug,
			'screen_function' => array( $view_helper, 'render' ),
			'position'        => 10,
			'item_css_id'     => 'gallery-my-gallery',
		);

		if ( psmt_user_can_create_gallery( $component, get_current_user_id() ) ) {
			// Add the Create gallery link to gallery nav.
			$sub_nav[] = array(
				'name'            => __( 'Erstelle eine Galerie', 'psourcemediathek' ),
				'slug'            => 'create',
				'parent_url'      => $gallery_link,
				'parent_slug'     => $this->slug,
				'screen_function' => array( $view_helper, 'render' ),
				'user_has_access' => bp_is_my_profile(),
				'position'        => 20,
			);

		}

		if ( psmt_component_has_type_filters_enabled( $component, $component_id ) ) {
			$i               = 10;
			$supported_types = psmt_component_get_supported_types( $component );

			foreach ( $supported_types as $type ) {

				if ( ! psmt_is_active_type( $type ) ) {
					continue;
				}

				$type_object = psmt_get_type_object( $type );

				$sub_nav[] = array(
					'name'            => $type_object->label,
					'slug'            => 'type/' . $type,
					'parent_url'      => $gallery_link,
					'parent_slug'     => $this->slug,
					'screen_function' => array( $view_helper, 'render' ),
					// 'user_has_access'	=> bp_is_my_profile(),
					'position'        => 20 + $i,
				);

				$i = $i + 10; // increment the position.
			}
		}

		// Add the Upload link to gallery nav
		/*$sub_nav[] = array(
			'name'				=> __( 'Upload', 'psourcemediathek'),
			'slug'				=> 'upload',
			'parent_url'		=> $gallery_link,
			'parent_slug'		=> $this->slug,
			'screen_function'	=> array( $view_helper, 'upload_media' ),
			'user_has_access'	=> bp_is_my_profile(),
			'position'			=> 30
		);*/

		parent::setup_nav( $main_nav, $sub_nav );

		// disallow these names in various lists
		// we have yet to implement it.
		$this->forbidden_names = apply_filters( 'psmt_forbidden_names', array(
			'gallery',
			'galleries',
			'my-gallery',
			'create',
			'delete',
			'upload',
			'add',
			'edit',
			'admin',
			'request',
			'upload',
			'tags',
			'audio',
			'video',
			'photo',
		) );

		// use this to extend the valid status.
		$this->valid_status = apply_filters( 'psmt_valid_gallery_status', array_keys( psmt_get_active_statuses() ) );

		do_action( 'psmt_setup_nav' ); // $bp->gallery->current_gallery->user_has_access.
	}


	/**
	 * Setup title for various screens
	 */
	public function setup_title() {
		parent::setup_title();
	}

	/**
	 * Set up the Toolbar.
	 *
	 * @param array $wp_admin_nav See {BP_Component::setup_admin_bar()}
	 *        for details.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		$bp = buddypress();

		// Menus for logged in user if the members gallery is enabled.
		if ( is_user_logged_in() && psmt_is_enabled( 'members', bp_loggedin_user_id() ) ) {

			$component    = 'members';
			$component_id = get_current_user_id();

			$gallery_link = psmt_get_gallery_base_url( $component, $component_id );

			$title        = __( 'Galerie', 'psourcemediathek' );
			$my_galleries = __( 'Meine Galerie', 'psourcemediathek' );
			$create       = __( 'Erstellen', 'psourcemediathek' );

			// Add main psourcemediathek menu.
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $gallery_link,
			);
			// Add main psourcemediathek menu.
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-my-galleries',
				'title'  => $my_galleries,
				'href'   => $gallery_link,
			);

			if ( psmt_user_can_create_gallery( $component, $component_id ) ) {

				$wp_admin_nav[] = array(
					'parent' => 'my-account-' . $this->id,
					'id'     => 'my-account-' . $this->id . '-create',
					'title'  => $create,
					'href'   => psmt_get_gallery_create_url( $component, $component_id ),
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}
}


/**
 * Setup PsourceMediathek BP Component
 */
function psmt_setup_psourcemediathek_component() {

	$bp             = buddypress();
	$bp->psourcemediathek = PSMT_BuddyPress_Component::get_instance();
}
add_action( 'bp_loaded', 'psmt_setup_psourcemediathek_component' );
