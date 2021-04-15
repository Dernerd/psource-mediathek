<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode Entry.
 *
 * psourcemediathek/shortcodes/gallery-entry.php
 *
 * Single gallery entry for psmt-gallery shortcode
 */
//$query = psmt_shortcode_get_gallery_data( 'gallery_list_query' );
/**
 * @see psmt_shortcode_list_gallery() for the meaning of $query.
 */
if ( empty( $query ) ) {
	return;
}
?>
<?php if ( $query->have_galleries() ) : ?>
	<div class="psmt-container psmt-shortcode-wrapper psmt-shortcode-gallery-wrapper">
		<div class="psmt-g psmt-item-list psmt-gallery-list psmt-shortcode-item-list psmt-shortcode-list-gallery">

			<?php while ( $query->have_galleries() ) : $query->the_gallery(); ?>
				<?php $type = psmt_get_gallery_type(); ?>
				<div class="<?php psmt_gallery_class( psmt_get_grid_column_class( $shortcode_column ) ); ?>" id="psmt-gallery-<?php psmt_gallery_id(); ?>" data-psmt-type="<?php echo $type;?>">

					<?php do_action( 'psmt_before_gallery_shortcode_entry' ); ?>

					<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-shortcode-item-meta psmt-gallery-meta-top psmt-gallery-shortcode-item-meta-top">
						<?php do_action( 'psmt_gallery_shortcode_item_meta_top' ); ?>
					</div>

					<div class="psmt-item-entry psmt-gallery-entry">
						<a href="<?php psmt_gallery_permalink(); ?>" <?php psmt_gallery_html_attributes( array(
							'class'            => 'psmt-item-thumbnail psmt-gallery-cover',
							'data-psmt-context' => 'shortcode',
						) ); ?> data-psmt-type="<?php echo $type;?>">

							<img src="<?php psmt_gallery_cover_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_gallery_title() ); ?>"/>
						</a>
					</div>

					<?php do_action( 'psmt_before_gallery_title' ); ?>

					<a href="<?php psmt_gallery_permalink(); ?>" <?php psmt_gallery_html_attributes( array(
						'class'            => 'psmt-item-title psmt-gallery-title',
						'data-psmt-context' => 'shortcode',
					) );
					?> data-psmt-type="<?php echo $type;?>">
						<?php psmt_gallery_title(); ?>
					</a>

					<?php if ( $show_creator ) : ?>
                        <div class="psmt-gallery-creator-link psmt-shortcode-gallery-creator-link">
							<?php echo $before_creator; ?><?php psmt_gallery_creator_link(); ?><?php echo $after_creator; ?>
                        </div>
					<?php endif; ?>

					<?php do_action( 'psmt_before_gallery_type_icon' ); ?>

					<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_gallery_type(), psmt_get_gallery() ); ?></div>

					<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-shortcode-item-meta psmt-gallery-meta-bottom psmt-gallery-shortcode-item-meta-bottom">
						<?php do_action( 'psmt_gallery_shortcode_item_meta' ); ?>
					</div>


					<?php do_action( 'psmt_after_gallery_shortcode_entry' ); ?>

				</div>
			<?php endwhile; ?>

			<?php psmt_reset_gallery_data(); ?>
		</div>

		<?php if ( $show_pagination ) : ?>
			<div class="psmt-paginator">
				<?php echo $query->paginate( false ); ?>
			</div>
		<?php endif; ?>

	</div>
<?php endif; ?>
