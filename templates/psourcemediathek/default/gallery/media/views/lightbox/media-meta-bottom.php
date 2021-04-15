<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$media = psmt_get_current_media();
?>
<div class="psmt-item-meta psmt-media-meta psmt-lightbox-media-meta psmt-lightbox-media-meta-bottom">

	<div class="psmt-media-title-info psmt-lightbox-media-title-info psmt-lightbox-media-title-info-bottom">
		<?php psmt_media_title( $media ); ?>
	</div>
	<?php do_action( 'psmt_lightbox_media_meta', $media ); ?>
</div>
