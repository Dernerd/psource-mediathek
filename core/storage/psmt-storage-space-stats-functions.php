<?php
/**
 * Storage space stats related functions.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is upload space available for the given component( based on type & ID )
 *
 * @param string $component component name(e.g groups, members , sitewide etc).
 * @param int    $component_id context based component id(group_id, user_id etc).
 *
 * @return boolean
 */
function psmt_has_available_space( $component, $component_id ) {

	// how much.
	$allowed_space = psmt_get_allowed_space( $component, $component_id );

	$used_space = psmt_get_used_space( $component, $component_id );

	if ( ( $allowed_space - $used_space ) <= 0 ) {
		return false;
	}

	return true;
}

/**
 * Get allowed space for the given component( In MB)
 *
 * @param string $component component name(e.g groups, members , sitewide etc).
 * @param int    $component_id context based component id(group_id, user_id etc).
 *
 * @return float : no. of MBs
 */
function psmt_get_allowed_space( $component, $component_id = null ) {
	$space_allowed = '';
	if ( ! empty( $component_id ) ) {

		if ( $component == 'members' ) {
			$space_allowed = psmt_get_user_meta( $component_id, 'psmt_upload_space', true );
		} elseif ( $component == 'groups' && function_exists( 'groups_get_groupmeta' ) ) {
			$space_allowed = groups_get_groupmeta( $component_id, 'psmt_upload_space', true );
		}
	}

	if ( empty( $component_id ) || ! is_numeric( $space_allowed ) ) {
		// if owner id is empty
		// get the gallery/group space.
		if ( $component == 'members' ) {
			$space_allowed = psmt_get_option( 'psmt_upload_space' );
		} elseif ( $component == 'groups' ) {
			$space_allowed = psmt_get_option( 'psmt_upload_space_groups' );
		}
	}

	if ( ! is_numeric( $space_allowed ) ) {
		$space_allowed = psmt_get_option( 'psmt_upload_space', 10 );
	}

	// allow to override for specific users/groups.
	return apply_filters( 'psmt_allowed_space', $space_allowed, $component, $component_id );
}

/**
 * Get the Used space by a component
 *
 * @param string $component component name(e.g groups, members , sitewide etc).
 * @param int    $component_id context based component id(group_id, user_id etc).
 *
 * @return float storage space in MB
 */
function psmt_get_used_space( $component, $component_id ) {

	// get default storage manager.
	$storage_manager = psmt_get_storage_manager();

	return apply_filters( 'psmt_used_space', $storage_manager->get_used_space( $component, $component_id ), $component, $component_id );
}

/**
 * Get the remaining space in MBs
 *
 * @param string $component component name(e.g groups, members , sitewide etc).
 * @param int    $component_id context based component id(group_id, user_id etc).
 *
 * @return float
 */
function psmt_get_remaining_space( $component, $component_id ) {

	$allowed = psmt_get_allowed_space( $component, $component_id );
	$used    = psmt_get_used_space( $component, $component_id );

	return floatval( $allowed - $used );
}

/**
 * Display message showing the used space.
 *
 * @param string $component component name(e.g groups, members , sitewide etc).
 * @param int    $component_id context based component id(group_id, user_id etc).
 */
function psmt_display_space_usage( $component = null, $component_id = null ) {

	if ( ! $component ) {
		$component = psmt_get_current_component();
	}

	if ( ! $component_id ) {
		$component_id = psmt_get_current_component_id();
	}

	$total_space = psmt_get_allowed_space( $component, $component_id );

	$used = psmt_get_used_space( $component, $component_id );

	if ( $used > $total_space ) {
		$percentused = '100';
	} else {
		$percentused = ( $used / $total_space ) * 100;
	}

	$decimals = $total_space % 1000 == 0 ? 0 : 1;

	if ( $total_space >= 1000 ) {
		$total_space = number_format( $total_space / 1000, $decimals );
		$total_space .= __( 'GB', 'psourcemediathek' );
	} else {
		$total_space .= __( 'MB', 'psourcemediathek' );
	}

	?>
    <strong><?php printf( __( 'Du hast <span> %1$s%%</span> von %2$s Speicherplatz verbleibend', 'psourcemediathek' ), number_format( 100 - $percentused ), $total_space ); ?></strong>
	<?php
}

