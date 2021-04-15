<?php
/**
 * Handling PsourceMediathek Core setup
 *
 * @package PsourceMediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Sets up PsourceMediathek core
 * Initializes various core settings/modules like
 *
 * Registers various core features
 * Registers statuses
 * Registers types(media type)
 * Also registers Component types
 * Uploaders, MediaSizes etc
 */
function psmt_setup_core() {

	// if the 'gallery' slug is not set , set it to psourcemediathek?
	if ( ! defined( 'PSMT_GALLERY_SLUG' ) ) {
		define( 'PSMT_GALLERY_SLUG', 'psourcemediathek' );
	}
	// Register privacies(status).
	// Public status.
	psmt_register_status( array(
		'key'              => 'public',
		'label'            => __( 'Öffentlich', 'psourcemediathek' ),
		'labels'           => array(
			'singular_name' => __( 'Öffentlich', 'psourcemediathek' ),
			'plural_name'   => __( 'Öffentliche', 'psourcemediathek' ),
		),
		'description'      => __( 'Öffentliche Galerie Datenschutzart', 'psourcemediathek' ),
		'callback'         => 'psmt_check_public_access',
		'activity_privacy' => 'public',
	) );

	// Private status.
	psmt_register_status( array(
		'key'              => 'private',
		'label'            => __( 'Privat', 'psourcemediathek' ),
		'labels'           => array(
			'singular_name' => __( 'Privat', 'psourcemediathek' ),
			'plural_name'   => __( 'Private', 'psourcemediathek' ),
		),
		'description'      => __( 'Private Galerie Datenschutzart', 'psourcemediathek' ),
		'callback'         => 'psmt_check_private_access',
		'activity_privacy' => 'onlyme',
	) );

	// Loggedin members only status.
	psmt_register_status( array(
		'key'              => 'loggedin',
		'label'            => __( 'Nur angemeldete Benutzer', 'psourcemediathek' ),
		'labels'           => array(
			'singular_name' => __( 'Nur angemeldeter Benutzer', 'psourcemediathek' ),
			'plural_name'   => __( 'Nur angemeldete Benutzer', 'psourcemediathek' ),
		),
		'description'      => __( 'Nur Angemeldeter Benutzer Datenschutztyp', 'psourcemediathek' ),
		'callback'         => 'psmt_check_loggedin_access',
		'activity_privacy' => 'loggedin',
	) );

	/**
	 * For BuddyPress specific status, please check modules/buddypress/loader.php.
	 */

	// Register Components.
	// Register sitewide gallery component.
	psmt_register_component( array(
		'key'         => 'sitewide',
		'label'       => __( 'Webseitenweite Galerien', 'psourcemediathek' ),
		'labels'      => array(
			'singular_name' => __( 'Webseitenweite Galerie', 'psourcemediathek' ),
			'plural_name'   => __( 'Webseitenweite Galerien', 'psourcemediathek' ),
		),
		'description' => __( 'Webseitenweite Galerien', 'psourcemediathek' ),
	) );

	// Register media/gallery types.
	// Photo.
	psmt_register_type( array(
		'key'         => 'photo',
		'label'       => __( 'Bild', 'psourcemediathek' ),
		'description' => __( 'taxonomy for image media type', 'psourcemediathek' ),
		'labels'      => array(
			'singular_name' => __( 'Bild', 'psourcemediathek' ),
			'plural_name'   => __( 'Bilder', 'psourcemediathek' ),
		),
		'extensions'  => array( 'jpeg', 'jpg', 'gif', 'png' ),
	) );

	// video.
	psmt_register_type( array(
		'key'         => 'video',
		'label'       => __( 'Video', 'psourcemediathek' ),
		'labels'      => array(
			'singular_name' => __( 'Video', 'psourcemediathek' ),
			'plural_name'   => __( 'Videos', 'psourcemediathek' ),
		),
		'description' => __( 'Taxonomie für Videomedien', 'psourcemediathek' ),
		'extensions'  => array( 'mp4', 'flv', 'mpeg' ),
	) );

	// audio.
	psmt_register_type( array(
		'key'         => 'audio',
		'label'       => __( 'Audio', 'psourcemediathek' ),
		'labels'      => array(
			'singular_name' => __( 'Audio', 'psourcemediathek' ),
			'plural_name'   => __( 'Audios', 'psourcemediathek' ),
		),
		'description' => __( 'Taxonomie für Audio Media', 'psourcemediathek' ),
		'extensions'  => array( 'mp3', 'wmv', 'midi' ),
	) );

	// doc.
	psmt_register_type( array(
		'key'         => 'doc',
		'label'       => __( 'Dateien', 'psourcemediathek' ),
		'labels'      => array(
			'singular_name' => __( 'Datei', 'psourcemediathek' ),
			'plural_name'   => __( 'Dateien', 'psourcemediathek' ),
		),
		'description' => __( 'Dies ist eine Dateigalerie', 'psourcemediathek' ),
		'extensions'  => array( 'zip', 'gz', 'doc', 'pdf', 'docx', 'xls' ),
	) );

	$size_thumb = psmt_get_option('size_thumbnail', array(
		'width'     => 200,
		'height'    => 200,
		'crop'      => 1,
	) );

	// Register media sizes.
	psmt_register_media_size( array(
		'name'   => 'thumbnail',
		'label'  => _x( 'Vorschaubild', 'Vorschaubild Dateiname', 'psourcemediathek' ),
		'height' => $size_thumb['height'],
		'width'  => $size_thumb['width'],
		'crop'   => isset( $size_thumb['crop'] ) ? $size_thumb['crop'] : 0,
		'type'   => 'default',
	) );

	$size_mid = psmt_get_option('size_mid', array(
		'width'     => 350,
		'height'    => 350,
		'crop'      => 1,
	) );
	psmt_register_media_size( array(
		'name'   => 'mid',
		'label'  => _x( 'Mittel', 'Nittlere Größe Name', 'psourcemediathek' ),
		'height' => $size_mid['height'],
		'width'  => $size_mid['width'],
		'crop'   => isset( $size_mid['crop'] ) ? $size_mid['crop'] : 0,
		'type'   => 'default',
	) );

	$size_large = psmt_get_option('size_large', array(
		'width'     => 600,
		'height'    => 600,
		'crop'      => 0,
	) );

	psmt_register_media_size( array(
		'name'   => 'large',
		'label'  => _x( 'Groß', 'Große Größe Name', 'psourcemediathek' ),
		'height' => $size_large['height'],
		'width'  => $size_large['width'],
		'crop'   => isset( $size_large['crop'] ) ? $size_large['crop'] : 0,
		'type'   => 'default',
	) );

	// Register status support for components.
	// Sitewide gallery supports 'public', 'private', 'loggedin'.
	psmt_component_add_status_support( 'sitewide', 'public' );
	psmt_component_add_status_support( 'sitewide', 'private' );
	psmt_component_add_status_support( 'sitewide', 'loggedin' );

	// Register type support for sitewide gallery.
	psmt_component_init_type_support( 'sitewide' );

	// Register storage managers here
	// Register 'local' storage manager.
	psmt_register_storage_manager( 'local', PSMT_Local_Storage::get_instance() );

	// Register default gallery viewer.
	$default_view = PSMT_Gallery_View_Default::get_instance();

	// All gallery types support default viewer.
	psmt_register_gallery_view( 'photo', $default_view );
	psmt_register_gallery_view( 'video', $default_view );
	psmt_register_gallery_view( 'audio', $default_view );
	psmt_register_gallery_view( 'doc', $default_view );

	$list_view = PSMT_Gallery_View_List::get_instance();
	// All Gallery types support list view.
	psmt_register_gallery_view( 'photo', $list_view );
	psmt_register_gallery_view( 'video', $list_view );
	psmt_register_gallery_view( 'audio', $list_view );
	psmt_register_gallery_view( 'doc', $list_view );

	// Video playlist view is only supported by video type.
	psmt_register_gallery_view( 'video', PSMT_Gallery_View_Video_Playlist::get_instance() );
	// Audio playlist view is only supported by Audio type.
	psmt_register_gallery_view( 'audio', PSMT_Gallery_View_Audio_Playlist::get_instance() );

	// Media viewer support.
	psmt_register_media_view( 'photo', 'default', new PSMT_Media_View_Photo() );
	psmt_register_media_view( 'doc', 'default', new PSMT_Media_View_Docs() );

	// we are registering for video so we can replace it in future for flexible video views.
	psmt_register_media_view( 'video', 'default', new PSMT_Media_View_Video() );

	// audio view.
	psmt_register_media_view( 'audio', 'default', new PSMT_Media_View_Audio() );

	// should we register a photo viewer too? may be for the sake of simplicity?

	// setup the tabs(menu).
	psourcemediathek()->add_menu( 'gallery', new PSMT_Gallery_Menu() );
	psourcemediathek()->add_menu( 'media', new PSMT_Media_Menu() );

	do_action( 'psmt_setup_core' );
}

// initialize PsourceMediathek core.
add_action( 'psmt_setup', 'psmt_setup_core' );

/**
 * Setup gallery menu
 */
function psmt_setup_gallery_nav() {

	// only add on single gallery.
	if ( ! psmt_is_single_gallery() ) {
		return;
	}

	$gallery = psmt_get_current_gallery();

	$url = '';

	if ( $gallery ) {
		$url = psmt_get_gallery_permalink( $gallery );
	}

	// only add view/edit/dele links on the single mgallery view.
	psmt_add_gallery_nav_item( array(
		'label'  => __( 'Ansehen', 'psourcemediathek' ),
		'url'    => $url,
		'action' => 'view',
		'slug'   => 'view',
	) );

	$user_id = get_current_user_id();

	if ( psmt_user_can_edit_gallery( $gallery->id, $user_id ) ) {

		psmt_add_gallery_nav_item( array(
			'label'  => __( 'Medien bearbeiten', 'psourcemediathek' ), // we can change it to media type later.
			'url'    => psmt_get_gallery_edit_media_url( $gallery ),
			'action' => 'edit',
			'slug'   => 'edit',
		) );
	}

	if ( psmt_user_can_upload( $gallery->component, $gallery->component_id, $gallery ) ) {

		psmt_add_gallery_nav_item( array(
			'label'  => __( 'Medien hinzufügen', 'psourcemediathek' ), // we can change it to media type later.
			'url'    => psmt_get_gallery_add_media_url( $gallery ),
			'action' => 'add',
			'slug'   => 'add',
		) );
	}

	if ( psmt_user_can_edit_gallery( $gallery->id, $user_id ) ) {

		psmt_add_gallery_nav_item( array(
			'label'  => __( 'Umordnen', 'psourcemediathek' ), // we can change it to media type later.
			'url'    => psmt_get_gallery_reorder_media_url( $gallery ),
			'action' => 'reorder',
			'slug'   => 'reorder',
		) );

		psmt_add_gallery_nav_item( array(
			'label'  => __( 'Details bearbeiten', 'psourcemediathek' ),
			'url'    => psmt_get_gallery_settings_url( $gallery ),
			'action' => 'settings',
			'slug'   => 'settings',
		) );
	}

	if ( psmt_user_can_delete_gallery( $gallery->id ) ) {

		psmt_add_gallery_nav_item( array(
			'label'  => __( 'Löschen', 'psourcemediathek' ),
			'url'    => psmt_get_gallery_delete_url( $gallery ),
			'action' => 'delete',
			'slug'   => 'delete',
		) );
	}

}
add_action( 'psmt_setup_globals', 'psmt_setup_gallery_nav' );
