<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// only load if not already loaded.
if ( ! class_exists( 'PSMT_Admin_Settings_Page' ) ) :

	// $ob_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
	$ob_path = psourcemediathek()->get_path() . 'admin/psmt-settings-manager/';

	require_once $ob_path . 'core/class-psmt-admin-settings-field.php';
	require_once $ob_path . 'core/class-psmt-admin-settings-section.php';
	require_once $ob_path . 'core/class-psmt-admin-settings-panel.php';
	require_once $ob_path . 'core/class-psmt-admin-settings-page.php';
	require_once $ob_path . 'core/class-psmt-admin-settings-helper.php';

endif;


/**
 * Register a loader to load Field Class dynamically if they exist in fields/ directory
 *
 * @param string $class name of the class.
 */
function psmt_admin_settings_field_class_loader( $class ) {

	// let us just get the part after PSMT_Admin_Settings_Field_ string e.g for PSMT_Admin_Settings_Field_Text class it loads fields/text.php.
	$file_name = strtolower( str_replace( 'PSMT_Admin_Settings_Field_', '', $class ) );

	// let us reach to the file.
	$file = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . $file_name . '.php';

	if ( is_readable( $file ) ) {
		require_once $file;
	}

}

spl_autoload_register( 'psmt_admin_settings_field_class_loader' );
