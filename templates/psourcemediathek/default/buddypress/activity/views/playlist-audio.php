<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-activity-audio-list psmt-media-list-view-playlist psmt-audio-playlist psmt-activity-audio-playlist">
	<?php
	$ids = psmt_activity_get_displayable_media_ids( $activity_id );
	// if there is only one media, use the poster too.
	if ( count( $ids ) == 1 ) {
		$ids   = array_pop( $ids );
		$media = psmt_get_media( $ids );
		$args  = array(
			'src'    => psmt_get_media_src( '', $media ),
			'poster' => psmt_get_media_src( 'thumbnail', $media ),

		);
		echo wp_audio_shortcode( $args );

	} else {
		// show playlist, should we use the gallery cover as poster?
		echo wp_playlist_shortcode( array( 'ids' => $ids ) );

	}
	?>
	<script type='text/javascript'>
		psmt_mejs_activate(<?php echo $activity_id;?>);
	</script>
</div>
