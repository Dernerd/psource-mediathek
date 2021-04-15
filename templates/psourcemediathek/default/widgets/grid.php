<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * psourcemediathek/widgets/loop.php
 * Default fallback media loop
 */
$query = psmt_widget_get_media_data( 'query' ); ?>

<?php if ( $query->have_media() ) : ?>

	<div class="psmt-container psmt-widget-container psmt-media-widget-container psmt-media-video-widget-container">
		<div class='psmt-g psmt-item-list psmt-media-list psmt-video-list'>

			<?php while ( $query->have_media() ) : $query->the_media(); ?>
				<?php $type = psmt_get_media_type(); ?>
				<div class="<?php psmt_media_class( 'psmt-widget-item psmt-widget-media-item ' . psmt_get_grid_column_class( 1 ) ); ?>" data-psmt-type="<?php echo $type;?>">

					<?php do_action( 'psmt_before_media_widget_item' ); ?>

					<div class="psmt-item-meta psmt-media-meta psmt-media-widget-item-meta psmt-media-meta-top psmt-media-widget-item-meta-top">
						<?php do_action( 'psmt_media_widget_item_meta_top' ); ?>
					</div>

					<div class='psmt-item-entry psmt-media-entry psmt-photo-entry'>

						<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
							'class'            => 'psmt-item-thumbnail psmt-media-thumbnail',
							'data-psmt-context' => 'widget',
						) ); ?> data-psmt-type="<?php echo $type;?>">
							<img src="<?php psmt_media_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_media_title() ); ?> "/>
						</a>

					</div>

                    <a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes(
	                    array(
		                    'class'            => 'psmt-item-title psmt-media-title ',
		                    'data-psmt-context' => 'widget',
	                    ) ); ?> data-psmt-type="<?php echo $type;?>">
                        <?php psmt_media_title(); ?>
                    </a>

					<div class="psmt-item-actions psmt-media-actions psmt-photo-actions">
						<?php psmt_media_action_links(); ?>
					</div>

					<div class="psmt-item-meta psmt-media-meta psmt-media-widget-item-meta psmt-media-meta-bottom psmt-media-widget-item-meta-bottom">
						<?php do_action( 'psmt_media_widget_item_meta' ); ?>
					</div>

					<?php do_action( 'psmt_after_media_widget_item' ); ?>

				</div>

			<?php endwhile; ?>

			<?php psmt_reset_media_data(); ?>

		</div>
	</div>
<?php endif; ?>
