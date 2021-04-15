<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php while ( psmt_have_media() ) : psmt_the_media(); ?>
	<?php $media = psmt_get_media(); ?>
	<?php $type = psmt_get_media_type( $media ); ?>
	<div class="psmt-u <?php psmt_media_class( psmt_get_media_grid_column_class() ); ?>" data-psmt-type="<?php echo $type;?>">

		<?php do_action( 'psmt_before_media_item' ); ?>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-top">
			<?php do_action( 'psmt_media_meta_top' ); ?>
		</div>

		<div class='psmt-item-entry psmt-media-entry psmt-photo-entry'>
			<?php
			if ( ! psmt_is_doc_viewable( $media ) ) {
				$url   = psmt_get_media_src( '', $media );
				$class = 'psmt-no-lightbox';

			} else {
				$url   = psmt_get_media_permalink( $media );
				$class = '';
			}
			?>
            <a href="<?php echo esc_attr( $url ); ?>" <?php psmt_media_html_attributes( array( 'class' => "psmt-item-thumbnail psmt-media-thumbnail psmt-photo-thumbnail {$class}" ) ); ?> data-psmt-type="<?php echo $type;?>">
                <img src="<?php psmt_media_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_media_title() ); ?> "/>
            </a>
            <a href="<?php echo esc_url( $url ); ?>" <?php psmt_media_html_attributes(
				array(
					'class' => "psmt-item-title psmt-media-title {$class}",
				) ); ?> data-psmt-type="<?php echo $type;?>" >
				<?php psmt_media_title(); ?>
            </a>

        </div>

		<div class="psmt-item-actions psmt-media-actions">
			<?php psmt_media_action_links(); ?>
		</div>

		<div class="psmt-item-meta psmt-media-meta psmt-media-meta-bottom">
			<?php do_action( 'psmt_media_meta' ); ?>
		</div>

		<?php do_action( 'psmt_after_media_item' ); ?>
	</div>

<?php endwhile; ?>
