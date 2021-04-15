<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form action="" method="post" id="psmt-media-reorder-form" class="psmt-form psmt-form-stacked psmt-form-reorder-media ">

<?php do_action( 'psmt_before_sortable_media_form' ); ?>
	
<div id="psmt-sortable" class='psmt-g'>
	
	<?php  $items = new PSMT_Media_Query( array( 'gallery_id' => psmt_get_current_gallery_id(), 'per_page' => -1, 'nopaging' => true ) );	?>

	<?php while ( $items->have_media() ) : $items->the_media(); ?>

		<div class='psmt-u-1-4 psmt-reorder-media ' id="psmt-reorder-media-<?php psmt_media_id(); ?>">
			
			<div class='psmt-reorder-media-cover'>
				<?php do_action( 'psmt_before_sortable_media_item' ); ?>
				
				<img src="<?php psmt_media_src( 'thumbnail' ); ?>" />
				<input type='hidden' name="psmt-media-ids[]" value="<?php psmt_media_id();?>" />
				<h4><?php psmt_media_title();?></h4>
				<?php do_action( 'psmt_after_sortable_media_item' ); ?>
			 </div>
			
		</div>	
	<?php endwhile;	?>
	
</div>

	<?php do_action( 'psmt_after_sortable_media_form' ); ?>
	
<?php wp_nonce_field( 'psmt-reorder-gallery-media', 'psmt-nonce' ); ?>

<?php psmt_reset_media_data(); ?>
	
<input type="hidden" name='psmt-action' value='reorder-gallery-media' />
	
<button type="submit" name="psmt-reorder-media-submit"  id="psmt-reorder-media-submit" ><?php _e( 'Speichern','psourcemediathek' );?> </button>

</form>
