<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form method="post" action="" id="psmt-gallery-edit-form" class="psmt-form psmt-form-stacked psmt-gallery-delete-form">
<div class="psmt-notice psmt-warning">

	<p class="psmt-gallery-delete-warning"> <?php _e( 'Möchtest Du diese Galerie wirklich löschen? Du wirst alle Medien verlieren!', 'psourcemediathek' );?></p>

	<?php do_action( 'psmt_gallery_delete_form_fields' ); ?>

	<input type="checkbox" id="psmt-delete-gallery-agree" value="1" name="psmt-delete-agree" /><label for="psmt-delete-gallery-agree" class="screen-reader-text"><?php _e( 'Ja, ich möchte diese Galerie löschen.', 'psourcemediathek' ); ?></label>

	<input type='hidden' name='psmt-action' value='delete-gallery' />
	<input type='hidden' name='gallery_id' value="<?php echo psmt_get_current_gallery_id() ;?>" />
	<?php wp_nonce_field( 'psmt-delete-gallery', 'psmt-nonce' );?>

	<button type="submit" class="psmt-button psmt-button-warning">
		<?php _e( 'Ja, ich verstehe und ich möchte löschen!', 'psourcemediathek' );?>
	</button>
</div>

</form>
