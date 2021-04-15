<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * psourcemediathek/widgets/loop-video-
 */
$query = psmt_widget_get_media_data( 'query' ); ?>

<?php if ( $query->have_media() ) : ?>

	<div class="psmt-container psmt-widget-container psmt-media-widget-container psmt-media-video-widget-container">
		<div class='psmt-g psmt-item-list psmt-media-list psmt-video-list'>

			<?php while ( $query->have_media() ) : $query->the_media(); ?>
				<?php $type = psmt_get_media_type(); ?>
				<div class="<?php psmt_media_class( 'psmt-widget-item psmt-widget-video-item ' . psmt_get_grid_column_class( 1 ) ); ?>" data-psmt-type="<?php echo $type;?>">
					<?php do_action( 'psmt_before_media_widget_item' ); ?>

					<div class="psmt-item-meta psmt-media-meta psmt-media-widget-item-meta psmt-media-meta-top psmt-media-widget-item-meta-top">
						<?php do_action( 'psmt_media_widget_item_meta_top' ); ?>
					</div>

					<?php

					$args = array(
						'src'      => psmt_get_media_src(),
						'loop'     => false,
						'autoplay' => false,
						'poster'   => psmt_get_media_src( 'thumbnail' ),
						'width'    => 320,
						'height'   => 180,
					);


					//$ids = psmt_get_all_media_ids();
					//echo wp_playlist_shortcode( array( 'ids' => $ids));

					?>
					<div class='psmt-item-entry psmt-media-entry psmt-video-entry'>

					</div>
					<div class="psmt-item-content psmt-video-content psmt-video-player">
						<?php if ( psmt_is_oembed_media( psmt_get_media_id() ) ) : ?>
							<?php echo psmt_get_oembed_content( psmt_get_media_id(), 'mid' ); ?>
						<?php else : ?>
							<?php echo wp_video_shortcode( $args ); ?>
						<?php endif; ?>
					</div>
					<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
						'class'            => 'psmt-item-title psmt-media-title psmt-video-title',
						'data-psmt-context' => 'widget',
					) ); ?> data-psmt-type="<?php echo $type;?>">
						<?php psmt_media_title(); ?>
					</a>

					<div class="psmt-item-actions psmt-media-actions psmt-audio-actions">
						<?php psmt_media_action_links(); ?>
					</div>

					<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_media_type(), psmt_get_media() ); ?></div>

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
