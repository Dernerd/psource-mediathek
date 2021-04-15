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
 * Helper class
 */
class PSMT_BuddyPress_Helper {

	/**
	 * Singleton instance
	 *
	 * @var PSMT_BuddyPress_Helper
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->setup();
	}

	/**
	 * Get singleton instance
	 *
	 * @return PSMT_BuddyPress_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup various hooks for BuddyPress integration.
	 */
	private function setup() {

		if ( ! psourcemediathek()->is_bp_active() ) {
			return;
		}

		add_action( 'psmt_setup', array( $this, 'init' ) );
		add_action( 'bp_include', array( $this, 'load' ), 2 );

		add_filter( 'psmt_get_current_component', array( $this, 'setup_current_component_type_for_members' ) );
		add_filter( 'psmt_get_current_component_id', array( $this, 'setup_current_component_id_for_members' ) );
		add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'fix_compat' ), 100 );
	}

	/**
	 * Load files required for BuddyPress compatibility.
	 */
	public function load() {

		$path = psourcemediathek()->get_path() . 'modules/buddypress/';

		$files = array(
			'psmt-bp-component.php',
			'activity/class-psmt-activity-media-cache-helper.php',
			'activity/psmt-activity-functions.php',
			'activity/psmt-activity-actions.php',
			'activity/psmt-activity-template.php',
			'activity/psmt-activity-hooks.php',
		);

		if ( bp_is_active( 'groups' ) ) {
			$files[] = 'groups/psmt-bp-groups-loader.php';
		}

		$notifications = false;

		if ( bp_is_active( 'notifications' ) && apply_filters( 'psmt_send_bp_notifications', false ) ) {
			$notifications = true;
			$files[] = 'notifications/psmt-notifications-functions.php';
			$files[] = 'notifications/psmt-bp-notifications-helper.php';
		}

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		if ( $notifications ) {
			PSMT_BP_Notifications_Helper::boot();
		}

		// PsourceMediathek BuddyPress module is loaded now.
		do_action( 'psmt_buddypress_module_loaded' );
	}

	/**
	 * Initialize settings for BuddyPress integration.
	 */
	public function init() {

		// Register status
		// if friends component is active, only then.
		psmt_register_status( array(
			'key'              => 'friendsonly',
			'label'            => __( 'Nur Freunde', 'psourcemediathek' ),
			'labels'           => array(
				'singular_name' => __( 'Nur Freunde', 'psourcemediathek' ),
				'plural_name'   => __( 'Nur Freunde', 'psourcemediathek' ),
			),
			'description'      => __( 'Nur Freunde Privacy Type', 'psourcemediathek' ),
			'callback'         => 'psmt_check_friends_access',
			'activity_privacy' => 'friends',
		) );

		// if followers component is active only then.
		if ( function_exists( 'bp_follow_is_following' ) ) {

			psmt_register_status( array(
				'key'              => 'followersonly',
				'label'            => __( 'Followers Only', 'psourcemediathek' ),
				'labels'           => array(
					'singular_name' => __( 'Followers Only', 'psourcemediathek' ),
					'plural_name'   => __( 'Followers Only', 'psourcemediathek' ),
				),
				'description'      => __( 'Followers Only Privacy Type', 'psourcemediathek' ),
				'callback'         => 'psmt_check_followers_access',
				'activity_privacy' => 'followers',
			) );

			psmt_register_status( array(
				'key'              => 'followingonly',
				'label'            => __( 'Persons I Follow', 'psourcemediathek' ),
				'labels'           => array(
					'singular_name' => __( 'Persons I Follow', 'psourcemediathek' ),
					'plural_name'   => __( 'Persons I Follow', 'psourcemediathek' ),
				),
				'description'      => __( 'Following Only Privacy Type', 'psourcemediathek' ),
				'callback'         => 'psmt_check_following_access',
				'activity_privacy' => 'following', // this is not implemented by BP Activity privacy at the moment.
			) );

		}//end of check for followers plugin

		psmt_register_component( array(
			'key'         => 'members',
			'label'       => __( 'Benutzergalerien', 'psourcemediathek' ),
			'labels'      => array(
				'singular_name' => __( 'User Gallery', 'psourcemediathek' ),
				'plural_name'   => __( 'Benutzergalerien', 'psourcemediathek' ),
			),
			'description' => __( 'Benutzergalerien', 'psourcemediathek' ),
		) );

		// add support.
		psmt_component_add_status_support( 'members', 'public' );
		psmt_component_add_status_support( 'members', 'private' );
		psmt_component_add_status_support( 'members', 'loggedin' );

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'friends' ) ) {
			psmt_component_add_status_support( 'members', 'friendsonly' );
		}

		// allow members component to support the followers privacy.
		if ( function_exists( 'bp_follow_is_following' ) ) {
			psmt_component_add_status_support( 'members', 'followersonly' );
			psmt_component_add_status_support( 'members', 'followingonly' );
		}

		// register type support.
		psmt_component_init_type_support( 'members' );

		psmt_register_component( array(
			'key'         => 'groups',
			'label'       => __( 'Gruppengalerien', 'psourcemediathek' ),
			'labels'      => array(
				'singular_name' => __( 'Gruppengalerien', 'psourcemediathek' ),
				'plural_name'   => __( 'Group Gallery', 'psourcemediathek' ),
			),
			'description' => __( 'Groups Galleries', 'psourcemediathek' ),
		) );

		psmt_component_add_status_support( 'groups', 'public' );
		psmt_component_add_status_support( 'groups', 'private' );
		psmt_component_add_status_support( 'groups', 'loggedin' );
		psmt_component_add_status_support( 'groups', 'groupsonly' );
		// register media sizes
		// initialize type support for groups component.
		psmt_component_init_type_support( 'groups' );

	}

	/**
	 * Setup the component_id provided by psmt_get_current_component_id() for the members section.
	 *
	 * @param int $component_id numeric component id.
	 *
	 * @return int
	 */
	public function setup_current_component_id_for_members( $component_id ) {

		if ( bp_is_user() ) {
			return bp_displayed_user_id();
		}

		return $component_id;
	}

	/**
	 * Setup current_component for psmt_get_current_component()
	 *
	 * @param string $component component type(members|groups|sitewide).
	 *
	 * @return string component.
	 */
	public function setup_current_component_type_for_members( $component ) {

		if ( bp_is_user() ) {
			return buddypress()->members->id;
		}

		return $component;
	}

	/**
	 * Since BuddyPress sets is_page=true on profile pages, we are forcing is_singular=true to avoid the notice.
	 */
	public function fix_compat() {
		if ( ! bp_is_user() || ! psmt_is_gallery_component() ) {
			return;
		}

		global $wp_query;
		$wp_query->is_singular = true; // override. BuddyPress should have done it.
	}
}

// Initialize.
PSMT_BuddyPress_Helper::get_instance();
