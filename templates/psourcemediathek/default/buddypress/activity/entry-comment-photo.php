<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Attachment in single media comment
 * This is a fallback template for new media types
 */

$psmtq = new PSMT_Cached_Media_Query( array( 'in' => (array) psmt_activity_get_media_id( $activity_id ) ) );

if ( $psmtq->have_media() ) : ?>
	<div class="psmt-container psmt-media-list psmt-activity-comment-media-list psmt-activity-comment-photo-list">

		<?php while ( $psmtq->have_media() ) : $psmtq->the_media(); ?>
			<?php $type = psmt_get_media_type(); ?>
            <?php if ( psmt_user_can_view_media( psmt_get_media_id() ) ) : ?>

                <div class="<?php psmt_media_class( 'psmt-activity-comment-media-entry psmt-activity-comment-media-entry-photo' ); ?>" id="psmt-activity-comment-media-entry-<?php psmt_media_id(); ?>" data-psmt-type="<?php echo $type;?>">

                    <a href="<?php psmt_media_permalink(); ?>" title="<?php echo esc_attr( psmt_get_media_title() ); ?>" data-psmt-type="<?php echo $type;?>" data-psmt-activity-id="<?php echo $activity_id; ?>"  data-psmt-media-id="<?php psmt_media_id(); ?>" class="psmt-media psmt-activity-comment-media psmt-activity-comment-photo">
                        <img src="<?php psmt_media_src( 'thumbnail' ); ?>" class='psmt-attached-media-item' />
                    </a>

                </div>

            <?php endif; ?>

		<?php endwhile; ?>
	</div>
<?php endif; ?>
<?php psmt_reset_media_data(); ?>
