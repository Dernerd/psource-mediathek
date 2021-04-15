<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * PsourceMediathek Activity Comment template
 * Used on single media/single gallery and lightbox to show the media/gallery activity
 *
 */
?>
<?php

do_action( 'psmt_before_activity_comment' ); ?>

<li id="psmt-acomment-<?php bp_activity_comment_id(); ?>">
	<div class="psmt-acomment-avatar">
		<a href="<?php bp_activity_comment_user_link(); ?>">
			<?php bp_activity_avatar( 'type=thumb&user_id=' . bp_get_activity_comment_user_id() ); ?>
		</a>
	</div>

	<div class="psmt-acomment-meta">
		<?php
		/* translators: 1: user profile link, 2: user name, 3: activity permalink, 4: activity timestamp */
		printf( __( '<a href="%1$s">%2$s</a> <a href="%3$s" class="activity-time-since"><span class="time-since">%4$s</span></a>', 'psourcemediathek' ), bp_get_activity_comment_user_link(), bp_get_activity_comment_name(), bp_get_activity_comment_permalink(), bp_get_activity_comment_date_recorded() );
		?>
	</div>

	<div class="psmt-acomment-content"><?php bp_activity_comment_content(); ?></div>

	<div class="psmt-acomment-options">

		<?php if ( is_user_logged_in() && bp_activity_can_comment_reply( bp_activity_current_comment() ) ) : ?>

			<a href="#acomment-<?php bp_activity_comment_id(); ?>" class="psmt-acomment-reply psmt-bp-primary-action" id="psmt-acomment-reply-<?php bp_activity_id(); ?>-from-<?php bp_activity_comment_id(); ?>">
				<?php _e( 'Reply', 'psourcemediathek' ); ?>
			</a>

		<?php endif; ?>

		<?php if ( bp_activity_user_can_delete() ) : ?>
			<a href="<?php bp_activity_comment_delete_link(); ?>" class="delete psmt-acomment-delete confirm psmt-bp-secondary-action" rel="nofollow">
				<?php _e( 'Delete', 'psourcemediathek' ); ?>
			</a>
		<?php endif; ?>

		<?php do_action( 'psmt_activity_comment_options' ); ?>

	</div>

	<?php psmt_activity_recurse_comments( bp_activity_current_comment() ); ?>
</li>

<?php

/**
 * Fires after the display of an activity comment.
 */
do_action( 'psmt_after_activity_comment' ); ?>
