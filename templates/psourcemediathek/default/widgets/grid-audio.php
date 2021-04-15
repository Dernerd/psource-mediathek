<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Audio list in shortcode grid view
 */
$query = psmt_widget_get_media_data( 'query' ); ?>

<?php if ( $query->have_media() ) : ?>

	<div class="psmt-container psmt-widget-container psmt-media-widget-container psmt-media-audio-widget-container">
		<div class='psmt-g psmt-item-list psmt-media-list psmt-audio-list'>

			<?php while ( $query->have_media() ) : $query->the_media(); ?>
				<?php $type = psmt_get_media_type(); ?>
				<div class="<?php psmt_media_class( 'psmt-widget-item psmt-widget-audio-item ' . psmt_get_grid_column_class( 1 ) ); ?>" data-psmt-type="<?php echo $type;?>">

					<?php do_action( 'psmt_before_media_widget_item' ); ?>

					<div class="psmt-item-meta psmt-media-meta psmt-media-widget-item-meta psmt-media-meta-top psmt-media-widget-item-meta-top">
						<?php do_action( 'psmt_media_widget_item_meta_top' ); ?>
					</div>
					<?php

					$args = array(
						'src'      => psmt_get_media_src(),
						'loop'     => false,
						'autoplay' => false,
					);

					//$ids = psmt_get_all_media_ids();
					//echo wp_playlist_shortcode( array( 'ids' => $ids));

					?>
					<div class='psmt-item-entry psmt-media-entry psmt-audio-entry'>

						<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
							'class'            => 'psmt-item-thumbnail psmt-media-thumbnail psmt-audio-thumbnail',
							'psmt-data-context' => 'shortcode',
						) ); ?> data-psmt-type="<?php echo $type;?>">
							<img src="<?php psmt_media_src( 'thumbnail' ); ?>" alt="<?php psmt_media_title(); ?> "/>
						</a>

					</div>

					<div class="psmt-item-content psmt-audio-content psmt-audio-player">
						<?php echo wp_audio_shortcode( $args ); ?>
					</div>

					<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
						'class'            => 'psmt-item-title psmt-media-title psmt-audio-title',
						'psmt-data-context' => 'shortcode',
					) ); ?> data-psmt-type="<?php echo $type;?>">
						<?php psmt_media_title(); ?>
					</a>

					<div class="psmt-item-actions psmt-media-actions psmt-audio-actions">
						<?php psmt_media_action_links(); ?>
					</div>

					<div
						class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_media_type(), psmt_get_media() ); ?></div>

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
