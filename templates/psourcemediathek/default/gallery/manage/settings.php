<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( psmt_user_can_edit_gallery( psmt_get_current_gallery_id() ) ) : ?>

	<?php $gallery = psmt_get_current_gallery(); ?>

	<form method="post" action="" id="psmt-gallery-edit-form" class="psmt-form psmt-form-stacked psmt-gallery-edit-form">

		<div class="psmt-g">

			<div class="psmt-u-1-1 psmt-clearfix">
				<?php do_action( 'psmt_before_edit_gallery_form_fields', $gallery->id ); ?>
			</div>

			<div class="psmt-u-1-2 psmt-gallery-type psmt-cover-wrapper">
				<div class="psmt-editable-cover psmt-gallery-editable-cover" id="psmt-cover-<?php echo $gallery->id; ?>">
					<img src="<?php psmt_gallery_cover_src( 'thumbnail' ); ?>" class='psmt-image psmt-cover-image psmt-gallery-cover-image '/>
					<input type="hidden" class="psmt-gallery-id" value="<?php echo $gallery->id; ?>"/>
					<input type="hidden" class="psmt-parent-id" value="<?php echo $gallery->id; ?>"/>
					<input type="hidden" class="psmt-parent-type" value="gallery"/>

				</div>

				<div id="change-gallery-cover">
					<a href="#" id="psmt-cover-upload"><?php _e( 'Neues Cover hochladen', 'psourcemediathek' ); ?></a>
					<?php if ( psmt_gallery_has_cover_image() ) : ?>
						<a href="<?php psmt_gallery_cover_delete_url(); ?>"><?php _e( 'Cover löschen', 'psourcemediathek' ); ?> </a>
					<?php endif; ?>
				</div>

			</div>

			<div class="psmt-u-1-2 psmt-gallery-status">
				<label for="psmt-gallery-status"><?php _e( 'Status', 'psourcemediathek' ); ?> </label>
				<?php psmt_status_dd( array( 'selected' => $gallery->status, 'component' => $gallery->component ) ); ?>
			</div>

			<div class="psmt-u-1-1 psmt-gallery-title">
				<label for="psmt-gallery-title"><?php _e( 'Titel', 'psourcemediathek' ); ?></label>
				<input type="text" id="psmt-gallery-title" class="psmt-input-1" placeholder="<?php _ex( 'Galerietitel (Erforderlich)', 'Platzhalter für den Titel für die Galerie', 'psourcemediathek' ); ?>" name="psmt-gallery-title" value="<?php echo esc_attr( $gallery->title ); ?>"/>
			</div>

			<div class="psmt-u-1 psmt-gallery-description">
				<label for="psmt-gallery-description"><?php _e( 'Beschreibung', 'psourcemediathek' ); ?></label>
				<textarea id="psmt-gallery-description" name="psmt-gallery-description" rows="3" class="psmt-input-1"><?php echo esc_textarea( $gallery->description ); ?></textarea>
			</div>

			<div class="psmt-u-1-1 psmt-clearfix">
				<?php do_action( 'psmt_after_edit_gallery_form_fields' ); ?>
			</div>

			<?php do_action( 'psmt_before_edit_gallery_form_submit_field' ); ?>

            <input type='hidden' name="psmt-action" value='edit-gallery'/>
			<input type="hidden" name='psmt-gallery-id' value="<?php echo psmt_get_current_gallery_id(); ?> "/>

			<?php wp_nonce_field( 'psmt-edit-gallery', 'psmt-nonce' ); ?>

			<div class="psmt-u-1 psmt-clearfix psmt-submit-button">
				<button type="submit" class='psmt-button-primary psmt-button-secondary psmt-align-right'> <?php _e( 'Speichern', 'psourcemediathek' ); ?></button>
			</div>

		</div><!-- end of .psmt-g -->

	</form>

<?php else: ?>
	<div class='psmt-notice psmt-unauthorized-access'>
		<p><?php _e( 'Unautorisierter Zugriff!', 'psourcemediathek' ); ?></p>
	</div>
<?php endif; ?>
