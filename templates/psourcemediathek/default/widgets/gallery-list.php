<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * List all galleries for the current widget
 */

$query = psmt_widget_get_gallery_data( 'query' );
?>

<?php if ( $query->have_galleries() ) : ?>
	<div class="psmt-container psmt-widget-container psmt-gallery-widget-container">
		<div class='psmt-g psmt-item-list psmt-galleries-list'>

			<?php while ( $query->have_galleries() ) : $query->the_gallery(); ?>
                <?php $type = psmt_get_gallery_type();?>
				<div class="<?php psmt_gallery_class( 'psmt-u-1-1' ); ?>" data-psmt-type="<?php echo $type;?>">

					<?php do_action( 'psmt_before_gallery_widget_entry' ); ?>

					<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-widget-item-meta psmt-gallery-meta-top psmt-gallery-widget-item-meta-top">
						<?php do_action( 'psmt_gallery_widget_item_meta_top' ); ?>
					</div>

					<div class="psmt-item-entry psmt-gallery-entry">

						<a href="<?php psmt_gallery_permalink(); ?>" <?php psmt_gallery_html_attributes( array(
							'class'            => 'psmt-item-thumbnail psmt-gallery-cover',
						    'data-psmt-context' => 'widget',
						) ); ?> data-psmt-type="<?php echo $type;?>">
							<img src="<?php psmt_gallery_cover_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_gallery_title() ); ?>"/>
						</a>

					</div>

					<a href="<?php psmt_gallery_permalink(); ?>" <?php psmt_gallery_html_attributes( array(
						'class'            => 'psmt-item-title psmt-gallery-title',
						'data-psmt-context' => 'widget',
					) ); ?> data-psmt-type="<?php echo $type;?>">
						<?php psmt_gallery_title(); ?>
					</a>

					<div class="psmt-item-actions psmt-gallery-actions">
						<?php psmt_gallery_action_links(); ?>
					</div>

					<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_gallery_type(), psmt_get_gallery() ); ?></div>

					<div class="psmt-item-meta psmt-gallery-meta psmt-gallery-widget-item-meta psmt-gallery-meta-bottom psmt-gallery-widget-item-meta-bottom">
						<?php do_action( 'psmt_gallery_widget_item_meta' ); ?>
					</div>

					<?php do_action( 'psmt_after_gallery_widget_entry' ); ?>

				</div>

			<?php endwhile; ?>

			<?php psmt_reset_gallery_data(); ?>

		</div>
	</div>
<?php else : ?>
	<div class="psmt-notice psmt-no-gallery-notice">
		<p> <?php _ex( 'Es sind keine Galerien verfügbar!', 'Keine Galerie-Nachricht', 'psourcemediathek' ); ?>
	</div>
<?php endif; ?>
