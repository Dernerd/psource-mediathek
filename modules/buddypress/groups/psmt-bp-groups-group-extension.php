<?php
/**
 * Group Gallery extensions.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * PsourceMediathek Group extension.
 */
if ( class_exists( 'BP_Group_Extension' ) ) :
	/**
	 * Group extension for PsourceMediathek to BuddyPress Group integration.
	 */
	class PSMT_Group_Gallery_Extension extends BP_Group_Extension {

		/**
		 * PSMT_Group_Gallery_Extension constructor.
		 */
		public function __construct() {
			$has_access = true;
			if ( bp_is_group() && groups_get_current_group() ) {
				$has_access = groups_get_current_group()->user_has_access;
			}

			$args = array(
				'slug'              => PSMT_GALLERY_SLUG,
				'name'              => __( 'Galerie', 'psourcemediathek' ),
				'visibility'        => 'public',
				'nav_item_position' => 80,
				'nav_item_name'     => __( 'Galerie', 'psourcemediathek' ),
				'enable_nav_item'   => psmt_group_is_gallery_enabled() && $has_access,// true by default.
				//'display_hook' => 'groups_custom_group_boxes', // meta box hook.
				//'template_file'=> 'groups/single/plugins.php',.
				'screens'           => array(
					'create' => array(
						'enabled' => false,
					),
					'edit'   => array(
						'enabled' => false,
					),
					'admin'  => array(
						//'metabox_context' => normal,
						//'metabox_priority' => '',
						'enabled' => false,
						//'name'	=> 'Gallery Settings',
						//'slug'	=> PSMT_GALLERY_SLUG,
						//'screen_callback' => '',
						//'screen_save_callback' => ''
					),
				),
			);
			parent::init( $args );


		}

		/**
		 * Render tab.
		 *
		 * @param int $group_id group id.
		 */
		public function display( $group_id = null ) {

			psmt_get_component_template_loader( 'groups' )->load_template();
		}

		/**
		 * The settings_screen() is the catch-all method for displaying the content
		 * of the edit, create, and Dashboard admin panels
		 */
		public function settings_screen( $group_id = null ) {

		}

		/**
		 * The settings_screen_save() contains the catch-all logic for saving
		 * settings from the edit, create, and Dashboard admin panels.
		 */
		public function settings_screen_save( $group_id = null ) {

		}
	}

	bp_register_group_extension( 'PSMT_Group_Gallery_Extension' );

endif;

/**
 * Display form for enabling/disabling PsourceMediathek
 */
function psmtp_group_enable_form() {

	if ( ! psmt_is_active_component( 'groups' ) ) {
		return;// do not show if gallery is not enabled for group component.
	}
	?>
	<div class="checkbox psmt-group-gallery-enable">
		<label for="psmt-enable-gallery">
			<input type="checkbox" name="psmt-enable-gallery" id="psmt-enable-gallery" value="yes" <?php echo checked( 1, psmt_group_is_gallery_enabled() ); ?>/>
			<?php _e( 'Enable Gallery', 'psourcemediathek' ) ?>
		</label>
	</div>
	<?php
}

add_action( 'bp_before_group_settings_admin', 'psmtp_group_enable_form' );
add_action( 'bp_before_group_settings_creation_step', 'psmtp_group_enable_form' );

/**
 * Save group Preference
 *
 * @param int $group_id group id.
 */
function psmt_group_save_preference( $group_id ) {

	$enabled = isset( $_POST['psmt-enable-gallery'] ) ? $_POST['psmt-enable-gallery'] : 'no';

	if ( $enabled != 'yes' && $enabled != 'no' ) {// invalid value.
		$enabled = 'no';// set it to no.
	}

	psmt_group_set_gallery_state( $group_id, $enabled );
}

add_action( 'groups_group_settings_edited', 'psmt_group_save_preference' );
add_action( 'groups_create_group', 'psmt_group_save_preference' );
add_action( 'groups_update_group', 'psmt_group_save_preference' );

