<?php
/**
 * Single video view
 *
 */
$media = psmt_get_current_media();
if ( ! $media ) {
	return;
}

$args = array(
	'src'    => psmt_get_media_src( '', $media ),
	'poster' => psmt_get_media_src( 'thumbnail', $media ),

);

echo wp_video_shortcode( $args );
?>
<script type='text/javascript'>
    psmt_mejs_activate_lightbox_player();
</script>
