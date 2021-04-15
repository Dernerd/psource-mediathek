<?php
/**
 * List all items as unordered list
 *
 */
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
$gallery = psmt_get_current_gallery();
$type    = $gallery->type;
?>
<ul class="psmt-u psmt-item-list psmt-list-item-<?php echo $type; ?>">

	<?php while ( psmt_have_media() ) : psmt_the_media(); ?>

		<li class="psmt-list-item-entry psmt-list-item-entry-<?php echo $type; ?>" data-psmt-type="<?php echo $type;?>">

			<?php do_action( 'psmt_before_media_item' ); ?>

			<a href="<?php psmt_media_permalink(); ?>" class="psmt-item-title psmt-media-title" data-psmt-type="<?php echo $type;?>"><?php psmt_media_title(); ?></a>

			<div class="psmt-item-actions psmt-media-actions">
				<?php psmt_media_action_links(); ?>
			</div>

			<?php do_action( 'psmt_after_media_item' ); ?>

		</li>

	<?php endwhile; ?>

</ul>
