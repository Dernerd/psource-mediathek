<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery Link template tags
 */

/**
 * Get the base url for the component gallery home page
 * e.g http://site.com/members/user-name/gallery //without any trailing slash
 *
 * @param string $component component name.
 * @param int    $component_id component id.
 *
 * @return string
 *
 * @todo In future, avoid dependency on BuddyPress
 */
function psmt_get_gallery_base_url( $component, $component_id ) {

	$base_url = '';

	if ( 'members' === $component ) {
		$base_url = psmt_get_user_url( $component_id ) . PSMT_GALLERY_SLUG;
	} elseif ( 'groups' === $component && function_exists( 'bp_get_group_permalink' ) ) {
		$base_url = bp_get_group_permalink( new BP_Groups_Group( $component_id ) ) . PSMT_GALLERY_SLUG;
	}
	// for admin new/edit gallery, specially new gallery.
	if ( ! $base_url && ( empty( $component ) || empty( $component_id ) ) ) {
		$base_url = psmt_get_user_url( get_current_user_id() ) . PSMT_GALLERY_SLUG;
	}

	return apply_filters( 'psmt_get_gallery_base_url', trailingslashit( $base_url ), $component, $component_id );
}

/**
 * Display the permalink for the current gallery
 */
function psmt_gallery_permalink() {
	echo psmt_get_gallery_permalink();
}

/**
 *  Get the url/permalink of the current gallery in the loop or the given gallery
 *
 * @param int|PSMT_Gallery $gallery gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_permalink( $gallery = null ) {

	$gallery   = psmt_get_gallery( $gallery );
	$permalink = get_permalink( $gallery->id );

	return apply_filters( 'psmt_get_gallery_permalink', trailingslashit( $permalink ), $gallery );
}

/**
 * Action URLs
 */

/**
 * Print the  url of Galerie erstellen page for the given component, defaults to user
 *
 * @param string $component component name.
 * @param int    $component_id component id.
 */
function psmt_gallery_create_url( $component, $component_id ) {
	echo psmt_get_gallery_create_url( $component, $component_id );
}

/**
 * Get the url of the gallery creation page for the given component
 *
 * @param string $component component name.
 * @param int    $component_id component id.
 *
 * @return string
 */
function psmt_get_gallery_create_url( $component, $component_id ) {

	$link = psmt_get_gallery_base_url( $component, $component_id ) . 'create/?_wpnonce=' . wp_create_nonce( 'create-gallery' );

	return apply_filters( 'psmt_get_gallery_create_url', $link, $component );
}

/**
 * Default action for gallery management page
 *
 * It is used to decide what should be shown on the main page of management
 *
 * @param PSMT_Gallery $gallery Gallery object.
 *
 * @return string action name.
 */
function psmt_get_gallery_management_default_action( $gallery ) {
	return apply_filters( 'psmt_get_gallery_management_default_action', 'edit', $gallery );
}

/**
 * Print the url of the single gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_management_base_url( $gallery = null ) {
	echo psmt_get_gallery_management_base_url( $gallery );
}

/**
 * Get the url for gallery management page
 *
 * It is like http://site.com/xyz/single-gallery-permalink/manage/ [ single-gallary-permalink/manage/
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_management_base_url( $gallery = null ) {

	$gallery = psmt_get_gallery( $gallery );
	$link    = untrailingslashit( psmt_get_gallery_permalink( $gallery ) ) . '/manage/';

	$link = apply_filters( 'psmt_get_gallery_management_base_url', $link, $gallery );

	return $link;
}

/**
 * Print the url for gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_management_url( $gallery = null ) {
	echo psmt_get_gallery_management_url( $gallery );
}

/**
 * Get the url for gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 * @param string          $action action name.
 *
 * @return string
 */
function psmt_get_gallery_management_url( $gallery = null, $action = null ) {

	$gallery = psmt_get_gallery( $gallery );

	if ( ! $action ) {
		$action = psmt_get_gallery_management_default_action( $gallery );
	}

	$link = psmt_get_gallery_management_base_url( $gallery ) . $action . '/?_wpnonce=' . wp_create_nonce( $action ) . '&gallery_id=' . $gallery->id;

	$link = apply_filters( 'psmt_get_gallery_management_url', $link, $action, $gallery );

	return $link;
}

/**
 * Print the url of the add media  sub page for the gallery management screen
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_add_media_url( $gallery = null ) {
	echo psmt_get_gallery_add_media_url( $gallery );
}

/**
 * Get the url of the add media  sub page for the gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_add_media_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'add' );

	$link = apply_filters( 'psmt_get_gallery_add_media_url', $link, $gallery );

	return $link;
}

/**
 * Print the url of the media reorder sub page for the gallery management screen
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_edit_media_url( $gallery = null ) {
	echo psmt_get_gallery_edit_media_url( $gallery );
}

/**
 * Get the url of the media reorder sub page for the gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_edit_media_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'edit' ); // edit media.

	$link = apply_filters( 'psmt_get_gallery_edit_media_url', $link, $gallery );

	return $link;
}

/**
 * Print the url of the media reorder sub page for the gallery management screen
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_reorder_media_url( $gallery = null ) {
	echo psmt_get_gallery_reorder_media_url( $gallery );
}

/**
 * Get the url of the media reorder sub page for the gallery management page
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_reorder_media_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'reorder' );

	$link = apply_filters( 'psmt_get_gallery_reorder_media_url', $link, $gallery );

	return $link;
}

/**
 * Print gallery settings url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_settings_url( $gallery = null ) {
	echo psmt_get_gallery_settings_url( $gallery );
}

/**
 * Get gallery settings url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_settings_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'settings' );

	$link = apply_filters( 'psmt_get_gallery_settings_url', $link, $gallery );

	return $link;
}

/**
 * Print gallery delete url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_delete_url( $gallery = null ) {
	echo psmt_get_gallery_delete_url( $gallery );
}

/**
 * Get gallery delete url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_delete_url( $gallery = null ) {

	// should we have some option to ask for confirmation or not
	// let us implement 2 step delete for now.
	$link = psmt_get_gallery_management_url( $gallery, 'delete' );

	$link = apply_filters( 'psmt_get_gallery_delete_url', $link, $gallery );

	return $link;
}

/**
 * Cover Images
 */

/**
 * Print cover delete url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_cover_delete_url( $gallery = null ) {
	echo psmt_get_gallery_cover_delete_url( $gallery );
}

/**
 * Get cover delete url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_get_gallery_cover_delete_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'delete-cover' );

	$link = apply_filters( 'psmt_get_gallery_cover_delete_url', $link, $gallery );

	return $link;
}

/**
 * Print activity publish url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_publish_activity_url( $gallery = null ) {
	echo psmt_gallery_get_publish_activity_url( $gallery );
}

/**
 * Get gallery activity publish url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_gallery_get_publish_activity_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'publish' );

	$link = apply_filters( 'psmt_gallery_publish_activity_url', $link, $gallery );

	return $link;
}

/**
 * Print gallery unpublish url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 */
function psmt_gallery_unpublished_media_delete_url( $gallery = null ) {
	echo psmt_gallery_get_unpublished_media_delete_url( $gallery );
}

/**
 * Get gallery unpublish url.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 *
 * @return string
 */
function psmt_gallery_get_unpublished_media_delete_url( $gallery = null ) {

	$link = psmt_get_gallery_management_url( $gallery, 'delete-unpublished' );

	$link = apply_filters( 'psmt_gallery_unpublish_media_delete_url', $link, $gallery );

	return $link;
}

/**
 * Print publish to activity url for the given gallery
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 * @param string          $label label for the link.
 *
 */
function psmt_gallery_publish_activity_link( $gallery, $label = '' ) {
	echo psmt_gallery_get_publish_activity_link( $gallery, $label );
}

/**
 * Get the publish activity link for given gallery
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 * @param string          $label label for the link.
 *
 * @return string
 */
function psmt_gallery_get_publish_activity_link( $gallery, $label = '' ) {

	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) || ! psmt_gallery_has_unpublished_media( $gallery ) || ! psmt_user_can_publish_gallery_activity( $gallery ) ) {
		return '';
	}
	// this gallery has unpublished media and the user can publish the media to activity.
	if ( empty( $label ) ) {
		$label = _x( 'Veröffentlichen', ' Publish to activity button label', 'psourcemediathek' );
	}

	$title = __( 'In der Aktivität veröffentlichen', 'psourcemediathek' );

	$url = psmt_gallery_get_publish_activity_url( $gallery );

	return sprintf( "<a href='%s' title ='%s' class='button psmt-button psmt-action-button psmt-publish-to-activity-button'>%s</a>", $url, $title, $label );
}

/**
 * Print Unpublished media delete link.
 *
 * @param PSMT_Gallery|int $gallery Gallery id or object.
 * @param string          $label label for the link.
 */
function psmt_gallery_unpublished_media_delete_link( $gallery, $label = '' ) {
	echo psmt_gallery_get_unpublished_media_delete_link( $gallery, $label );
}

/**
 * Get unpublished media delete link.
 *
 * @param int|PSMT_Gallery $gallery gallery id or object.
 * @param string          $label label for the link.
 *
 * @return string
 */
function psmt_gallery_get_unpublished_media_delete_link( $gallery, $label = '' ) {

	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) || ! psmt_gallery_has_unpublished_media( $gallery ) || ! psmt_user_can_publish_gallery_activity( $gallery ) ) {
		return '';
	}
	// this gallery has unpublished media and the user can publish the media to activity.
	if ( empty( $label ) ) {
		$label = _x( 'Ausblenden', 'Clear unpublished media notification', 'psourcemediathek' );
	}

	$title = __( 'Unveröffentlichte Medienbenachrichtigung löschen', 'psourcemediathek' );

	$url = psmt_gallery_get_unpublished_media_delete_url( $gallery );

	return sprintf( "<a href='%s' title ='%s' class='button psmt-button psmt-action-button psmt-delete-unpublished-media-button'>%s</a>", $url, $title, $label );
}

/**
 * Print gallery create form action url.
 */
function psmt_gallery_create_form_action() {
	echo psmt_get_gallery_base_url( psmt_get_current_component(), psmt_get_current_component_id() ) . 'create/';
}

/**
 * Display a create Gallery Button
 *
 * @return string
 */
function psmt_gallery_create_button() {

	// check whether to display the link or not?
	$component    = psmt_get_current_component();
	$component_id = psmt_get_current_component_id();

	if ( ! psmt_user_can_create_gallery( $component, $component_id ) ) {
		return false;
	}

	?>
    <a id="add_new_gallery_link" href="<?php psmt_gallery_create_url( $component, $component_id ); ?>"><?php _e( 'Galerie hinzufügen', 'psourcemediathek' );?></a>
	<?php
}
