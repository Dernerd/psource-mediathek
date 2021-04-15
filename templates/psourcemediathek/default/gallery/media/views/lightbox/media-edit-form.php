<?php
/**
 * Edit media form for media lightbox.
 */
?>
<form class="psmt-lightbox-media-edit-form psmt-form-hidden psmt-clearfix" id="psmt-lightbox-media-edit-form-<?php psmt_media_id(); ?>" data-media-id="<?php psmt_media_id(); ?>" method="post" action="">
    <div class="psmt-g psmt-lightbox-media-edit-details-wrapper">

		<?php $media = psmt_get_media(); ?>

        <div class="psmt-u-1-1 psmt-media-status">
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
            <input type="text" id="psmt-media-title" class="psmt-input-1"
                   placeholder="<?php _ex( 'Medientitel (Erforderlich)', 'Platzhalter für den Titel des Medienbearbeitungsformulars', 'psourcemediathek' ); ?>"
                   name="psmt-media-title" value="<?php echo esc_attr( $media->title ); ?>"/>
        </div>

        <div class="psmt-u-1 psmt-media-description">
            <label for="psmt-media-description"><?php _e( 'Beschreibung', 'psourcemediathek' ); ?></label>
            <textarea id="psmt-media-description" name="psmt-media-description" rows="3" class="psmt-input-1"><?php echo esc_textarea( $media->description ); ?></textarea>
        </div>

		<?php do_action( 'psmt_after_lightbox_edit_media_form_fields' ); ?>
        <input type='hidden' name="psmt-action" value='edit-lightbox-media'/>
        <input type="hidden" name='psmt-media-id' value="<?php psmt_media_id(); ?> "/>
		<?php wp_nonce_field( 'psmt-lightbox-edit-media', 'psmt-nonce' ); ?>

        <div class="psmt-u-1 psmt-clearfix psmt-lightbox-edit-media-buttons-row">
            <img src="<?php echo psmt_get_asset_url( 'assets/images/loader.gif', 'psmt-loader' ); ?>"
                 class="psmt-loader-image"/>
            <button type="submit" class='psmt-button-secondary psmt-lightbox-edit-media-submit-button psmt-align-right'
                    data-psmt-media-id="<?php psmt_media_id(); ?>"> <?php _e( 'Speichern', 'psourcemediathek' ); ?></button>
            <button class='psmt-button-secondary psmt-lightbox-edit-media-cancel-button psmt-align-right'
                    data-psmt-media-id="<?php psmt_media_id(); ?>"> <?php _e( 'Abbrechen', 'psourcemediathek' ); ?></button>
        </div>

    </div>
</form>
