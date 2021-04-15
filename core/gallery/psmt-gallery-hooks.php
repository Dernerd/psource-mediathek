<?php

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter get_permalink for psourcemediathek gallery post type( psmt-gallery)
 * and make it like site.com/members/username/psourcemediathekslug/gallery-name or site.com/{component-page}/{single-component}/psourcemediathek-slug/gallery-name
 * It allows us to get the permalink to gallery by using the_permalink/get_permalink functions
 */
function psmt_filter_gallery_permalink( $permalink, $post, $leavename, $sample ) {

	// check if BuddyPress is active, if not, we don't filter it yet
	// lightweight check.
	if ( ! psourcemediathek()->is_bp_active() ) {
		return $permalink;
	}
	// a little more expensive.
	if ( psmt_get_gallery_post_type() !== $post->post_type ) {
		return $permalink;
	}

	// this is expensive if the post is not cached
	// If you see too many queries, just make sure to call _prime_post_caches($ids, true, true ); where $ids is collection of post ids
	// that will save a lot of query.
	$gallery = psmt_get_gallery( $post );

	// do not modify permalinks for Sitewide gallery.
	if ( 'sitewide' === $gallery->component ) {
		return $permalink;
	}

	$slug = $gallery->slug;

	$base_url = psmt_get_gallery_base_url( $gallery->component, $gallery->component_id );

	return apply_filters( 'psmt_get_gallery_permalink', trailingslashit( $base_url . $slug ), $gallery );
}
add_filter( 'post_type_link', 'psmt_filter_gallery_permalink', 10, 4 );

// Sanitize etc.
// for title.
add_filter( 'psmt_get_gallery_title', 'wp_kses_post' );
add_filter( 'psmt_get_gallery_title', 'wptexturize' );
add_filter( 'psmt_get_gallery_title', 'convert_chars' );
add_filter( 'psmt_get_gallery_title', 'trim' );
// for content.
add_filter( 'psmt_get_gallery_description', 'wp_kses_post' );
add_filter( 'psmt_get_gallery_description', 'wptexturize' );
add_filter( 'psmt_get_gallery_description', 'convert_smilies' );
add_filter( 'psmt_get_gallery_description', 'convert_chars' );
add_filter( 'psmt_get_gallery_description', 'wpautop' );
add_filter( 'psmt_get_gallery_description', 'make_clickable' );
