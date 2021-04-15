<?php
/**
 * Hooks.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Filter current component for the sitewide gallery pages.
 *
 * @param string $component component name(e.g. members|groups|stewide).
 *
 * @return string
 */
function psmt_filter_current_component_for_sitewide( $component ) {

	if ( ! psourcemediathek()->is_bp_active() ) {
		return $component;
	}

	if ( psmt_admin_is_add_gallery() || psmt_admin_is_edit_gallery() ) {
		global $post;

		$gallery = psmt_get_gallery( $post );

		if ( $gallery && $gallery->component ) {
			$component = $gallery->component;
		} else {
			$component = 'sitewide';
		}
	}

	return $component;
}
add_filter( 'psmt_get_current_component', 'psmt_filter_current_component_for_sitewide' );

/**
 * Do now allow reserved slugs in the attachment slug.
 *
 * @param bool   $is_bad indicates if it is bad.
 * @param string $slug current slug.
 *
 * @return bool
 */
function psmt_filter_attachment_slug( $is_bad, $slug ) {
	return psmt_is_reserved_slug( $slug );
}
add_filter( 'wp_unique_post_slug_is_bad_attachment_slug', 'psmt_filter_attachment_slug', 10, 2 );

/**
 * Filter slugs for Gallery
 *
 * @param bool   $is_bad indicates if the slug is bad.
 * @param string $slug slug.
 * @param string $post_type post type name.
 *
 * @return boolean
 */
function psmt_filter_reserved_gallery_slug( $is_bad, $slug, $post_type ) {

	if ( psmt_get_gallery_post_type() == $post_type ) {
		$is_bad = psmt_is_reserved_slug( $slug );
	}

	return $is_bad;
}
add_filter( 'wp_unique_post_slug_is_bad_flat_slug', 'psmt_filter_reserved_gallery_slug', 10, 3 );

/**
 * If BuddyPress is active and directory is enabled,
 *  redirect archive page to BuddyPress Gallery Directory.
 */
function psmt_gallery_archive_redirect() {

	if ( is_post_type_archive( psmt_get_gallery_post_type() ) && psourcemediathek()->is_bp_active() && psmt_get_option( 'has_gallery_directory' ) && isset( buddypress()->pages->psourcemediathek->id ) ) {
		wp_safe_redirect( get_permalink( buddypress()->pages->psourcemediathek->id ), 301 );
		exit( 0 );
	}
}
add_action( 'psmt_actions', 'psmt_gallery_archive_redirect', 11 );

/**
 * Filter which galleries can be listed on archive page.
 *
 * Only list public galleries on the archive page if user is not logged in.
 * We do include "Logged In" galleries if the user is logged in.
 *
 * @param PSMT_Gallery_Query $query gallery query.
 */
function psmt_filter_archive_page_galleries( $query ) {

	if ( is_admin() ) {
		return;
	}

	if ( ! $query->is_main_query() || ! $query->is_post_type_archive( psmt_get_gallery_post_type() ) ) {
		return;
	}

	// confirmed that we are on gallery archive page.
	$active_components = psmt_get_active_components();
	$active_types      = psmt_get_active_types();

	$status = array();
	if ( psmt_is_active_status( 'public' ) ) {
		$status[] = 'public';
	}

	if ( is_user_logged_in() && psmt_is_active_status( 'loggedin' ) ) {
		$status[] = 'loggedin';
	}


	$tax_query = $query->get( 'tax_query' );

	if ( empty( $tax_query ) ) {
		$tax_query = array();
	}
	// it will be always true.
	if ( $status ) {
		$status = psmt_string_to_array( $status ); // future proofing.

		$status_keys = array_map( 'psmt_underscore_it', $status );

		$tax_query[] = array(
			'taxonomy' => psmt_get_status_taxname(),
			'field'    => 'slug',
			'terms'    => $status_keys,
			'operator' => 'IN',
		);
	}
	// should we only show sitewide galleries here? will update based on feedback.
	if ( ! empty( $active_components ) ) {
		$component_keys = array_keys( $active_components );
		$component_keys = array_map( 'psmt_underscore_it', $component_keys );

		$tax_query[] = array(
			'taxonomy' => psmt_get_component_taxname(),
			'field'    => 'slug',
			'terms'    => $component_keys,
			'operator' => 'IN',
		);
	}

	if ( ! empty( $active_types ) ) {
		$type_keys = array_keys( $active_types );
		$type_keys = array_map( 'psmt_underscore_it', $type_keys );

		$tax_query[] = array(
			'taxonomy' => psmt_get_type_taxname(),
			'field'    => 'slug',
			'terms'    => $type_keys,
			'operator' => 'IN',
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query->set( 'tax_query', $tax_query );
	$query->set( 'update_post_term_cache', true );
	$query->set( 'update_post_meta_cache', true );
	$query->set( 'cache_results', true );
}

add_action( 'pre_get_posts', 'psmt_filter_archive_page_galleries' );

/**
 * Filter on user_has_cap to assign every logged in person read cap
 *
 * @param array $allcaps all wp caps.
 * @param array $cap current cap details.
 * @param array $args na/a.
 *
 * @return mixed
 */
function psmt_assign_user_read_cap( $allcaps, $cap, $args ) {

	if ( $args[0] == 'read' && is_user_logged_in() ) {
		$allcaps[ $cap[0] ] = true;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'psmt_assign_user_read_cap', 0, 3 );

/**
 * Hooks applied which are not specific to any gallery component and applies to all
 *
 * @param string $complete_title page title.
 * @param string $title title.
 * @param string $sep title component separator.
 * @param string $seplocation where to use separator.
 *
 * @return string
 */
function psmt_modify_page_title( $complete_title, $title, $sep, $seplocation ) {

	$sub_title = array();

	if ( ! psmt_is_component_gallery() && ! psmt_is_gallery_component() ) {
		return $complete_title;
	}

	if ( psmt_is_single_gallery() ) {
		$sub_title[] = get_the_title( psmt_get_current_gallery_id() );
	}

	if ( psmt_is_single_media() ) {
		$sub_title[] = get_the_title( psmt_get_current_media_id() );
	}

	if ( psmt_is_gallery_management() || psmt_is_media_management() ) {
		$sub_title[] = ucwords( psourcemediathek()->get_action() );
		$sub_title[] = ucwords( psourcemediathek()->get_edit_action() );
	}

	$sub_title = array_filter( $sub_title );

	if ( ! empty( $sub_title ) ) {
		$sub_title      = array_reverse( $sub_title );
		$complete_title = join( ' | ', $sub_title ) . ' | ' . $complete_title;
	}

	return $complete_title;
}
add_filter( 'bp_modify_page_title', 'psmt_modify_page_title', 20, 4 );

/**
 * Filter body class
 *
 * @param array  $classes class classes.
 * @param string $class class name.
 *
 * @return array
 */
function psmt_filter_body_class( $classes, $class ) {

	$new_classes = array();

	$component = psmt_get_current_component();
	// if not psourcemediathek pages, return.
	if ( ! psmt_is_gallery_component() && ! psmt_is_component_gallery() ) {
		return $classes;
	}

	// ok, It must be psmt pages.
	// for all psourcemediathek pages
	$new_classes[] = 'psmt-page'; //
	// if it is a directory page.
	if ( psmt_is_gallery_directory() ) {
		$new_classes[] = 'psmt-page-directory';
	} elseif ( psmt_is_gallery_component() || psmt_is_component_gallery() ) {
		// we are on user gallery  page or a component gallery page
		// append class psmt-page-members or psmt-page-groups or psmt-page-events etc
		// depending on the current associated component.
		$new_classes[] = 'psmt-page-' . $component;

		if ( psmt_is_media_management() ) {
			// is it edit media?
			$new_classes[] = 'psmt-page-media-management';
			// psmt-photo-management, psmt-audio-management.
			$new_classes[] = 'psmt-page-media-management-' . psmt_get_media_type();
			// psmt-photo-management, psmt-audio-management.
			$new_classes[] = 'psmt-page-media-manage-action-' . psourcemediathek()->get_edit_action();
		} elseif ( psmt_is_single_media() ) {
			// is it single media.
			$new_classes[] = 'psmt-page-media-single';
			$new_classes[] = 'psmt-page-media-single-' . psmt_get_media_type();
		} elseif ( psmt_is_gallery_management() ) {
			// id gallery management?
			$new_classes[] = 'psmt-page-gallery-management';
			$new_classes[] = 'psmt-page-gallery-management-' . psmt_get_gallery_type();

			$new_classes[] = 'psmt-page-gallery-manage-action-' . psourcemediathek()->get_edit_action();
		} elseif ( psmt_is_single_gallery() ) {
			// is single gallery.
			$new_classes[] = 'psmt-page-single-gallery';
			$new_classes[] = 'psmt-page-single-gallery-' . psmt_get_gallery_type();
			$new_classes[] = 'psmt-page-single-gallery-' . psmt_get_gallery_status();
		} else {
			// it is the gallery listing page of the component.
			// home could have been a better name.
			$new_classes[] = 'psmt-page-gallery-list';
			// home could have been a better name.
			$new_classes[] = 'psmt-page-gallery-list-' . $component;
		}
	}

	if ( ! empty( $new_classes ) ) {
		$classes = array_merge( $classes, $new_classes );
	}

	return $classes;
}

add_filter( 'body_class', 'psmt_filter_body_class', 12, 2 );

/**
 * Filter comment open/close status
 *
 * If BuddyPress is active, the WordPress comments on gallery/attachment is always disabled and we use the BuddyPress activity instead
 *
 * @param string $open is it open.
 * @param int    $post_id post id.
 *
 * @return int
 */
function psmt_filter_comment_settings( $open, $post_id ) {

	$is_bp = 0;
	// if BuddyPress is active.
	if ( psourcemediathek()->is_bp_active() ) {
		$is_bp = 1;
	}

	if ( psmt_is_valid_gallery( $post_id ) ) {

		if ( ! psmt_get_option( 'enable_gallery_comment' ) || $is_bp ) {
			$open = 0;
		}
	} elseif ( psmt_is_valid_media( $post_id ) ) {
		if ( ! psmt_get_option( 'enable_media_comment' ) || $is_bp ) {
			$open = 0;
		}
	}


	return $open;
}
add_filter( 'comments_open', 'psmt_filter_comment_settings', 101, 2 );
