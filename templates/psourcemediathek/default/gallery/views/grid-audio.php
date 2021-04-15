<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php while ( psmt_have_media() ) : psmt_the_media(); ?>
	<?php $type = psmt_get_media_type(); ?>
	<div class="<?php psmt_media_class( 'psmt-u-6-24' ); ?>" data-psmt-type="<?php echo $type;?>">

		<?php do_action( 'psmt_before_media_item' ); ?>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-top">
			<?php do_action( 'psmt_media_meta_top' ); ?>
		</div>

		<div class='psmt-item-entry psmt-media-entry psmt-audio-entry'>
			<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array( 'class' => 'psmt-item-thumbnail psmt-media-thumbnail psmt-audio-thumbnail' ) ); ?> data-psmt-type="<?php echo $type;?>">
				<img src="<?php psmt_media_src( 'thumbnail' ); ?>" alt="<?php psmt_media_title(); ?> "/>
			</a>
		</div>

		<div class="psmt-item-content psmt-audio-content psmt-audio-player">
			<?php psmt_media_content(); ?>
		</div>

		<a href="<?php psmt_media_permalink(); ?>" class="psmt-item-title psmt-media-title psmt-audio-title"><?php psmt_media_title(); ?></a>

		<div class="psmt-item-actions psmt-media-actions psmt-audio-actions">
			<?php psmt_media_action_links(); ?>
		</div>

		<div class="psmt-type-icon"><?php do_action( 'psmt_type_icon', psmt_get_media_type(), psmt_get_media() ); ?></div>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-bottom">
			<?php do_action( 'psmt_media_meta' ); ?>
		</div>

		<?php do_action( 'psmt_after_media_item' ); ?>
	</div>

<?php endwhile; ?>