<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$media = psmt_get_current_media();
?>
<div class="psmt-lightbox-media-uploader-meta psmt-clearfix">
	<div class="psmt-lightbox-media-uploader-avatar">
		<a href="<?php echo bp_core_get_user_domain( psmt_get_media_creator_id() ); ?>">
			<?php echo bp_core_fetch_avatar( array(
				'item_id' => psmt_get_media_creator_id(),
				'object'  => 'user',
				'width'   => bp_core_avatar_thumb_width(),
				'height'  => bp_core_avatar_thumb_height(),
			) ); ?>
		</a>
	</div>

	<div class="psmt-lightbox-uploader-upload-details">
		<div class="psmt-lightbox-uploader-link">
			<?php echo bp_core_get_userlink( psmt_get_media_creator_id() ); ?>
		</div>
		<span class="psmt-lightbox-upload-time"><?php echo bp_core_time_since( psmt_get_media_date_created( null, 'Y-m-d H:i:s', false ) ); ?></span>
		<div class="psmt-lightbox-action-links">
			<?php do_action( 'psmt_lightbox_media_action_before_link', $media );?>
			<?php if ( psmt_user_can_edit_media( psmt_get_media_id() ) ) : ?>
                <a class="psmt-lightbox-media-action-link psmt-lightbox-edit-media-link" href="#" data-psmt-media-id="<?php psmt_media_id();?>"><?php _ex('Edit', 'lightbox edit media edit action label', 'psourcemediathek' );?> </a>
                <a class="psmt-lightbox-media-action-link psmt-lightbox-edit-media-cancel-link" href="#" data-psmt-media-id="<?php psmt_media_id();?>"><?php _ex('Cancel', 'lightbox edit media cancel action label', 'psourcemediathek' );?></a>
			<?php endif;?>
			<?php do_action( 'psmt_lightbox_media_action_after_link', $media );?>
        </div>
	</div>
</div><!--end of the top row -->
<?php
if ( psmt_media_has_description() ) {
	$class = 'psmt-media-visible-description';
} else {
	$class = 'psmt-media-hidden-description';
}
?>
<div class="psmt-item-description psmt-media-description psmt-lightbox-media-description <?php echo $class; ?> psmt-clearfix">
	<?php psmt_media_description(); ?>
</div>

<?php if ( psmt_user_can_edit_media( psmt_get_media_id() ) ) : ?>
	<?php psmt_locate_template( array( 'gallery/media/views/lightbox/media-edit-form.php' ), true ); ?>
<?php endif; ?>
