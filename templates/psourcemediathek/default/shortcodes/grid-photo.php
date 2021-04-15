<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Photo list in shortcode grid view
 * You can override it in yourtheme/psourcemediathek/default/shortcodes/grid-photo.php
 *
 */
$query            = psmt_shortcode_get_media_data( 'query' );
$lightbox_enabled = ! empty( $atts['lightbox'] ) ? 1 : 0;
$lightbox_class   = $lightbox_enabled ? 'psmt-shortcode-lightbox-enabled' : '';
$media_ids        = join( ',', $query->get_ids() );

?>
<?php if ( $query->have_media() ) : ?>
	<div class="psmt-container psmt-shortcode-wrapper psmt-shortcode-media-list-wrapper">
		<div class="psmt-g psmt-item-list psmt-media-list psmt-shortcode-item-list psmt-shortcode-list-media psmt-shortcode-list-media-photo <?php echo $lightbox_class; ?> " data-media-ids="<?php echo $media_ids; ?>">

			<?php while ( $query->have_media() ) : $query->the_media(); ?>
				<?php $type = psmt_get_media_type(); ?>
				<div class="psmt-u <?php psmt_media_class( psmt_get_grid_column_class( psmt_shortcode_get_media_data( 'column' ) ) ); ?>" data-psmt-type="<?php echo $type;?>">

					<?php do_action( 'psmt_before_media_shortcode_item' ); ?>

					<div class="psmt-item-meta psmt-media-meta psmt-media-shortcode-item-meta psmt-media-meta-top psmt-media-shortcode-item-meta-top">
						<?php do_action( 'psmt_media_shortcode_item_meta_top' ); ?>
					</div>

					<div class='psmt-item-entry psmt-media-entry psmt-photo-entry'>

						<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
							'class'            => "psmt-item-thumbnail psmt-media-thumbnail psmt-photo-thumbnail",
							'data-psmt-context' => 'shortcode',
						) ); ?> data-psmt-type="<?php echo $type;?>">

							<img src="<?php psmt_media_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( psmt_get_media_title() ); ?> "/>
						</a>

					</div>

					<a href="<?php psmt_media_permalink(); ?>" <?php psmt_media_html_attributes( array(
						'class'            => "psmt-item-title psmt-media-title psmt-photo-title",
						'data-psmt-context' => 'shortcode',
					) ); ?> data-psmt-type="<?php echo $type;?>">
						<?php psmt_media_title(); ?>
					</a>
					<?php if ( $show_creator ) : ?>
                        <div class="psmt-media-creator-link psmt-shortcode-media-creator-link">
							<?php echo $before_creator; ?><?php psmt_media_creator_link(); ?><?php echo $after_creator; ?>
                        </div>
					<?php endif; ?>

                    <div class="psmt-item-actions psmt-media-actions psmt-photo-actions">
						<?php psmt_media_action_links(); ?>
					</div>

					<div class="psmt-item-meta psmt-media-meta psmt-media-shortcode-item-meta psmt-media-meta-bottom psmt-media-shortcode-item-meta-bottom">
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
