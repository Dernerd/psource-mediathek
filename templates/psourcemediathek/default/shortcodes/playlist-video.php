<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * psourcemediathek/shortcodes/media-loop-video-playlist.php
 *
 * Shortcode Video Playlist
 */
$query = psmt_shortcode_get_media_data( 'query' );

if ( $query->have_media() ) :
	$ids = $query->get_ids();

	?>

	<div class="psmt-item-playlist psmt-u-1-1 psmt-item-playlist-video psmt-item-playlist-video-shortcode">
		<?php do_action( 'psmt_before_widget_playlist', $ids ); ?>

		<?php
			echo wp_playlist_shortcode( array( 'ids' => $ids, 'type' => 'video' ) );
		?>

		<?php do_action( 'psmt_after_widget_playlist', $ids ); ?>
	</div>

<?php endif; ?>
<?php psmt_reset_media_data(); ?>
