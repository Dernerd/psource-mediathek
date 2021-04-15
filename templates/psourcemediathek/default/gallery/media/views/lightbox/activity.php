<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Single Media Activity list
 *
 * @package psourcemediathek
 */
//is buddypress active and activity enabled? If not, no need to load this page
if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
	return;
}
//is commenting enabled?
if ( ! psmt_get_option( 'enable_media_comment' ) ) {
	return;
}

?>

<?php do_action( 'psmt_before_activity_loop' ); ?>

<div class="psmt-activity psmt-media-activity " id="psmt-media-activity-list">

	<?php if ( is_user_logged_in() && psmt_media_user_can_comment( psmt_get_current_media_id() ) ) : ?>
		<?php psmt_locate_template( array( 'buddypress/activity/post-form.php' ), true ); ?>
	<?php endif; ?>

	<?php if ( psmt_media_has_activity( array( 'media_id' => psmt_get_media_id() ) ) ) : ?>
		<?php /* Show pagination if JS is not enabled, since the "Mehr laden" link will do nothing */ ?>
		<noscript>
			<div class="pagination">
				<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
				<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
			</div>
		</noscript>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			<ul id="psmt-activity-stream" class="psmt-activity-list item-list">
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
		<form action="" name="psmt-activity-loop-form" id="psmt-activity-loop-form" method="post">
			<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
		</form>
	<?php endif; ?>
</div><!-- /#psmt-media-activity-list -->
