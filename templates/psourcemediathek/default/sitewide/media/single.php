<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * @package psourcemediathek
 *
 * Single Gallery template
 * If you need specific template for various types, you can copy this file and create new files with name like
 * This comes as the fallback template in our template hierarchy
 * Before loading this file, PsourceMediathek will search for
 * single-{type}-{status}.php
 * single-{type}.php
 * and then fallback to
 * single.php
 * Where type=photo|video|audio|any active type
 *         status =public|private|friendsonly|groupsonly|any registered status
 *
 *
 * Please create your template if you need specific templates for photo, video etc
 *
 *
 *
 * Fallback single Gallery View
 */
?>
<?php if ( psmt_have_media() ) : ?>

	<?php while ( psmt_have_media() ) : psmt_the_media(); ?>

		<?php if ( psmt_user_can_view_media( psmt_get_media_id() ) ) : ?>

			<div class="<?php psmt_media_class(); ?>" id="psmt-media-<?php psmt_media_id(); ?>">

				<?php do_action( 'psmt_before_single_media_item' ); ?>

				<div class="psmt-item-meta psmt-media-meta psmt-media-meta-top">
					<?php do_action( 'psmt_media_meta_top' ); ?>
				</div>

				<div class="psmt-item-title psmt-media-title"> <?php psmt_media_title(); ?></div>

				<?php do_action( 'psmt_after_single_media_title' ); ?>

				<div class="psmt-item-entry psmt-media-entry">

					<?php do_action( 'psmt_before_single_media_content' ); ?>

					<?php psmt_load_media_view( psmt_get_media() ); ?>

					<?php do_action( 'psmt_after_single_media_content' ); ?>

				</div>

				<div class="psmt-item-meta psmt-media-meta psmt-media-meta-bottom">
					<?php do_action( 'psmt_media_meta' ); ?>
				</div>

				<?php if ( psmt_show_media_description() ) : ?>
					<div class="psmt-item-description psmt-media-description psmt-single-media-description psmt-media-<?php psmt_media_type(); ?>-description psmt-clearfix">
						<?php psmt_media_description(); ?>
					</div>
				<?php endif; ?>

				<?php do_action( 'psmt_after_single_media_item' ); ?>

			</div>

		<?php else : ?>
			<div class="psmt-notice psmt-gallery-prohibited">
				<p><?php printf( __( 'Die Datenschutzeinstellung erlaubt Dir nicht, dies anzuzeigen.', 'psourcemediathek' ) ); ?></p>
			</div>
		<?php endif; ?>

	<?php endwhile; ?>
    <div class="psmt-single-media-prev-next psmt-clearfix">
	    <?php psmt_previous_media_link(); ?>
	    <?php psmt_next_media_link(); ?>
    </div>

	<?php psmt_locate_template( array( 'buddypress/members/media/activity.php' ), true ); ?>

<?php else : ?>
	<div class="psmt-notice psmt-no-gallery-notice">
		<p> <?php _ex( 'Hier gibt es nichts zu sehen!', 'Keine Mediennachricht', 'psourcemediathek' ); ?>
	</div>
<?php endif; ?>
