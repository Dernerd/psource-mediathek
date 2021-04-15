<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$media = psmt_get_current_media();

if ( ! $media ) {
	return;
}

?>
<div class="psmt-lightbox-content psmt-lightbox-content-without-comment psmt-clearfix">
	<div class="psmt-lightbox-media-container">

		<?php do_action( 'psmt_before_lightbox_media', $media ); ?>

		<div class="psmt-item-meta psmt-media-meta psmt-lightbox-media-meta psmt-lightbox-media-meta-top">
			<?php do_action( 'psmt_lightbox_media_meta_top', $media ); ?>
		</div>

        <div class="psmt-lightbox-media-entry psmt-lightbox-no-comment-media-entry">
			<?php psmt_lightbox_content( $media );?>
        </div>

		<div class="psmt-item-meta psmt-media-meta psmt-lightbox-media-meta psmt-lightbox-media-meta-bottom">
			<?php do_action( 'psmt_lightbox_media_meta', $media ); ?>
		</div>

		<?php do_action( 'psmt_after_lightbox_media', $media ); ?>

	</div>

</div>
