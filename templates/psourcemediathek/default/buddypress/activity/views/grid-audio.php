<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php

$psmtq = new PSMT_Cached_Media_Query( array( 'in' => psmt_activity_get_displayable_media_ids( $activity_id ) ) );

if ( $psmtq->have_media() ) : ?>
	<div class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-activity-audio-list psmt-activity-audio-player psmt-media-list-view-grid psmt-audio-view-grid psmt-activity-audio-view-grid">

		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>
			<?php $type = psmt_get_media_type(); ?>
			<div class="psmt-item-content psmt-activity-item-content psmt-audio-content psmt-activity-audio-content" data-psmt-type="<?php echo $type;?>">
				<?php psmt_media_content(); ?>
                <a href="<?php psmt_media_permalink() ?>" title="<?php echo esc_attr( psmt_get_media_title() ); ?>" class="psmt-activity-item-title psmt-activity-audio-title" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>" data-psmt-media-id="<?php psmt_media_id(); ?>"><?php psmt_media_title(); ?></a>
			</div>

		<?php endwhile; ?>
		<script type='text/javascript'>
			psmt_mejs_activate(<?php echo $activity_id;?>);
		</script>
	</div>
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
