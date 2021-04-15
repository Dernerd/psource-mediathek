<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//action:psourcemediathek/create :- Galerie erstellen template
?>
<div id="psmt-gallery-create-form-wrapper" class="psmt-container">

	<?php if ( psmt_user_can_create_gallery( psmt_get_current_component(), psmt_get_current_component_id() ) ) : ?>

		<form method="post" action="" id="psmt-gallery-create-form" class="psmt-form psmt-form-stacked psmt-gallery-create-form">
			<?php
			$title  = $description = $type = '';
			$status = psmt_get_default_status();

			if ( ! empty( $_POST['psmt-gallery-title'] ) ) {
				$title = $_POST['psmt-gallery-title'];
			}

			if ( ! empty( $_POST['psmt-gallery-description'] ) ) {
				$description = $_POST['psmt-gallery-description'];
			}

			if ( ! empty( $_POST['psmt-gallery-status'] ) ) {
				$status = $_POST['psmt-gallery-status'];
			}

			if ( ! empty( $_POST['psmt-gallery-type'] ) ) {
				$type = $_POST['psmt-gallery-type'];
			}

			?>

			<?php do_action( 'psmt_before_create_gallery_form' ); ?>

			<div class="psmt-g psmt-form-wrap">
				<div class="psmt-u-1-1 psmt-before-create-gallery-form-fields">
					<?php // use this hook to add anything at the top of the gallery create form.  ?>
					<?php do_action( 'psmt_before_create_gallery_form_fields' ); ?>
				</div>

				<div class="psmt-u-1-2 psmt-editable-gallery-type">
					<label for="psmt-gallery-type"><?php _e( 'Typ', 'psourcemediathek' ); ?></label>
					<?php psmt_type_dd( array( 'selected' => $type, 'component' => psmt_get_current_component() ) ) ?>
				</div>

				<div class="psmt-u-1-2 psmt-editable-gallery-status">
					<label for="psmt-gallery-status"><?php _e( 'Status', 'psourcemediathek' ); ?></label>
					<?php psmt_status_dd( array(
						'selected'  => $status,
				        'component' => psmt_get_current_component(),
					) ); ?>
				</div>

				<div class="psmt-u-1-1 psmt-editable-gallery-title">
					<label for="psmt-gallery-title"><?php _e( 'Titel:', 'psourcemediathek' ); ?></label>
					<input id="psmt-gallery-title" type="text" value="<?php echo esc_attr( $title ) ?>" class="psmt-input-1" placeholder="<?php _ex( 'Galerietitel (Erforderlich)', 'Placeholder for gallery create form title', 'psourcemediathek' ); ?>" name="psmt-gallery-title"/>
				</div>

				<div class="psmt-u-1-1 psmt-editable-gallery-description">
					<label for="psmt-gallery-description"><?php _e( 'Beschreibung', 'psourcemediathek' ); ?></label>
					<textarea id="psmt-gallery-description" name="psmt-gallery-description" rows="3" class="psmt-input-1"><?php echo esc_textarea( $description ); ?></textarea>
				</div>

				<div class="psmt-u-1-1 psmt-after-create-gallery-form-fields">
					<?php // use this hook to add any extra data here for settings or other things at the bottom of create gallery form. ?>
					<?php do_action( 'psmt_after_create_gallery_form_fields' ); ?>

				</div>

				<?php do_action( 'psmt_before_create_gallery_form_submit_field' ); ?>
				<?php
				// do not delete this line, we need it to validate.
				wp_nonce_field( 'psmt-create-gallery', 'psmt-nonce' );
				// also do not delete the next line <input type='hidde' name='psmt-action' value='create-gallery' >
				?>

				<input type='hidden' name="psmt-action" value='create-gallery'/>

				<div class="psmt-u-1-1 psmt-clearfix psmt-submit-button">
					<button type="submit" class='psmt-align-right psmt-button-primary psmt-create-gallery-button '> <?php _e( 'Erstellen', 'psourcemediathek' ); ?></button>
				</div>

			</div><!-- end of .psmt-g -->

			<?php do_action( 'psmt_after_create_gallery_form' ); ?>
		</form>

	<?php else: ?>
		<div class='psmt-notice psmt-unauthorized-access'>
			<p><?php _e( 'Unautorisierter Zugriff!', 'psourcemediathek' ); ?></p>
		</div>
	<?php endif; ?>
</div><!-- end of psmt-container -->
