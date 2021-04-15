<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template specific hooks
 * Used to attach functionality to template
 */

/**
 * Show the publish to activity on psourcemediathek edit gallery page
 */
function psmt_gallery_show_publish_gallery_activity_button() {

	if ( ! psourcemediathek()->is_bp_active() ) {
		return;
	}

	$gallery_id = psmt_get_current_gallery_id();
	// if not a valid gallery id or no unpublished media exists, just don't show it.
	if ( ! $gallery_id || ! psmt_gallery_has_unpublished_media( $gallery_id ) ) {
		return;
	}

	$gallery = psmt_get_gallery( $gallery_id );

	$unpublished_media = psmt_gallery_get_unpublished_media( $gallery_id );
	// unpublished media count.
	$unpublished_media_count = count( $unpublished_media );

	$type = $gallery->type;

	$type_name = _n( psmt_get_type_singular_name( $type ), psmt_get_type_plural_name( $type ), $unpublished_media_count );

	// if we are here, there are unpublished media.
	?>
    <div id="psmt-unpublished-media-info">
        <p> <?php printf( __( 'Du hast %d %s nicht für Aktivitäten veröffentlicht.', 'psourcemediathek' ), $unpublished_media_count, strtolower( $type_name ) ); ?>
            <span class="psmt-gallery-publish-activity"><?php psmt_gallery_publish_activity_link( $gallery_id ); ?></span>
            <span class="psmt-gallery-unpublish-activity"><?php psmt_gallery_unpublished_media_delete_link( $gallery_id ); ?></span>
        </p>
    </div>

	<?php
}
add_action( 'psmt_before_bulkedit_media_form', 'psmt_gallery_show_publish_gallery_activity_button' );

/**
 * Generate the dropzone
 *
 * @param string $context context for the dropzone.
 */
function psmt_upload_dropzone( $context ) {
	?>
    <div id="psmt-upload-dropzone-<?php echo $context; ?>" class="psmt-dropzone">
        <div class="psmt-drag-drop-inside">
            <p class="psmt-drag-drop-info"><?php _e( 'Dateien hier ablegen', 'psourcemediathek' ); ?></p>
            <p><?php _e( 'oder', 'psourcemediathek' ); ?></p>
            <p class="psmt-drag-drop-buttons">
                <input id="psmt-upload-media-button-<?php echo $context; ?>" type="button" class="button psmt-button-select-files" value="<?php _e( 'Dateien auswählen', 'psourcemediathek' ); ?>"/>
            <p class="psmt-uploader-allowed-file-type-info"></p>
            <?php if ( psmt_get_option('show_max_upload_file_size' ) ) : ?>
                <p class="psmt-uploader-allowed-max-file-size-info"></p>
            <?php endif; ?>
        </div>
    </div>
	<?php wp_nonce_field( 'psmt-manage-gallery', '_psmt_manage_gallery_nonce' ); ?>
	<?php
}
