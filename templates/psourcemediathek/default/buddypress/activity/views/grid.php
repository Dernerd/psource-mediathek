<?php
// Exit if the file is accessed directly over web.
// fallback view for activity media grid.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Default grid view for media items.
 *
 * Media List attached to an activity
 * This is a fallback template for new media types
 */


$psmtq = new PSMT_Cached_Media_Query( array( 'in' => psmt_activity_get_displayable_media_ids( $activity_id ) ) );

if ( $psmtq->have_media() ) : ?>
	<div class="psmt-container psmt-activity-container psmt-media-list psmt-activity-media-list psmt-media-default-list psmt-activity-default-media-list psmt-media-default-list-view-grid psmt-activity-media-default-list-view-grid">

		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>
            <?php $type = psmt_get_media_type(); ?>

            <div class="psmt-item-content psmt-activity-item-content psmt-<?php echo $type;?>-content psmt-activity-<?php echo $type;?>-content" data-psmt-type="<?php echo $type;?>">

                <a href="<?php psmt_media_permalink(); ?>" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>" data-psmt-media-id="<?php psmt_media_id(); ?>" class="psmt-media psmt-activity-media psmt-activity-media-<?php echo $type;?>">
                    <img src="<?php psmt_media_src( 'thumbnail' ); ?>" class='psmt-attached-media-item' title="<?php echo esc_attr( psmt_get_media_title() ); ?>" />
                </a>

                <a href="<?php psmt_media_permalink() ?>" title="<?php echo esc_attr( psmt_get_media_title() ); ?>" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>" data-psmt-media-id="<?php psmt_media_id(); ?>" class="psmt-activity-item-title psmt-activity-<?php echo $type;?>-title"><?php psmt_media_title(); ?></a>
            </div>

		<?php endwhile; ?>
	</div>
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
