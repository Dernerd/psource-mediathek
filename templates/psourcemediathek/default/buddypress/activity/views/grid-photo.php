<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/***
 * List Photos attched to an activity
 *
 * Media List attached to an activity
 *
 */


$psmtq = new PSMT_Cached_Media_Query( array( 'in' => psmt_activity_get_displayable_media_ids( $activity_id ) ) );

if ( $psmtq->have_media() ) : ?>

	<div class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-activity-photo-list psmt-media-list-view-grid psmt-photo-view-grid psmt-activity-photo-view-grid" >

		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>
			<?php $type = psmt_get_media_type(); ?>
			<a href="<?php psmt_media_permalink(); ?>" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>" data-psmt-media-id="<?php psmt_media_id(); ?>" class="psmt-media psmt-activity-media psmt-activity-media-photo">
				<img src="<?php psmt_media_src( 'thumbnail' ); ?>" class='psmt-attached-media-item' title="<?php echo esc_attr( psmt_get_media_title() ); ?>"/>
			</a>

		<?php endwhile; ?>
	</div><!-- end of .psmt-activity-media-list -->
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
