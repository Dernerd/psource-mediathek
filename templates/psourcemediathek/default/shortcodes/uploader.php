<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="psmt-upload-shortcode">
    <div class="psmt-media-upload-container"><!-- psourcemediathek upload container -->
	<!-- append uploaded media here -->
	<div id="psmt-uploaded-media-list-shortcode" class="psmt-uploading-media-list">
		<ul>

		</ul>
	</div>

	<?php do_action( 'psmt_after_shortcode_upload_medialist' ); ?>

	<!-- drop files here for uploading -->
	<?php psmt_upload_dropzone( $context ); ?>
	<?php do_action( 'psmt_after_shortcode_upload_dropzone' ); ?>

	<!-- show any feedback here -->
	<div id="psmt-upload-feedback-shortcode" class="psmt-feedback">
		<ul></ul>
	</div>

	<?php do_action( 'psmt_after_shortcode_upload_feedback' ); ?>

	<?php if ( psmt_is_remote_enabled( 'shortcode' ) ) : ?>
        <!-- remote media -->
        <div class="psmt-remote-media-container">
            <div class="psmt-feedback psmt-remote-media-upload-feedback">
                <ul></ul>
            </div>
            <div class="psmt-remote-add-media-row">
                <input type="text" placeholder="<?php _e( 'Gib einen Link ein', 'psourcemediathek' );?>" value="" name="psmt-remote-media-url" id="psmt-remote-media-url" class="psmt-remote-media-url"/>
                <button id="psmt-add-remote-media" class="psmt-add-remote-media"><?php _e( '+Hinzufügen', 'psourcemediathek' ); ?></button>
            </div>

			<?php wp_nonce_field( 'psmt_add_media', 'psmt-remote-media-nonce' ); ?>
        </div>
        <!-- end of remote media -->
	<?php endif;?>

    <input type='hidden' name='psmt-context' class="psmt-context" id='psmt-context' value="<?php echo $context; ?>"/>

	<?php if ( $type ) : ?>
		<input type='hidden' name='psmt-uploading-media-type' class='psmt-uploading-media-type' value="<?php echo $type; ?>"/>
	<?php endif; ?>

	<?php if ( $skip_gallery_check ) : ?>
		<input type="hidden" name="psmt-shortcode-skip-gallery-check" value="1" id="psmt-shortcode-skip-gallery-check"/>
	<?php endif; ?>

	<?php if ( $gallery_id || $skip_gallery_check ) : ?>
		<input type='hidden' name='psmt-shortcode-upload-gallery-id' id='psmt-shortcode-upload-gallery-id' value="<?php echo $gallery_id; ?>"/>

	<?php else : ?>
		<?php
		psmt_list_galleries_dropdown( array(
			'name'           => 'psmt-shortcode-upload-gallery-id',
			'id'             => 'psmt-shortcode-upload-gallery-id',
			'selected'       => $gallery_id,
			'type'           => $type,
			'status'         => $status,
			'component'      => $component,
			'component_id'   => $component_id,
			'posts_per_page' => - 1,
			'label_empty'    => $label_empty,
		) );
		?>
	<?php endif; ?>
    </div><!-- end of psourcemediathek form container -->
</div>
