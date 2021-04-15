<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * List all galleries for the current component
 *
 */
?>

<?php if ( psmt_have_galleries() ) : ?>
	<div class='psmt-g psmt-item-list psmt-galleries-list'>

		<?php while ( psmt_have_galleries() ) : psmt_the_gallery(); ?>
			<?php $type = psmt_get_gallery_type(); ?>
			<div class="<?php psmt_gallery_class( psmt_get_gallery_grid_column_class() ); ?>" id="psmt-gallery-<?php psmt_gallery_id(); ?>" data-psmt-type="<?php echo $type;?>" >

				<?php do_action( 'psmt_before_gallery_entry' ); ?>

				<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-meta-top">
					<?php do_action( 'psmt_gallery_meta_top' ); ?>
				</div>

				<div class="psmt-item-entry psmt-gallery-entry">
					<a href="<?php psmt_gallery_permalink(); ?>" <?php psmt_gallery_html_attributes( array( 'class' => 'psmt-item-thumbnail psmt-gallery-cover' ) ); ?> data-psmt-type="<?php echo $type;?>">
						<img src="<?php psmt_gallery_cover_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_gallery_title() ); ?>"/>
					</a>
				</div>

				<?php do_action( 'psmt_before_gallery_title' ); ?>

				<a href="<?php psmt_gallery_permalink(); ?>" class="psmt-gallery-title" data-psmt-type="<?php echo $type;?>"><?php psmt_gallery_title(); ?></a>

				<?php do_action( 'psmt_before_gallery_actions' ); ?>

				<div class="psmt-item-actions psmt-gallery-actions">
					<?php psmt_gallery_action_links(); ?>
				</div>

				<?php do_action( 'psmt_before_gallery_type_icon' ); ?>

				<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_gallery_type(), psmt_get_gallery() ); ?></div>

				<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-meta-bottom">
					<?php do_action( 'psmt_gallery_meta' ); ?>
				</div>

				<?php do_action( 'psmt_after_gallery_entry' ); ?>
			</div>

		<?php endwhile; ?>

	</div>
	<?php psmt_gallery_pagination(); ?>
	<?php psmt_reset_gallery_data(); ?>
<?php else : ?>
	<div class="psmt-notice psmt-no-gallery-notice">
		<p> <?php _ex( 'Es sind keine Galerien verfügbar!', 'Keine Galerie-Nachricht', 'psourcemediathek' ); ?>
	</div>
<?php endif; ?>
