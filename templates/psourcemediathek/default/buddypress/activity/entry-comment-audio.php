<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php

$psmtq = new PSMT_Cached_Media_Query( array( 'in' => (array) psmt_activity_get_media_id( $activity_id ) ) );
?>
<?php if ( $psmtq->have_media() ) : ?>

	<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>

		<?php if ( psmt_user_can_view_media( psmt_get_media_id() ) ) : ?>
			<?php $type = psmt_get_media_type(); ?>
			<div class="<?php psmt_media_class( 'psmt-activity-comment-media-entry psmt-activity-comment-media-entry-audio' ); ?>" id="psmt-activity-comment-media-entry-<?php psmt_media_id(); ?>" data-psmt-type="<?php echo $type;?>">

				<div class="psmt-activity-comment-media-content psmt-activity-comment-media-audio-content psmt-activity-comment-media-audio-player">
					<?php psmt_media_content(); ?>
				</div>

				<script type='text/javascript'>
					psmt_mejs_activate(<?php echo $activity_id;?>);
				</script>
			</div>

		<?php else : ?>

			<div class="psmt-notice psmt-gallery-prohibited">
				<p><?php printf( __( 'Die Datenschutzeinstellung erlaubt Dir nicht, dies anzuzeigen.', 'psourcemediathek' ) ); ?></p>
			</div>

		<?php endif; ?>

	<?php endwhile; ?>

<?php else : ?>

	<div class="psmt-notice psmt-gallery-prohibited">
		<p><?php printf( __( 'Die Datenschutzeinstellung erlaubt Dir nicht, dies anzuzeigen.', 'psourcemediathek' ) ); ?></p>
	</div>

<?php endif; ?>
<?php psmt_reset_media_data(); ?>