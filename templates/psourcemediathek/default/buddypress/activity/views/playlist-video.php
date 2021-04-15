<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Activity Videos:- Playlist View.
 *
 * List videos attached to activity
 */
?>
<div class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-activity-video-list psmt-media-list-view-playlist psmt-video-playlist psmt-activity-video-playlist">
	<?php
	$ids = psmt_activity_get_displayable_media_ids( $activity_id );
	// is there only one video attached?
	if ( count( $ids ) == 1 ) {
		$ids   = array_pop( $ids );
		$media = psmt_get_media( $ids );
		$args  = array(
			'src'    => psmt_get_media_src( '', $media ),
			'poster' => psmt_get_media_src( 'thumbnail', $media ),

		);
		// show single video with poster.
		echo wp_video_shortcode( $args );

	} else {
		// show all videos as playlist.
		echo wp_playlist_shortcode( array( 'ids' => $ids, 'type' => 'video' ) );
	}
	?>
	<script type='text/javascript'>
		psmt_mejs_activate(<?php echo $activity_id;?>);
	</script>
</div>
