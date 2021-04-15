<?php
/**
 * Media link tags.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print media permalink.
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_permalink( $media = null ) {
	echo psmt_get_media_permalink( $media );

}

/**
 * Get media permalink
 *
 * @param PSMT_Media $media media object.
 *
 * @return string
 */
function psmt_get_media_permalink( $media = null ) {

	$media = psmt_get_media( $media );

	$gallery_permalink = untrailingslashit( psmt_get_gallery_permalink( $media->gallery_id ) );

	if ( $media->component == 'sitewide' ) {
		$gallery_permalink .= '/media';
	}

	return apply_filters( 'psmt_get_media_permalink', trailingslashit( $gallery_permalink . '/' . psmt_get_media_slug( $media ) ) );

}

/**
 * An alias for psmt_media_permalink.
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_url( $media = null ) {
	echo psmt_get_media_url( $media );
}

/**
 * Alias of psmt_get_media_permalink.
 *
 * @param PSMT_Media $media media object.
 *
 * @return string
 */
function psmt_get_media_url( $media = null ) {
	return psmt_get_media_permalink( $media );
}

/**
 * Print Edit Media URL
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_edit_url( $media = null ) {
	echo psmt_get_media_edit_url( $media );
}

/**
 * Get the Edit media URL
 *
 * @param PSMT_Media $media media object.
 *
 * @return string
 */
function psmt_get_media_edit_url( $media = null ) {
	$permalink = psmt_get_media_permalink( $media );
	return $permalink . 'edit/';
}

/**
 * Print delete media url
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_delete_url( $media = null ) {
	echo psmt_get_media_delete_url( $media );
}

/**
 * Get Media delete url
 *
 * @param PSMT_Media $media media object.
 *
 * @return string
 */
function psmt_get_media_delete_url( $media = null ) {

	$media = psmt_get_media( $media );

	// needs improvement.
	$link = psmt_get_media_edit_url( $media ) . 'delete/?psmt-action=delete-media&psmt-nonce=' . wp_create_nonce( 'psmt-delete-media' ) . '&psmt-media-id=' . $media->id;

	return $link;

}

/**
 * Print media cover delete url.
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_cover_delete_url( $media = null ) {
	echo psmt_get_media_cover_delete_url( $media );
}

/**
 * Get media cover delete url.
 *
 * @param PSMT_Media $media media object.
 *
 * @return string
 */
function psmt_get_media_cover_delete_url( $media = null ) {

	$link = psmt_get_media_edit_url( $media ) . '?_wpnonce=' . wp_create_nonce( 'cover-delete' ) . '&psmt-action=cover-delete&media_id=' . $media->id;

	$link = apply_filters( 'psmt_get_media_cover_delete_url', $link, $media );

	return $link;
}
