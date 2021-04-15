<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bulk Edit Media template for single gallery bulk media edit page
 * action:psourcemediathek/gallery/galleryname/manage/edit/
 */
?>
<?php
// fetch all media in the gallery.
$items = new PSMT_Media_Query( array(
	'gallery_id' => psmt_get_current_gallery_id(),
	'per_page'   => - 1,
	'nopaging'   => true,
) );
?>
<?php if ( $items->have_media() ) : ?>

	<form action="" method="post" id="psmt-media-bulkedit-form" class="psmt-form psmt-form-stacked psmt-form-bulkedit-media ">

		<?php do_action( 'psmt_before_bulkedit_media_form' ); ?>

		<div class="psmt-g psmt-bulk-edit-media-action-row">
			<div class="psmt-u-2-24 psmt-bulk-edit-media-checkbox">
				<?php //allow to check/uncheck. ?>
				<input type="checkbox" name="psmt-check-all" value="1" id="psmt-check-all"/><label for="psmt-check-all" class="screen-reader-text"><?php _e( 'Wähle Alle', 'psourcemediathek' ); ?></label>
			</div>

			<div class="psmt-u-17-24 psmt-bulk-edit-media-action">

				<label for="psmt-edit-media-bulk-action" class="screen-reader-text"><?php _e( 'Massenaktion auswählen', 'psourcemediathek' ); ?></label>

				<select name="psmt-edit-media-bulk-action" id="psmt-edit-media-bulk-action">
					<option value=""><?php _e( 'Massenaktion', 'psourcemediathek' ); ?></option>
					<option value="delete"><?php _e( 'Löschen', 'psourcemediathek' ); ?></option>
				</select>

				<?php do_action( 'psmt_after_media_bulkedit_actions' ); ?>
				<?php //bulk action. ?>
				<button class="psmt-button psmt-button-success psmt-button-primary psmt-bulk-action-apply-button" name="bulk-action-apply"><?php _e( 'Anwenden', 'psourcemediathek' ); ?></button>

			</div>

			<div class="psmt-u-5-24 psmt-bulk-edit-media-submit">
				<button type="submit" name="psmt-edit-media-submit" id="psmt-edit-media-submit"><?php _e( 'Aktualisieren', 'psourcemediathek' ); ?> </button>
			</div>

		</div> <!-- end of bulk action row -->

		<?php do_action( 'psmt_before_bulkedit_media_list' ); ?>

		<div id="psmt-editable-media-list">

			<?php while ( $items->have_media() ) : $items->the_media(); ?>

				<?php
				$media    = psmt_get_media();
				$media_id = $media->id;
				?>

				<div class='psmt-g psmt-bulk-edit-media-item' id="psmt-edit-media-<?php psmt_media_id(); ?>">

					<div class="psmt-u-2-24">
						<input type="checkbox" id="psmt-delete-media-check[<?php echo $media_id; ?>]" name="psmt-delete-media-check[<?php echo $media_id; ?>]" class="psmt-delete-media-check" value='1'/>
						<label for="psmt-delete-media-check[<?php echo $media_id; ?>]" class="screen-reader-text">
							<?php _e( 'Medienprüfung löschen', 'psourcemediathek' ); ?>
						</label>
					</div>

					<div class='psmt-u-8-24 psmt-bulk-edit-media-cover'>

						<?php do_action( 'psmt_before_bulk_edit_media_item_thumbnail' ); ?>
						<img src="<?php psmt_media_src( 'thumbnail' ); ?>" class="psmt-image"/>
						<?php do_action( 'psmt_after_bulk_edit_media_item_thumbnail' ); ?>

					</div>

					<div class='psmt-u-14-24 psmt-bulk-edit-media-details'>
						<div class="psmt-g psmt-bulk-edit-media-details-entry">
							<?php do_action( 'psmt_before_bulk_edit_media_item_form_fields' ); ?>

							<?php $status_name = 'psmt-media-status[' . $media_id . ']'; ?>
							<div class="psmt-u-1-1 psmt-bulk-edit-media-status">
								<label for="<?php echo $status_name; ?>"><?php _ex( 'Status', 'Medienstatusbezeichnung für Bearbeiten', 'psourcemediathek' ); ?></label>
								<?php psmt_status_dd( array(
									'name'      => $status_name,
									'id'        => $status_name,
									'selected'  => psmt_get_media_status(),
									'component' => $media->component,
								) ); ?>
							</div>

							<div class="psmt-u-1-1 psmt-bulk-edit-media-title">
								<label for="psmt-media-title[<?php echo $media_id; ?>]"><?php _ex( 'Titel:', 'Medientitelbezeichnung für bearbeiten', 'psourcemediathek' ); ?></label>
								<input type="text" id="psmt-media-title[<?php echo $media_id; ?>]" class="psmt-input-1" placeholder="<?php _ex( 'Title (Required)', 'Platzhalter für den Titel des Medienbearbeitungsformulars', 'psourcemediathek' ); ?>" name="psmt-media-title[<?php echo $media_id; ?>]" value="<?php echo esc_attr( psmt_get_media_title() ); ?>"/>
							</div>

							<div class="psmt-u-1 psmt-bulk-edit-media-description">
								<label for="psmt-media-description"><?php _ex( 'Beschreibung', 'Medienbeschreibungsetikett für Bearbeiten', 'psourcemediathek' ); ?></label>
								<textarea id="psmt-media-description" name="psmt-media-description[<?php echo $media_id; ?>]" rows="3" class="psmt-input-1"><?php echo esc_textarea( $media->description ); ?></textarea>
							</div>

							<?php do_action( 'psmt_after_bulk_edit_media_item_form_fields' ); ?>

						</div><!-- end of .psmt-bulk-edit-media-details-entry -->
					</div>    <!--end of edit section -->
					<hr/>
				</div>
			<?php endwhile; ?>

			<?php $ids = $items->get_ids(); ?>

			<input type='hidden' name='psmt-editing-media-ids' value="<?php echo join( ',', $ids ); ?>"/>

		</div>

		<?php do_action( 'psmt_after_bulkedit_media_list' ); ?>

		<?php //please do not delete the 2 lines below ; ?>
		<input type='hidden' name="psmt-action" value='edit-gallery-media'/>
		<?php wp_nonce_field( 'psmt-edit-gallery-media', 'psmt-nonce' ); ?>

		<button type="submit" name="psmt-edit-media-submit" id="psmt-edit-media-submit"><?php _e( 'Aktualisieren', 'psourcemediathek' ); ?> </button>

	</form>
	<?php psmt_reset_media_data(); ?>
<?php else: ?>

	<div class="psmt-notice psmt-empty-gallery-notice">
		<p><?php _e( 'Es gibt keine Medien in dieser Galerie. Bitte füge Medien hinzu, um sie hier zu sehen!', 'psourcemediathek' ); ?></p>
	</div>

<?php endif; ?>
