<?php
/**
 * PsourceMediathek Theme compat. Theme compat for Directory pages.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle the display of the psourcemediathek directory index.
 */
function psmt_gallery_screen_directory() {

	if ( psmt_is_gallery_directory() ) {

		bp_update_is_directory( true, 'psourcemediathek' );

		do_action( 'psmt_gallery_screen_directory' );

		bp_core_load_template( apply_filters( 'psmt_gallery_screen_directory', 'psourcemediathek/directory/index-full' ) );
	}
}

add_action( 'psmt_screens', 'psmt_gallery_screen_directory', 1 );

/**
 * This class sets up the necessary theme compatability actions to safely output
 * registration template parts to the_title and the_content areas of a theme.
 */
class PSMT_Directory_Theme_Compat {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bp_setup_theme_compat', array( $this, 'is_directory' ) );
	}

	/**
	 * Are we looking at Gallery or Media Directories?
	 */
	public function is_directory() {

		// Bail if not looking at the registration or activation page.
		if ( ! psmt_is_gallery_directory() ) {
			return;
		}

		bp_set_theme_compat_active( true );

		buddypress()->theme_compat->use_with_current_theme = true;
		// Not a directory.
		bp_update_is_directory( true, 'psourcemediathek' );

		// Setup actions.
		add_filter( 'bp_get_buddypress_template', array( $this, 'template_hierarchy' ) );
		add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'dummy_post' ) );
		add_filter( 'bp_replace_the_content', array( $this, 'directory_content' ) );

	}

	/**
	 * Add our own hierarchy.
	 *
	 * @param array $templates array of templates.
	 *
	 * @return array
	 */
	public function template_hierarchy( $templates = array() ) {

		// Setup our templates based on priority.
		$new_templates = apply_filters( 'psmt_template_hierarchy_directory', array(
			'psourcemediathek/directory/index-full.php',
		) );

		// Merge new templates with existing stack
		// @see bp_get_theme_compat_templates().
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with dummy data
	 */
	public function dummy_post() {
		// Directory page.
		if ( psmt_is_gallery_directory() ) {
			$title = __( 'Gallery Directory', 'psourcemediathek' );
		}

		bp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => bp_get_directory_title( 'psourcemediathek' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => psmt_get_gallery_post_type(),
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed',
		) );
	}

	/**
	 * Filter the_content with psourcemediathek directory page.
	 */
	public function directory_content() {

		if ( psmt_is_gallery_component() ) {
			$template = function_exists( 'bp_nouveau' ) ? 'directory/nouveau' : 'directory/index';

			return bp_buffer_template_part( 'psourcemediathek/default/buddypress/' . $template, null, false );
		}

	}
}

new PSMT_Directory_Theme_Compat();

/**
 * Add our path to bp template stack.
 *
 * @param array $templates templates array.
 *
 * @return array
 */
function psmt_add_bp_template_stack( $templates ) {
	// if we're on a page of our plugin and the theme is not BP Default, then we
	// add our path to the template path array.
	if ( psmt_is_gallery_component() ) {
		$templates[] = psourcemediathek()->get_path() . 'templates/';
	}

	return $templates;
}
add_filter( 'bp_get_template_stack', 'psmt_add_bp_template_stack', 10, 1 );


/**
 * Load the Page template for PsourceMediathek Single Sitewide Gallery
 * Looks for psourcemediathek/default/single-gallery-$type-$status.php
 *             psourcemediathek/default/single-gallery-$type.php
 *             psourcemediathek/default/single-gallery.php
 *             single-psmt-gallery.php
 *             singular.php
 *             index.php
 * in the child theme, then parent theme and finally falls back to check in wp-content/psourcemediathek/template/psourcemediathek/default
 * We don't provide any default copy for this as we are not going to mess with the page layout. Still, a theme developer has the choice to do it their own way
 *
 * Look at template_include hook and
 *
 * @see get_single_template()
 * @see get_query_template()
 *
 * @param string $template absolute path to the template file.
 *
 * @return string absolute path to the template file
 */
function psmt_filter_single_template_for_sitewide_gallery( $template ) {
	// our sitewide gallery is not enabled
	// or we are not on single sitewide gallery no need to bother.
	if ( ! psmt_is_active_component( 'sitewide' ) || ! psmt_is_sitewide_gallery_component() ) {
		return $template;
	}

	// modify it to use the current default template.
	$default_template = 'psourcemediathek/default/sitewide/home.php';
	// load our template
	// should we load separate template for edit actions?
	$gallery = psmt_get_current_gallery();
	$media   = psmt_get_current_media();

	$templates = array( $default_template );
	/*
	 if ( $media ) {
		$type = $media->type;
		$status = $media->status;
		$slug =  'single-media';
		//this is single media page
	} elseif( $gallery ) {
		//single gallery page
		$slug = 'single-gallery';
		$type = $gallery->type;
		$status = $gallery->status;
	}
	// look inside theme's psourcemediathek/ directory.
	$templates = $default_template . $slug . '-' . $type . '-' . $status . '.php';//single-gallery-photo-public.php/single-media-photo-public.php 
	$templates = $default_template . $slug . '-' . $type . '.php'; //single-gallery-photo.php/single-media-photo.php 
	$templates = $default_template . $slug . '.php'; //single-gallery.php/single-media.php 
	*/

	// we need to locate the template
	// and if the template is not present in the theme, we need to setup theme compat.
	$located = locate_template( $templates );

	if ( $located ) {
		// psourcemediathek()->set_theme_compat( false );
		$template = $located;
	} else {
		// if not found, setup theme compat.
		psmt_setup_sitewide_gallery_theme_compat();
	}

	return $template;
}
add_filter( 'single_template', 'psmt_filter_single_template_for_sitewide_gallery' );

/**
 * Sitewide gallery theme compat.
 */
function psmt_setup_sitewide_gallery_theme_compat() {

	add_action( 'loop_start', 'psmt_check_sitewide_gallery_main_loop' );
	// filter 'the_content' to show the gallery thing.
	add_filter( 'the_content', 'psmt_replace_the_content' );
}

/**
 * Replace the content of post with gallery
 *
 * @staticvar boolean $_psmt_filter_applied
 *
 * @param string $content page content.
 *
 * @return string
 */
function psmt_replace_the_content( $content = '' ) {

	static $_psmt_filter_applied;
	// Bail if not the main loop where theme compat is happening.
	if ( ! psourcemediathek()->is_using_theme_compat() || isset( $_psmt_filter_applied ) ) {
		return $content;
	}
	$_psmt_filter_applied = true;

	$new_content = apply_filters( 'psmt_replace_the_content', $content );

	// Juggle the content around.
	if ( ! empty( $new_content ) && ( $new_content !== $content ) ) {

		// Set the content to be the new content.
		$content = $new_content;

		// Clean up after ourselves.
		unset( $new_content );

		// Reset the $post global
		//wp_reset_postdata();
	}

	psourcemediathek()->set_theme_compat( false );

	return $content;
}

/**
 * Hook to replace content.
 *
 * @param WP_Query $query Query.
 */
function psmt_check_sitewide_gallery_main_loop( $query ) {

	if ( $query->is_main_query() ) {
		psourcemediathek()->set_theme_compat( true );
	} else {
		psourcemediathek()->set_theme_compat( false );
	}
}
add_filter( 'psmt_replace_the_content', 'psmt_sitewide_gallery_theme_compat_content' );

/**
 * Generated content for sitewide gallery theme compat.
 *
 * @return string
 */
function psmt_sitewide_gallery_theme_compat_content() {

	ob_start();

	psmt_get_component_template_loader( 'sitewide' )->load_template();

	$content = ob_get_clean();

	return $content;
}
