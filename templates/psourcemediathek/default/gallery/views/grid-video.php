<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php while ( psmt_have_media() ): psmt_the_media(); ?>
	<?php $type = psmt_get_media_type(); ?>
	<div class="<?php psmt_media_class( 'psmt-u-12-24' ); ?>" data-psmt-type="<?php echo $type;?>">

		<?php do_action( 'psmt_before_media_item' ); ?>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-top">
			<?php do_action( 'psmt_media_meta_top' ); ?>
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


		// $ids = psmt_get_all_media_ids();
		// echo wp_playlist_shortcode( array( 'ids' => $ids));
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
		   class="psmt-item-title psmt-media-title psmt-audio-title"><?php psmt_media_title(); ?></a>

		<div class="psmt-item-actions psmt-media-actions psmt-video-actions">
			<?php psmt_media_action_links(); ?>
		</div>

		<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_media_type(), psmt_get_media() ); ?></div>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-bottom">
			<?php do_action( 'psmt_media_meta' ); ?>
		</div>

		<?php do_action( 'psmt_after_media_item' ); ?>
	</div>

<?php endwhile; ?>