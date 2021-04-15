<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Media/Gallery Activity Post Form
 */
?>
<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="psmt-whats-new-form" class="psmt-activity-post-form clearfix" name="psmt-whats-new-form" role="complementary">

	<?php do_action( 'psmt_before_activity_post_form' ); ?>

	<div id="psmt-whats-new-avatar">
		<a href="<?php echo bp_loggedin_user_domain(); ?>">
			<?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
		</a>
	</div>

	<p class="activity-greeting">
		<?php printf( __( 'Willst du etwas sagen, %s?', 'psourcemediathek' ), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) ); ?>
	</p>

	<div id="psmt-whats-new-content">

		<div id="psmt-whats-new-textarea">
			<label for="psmt-whats-new" class="screen-reader-text"><?php _e( 'Update veröffentlichen', 'psourcemediathek' ); ?></label>
			<textarea name="psmt-whats-new" id="psmt-whats-new" cols="50" rows="2"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?><?php endif; ?></textarea>
		</div>

		<div id="psmt-whats-new-options">
			<div id="psmt-whats-new-submit">
				<input type="submit" name="psmt-aw-whats-new-submit" id="psmt-aw-whats-new-submit" value="<?php esc_attr_e( 'Veröffentlichen', 'psourcemediathek' ); ?>"/>
			</div>

			<?php if ( bp_is_active( 'groups' ) && bp_is_group() ) : ?>

				<input type="hidden" id="psmt-whats-new-post-object" name="whats-new-post-object" value="groups"/>
				<input type="hidden" id="psmt-whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id( groups_get_current_group() ); ?>"/>

			<?php endif; ?>
			<?php if ( psmt_is_single_gallery() && ! psmt_is_single_media() ) : ?>
				<input type="hidden" name='psmt-item-id' id="psmt-item-id" value="<?php echo psmt_get_current_gallery_id(); ?>"/>
				<input type="hidden" name='psmt-activity-type' id="psmt-activity-type" value="gallery"/>
			<?php else : ?>

				<input type="hidden" name='psmt-item-id' id="psmt-item-id" value="<?php echo psmt_get_current_media_id(); ?>"/>
				<input type="hidden" name='psmt-activity-type' id="psmt-activity-type" value="media"/>
			<?php endif; ?>

			<?php do_action( 'bp_activity_post_form_options' ); ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'psmt_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
