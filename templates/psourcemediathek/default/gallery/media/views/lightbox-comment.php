<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$media = psmt_get_current_media();
?>
<div class="psmt-lightbox-content psmt-lightbox-with-comment-content psmt-clearfix" id="psmt-lightbox-media-<?php psmt_media_id(); ?>">

    <div class="psmt-lightbox-media-container psmt-lightbox-with-comment-media-container">

		<?php do_action( 'psmt_before_lightbox_media', $media ); ?>

        <?php psmt_locate_template( array( 'gallery/media/views/lightbox/media-meta-top.php' ), true ); ?>

        <div class="psmt-lightbox-media-entry psmt-lightbox-with-comment-media-entry">
	        <?php psmt_lightbox_content( $media );?>
        </div>

	    <?php psmt_locate_template( array( 'gallery/media/views/lightbox/media-meta-bottom.php' ), true ); ?>

        <?php do_action( 'psmt_after_lightbox_media', $media ); ?>
    </div> <!-- end of media container -->

    <div class="psmt-lightbox-activity-container">

        <?php psmt_locate_template( array( 'gallery/media/views/lightbox/media-info.php' ), true ); ?>

        <div class="psmt-lightbox-item-meta-activities psmt-lightbox-item-meta-activities-top">
			<?php do_action( 'psmt_before_lightbox_media_activity', $media ); ?>
        </div>

		<?php psmt_locate_template( array( 'gallery/media/views/lightbox/activity.php' ), true ); ?>

        <div class="psmt-lightbox-item-meta-activities psmt-lightbox-item-meta-activities-bottom">
	        <?php do_action( 'psmt_after_lightbox_media_activity', $media ); ?>
        </div>
    </div><!-- end of right panel -->

</div> <!-- end of lightbox content -->
