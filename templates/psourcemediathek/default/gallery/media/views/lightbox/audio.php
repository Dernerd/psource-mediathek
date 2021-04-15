<?php
/**
 * Single audio view
 */
$media = psmt_get_current_media();
if ( ! $media ) {
	return '';
}

$args = array(
	'src'      => psmt_get_media_src(),
	'loop'     => false,
	'autoplay' => false,
);

echo wp_audio_shortcode( $args );
?>
<script type='text/javascript'>
    psmt_mejs_activate_lightbox_player();
</script>