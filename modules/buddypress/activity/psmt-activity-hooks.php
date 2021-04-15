<?php
/**
 * Activity hooks.
 *
 * @package psourcemediathek
 */

// No direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Filter activity permalink and make it point to single media if the activity has an associated media
 *
 * @param string               $link activity link.
 * @param BP_Activity_Activity $activity activity.
 *
 * @return string
 */
function psmt_filter_activity_permalink( $link, $activity ) {

	$activity_id = $activity->id;
	// get parent activity id.
	if ( 'activity_comment' === $activity->type ) {
		$activity_id = $activity->item_id;
		$activity = new BP_Activity_Activity( $activity_id );
	}

	if ( 'psmt_media_upload' !== $activity->type ) {
		return $link;
	}

	$context = psmt_activity_get_context( $activity_id );
	$gallery_id = psmt_activity_get_gallery_id( $activity->id );

	if ( ! $gallery_id ) {
		return $link;
	}

	// make sure that gallery exists.
	$gallery = psmt_get_gallery( $gallery_id );

	if ( ! $gallery ) {
		return $link;
	}

	// if we are here, It is a gallery/media activity.
	$media_id = psmt_activity_get_media_id( $activity_id );

	if ( $media_id && $media = psmt_get_media( $media_id ) ) {
		$link = psmt_get_media_permalink( $media ) . "#activity-{$activity_id}";
	} else {
		$link = psmt_get_gallery_permalink( $gallery ) . "#activity-{$activity_id}";
	}

	return $link;
}
add_filter( 'bp_activity_get_permalink', 'psmt_filter_activity_permalink', 10, 2 );

/**
 * Show the list of attached media in an activity
 * Should we add a link to view gallery too?
 *
 * @return void
 */
function psmt_activity_inject_attached_media_html() {

	$media_ids = psmt_activity_get_attached_media_ids( bp_get_activity_id() );

	if ( empty( $media_ids ) ) {
		return;
	}

	$activity_id = bp_get_activity_id();

	$gallery_id = psmt_activity_get_gallery_id( $activity_id );

	$gallery = psmt_get_gallery( $gallery_id );

	if ( ! $gallery ) {
		return;
	}

	$type = $gallery->type;

	$view = psmt_get_activity_view( $type, $activity_id );

	$view->activity_display( $media_ids );
}
add_action( 'bp_activity_entry_content', 'psmt_activity_inject_attached_media_html' );

/**
 * Inject media in activity replies.
 */
function psmt_activity_inject_media_in_comment_replies() {

	$activity_id = bp_get_activity_id();

	if ( bp_get_activity_type() != 'activity_comment' ) {
		//return;
	}

	$media_id = psmt_activity_get_media_id( $activity_id );

	if ( empty( $media_id ) ) {
		return;
	}

	$media = psmt_get_media( $media_id );

	if ( ! $media ) {
		return;
	}

	$slug = $media->type;

	// media-loop-audio/media-loop-video,media-loop-photo, media-loop.
	$templates = array(
		"buddypress/activity/entry-comment-{$slug}.php",
		'buddypress/activity/entry-comment.php',
	);

	$template = psmt_locate_template( $templates, false );
	if ( $template ) {
		include $template;
	}
	//psmt_get_template_part( 'buddypress/activity/entry-comment', $slug );
}
add_action( 'bp_activity_entry_content', 'psmt_activity_inject_media_in_comment_replies' );

/**
 * Filter on the Context Gallery creation step to allow creating activity gallery
 *
 * @param PSMT_Gallery $gallery gallery object.
 * @param array       $args array of params.
 *
 * @return bool|PSMT_Gallery|null
 */
function psmt_get_activity_wall_gallery( $gallery, $args ) {

	if ( ! isset( $args['context'] ) || 'activity' !== $args['context'] ) {
		return $gallery;
	}

	// is activity upload enabled for this component[members/groups]?
	if ( ! psmt_is_activity_upload_enabled( $args['component'] ) ) {
		return false;
	}

	// check if a gallery exists for the combination.
	$gallery_id = psmt_get_wall_gallery_id( array(
		'component'		=> $args['component'],
		'component_id'	=> $args['component_id'],
		'media_type'	=> $args['type'],
	) );


	if ( ! $gallery_id || ! psmt_get_gallery( $gallery_id ) ) {
		// if gallery does not exist, create it
		// 1.  let us make sure that the wall gallery creation activity is never recorded
		// do not record gallery activity.
		add_filter( 'psmt_do_not_record_create_gallery_activity', '__return_true' );

		$title = sprintf( _x( 'Wand %s Galerie', 'wall gallery name', 'psourcemediathek' ), $args['type'] );

		// Allow developers to have flexible naming for the wall gallery.
		$title = apply_filters( 'psmt_wall_gallery_title', $title, $args );

		$gallery_id = psmt_create_gallery( array(
			'creator_id'	=> $args['user_id'],
			'title'			=> $title,
			'description'	=> '',
			'status'		=> 'public',
			'component'		=> $args['component'],
			'component_id'	=> $args['component_id'],
			'type'			=> $args['type'],
		) );

		// remove the filter we added.
		remove_filter( 'psmt_do_not_record_create_gallery_activity', '__return_true' );

		if ( $gallery_id ) {
			// save the wall gallery id.
			psmt_update_wall_gallery_id( array(
				'component'		=> $args['component'],
				'component_id'	=> $args['component_id'],
				'media_type'	=> $args['type'],
				'gallery_id'	=> $gallery_id,
			) );
		}
	}

	if ( $gallery_id ) {
		$gallery = psmt_get_gallery( $gallery_id );
	}

	return $gallery;

}
add_filter( 'psmt_get_context_gallery', 'psmt_get_activity_wall_gallery', 10, 2 );

/**
 * Disable the action filtering by BP Nouveau template pack.
 * Nouveau has a bug and causes incorrect markup to be shown if the action contains secondary avatars.
 */
function psmt_disable_bp_nouveau_filter() {
	if ( function_exists( 'bp_nouveau_activity_secondary_avatars' ) ) {
		remove_filter( 'bp_get_activity_action_pre_meta', 'bp_nouveau_activity_secondary_avatars', 10 );
	}

}

add_action( 'bp_nouveau_includes', 'psmt_disable_bp_nouveau_filter', 50 );
