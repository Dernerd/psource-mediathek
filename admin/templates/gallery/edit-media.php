<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bulk Edit Media template for single gallery bulk media edit page
 * action:psourcemediathek/gallery/galleryname/manage/edit/
 */


?>
<a href="#" id = "psmt-reload-bulk-edit-tab" class="psmt-reload" title="<?php _e( 'Reload media edit panel', 'psourcemediathek' );?>"><span class="dashicons dashicons-update"></span> <?php _e( 'Reload', 'psourcemediathek' );?></a>
<?php
$current_gallery_id = psmt_get_current_gallery_id();

	// fetch all media in the gallery.
	$items = psourcemediathek()->the_media_query;
?>
<?php if ( $items->have_media() ) : ?>
<div class="psmt-container" id="psmt-container">
	<div id="psmt-media-bulkedit-div" class="psmt-form psmt-form-stacked psmt-form-bulkedit-media ">

		<?php do_action( 'psmt_before_bulkedit_media_form' ); ?>

		<div class="psmt-g psmt-media-edit-bulk-action-row">
			<div class="psmt-u-2-24">
				<?php // allow to check/uncheck. ?>
				<input type="checkbox" name="psmt-check-all" value="1" id="psmt-check-all" /><label for="psmt-check-all" class="screen-reader-text"><?php _e( 'Bulk select or deselect', 'psourcemediathek' ); ?></label>
			</div>

			<div class="psmt-u-17-24">
				<label for="psmt-edit-media-bulk-action" class="screen-reader-text"><?php _e( 'Bulk Edit', 'psourcemediathek' ); ?></label>

                <select name="psmt-edit-media-bulk-action" id="psmt-edit-media-bulk-action">
					<option value=""><?php _e( 'Bulk Action', 'psourcemediathek' );?></option>
					<option value="delete"><?php _e( 'Delete', 'psourcemediathek' );?></option>
				</select>

				<?php do_action( 'psmt_after_media_bulkedit_actions' ); ?>
				<?php //bulk action ?>
				<button class="button button-primary psmt-button psmt-button-success psmt-button-primary psmt-bulk-action-apply-button" name="bulk-action-apply" id="bulk-action-apply"><?php _e( 'Apply', 'psourcemediathek' ) ;?></button>

			</div>

			<div class="psmt-u-5-24">
				<button type="submit" name="psmt-edit-media-submit"  id="psmt-edit-media-submit" class="button button-primary"><?php _e( 'Update','psourcemediathek' );?> </button>

			</div>

		</div> <!-- end of bulk action row -->

		<?php do_action( 'psmt_before_bulkedit_media_list' ); ?>

		<div id="psmt-editable-media-list" class="psmt-g">


			<?php while ( $items->have_media() ) : $items->the_media(); ?>

				<?php
					$media = psmt_get_media();
					$media_id = $media->id;
				?>

				<div class='psmt-edit-media' id="psmt-edit-media-<?php psmt_media_id(); ?>">

					<div class="psmt-u-2-24">
						<input type="checkbox" id="psmt-delete-media-check[<?php echo $media_id;?>]" name="psmt-delete-media-check[<?php echo $media_id;?>]" class="psmt-delete-media-check" value='1' /><label for="psmt-delete-media-check[<?php echo $media_id;?>]" class="screen-reader-text"><?php _e( 'Delete media', 'psourcemediathek' ); ?></label>
					</div>

					<div class='psmt-u-8-24 psmt-edit-media-cover'>

						<?php do_action( 'psmt_before_edit_media_item_thumbnail' ); ?>
						<img src="<?php psmt_media_src('thumbnail');?>" class="psmt-image" />
						<?php do_action( 'psmt_after_edit_media_item_thumbnail' ); ?>

					 </div>

					<div class='psmt-u-14-24'>
							<div class="psmt-g">
								<?php do_action( 'psmt_before_edit_media_item_form_fields' ); ?>

								<?php $status_name = 'psmt-media-status[' . $media_id . ']'; ?>
								<div class="psmt-u-1-1 psmt-media-status">
									<label for="<?php echo $status_name;?>"><?php _ex( 'Status', 'Medienstatusbezeichnung für Bearbeiten', 'psourcemediathek' ); ?></label>
									<?php psmt_status_dd( array( 'name' => $status_name, 'id'=> $status_name, 'selected' => psmt_get_media_status(), 'component' => $media->component  ) );?>
								</div>

								<div class="psmt-u-1-1 psmt-media-title">
									<label for="psmt-gallery-title[<?php echo $media_id;?>]"><?php _ex( 'Titel:', 'Medientitelbezeichnung für bearbeiten', 'psourcemediathek' ); ?></label>
									<input type="text" id="psmt-gallery-title[<?php echo $media_id;?>]" class="psmt-input-1" placeholder="<?php _ex( 'Title (Required)', 'Platzhalter für den Titel des Medienbearbeitungsformulars', 'psourcemediathek' ) ;?>" name="psmt-media-title[<?php echo $media_id;?>]" value="<?php echo esc_attr(psmt_get_media_title() );?>"/>

								</div>

								<div class="psmt-u-1 psmt-media-description">
									<label for="psmt-media-description"><?php _ex( 'Beschreibung', 'Medienbeschreibungsetikett für Bearbeiten', 'psourcemediathek' );?></label>
									<textarea id="psmt-media-description" name="psmt-media-description[<?php echo $media_id;?>]" rows="3" class="psmt-input-1"><?php echo esc_textarea( psmt_get_media_description() ); ?></textarea>
								</div>

								<?php do_action( 'psmt_after_edit_media_item_form_fields' ); ?>


							</div><!-- end of .psmt-g -->
					</div>	<!--end of edit section -->
					<hr />
				</div>
			<?php endwhile;	?>

			<?php $ids = $items->get_ids(); ?>

			<input type='hidden' name='psmt-editing-media-ids' value="<?php echo join( ',', $ids );?>" />

		</div>

		<?php do_action( 'psmt_after_bulkedit_media_list' ); ?>

		<?php psmt_reset_media_data(); ?>

        <?php // please do not delete the 2 lines below. ?>
		<input type='hidden' name="psmt-action" value='edit-gallery-media' />
		<?php wp_nonce_field( 'psmt-edit-gallery-media', 'psmt-nonce' ); ?>

		<button type="submit" name="psmt-edit-media-submit"  id="psmt-edit-media-submit" class="button button-primary"><?php _e( 'Aktualisieren','psourcemediathek' );?> </button>

	</div>
</div>
<?php else : ?>
	<div class="psmt-notice psmt-empty-gallery-notice">
		<p><?php _e( 'Es gibt keine Medien in dieser Galerie. Bitte füge Medien hinzu, um sie hier zu sehen!', 'psourcemediathek' );?></p>
	</div>

<?php endif; ?>
