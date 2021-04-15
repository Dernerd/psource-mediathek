<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="psmt-media-upload-container"><!-- psourcemediathek upload container -->
    <!-- append uploaded media here -->
    <div id="psmt-uploaded-media-list-gallery" class="psmt-uploading-media-list">
        <ul>
        </ul>
    </div>

	<?php do_action( 'psmt_after_gallery_upload_medialist' ); ?>
	<?php if ( psmt_is_file_upload_enabled( 'gallery' ) ): ?>
        <!-- drop files here for uploading -->
		<?php psmt_upload_dropzone( 'gallery' ); ?>
		<?php do_action( 'psmt_after_gallery_upload_dropzone' ); ?>
	<?php endif; ?>
    <!-- show any feedback here -->
    <div id="psmt-upload-feedback-gallery" class="psmt-feedback">
        <ul></ul>
    </div>

    <?php do_action( 'psmt_after_gallery_upload_feedback' ); ?>
    <input type='hidden' name='psmt-context' id='psmt-context' class="psmt-context" value='gallery'/>
    <input type='hidden' name='psmt-upload-gallery-id' id='psmt-upload-gallery-id' value="<?php echo psmt_get_current_gallery_id(); ?>"/>
    <?php if ( psmt_is_remote_enabled( 'gallery' ) ) : ?>
    <!-- remote media -->
    <div class="psmt-remote-media-container">
        <div class="psmt-feedback psmt-remote-media-upload-feedback">
            <ul></ul>
        </div>
        <div class="psmt-remote-add-media-row">
            <input type="text" placeholder="<?php _e( 'Enter a link', 'psourcemediathek' );?>" value="" name="psmt-remote-media-url" id="psmt-remote-media-url" class="psmt-remote-media-url"/>
            <button id="psmt-add-remote-media" class="psmt-add-remote-media"><?php _e( '+Hinzufügen', 'psourcemediathek' ); ?></button>
        </div>

		<?php wp_nonce_field( 'psmt_add_media', 'psmt-remote-media-nonce' ); ?>
    </div>
    <!-- end of remote media -->
    <?php endif;?>

</div><!-- end of psourcemediathek form container -->