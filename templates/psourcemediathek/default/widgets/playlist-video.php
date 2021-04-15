<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode Video Playlist
 */
$query = psmt_widget_get_media_data( 'query' );
if ( $query->have_media() ) :
	$ids = $query->get_ids();
	?>

	<div class="psmt-u-1-1 psmt-item-playlist  psmt-item-playlist-video psmt-item-playlist-video-widget">
		<?php do_action( 'psmt_before_widget_playlist', $ids ); ?>
		<?php
			echo wp_playlist_shortcode( array( 'ids' => $ids, 'type' => 'video' ) );
		?>
		<?php do_action( 'psmt_after_widget_playlist', $ids ); ?>
	</div>
	<?php psmt_reset_media_data(); ?>
<?php endif; ?>
