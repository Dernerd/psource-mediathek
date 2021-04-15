<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action:psourcemediathek/gallery/gallery_name/ Activity comments
 * Activity Loop to show Single gallery Item Activity
 */
if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
	return;
}
// if gallery comment is not enabled do not load it?
if ( ! psmt_get_option( 'enable_gallery_comment' ) ) {
	return;
}

?>

<div class="psmt-activity psmt-media-activity" id="psmt-media-activity-list">

	<?php if ( is_user_logged_in() && psmt_user_can_comment_on_gallery( psmt_get_current_gallery_id() ) ) : ?>
		<?php psmt_locate_template( array( 'buddypress/activity/post-form.php' ), true ); ?>
	<?php endif; ?>

	<?php do_action( 'psmt_before_activity_loop' ); ?>

	<?php if ( psmt_gallery_has_activity( array( 'gallery_id' => psmt_get_gallery_id() ) ) ) : ?>
		<?php /* Show pagination if JS is not enabled, since the "Mehr laden" link will do nothing */ ?>
		<noscript>
			<div class="pagination">
				<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
				<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
			</div>
		</noscript>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			<ul id="psmt-activity-stream" class="psmt-activity-list clearfix item-list">
		<?php endif; ?>

		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php psmt_locate_template( array( 'buddypress/activity/entry.php' ), true, false ); ?>
		<?php endwhile; ?>

		<?php if ( bp_activity_has_more_items() ) : ?>
			<li class="load-more">
				<a href="#more"><?php _e( 'Mehr laden', 'psourcemediathek' ); ?></a>
			</li>
		<?php endif; ?>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			</ul>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'psmt_after_activity_loop' ); ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
			<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
		</form>
	<?php endif; ?>
</div>
