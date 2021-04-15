<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Item list in shortcode list view
 * You can override it in yourtheme/psourcemediathek/default/shortcodes/list.php
 *
 */
$query = psmt_shortcode_get_media_data( 'query' );
?>

<?php if ( $query->have_media() ) : ?>

	<ul class="psmt-item-list psmt-list-item-shortcode">

		<?php while ( $query->have_media() ) : $query->the_media(); ?>
			<?php $type = psmt_get_media_type(); ?>
			<li class="psmt-list-item-entry psmt-list-item-entry-<?php psmt_media_type(); ?>" data-psmt-type="<?php echo $type;?>">

				<?php do_action( 'psmt_before_media_shortcode_item' ); ?>

				<a href="<?php psmt_media_permalink(); ?>" class="psmt-item-title psmt-media-title" data-psmt-type="<?php echo $type;?>"><?php psmt_media_title(); ?></a>

				<?php if ( $show_creator ) : ?>
                    <span class="psmt-media-creator-link psmt-shortcode-media-creator-link">
						<?php echo $before_creator; ?><?php psmt_media_creator_link(); ?><?php echo $after_creator; ?>
                    </span>
				<?php endif; ?>

                <?php do_action( 'psmt_after_media_shortcode_item' ); ?>

			</li>

		<?php endwhile; ?>

	</ul>
	<?php psmt_reset_media_data(); ?>
<?php endif; ?>
