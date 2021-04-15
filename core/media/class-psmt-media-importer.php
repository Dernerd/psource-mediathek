<?php
/**
 * PsourceMediathek Media Importer
 *
 * Allows importing files, attachment etc easily.
 *
 * @package    PsourceMediathek
 * @subpackage Core/Media
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit( 0 );

/**
 * Importer class.
 *
 * It hides the implementation details.
 *
 * @internal
 *
 * Do not directly use it.
 *
 * @see psmt_import_attachment()
 * @see psmt_import_file()
 */
class PSMT_Media_Importer {

	/**
	 * Import a WordPress attachment to a PsourceMediathek gallery.
	 *
	 * @since 1.3.8
	 *
	 * @see psmt_import_attachment() for usage.
	 *
	 * @param int             $attachment_id to be imported.
	 * @param int|PSMT_Gallery $gallery_id where the media will be moved.
	 * @param array           $override parameters to override while updating media details.
	 *
	 * @return WP_Error|int
	 */
	public static function import_attachment( $attachment_id, $gallery_id, $override = array() ) {

		$media_id = $attachment_id;

		if ( ! $media_id ) {
			return new WP_Error( 'invalid_media', __( 'Invalid attachment id given.', 'psourcemediathek' ) );
		}

		$post = get_post( $media_id );

		if ( ! $post || 'attachment' !== $post->post_type ) {
			return new WP_Error( 'invalid_attachment', __( 'Attachment is not valid.', 'psourcemediathek' ) );
		}

		$gallery = psmt_get_gallery( $gallery_id );

		if ( ! $gallery ) {
			return new WP_Error( 'gallery_not_exists', sprintf( __( 'Gallery Id %d does not exist or is not a valid gallery.', 'psourcemediathek' ), $gallery_id ) );
		}

		$file = get_attached_file( $media_id );

		$type = psmt_get_media_type_from_extension( psmt_get_file_extension( $file ) );

		if ( ! $type ) {
			return new WP_Error( 'invalid_type', __( 'Invalid media type.', 'psourcemediathek' ) );
		}

		if ( $type !== $gallery->type ) {
			return new WP_Error( 'invalid_gallery_type', __( 'Invalid file type for the gallery.', 'psourcemediathek' ) );
		}

		$storage = psmt_local_storage();

		// Copy file to PsourceMediathek dir.
		$info = $storage->import_file( $file, $gallery_id );

		if ( is_wp_error( $info ) ) {
			return $info;
		}

		$url       = $info['url'];
		$mime_type = $info['type'];
		$new_file  = $info['file'];

		// if we are here, let us delete all attachment sizes except the original.
		$storage->delete_all_sizes( $media_id );
		// all is good, delete original file(we have copied it before).
		@unlink( $file );

		// mark media as psmt media.
		psmt_update_media_meta( $media_id, '_psmt_is_psmt_media', 1 );

		// update media info.
		$details = wp_parse_args( $override, array(
			'id'             => $media_id,
			'gallery_id'     => $gallery->id,
			'post_parent'    => $gallery->id,
			'user_id'        => $gallery->user_id,
			'status'         => $gallery->status,
			'component'      => $gallery->component,
			'component_id'   => $gallery->component_id,
			'context'        => 'gallery',
			'type'           => $type,
			'storage_method' => 'local',
			'sort_order'     => 0, // sort order.
			'is_orphan'      => 0,
			'is_uploaded'    => 1,
			'is_remote'      => 0,
			'is_imported'    => 1,
			'mime_type'      => $mime_type,
			'src'            => $new_file,
			'url'            => $url,
		) );

		psmt_update_media( $details );
		psmt_gallery_increment_media_count( $gallery_id );
		// set attached file.
		update_attached_file( $media_id, $new_file );
		// regenerate.
		wp_update_attachment_metadata( $media_id, psmt_generate_media_metadata( $media_id, $new_file ) );
		// enjoy.
		return $media_id;
	}

	/**
	 * Import a local file from server to PsourceMediathek gallery.
	 *
	 * It does not delete the original file.
	 *
	 * @since 1.3.8
	 *
	 * @see psmt_import_file() for usage.
	 *
	 * @param string $file absolute path of the file.
	 * @param int    $gallery_id gallery where it should be imported.
	 * @param array  $override parameters to override while updating media details.
	 *
	 * @return WP_Error|int
	 */
	public static function import_file( $file, $gallery_id, $override = array() ) {

		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			return new WP_Error( 'file_not_readable', sprintf( __( 'File %s is not readable.', 'psourcemediathek' ), $file ) );
		}

		$gallery = psmt_get_gallery( $gallery_id );

		if ( ! $gallery ) {
			return new WP_Error( 'gallery_not_exists', sprintf( __( 'Gallery Id %d does not exist or is not a valid gallery.', 'psourcemediathek' ), $gallery_id ) );
		}

		$type = psmt_get_media_type_from_extension( psmt_get_file_extension( $file ) );

		if ( ! $type ) {
			return new WP_Error( 'invalid_file_type', __( 'Invalid file type.', 'psourcemediathek' ) );
		}

		if ( $type !== $gallery->type ) {
			return new WP_Error( 'invalid_gallery_type', __( 'Invalid file type for the gallery.', 'psourcemediathek' ) );
		}

		$storage = psmt_local_storage();
		// copy.
		$info = $storage->import_file( $file, $gallery_id );

		if ( is_wp_error( $info ) ) {
			return $info;
		}

		$url       = $info['url'];
		$mime_type = $info['type'];
		$new_file  = $info['file'];

		// file was uploaded successfully.
		$title = wp_basename( $info['file'] );

		$title_parts = pathinfo( $title );
		$title       = trim( substr( $title, 0, - ( 1 + strlen( $title_parts['extension'] ) ) ) );

		$content = '';

		$meta = $storage->get_meta( $info );


		$title_desc = psmt_get_title_desc_from_meta( $type, $meta );

		if ( ! empty( $title_desc ) ) {

			if ( empty( $title ) && ! empty( $title_desc['title'] ) ) {
				$title = $title_desc['title'];
			}

			if ( empty( $content ) && ! empty( $title_desc['content'] ) ) {
				$content = $title_desc['content'];
			}
		}

		$media_data = array(
			'title'          => $title,
			'description'    => $content,
			'gallery_id'     => $gallery_id,
			'user_id'        => get_current_user_id(),
			'is_remote'      => false,
			'type'           => $type,
			'mime_type'      => $mime_type,
			'src'            => $new_file,
			'url'            => $url,
			'status'         => $gallery->status,
			'comment_status' => 'open',
			'storage_method' => psmt_get_storage_method(),
			'component_id'   => $gallery->component_id,
			'component'      => $gallery->component,
			'context'        => 'gallery',
			'is_orphan'      => 0,
		);
		// do the override.
		$media_data = wp_parse_args( $override, $media_data );
		$id         = psmt_add_media( $media_data );

		if ( ! $id ) {
			return new WP_Error( 'media_not_added', __( 'Unable to import media', 'psourcemediathek' ) );
		}

		psmt_gallery_increment_media_count( $gallery_id );

		return $id;
	}
}
