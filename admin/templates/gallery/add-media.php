<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a href='#' id='psmt-reload-add-media-tab' class='psmt-reload' title="<?php _e( 'Lade Fenster zum Hinzufügen von Medien neu', 'psourcemediathek' );?>"><span class="dashicons dashicons-update"></span><?php _e( 'Neu laden', 'psourcemediathek' );?></a>
<div class="psmt-media-upload-container"><!-- psourcemediathek upload container -->
<!-- append uploaded media here -->
<div id="psmt-uploaded-media-list-admin" class="psmt-uploading-media-list">
	<ul> 
		<?php
		
			$psmtq = psourcemediathek()->the_media_query; //new PSMT_Media_Query( array( 'gallery_id' => $gallery_id, 'per_page' => -1, 'nopaging' => true ) );
		?>	
		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>

			<li id="psmt-uploaded-media-item-<?php psmt_media_id(); ?>" class="<?php psmt_media_class( 'psmt-uploaded-media-item' ); ?>" data-media-id="<?php psmt_media_id(); ?>">
				<img src="<?php psmt_media_src( 'thumbnail' ); ?>">
				<a href='#' class='psmt-delete-uploaded-media-item'>x</a>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
		<?php psmt_reset_media_data();// gallery_data(); ?>
		<?php // wp_reset_postdata();?>
	</ul>
</div>
    <input type="hidden" name="psmt-context" value="admin" class="psmt-context"/>
<!-- drop files here for uploading -->
<?php psmt_upload_dropzone( 'admin' );?>
<!-- show any feedback here -->
<div id="psmt-upload-feedback-admin" class="psmt-feedback">
	<ul> </ul>
</div>
    <input type='hidden' name='psmt-upload-gallery-id' id='psmt-upload-gallery-id' value="<?php echo psmt_get_current_gallery_id(); ?>"/>
	<?php if ( psmt_is_remote_enabled( 'admin' ) ) : ?>
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

</div><!-- end of psourcemediathek form container -->