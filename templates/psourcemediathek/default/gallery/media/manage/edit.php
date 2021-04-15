<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( psmt_user_can_edit_media( psmt_get_current_media_id() ) ) : ?>

	<?php $media = psmt_get_current_media(); ?>

	<form method="post" action="" id="psmt-media-edit-form" class="psmt-form psmt-form-stacked psmt-media-edit-form">

		<div class="psmt-g">
			<?php do_action( 'psmt_before_edit_media_form_fields', $media->id ); ?>

			<div class="psmt-u-1-2 psmt-media-thumbnail psmt-cover-wrapper ">
				<?php do_action( 'psmt_before_edit_media_thumbnail_field', $media->id ); ?>

				<div class="psmt-editable-cover psmt-media-editable-cover" id="psmt-cover-<?php echo $media->id; ?>">
					<img src="<?php psmt_media_src( 'thumbnail' ); ?>"
					     class='psmt-image psmt-cover-image psmt-media-cover-image '/>
					<input type="hidden" class="psmt-gallery-id" value="<?php echo psmt_get_current_gallery_id(); ?>"/>
					<input type="hidden" class="psmt-parent-id" value="<?php echo $media->id; ?>"/>
					<input type="hidden" class="psmt-parent-type" value="media"/>
				</div>

				<?php if ( $media->type != 'photo' ) : ?>
					<div id="change-gallery-cover">
						<a href="#" id="psmt-cover-upload"><?php _e( 'Lade neues Cover hoch', 'psourcemediathek' ); ?></a>
						<?php if ( psmt_media_has_cover_image( $media ) ) : ?>
							<a href="<?php psmt_media_cover_delete_url( $media ); ?>"><?php _e( 'Cover löschen', 'psourcemediathek' ); ?> </a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php do_action( 'psmt_after_edit_media_thumbnail_field', $media->id ); ?>
			</div>

			<div class="psmt-u-1-2 psmt-media-status">
				<label for="psmt-media-status"><?php _e( 'Status', 'psourcemediathek' ); ?></label>
				<?php psmt_status_dd( array(
					'name'      => 'psmt-media-status',
					'id'        => 'psmt-media-status',
					'selected'  => $media->status,
					'component' => $media->component,
				) );
				?>
			</div>

			<div class="psmt-u-1-1 psmt-media-title">
				<label for="psmt-media-title"> <?php _e( 'Titel', 'psourcemediathek' ); ?></label>
				<input type="text" id="psmt-media-title" class="psmt-input-1" placeholder="<?php _ex( 'Medientitel (Erforderlich)', 'Platzhalter für den Titel des Medienbearbeitungsformulars', 'psourcemediathek' ); ?>" name="psmt-media-title" value="<?php echo esc_attr( $media->title ); ?>"/>
			</div>

			<div class="psmt-u-1 psmt-media-description">
				<label for="psmt-media-description"><?php _e( 'Beschreibung', 'psourcemediathek' ); ?></label>
				<textarea id="psmt-media-description" name="psmt-media-description" rows="3" class="psmt-input-1"><?php echo esc_textarea( $media->description ); ?></textarea>
			</div>

			<?php do_action( 'psmt_after_edit_media_form_fields' ); ?>
			<input type='hidden' name="psmt-action" value='edit-media'/>
			<input type="hidden" name='psmt-media-id' value="<?php echo psmt_get_current_media_id(); ?> "/>
			<?php wp_nonce_field( 'psmt-edit-media', 'psmt-nonce' ); ?>

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
