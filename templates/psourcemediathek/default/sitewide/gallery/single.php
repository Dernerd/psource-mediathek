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
<?php

$gallery = psmt_get_current_gallery();
$type    = $gallery->type;

?>
<?php if ( psmt_have_media() ) : ?>

	<?php if ( psmt_user_can_list_media( psmt_get_current_gallery_id() ) ) : ?>

		<?php do_action( 'psmt_before_single_gallery' ); ?>

		<?php if ( psmt_show_gallery_description() ) : ?>
			<div class="psmt-gallery-description psmt-single-gallery-description psmt-<?php echo $type; ?>-gallery-description psmt-clearfix">
				<?php psmt_gallery_description(); ?>
			</div>
		<?php endif; ?>

		<div class='psmt-g psmt-item-list psmt-media-list psmt-<?php echo $type; ?>-list psmt-single-gallery-media-list psmt-single-gallery-<?php echo $type; ?>-list' data-gallery-id="<?php echo psmt_get_current_gallery_id();?>" data-psmt-type="<?php echo $type;?>">
			<?php // loads the media list. ?>
			<?php psmt_load_gallery_view( $gallery ); ?>
		</div>

		<?php do_action( 'psmt_after_single_gallery' ); ?>

		<?php psmt_media_pagination(); ?>

		<?php do_action( 'psmt_after_single_gallery_pagination' ); ?>

		<?php psmt_locate_template( array( 'sitewide/gallery/activity.php' ), true ); ?>

		<?php do_action( 'psmt_after_single_gallery_activity' ); ?>

	<?php else : ?>
		<div class="psmt-notice psmt-gallery-prohibited">
			<p><?php printf( __( 'Die Datenschutzeinstellung erlaubt Dir nicht, dies anzuzeigen.', 'psourcemediathek' ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php psmt_reset_media_data(); ?>
<?php else : ?>

	<?php // we should seriously think about adding create gallery button here. ?>

	<?php if ( psmt_user_can_upload( psmt_get_current_component(), psmt_get_current_component_id(), psmt_get_current_gallery() ) ) : ?>
		<?php psmt_get_template( 'gallery/manage/add-media.php' ); ?>
	<?php else : ?>
		<div class="psmt-notice psmt-no-gallery-notice">
			<p> <?php _ex( 'Es gibt hier nichts zu sehen!', 'Keine Mediennachricht', 'psourcemediathek' ); ?></p>
		</div>
	<?php endif; ?>

<?php endif; ?>
