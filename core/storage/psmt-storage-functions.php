<?php
/**
 * Storage related functions.
 *
 * @package psourcemediathek.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a new storage manager
 *
 * @param string              $method storage method name.
 * @param PSMT_Storage_Manager $object storage manager.
 */
function psmt_register_storage_manager( $method, PSMT_Storage_Manager $object ) {
	psourcemediathek()->storage_managers[ $method ] = $object;
}

/**
 * De register a previously registered storage method
 *
 * @param string $method storage method name(e.g local, remote etc).
 */
function psmt_deregister_storage_manager( $method ) {

	$psourcemediathek = psourcemediathek();

	unset( $psourcemediathek->storage_managers[ $method ] );
}

/**
 * Get all registered storage managers
 *
 * @return PSMT_Storage_Manager[]
 */
function psmt_get_registered_storage_managers() {
	return psourcemediathek()->storage_managers;
}

/**
 * Get storage manager for the given media or the default active storage manager
 *
 * @param int|string $id_or_method Media id or the storage method.
 *
 * @return boolean|PSMT_Storage_Manager
 */
function psmt_get_storage_manager( $id_or_method = '' ) {

	if ( ! $id_or_method || $id_or_method && is_numeric( $id_or_method ) ) {
		$method = psmt_get_storage_method( $id_or_method );
	} else {
		$method = trim( $id_or_method );
	}

	$adaptors = psmt_get_registered_storage_managers();

	if ( isset( $adaptors[ $method ] ) ) {
		return $adaptors[ $method ];
	}

	// adaptor not found for this method, we might have thrown exception as weel.
	return false;
}

/**
 * Get the storage method used for the given media.
 *
 * @param int $id media id.
 *
 * @return string
 */
function psmt_get_storage_method( $id = 0 ) {

	$type = '';

	if ( $id ) {
		$type = psmt_get_media_meta( $id, '_psmt_storage_method', true );
	}

	if ( ! $type ) {
		$type = psmt_get_default_storage_method();
	}

	return apply_filters( 'psmt_get_storage_method', $type, $id );
}

/**
 * Get default storage method (e.g local|s3|ftp etc)
 *
 * @return string default storage method
 */
function psmt_get_default_storage_method() {

	return apply_filters( 'psmt_get_default_storage_method', psmt_get_option( 'default_storage', 'local' ) );
}

/**
 * Get upload context.
 *
 * @param int    $media_id media id.
 * @param string $context current context.
 *
 * @return string
 */
function psmt_get_upload_context( $media_id = null, $context = null ) {

	$current_context = '';

	if ( $media_id ) {
		$current_context = psmt_get_media_meta( $media_id, '_psmt_context', true );
	}

	// if the media upload context is not known, let us see if a default is given.
	if ( ! $current_context && $context ) {
		$current_context = $context;
	}

	if ( ! $current_context ) {
		$current_context = 'profile';
	}

	return apply_filters( 'psmt_get_upload_context', $current_context, $media_id, $context );
}
