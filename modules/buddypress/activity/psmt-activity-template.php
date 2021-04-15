<?php
/**
 * Templates injecting to activity
 *
 * @package psourcemediathek
 */

// No direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Add various upload icons/buttons to activity post form
 */
function psmt_activity_upload_buttons() {

	$component    = psmt_get_current_component();
	$component_id = psmt_get_current_component_id();

	// If activity upload is disabled or the user is not allowed to upload to current component, don't show.
	if ( ! psmt_is_activity_upload_enabled( $component ) || ! psmt_user_can_upload( $component, $component_id ) ) {
		return;
	}

	// if we are here, the gallery activity stream upload is enabled,
	// let us see if we are on user profile and gallery is enabled.
	if ( ! psmt_is_enabled( $component, $component_id ) ) {
		return;
	}
	// if we are on group page and either the group component is not enabled or gallery is not enabled for current group, do not show the icons.
	if ( function_exists( 'bp_is_group' ) && bp_is_group() && ( ! psmt_is_active_component( 'groups' ) || ! ( function_exists( 'psmt_group_is_gallery_enabled' ) && psmt_group_is_gallery_enabled() ) ) ) {
		return;
	}
	// for now, avoid showing it on single gallery/media activity stream.
	if ( psmt_is_single_gallery() || psmt_is_single_media() ) {
		return;
	}

	?>
	<div id="psmt-activity-upload-buttons" class="psmt-upload-buttons">
		<?php do_action( 'psmt_before_activity_upload_buttons' ); // allow to add more type.  ?>

		<?php if ( psmt_is_active_type( 'photo' ) && psmt_component_supports_type( $component, 'photo' ) ) : ?>
			<a href="#" id="psmt-photo-upload" data-media-type="photo" title="<?php _e( 'Foto hochladen', 'psourcemediathek' ) ; ?>">
                <img src="<?php echo psmt_get_asset_url( 'assets/images/media-button-photo.png', 'media-photo-icon' ); ?>"/>
            </a>
		<?php endif; ?>

		<?php if ( psmt_is_active_type( 'audio' ) && psmt_component_supports_type( $component, 'audio' ) ) : ?>
			<a href="#" id="psmt-audio-upload" data-media-type="audio" title="<?php _e( 'Audio hochladen', 'psourcemediathek' ) ; ?>">
                <img src="<?php echo psmt_get_asset_url( 'assets/images/media-button-audio.png', 'media-audio-icon' ); ?>"/>
            </a>
		<?php endif; ?>

		<?php if ( psmt_is_active_type( 'video' ) && psmt_component_supports_type( $component, 'video' ) ) : ?>
			<a href="#" id="psmt-video-upload" data-media-type="video" title="<?php _e( 'Video hochladen', 'psourcemediathek' ) ; ?>">
                <img src="<?php echo psmt_get_asset_url( 'assets/images/media-button-video.png', 'media-video-icon' ) ?>"/>
            </a>
		<?php endif; ?>

		<?php if ( psmt_is_active_type( 'doc' ) && psmt_component_supports_type( $component, 'doc' ) ) : ?>
			<a href="#" id="psmt-doc-upload" data-media-type="doc" title="<?php _e( 'Dokument hochladen', 'psourcemediathek' ) ; ?>">
                <img src="<?php echo psmt_get_asset_url( 'assets/images/media-button-doc.png', 'media-doc-icon' ); ?>" />
            </a>
		<?php endif; ?>

		<?php do_action( 'psmt_after_activity_upload_buttons' ); // allow to add more type.  ?>

	</div>
	<?php
}
// Add to activity post form.
add_action( 'bp_after_activity_post_form', 'psmt_activity_upload_buttons' );

/**
 * Add dropzone/feedback/uploaded media list for activity
 */
function psmt_activity_dropzone() {
	?>
    <div id="psmt-activity-media-upload-container" class="psmt-media-upload-container psmt-upload-container-inactive"><!-- psourcemediathek upload container -->
            <a href="#" class="psmt-upload-container-close" title="<?php esc_attr_e('Schließen', 'psourcemediathek');?>"><span>x</span></a>
        <!-- append uploaded media here -->
        <div id="psmt-uploaded-media-list-activity" class="psmt-uploading-media-list">
            <ul></ul>
        </div>
		<?php do_action( 'psmt_after_activity_upload_medialist' ); ?>

		<?php if ( psmt_is_file_upload_enabled( 'activity' ) ): ?>
            <!-- drop files here for uploading -->
			<?php psmt_upload_dropzone( 'activity' ); ?>
			<?php do_action( 'psmt_after_activity_upload_dropzone' ); ?>
            <!-- show any feedback here -->
            <div id="psmt-upload-feedback-activity" class="psmt-feedback">
                <ul></ul>
            </div>
		<?php endif; ?>
        <input type='hidden' name='psmt-context' class='psmt-context' value="activity"/>
        <?php do_action( 'psmt_after_activity_upload_feedback' ); ?>

	    <?php if ( psmt_is_remote_enabled( 'activity' ) ) : ?>
            <!-- remote media -->
            <div class="psmt-remote-media-container">
                <div class="psmt-feedback psmt-remote-media-upload-feedback">
                    <ul></ul>
                </div>
                <div class="psmt-remote-add-media-row psmt-remote-add-media-row-activity">
                    <input type="text" placeholder="<?php _e( 'Gib einen Link ein', 'psourcemediathek' );?>" value="" name="psmt-remote-media-url" id="psmt-remote-media-url" class="psmt-remote-media-url"/>
                    <button id="psmt-add-remote-media" class="psmt-add-remote-media"><?php _e( '+Hinzufügen', 'psourcemediathek' ); ?></button>
                </div>

			    <?php wp_nonce_field( 'psmt_add_media', 'psmt-remote-media-nonce' ); ?>
            </div>
            <!-- end of remote media -->
	    <?php endif;?>

    </div><!-- end of psourcemediathek form container -->
	<?php
}
add_action( 'bp_after_activity_post_form', 'psmt_activity_dropzone' );

/**
 * Format activity action for 'psmt_media_upload' activity type.
 *
 * @param string $action activity action.
 * @param BP_Activity_Activity $activity Activity object.
 *
 * @return string
 */
function psmt_format_activity_action_media_upload( $action, $activity ) {

	$userlink = psmt_get_user_link( $activity->user_id );

	$media_id = psmt_activity_get_media_id( $activity->id );

	$media_ids = psmt_activity_get_attached_media_ids( $activity->id );

	if ( ! empty( $media_ids ) ) {
		$media_id = $media_ids[0];
	}

	$gallery_id = psmt_activity_get_gallery_id( $activity->id );

	if ( ! $media_id && ! $gallery_id ) {
		return $action; // not a gallery activity, no need to proceed further.
	}

	$media   = psmt_get_media( $media_id );
	$gallery = psmt_get_gallery( $gallery_id );

	if ( ! $media && ! $gallery ) {
		return $action;
	}

	// is a type specified?
	$activity_type = psmt_activity_get_activity_type( $activity->id );

	$skip = false;

	if ( $activity_type ) {
		if ( in_array( $activity_type, array( 'edit_gallery', 'add_media' ) ) ) {
			// 'create_gallery',
			$skip = true;
		}
	}

	// there us still a chance for improvement,
	// we should dynamically generate the action instead for the above actions too.
	if ( $skip ) {
		return $action;
	}

	// on uploads activity, if it contains content, do not modify action.
	if (  'media_upload' === $activity_type && $activity->content ) {
		return $action;
	}

	if ( 'media_upload' === $activity_type ) {

		$media_count = count( $media_ids );
		$media_id    = current( $media_ids );

		$type = $gallery->type;

		/**
		 * @todo add better support for plural
		 */
		// we need the type plural in case of multi. nee to change in future.
		$type = _n( strtolower( psmt_get_type_singular_name( $type ) ), strtolower( psmt_get_type_plural_name( $type ) ), $media_count ); // photo vs photos etc.

		$action = sprintf( __( '%s hat %d %s neu hinzugefügt', 'psourcemediathek' ), $userlink, $media_count, $type );

		// allow modules to filter the action and change the message.
		$action = apply_filters( 'psmt_activity_action_media_upload', $action, $activity, $media_id, $media_ids, $gallery );
	} elseif ( 'media_comment' === $activity_type ) {

		if ( psmt_is_single_media() ) {
			$action = sprintf( __( '%s', 'psourcemediathek' ), $userlink );
		} else {
			$action = sprintf( __( "%s hat %s's %s kommentiert", 'psourcemediathek' ), $userlink, psmt_get_user_link( $media->user_id ), strtolower( psmt_get_type_singular_name( $media->type ) ) ); //brajesh singh commented on @mercime's photo
		}
	} elseif ( 'gallery_comment' === $activity_type ) {

		if ( psmt_is_single_gallery() ) {
			$action = sprintf( '%s', $userlink );
		} else {
			$action = sprintf( __( "%s kommentierte %s's <a href='%s'>%s gallery</a>", 'psourcemediathek' ), $userlink, psmt_get_user_link( $gallery->user_id ), psmt_get_gallery_permalink( $gallery ), strtolower( psmt_get_type_singular_name( $gallery->type ) ) );
		}
	} elseif ( 'create_gallery' === $activity_type ) {
		$action = sprintf( __( '%s hat eine %s <a href="%s">Galerie</a> erstellt', 'psourcemediathek' ), $userlink, strtolower( psmt_get_type_singular_name( $gallery->type ) ), psmt_get_gallery_permalink( $gallery ) );
	} else {
		$action = sprintf( '%s', $userlink );
	}

	return apply_filters( 'psmt_format_activity_action_media_upload', $action, $activity, $media_id, $media_ids );
}
