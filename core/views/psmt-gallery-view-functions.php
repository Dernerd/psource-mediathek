<?php
/**
 * View functions.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the view associated with current gallery
 *
 * @param int    $gallery_id gallery id.
 * @param string $default fallback view id.
 *
 * @return string view id
 */
function psmt_get_gallery_view_id( $gallery_id, $default = '' ) {

	$view = psmt_get_gallery_view( psmt_get_gallery( $gallery_id ) );
	if (  $view ) {
		$view_id = $view->get_id();
	} else {
		$view_id = '';
	}

	return $view_id;
}

/**
 * Set gallery view
 *
 * @param int    $gallery_id gallery id.
 * @param string $view_id view id to set.
 *
 * @return int|bool
 */
function psmt_update_gallery_view_id( $gallery_id, $view_id ) {
	return psmt_update_gallery_meta( $gallery_id, '_psmt_view', $view_id );
}

/**
 * Delete gallery view
 *
 * @param int $gallery_id gallery id.
 *
 * @return bool
 */
function psmt_delete_gallery_view_id( $gallery_id ) {
	return psmt_delete_gallery_meta( $gallery_id, '_psmt_view' );
}

/**
 * Get template loader for the given component.
 *
 * @param string $component component name ( e.g groups, members, sitewide etc).
 *
 * @return PSMT_Gallery_Template_Loader
 */
function psmt_get_component_template_loader( $component ) {

	if ( ! class_exists( 'PSMT_Gallery_View_Loader' ) ) {
		$path = psourcemediathek()->get_path() . 'core/views/loaders/';

		require_once $path . 'class-psmt-gallery-view-loader.php';
		require_once $path . 'class-psmt-members-gallery-template-loader.php';
		require_once $path . 'class-psmt-groups-gallery-template-loader.php';
		require_once $path . 'class-psmt-sitewide-gallery-template-loader.php';
	}

	if ( $component == 'groups' ) {
		$loader = PSMT_Groups_Gallery_Template_Loader::get_instance();
	} elseif ( $component == 'members' ) {
		$loader = PSMT_Members_Gallery_Template_Loader::get_instance();
	} else {
		$loader = PSMT_Sitewide_Gallery_Template_Loader::get_instance();
	}

	return $loader;
}

/**
 * Get all registered views
 *
 * @param string $type gallery type.
 *
 * @return PSMT_Gallery_View[] | boolean
 */
function psmt_get_registered_gallery_views( $type ) {

	if ( ! $type ) {
		return false;
	}

	$views = array();

	$psmt = psourcemediathek();

	if ( isset( $psmt->gallery_views[ $type ] ) ) {
		$views = $psmt->gallery_views[ $type ];
	} else {
		// get the default view.
		$views = (array) $psmt->gallery_views['default'];
	}

	return $views;
}

/**
 * Get the Gallery View for given component.
 *
 * @param string $component component name( 'groups', 'members' etc).
 * @param string $type gallery type( 'photo', 'doc' etc).
 *
 * @return string
 */
function psmt_get_component_gallery_view( $component, $type ) {

	$key = "{$component}_{$type}_gallery_default_view";

	$view_id = psmt_get_option( $key, 'default' );

	return $view_id;
}

/**
 * Get activity view id
 *
 * @param string $type gallery type.
 *
 * @return string
 */
function psmt_get_activity_view_id( $type ) {

	$key = "activity_{$type}_default_view";

	$view_id = psmt_get_option( $key, 'default' );

	return $view_id;
}

/**
 * Get activity view renderer.
 *
 * @param string $type media type.
 * @param int    $activity_id activity id.
 *
 * @return boolean | PSMT_Gallery_View
 */
function psmt_get_activity_view( $type, $activity_id = null ) {

	if ( ! $type ) {
		return false;
	}

	$view_id = psmt_get_activity_view_id( $type );

	// if view id is still not found, lets fallback to default.
	if ( ! $view_id ) {
		$view_id = 'default';
	}

	// if we are here, we know the view_id and the type.
	$psmt  = psourcemediathek();
	$view = null;

	if ( isset( $psmt->gallery_views[ $type ][ $view_id ] ) ) {
		$view = $psmt->gallery_views[ $type ][ $view_id ];
	} else {
		$view = $psmt->gallery_views[ $type ]['default'];
	}

	return apply_filters( 'psmt_get_activity_view', $view, $type );
}
