<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$media = psmt_get_current_media();
ob_start();
do_action( 'psmt_lightbox_media_meta_top', $media );

$meta_top = ob_get_clean();
?>
<?php if ( $meta_top ): ?>
    <div class="psmt-item-meta psmt-media-meta psmt-lightbox-media-meta psmt-lightbox-media-meta-top">
		<?php echo $meta_top; ?>
    </div>
<?php endif; ?>