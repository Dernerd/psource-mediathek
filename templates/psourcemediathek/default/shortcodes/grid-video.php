<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Video list in shortcode grid view
 * You can override it in yourtheme/psourcemediathek/default/shortcodes/grid-video.php
 *
 */
$query = psmt_shortcode_get_media_data( 'query' );
?>
<?php if ( $query->have_media() ) : ?>
	<div class="psmt-container psmt-shortcode-wrapper psmt-shortcode-media-list-wrapper">
		<div class="psmt-g psmt-item-list psmt-media-list psmt-shortcode-item-list psmt-shortcode-list-media psmt-shortcode-list-media-video ">

			<?php while ( $query->have_media() ) : $query->the_media(); ?>
				<?php $type = psmt_get_media_type(); ?>
				<div class="<?php psmt_media_class( 'psmt-shortcode-item psmt-shortcode-video-item ' . psmt_get_grid_column_class( psmt_shortcode_get_media_data( 'column' ) ) ); ?>" data-psmt-type="<?php echo $type;?>">
					<?php do_action( 'psmt_before_media_shortcode_item' ); ?>

					<div class="psmt-item-meta psmt-media-meta psmt-media-shortcode-item-meta psmt-media-meta-top psmt-media-shortcode-item-meta-top">
						<?php do_action( 'psmt_media_shortcode_item_meta_top' ); ?>
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
					<div class='psmt-item-entry psmt-media-entry psmt-audio-entry'>

					</div>

					<div class="psmt-item-content psmt-video-content psmt-video-player">
						<?php if ( psmt_is_oembed_media( psmt_get_media_id() ) ) : ?>
							<?php echo psmt_get_oembed_content( psmt_get_media_id(), 'mid' ); ?>
						<?php else : ?>
							<?php echo wp_video_shortcode( $args ); ?>
						<?php endif; ?>
					</div>

					<a href="<?php psmt_media_permalink(); ?>"
						<?php psmt_media_html_attributes( array(
							'class'            => 'psmt-item-title psmt-media-title psmt-video-title',
							'data-psmt-context' => 'shortcode',
						) ); ?> data-psmt-type="<?php echo $type;?>" >
						<?php psmt_media_title(); ?>
					</a>
					<?php if ( $show_creator ) : ?>
                        <div class="psmt-media-creator-link psmt-shortcode-media-creator-link">
							<?php echo $before_creator; ?><?php psmt_media_creator_link(); ?><?php echo $after_creator; ?>
                        </div>
					<?php endif; ?>

					<div
						class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_media_type(), psmt_get_media() ); ?></div>

					<div
						class="psmt-item-meta psmt-media-meta psmt-media-shortcode-item-meta psmt-media-meta-bottom psmt-media-shortcode-item-meta-bottom">
						<?php do_action( 'psmt_media_shortcode_item_meta' ); ?>
					</div>

					<?php do_action( 'psmt_after_media_shortcode_item' ); ?>

				</div>

			<?php endwhile; ?>
			<?php psmt_reset_media_data(); ?>
		</div>

		<?php if ( $show_pagination ) : ?>
			<div class="psmt-paginator">
				<?php echo $query->paginate( false ); ?>
			</div>
		<?php endif; ?>

	</div>

<?php endif; ?>
