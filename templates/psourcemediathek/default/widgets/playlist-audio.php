<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode Audio Playlist
 */
// get the Query Object we had saved earlier.
$query = psmt_widget_get_media_data( 'query' );

$ids = $query->get_ids();

if ( $query->have_media() ) : ?>
	<div class="psmt-u-1-1 psmt-item-playlist  psmt-item-playlist-audio psmt-item-playlist-audio-widget">
		<?php do_action( 'psmt_before_widget_playlist', $ids ); ?>
		<?php
			echo wp_playlist_shortcode( array( 'ids' => $ids ) );
		?>
		<?php do_action( 'psmt_after_widget_playlist', $ids ); ?>
	</div>
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
