<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Activity:- Items List.
 *
 * Media List attached to an activity
 */


$psmtq = new PSMT_Cached_Media_Query( array( 'in' => psmt_activity_get_displayable_media_ids( $activity_id ) ) );

$ids = psmt_activity_get_attached_media_ids( $activity_id );

if ( $psmtq->have_media() ) : ?>

	<ul class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-media-list-view-list psmt-activity-media-list-view-list">
		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>
			<?php $type = psmt_get_media_type(); ?>
			<li class="psmt-list-item-entry psmt-list-item-entry-<?php psmt_media_type(); ?>" data-psmt-type="<?php echo $type;?>">
				<?php do_action( 'psmt_before_media_activity_item' ); ?>

				<a href="<?php psmt_media_permalink(); ?>" class="psmt-activity-item-title psmt-activity-<?php psmt_media_type(); ?>-title" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>" data-psmt-media-id="<?php psmt_media_id(); ?>"><?php psmt_media_title(); ?></a>

				<?php do_action( 'psmt_after_media_activity_item' ); ?>
			</li>

		<?php endwhile; ?>
	</ul>
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
