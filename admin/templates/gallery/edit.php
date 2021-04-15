<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( psmt_user_can_edit_gallery( psmt_get_current_gallery_id() ) ) :?>

<?php

	$gallery = psmt_get_current_gallery();
?>
<div class="psmt-container">
<div  id="psmt-gallery-edit-form" class="psmt-form psmt-form-stacked psmt-gallery-edit-form">



	<div class="psmt-g">

		<div class="psmt-u-1-1 psmt-clearfix">

				<?php do_action( 'psmt_before_edit_gallery_form_fields', $gallery->id ); ?>

		</div>

		<div class="psmt-u-1-2 psmt-gallery-type psmt-cover-wrapper">
			<div class="psmt-editable-cover psmt-gallery-editable-cover"  id="psmt-cover-<?php echo $gallery->id ;?>">
				<img src="<?php	psmt_gallery_cover_src( 'thumbnail' );?>" class='psmt-image psmt-cover-image  psmt-gallery-cover-image '/>
				<input type="hidden" class="psmt-gallery-id" value="<?php echo $gallery->id; ?>" />
				<input type="hidden" class="psmt-parent-id" value="<?php echo $gallery->id; ?>" />

			</div>
			<div id="change-gallery-cover">
				<a href="#" id="psmt-cover-upload" class="button button-primary button-small psmt-admin-button-primary"><?php _e( 'Neues Cover hochladen', 'psourcemediathek' ) ;?></a>
				<?php if( psmt_gallery_has_cover_image()) :?>
				<a id="psmt-cover-delete" href="<?php psmt_gallery_cover_delete_url();?>" title="<?php _e( 'Cover löschen', 'psourcemediathek' );?>" class="button button-primary button-small psmt-admin-button-delete"><?php _e( 'Cover löschen', 'psourcemediathek' );?> </a>
				<?php endif;?>
			</div>
		</div>

		<div class="psmt-u-1-2 psmt-gallery-status">
			<?php // do_action( 'psmt_admin_gallery_edit_');?>
		</div>



		<div class="psmt-u-1 psmt-gallery-description">
			<label for="psmt-gallery-description"><?php _e( 'Beschreibung', 'psourcemediathek' );?></label>
			<textarea id="psmt-gallery-description" name="psmt-gallery-description" rows="3" class="psmt-input-1"><?php echo esc_textarea( $gallery->description) ;?></textarea>
		</div>
		<div class="psmt-u-1-1 psmt-clearfix">
			<?php do_action( 'psmt_after_edit_gallery_form_fields' ); ?>
		</div>

		<input type='hidden' name="psmt-action" value='edit-gallery' />
		<input type="hidden" name='psmt-gallery-id' value="<?php echo psmt_get_current_gallery_id();?> " />

		<?php wp_nonce_field( 'psmt-edit-gallery', 'psmt-nonce' );?>

		<div class="psmt-u-1 psmt-clearfix psmt-submit-button">
			<button id="psmt-update-gallery-details" type="submit"  class='button button-primary psmt-button-primary psmt-button-secondary psmt-align-right'> <?php _e( 'Save', 'psourcemediathek' ) ;?></button>
		</div>


	</div><!-- end of .psmt-g -->

</div>
</div>

<?php else : ?>
<div class='psmt-notice psmt-unauthorized-access'>
	<p><?php _e( 'Unautorisierter Zugriff!', 'psourcemediathek' ) ;?></p>
</div>
<?php endif; ?>
