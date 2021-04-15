<?php
/**
 * Group specific loader.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load PsourceMediathek Group extension
 */
function psmt_group_extension_load() {

	$files = array(
		'psmt-bp-groups-actions.php',
		'psmt-bp-groups-functions.php',
		'psmt-bp-groups-hooks.php',
		'psmt-bp-groups-group-extension.php',
	);

	$path = psourcemediathek()->get_path() . 'modules/buddypress/groups/';

	foreach ( $files as $file ) {
		require_once $path . $file;
	}

	do_action( 'psmt_group_extension_loaded' );
}

add_action( 'bp_loaded', 'psmt_group_extension_load' );

//psmt_group_extension_load();

/**
 * Do group specific initialization.
 */
function psmt_group_init() {

	psmt_register_status( array(
		'key'              => 'groupsonly',
		'label'            => __( 'Nur Gruppe', 'psourcemediathek' ),
		'labels'           => array(
			'singular_name' => __( 'Nur Gruppe', 'psourcemediathek' ),
			'plural_name'   => __( 'Nur Gruppe', 'psourcemediathek' ),
		),
		'description'      => __( 'Nur Gruppe Privacy Type', 'psourcemediathek' ),
		'callback'         => 'psmt_check_groups_access',
		'activity_privacy' => 'grouponly',
	) );
}

add_action( 'psmt_setup', 'psmt_group_init' );

/**
 * Filter status lists to set/unset group specific statuses.
 *
 * @param array $statuses statuses.
 *
 * @return mixed
 */
function psmt_group_filter_status( $statuses ) {

	if ( bp_is_group() ) {
		unset( $statuses['friends'] );
		unset( $statuses['private'] );
	} else {
		unset( $statuses['groupsonly'] );
	}

	return $statuses;
}

add_filter( 'psmt_get_editable_statuses', 'psmt_group_filter_status' );
