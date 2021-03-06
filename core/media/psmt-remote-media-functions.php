<?php
/**
 * Remote Media functions.
 *
 * @package    PsourceMediathek
 * @subpackage Core/Media
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 0 );

/**
 * Is Remote media enabled?
 *
 * @param string $context context. Possible values 'activity', 'gallery', 'shortcode' etc.
 *
 * @return bool
 */
function psmt_is_remote_enabled( $context = '' ) {
	return psmt_get_option( 'enable_remote', 1 );
}

/**
 * Check if oembed enabled?
 *
 * @return bool
 */
function psmt_is_oembed_enabled() {
	return psmt_is_remote_enabled() && psmt_get_option( 'enable_oembed', 1 );
}

/**
 * Check if remote file suport is enabled.
 *
 * @return bool
 */
function psmt_is_remote_file_enabled() {
	return psmt_is_remote_enabled() && psmt_get_option( 'enable_remote_file', 0 );
}

/**
 * Check if remote file download is enabled.
 *
 * @return bool
 */
function psmt_is_remote_file_download_enabled() {
	return psmt_get_option( 'download_remote_file', 0 );
}

/**
 * Is given media remote?
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return boolean
 */
function psmt_is_remote_media( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_is_remote', $media->is_remote );

}

/**
 * Is it Oembed media?
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return boolean
 */
function psmt_is_oembed_media( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_is_oembed_media', $media->is_oembed );
}

/**
 * Get oembed details.
 *
 * @param int    $media_id media id.
 * @param string $size expected size.
 *
 * @return string
 */
function psmt_get_oembed_content( $media_id, $size = '' ) {
	$media = psmt_get_media( $media_id );

	if ( ! $media->is_oembed ) {
		return '';
	}

	if ( ! $size || 'original' == $size || 'large' == $size ) {
		$cache_key      = '_psmt_oembed_content';
		$cache_key_time = '_psmt_oembed_time';
		$size           = 'large';
	} else {
		$cache_key      = '_psmt_oembed_content_' . $size;
		$cache_key_time = '_psmt_oembed_time_' . $size;
	}

	// should we check for stale? may be in next version.
	$content = psmt_get_media_meta( $media_id, $cache_key, true );

	if ( $content ) {
		return $content;
	}

	$source = $media->source;

	if ( ! $source ) {
		return '';
	}

	$size_info = psmt_get_media_size( $size );
	$args      = array();

	if ( $size_info ) {
		$args['width'] = $size_info['width'];
	}

	$importer = new PSMT_Remote_Media_Parser( $source, $args );

	if ( ! $importer->is_oembed ) {
		return '';
	}

	$html = $importer->get_html();

	if ( ! $html ) {
		return '';
	}

	psmt_update_media_meta( $media_id, $cache_key, $html );
	psmt_update_media_meta( $media_id, $cache_key_time, time() );

	return $html;
}
