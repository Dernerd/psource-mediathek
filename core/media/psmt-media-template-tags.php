<?php
/**
 * Media template tags.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modeled after have_posts(), alternative for media loop
 *
 * Check if there are galleries.
 *
 * @return boolean
 */
function psmt_have_media() {

	$the_media_query = psourcemediathek()->the_media_query;

	if ( $the_media_query ) {
		return $the_media_query->have_media();
	}

	return false;
}

/**
 * Fetch the current media
 *
 * @return boolean
 */
function psmt_the_media() {
	return psourcemediathek()->the_media_query->the_media();
}

/**
 * Print media id
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_id( $media = null ) {
	echo psmt_get_media_id( $media );
}

/**
 * Get media id
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return int media id
 */
function psmt_get_media_id( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_id', $media->id );

}

/**
 * Print media title
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_title( $media = null ) {
	echo psmt_get_media_title( $media );
}

/**
 * Get media title
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string title.
 */
function psmt_get_media_title( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_title', $media->title, $media->id );

}

/**
 * Print media source url to be used for rendering( it is most of the time the value of src attribute)
 *
 * @param string             $size Registered media size type( e.g thumbnail, full, mid, original etc).
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_src( $size = '', $media = null ) {
	echo psmt_get_media_src( $size, $media );
}

/**
 * Get media source url to be used for rendering( it is most of the time the value of src attribute)
 *
 * @param string             $size Registered media size type( e.g thumbnail, full, mid, original etc).
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string absolute source url.
 */
function psmt_get_media_src( $size = '', $media = null ) {

	$media = psmt_get_media( $media );
	// if media is not photo and the type specified is empty, or not 'original' get cover.
	if ( 'photo' !== $media->type ) {

		if ( ! empty( $size ) && 'original' !== $size ) {
			return psmt_get_media_cover_src( $size, $media->id );
		}
	}
	$storage_manager = psmt_get_storage_manager( $media->id );

	return apply_filters( 'psmt_get_media_src', $storage_manager->get_src( $size, $media->id ), $size, $media );

}

/**
 * Print the absolute path to the media understandable by the storage manager.
 *
 * @param string             $size Registered media size type( e.g thumbnail, full, mid, original etc).
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_path( $size = '', $media = null ) {
	echo psmt_get_media_path( $size, $media );
}

/**
 * Get the absolute path to the media understandable by the storage manager.
 *
 * @param string             $size Registered media size type( e.g thumbnail, full, mid, original etc).
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return mixed
 */
function psmt_get_media_path( $size = '', $media = null ) {

	$media = psmt_get_media( $media );

	$storage_manager = psmt_get_storage_manager( $media->id );

	return $storage_manager->get_path( $size, $media->id );
}

/**
 *  Print media slug
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_slug( $media = null ) {
	echo psmt_get_media_slug( $media );
}

/**
 * Get media slug
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string
 */
function psmt_get_media_slug( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_slug', $media->slug, $media->id );

}

/**
 * To Generate the actual code for showing media
 * We will rewrite it with better api in future, currently, It acts as fallback
 *
 * The goal of this function is to generate appropriate output for listing media based on media type
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_load_media_view( $media = null ) {
	$view = psmt_get_media_view( $media );

	if ( ! $view ) {
		printf( __( 'Es ist kein Ansichtsobjekt registriert, das die Anzeige des Inhalts vom Typ <strong>%s</strong> ??bernimmt', 'psourcemediathek' ), strtolower( psmt_get_type_singular_name( $media->type ) ) );
	} else {
		$view->display( $media );
	}
}

/**
 * Display Media (Render it for viewing)
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_content( $media = null ) {
	if ( ! $media ) {
		$media = psmt_get_media();
	}

	psmt_load_media_view( $media );
}

/**
 * Show lightbox content.
 *
 * @param $media
 *
 * @return string
 */
function psmt_lightbox_content( $media ) {
	if ( ! $media ) {
		return '';
	}

	$media = psmt_get_media( $media );

	$type = $media->type;

	$templates = array(
		"gallery/media/views/lightbox/{$type}.php", // grid-audio.php etc .
		'gallery/media/views/lightbox/photo.php',
	);

	if ( $media->is_oembed ) {
		array_unshift( $templates, 'gallery/media/views/lightbox/oembed.php' );
	}

	psmt_locate_template( $templates, true );
}
/**
 * Check if the media has description.
 *
 * @since 1.1.1
 *
 * @param PSMT_Media|int|null $media media id or object.
 *
 * @return bool true if has description else false.
 */
function psmt_media_has_description( $media = null ) {
	$media = psmt_get_media( $media );

	if ( empty( $media ) || empty( $media->description ) ) {
		return false;
	}

	return true;
}

/**
 * Print media description
 *
 * @param PSMT_Media|int|null $media media id or object.
 */
function psmt_media_description( $media = null ) {
	echo psmt_get_media_description( $media );
}

/**
 * Get media description
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string
 */
function psmt_get_media_description( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_description', stripslashes( $media->description ), $media->id );

}

/**
 * Print the type of media
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_type( $media = null ) {
	echo psmt_get_media_type( $media );
}

/**
 * Get Media Type.
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string media type (audio|video|photo etc)
 */
function psmt_get_media_type( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_type', $media->type, $media->id );

}

/**
 * Print Gallery status (private|public)
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_status( $media = null ) {
	echo psmt_get_media_status( $media );
}

/**
 * Get media status.
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string Gallery status(public|private|friends only)
 */
function psmt_get_media_status( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_status', $media->status, $media->id );

}

/**
 * Print the date of creation for the media
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_date_created( $media = null ) {
	echo psmt_get_media_date_created( $media );
}

/**
 * Get the date this media was created
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string|int|bool Formatted date string or Unix timestamp. False if $date is empty.
 */
function psmt_get_media_date_created( $media = null, $format = '', $translate = true ) {
	if ( ! $format ) {
		$format = get_option( 'date_format' );
	}
	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_date_created', mysql2date( $format, $media->date_created, $translate ), $media->id );

}

/**
 * Print When was the last time media was updated
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_last_updated( $media = null ) {
	echo psmt_get_media_last_updated( $media );
}

/**
 * Get the date this media was last updated
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string|int|bool Formatted date string or Unix timestamp. False if $date is empty.
 */
function psmt_get_media_last_updated( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_date_updated', mysql2date( get_option( 'date_format' ), $media->date_updated, true ), $media->id );

}

/**
 * Print the user id of the person who created this media
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_creator_id( $media = null ) {
	echo psmt_get_media_creator_id( $media );
}

/**
 * Get the ID of the person who created this Gallery
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return int ID of the user who uploaded/created this media.
 */
function psmt_get_media_creator_id( $media = null ) {

	$media = psmt_get_media( $media );

	return apply_filters( 'psmt_get_media_creator_id', $media->user_id, $media->id );

}

/**
 * Print media creator's link.
 *
 * @param int|PSMT_Media $media media id or object.
 */
function psmt_media_creator_link( $media = null ) {
	echo psmt_get_media_creator_link( $media );
}

/**
 * Get media creator user link.
 *
 * @param int|PSMT_Media $media media id or object.
 *
 * @return string
 */
function psmt_get_media_creator_link( $media = null ) {
	$media = psmt_get_media( $media );

	return psmt_get_user_link( $media->user_id );
}
/**
 * Print the css class list
 *
 * @param string             $class Optional css classes to append.
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_class( $class = '', $media = null ) {
	echo psmt_get_media_class( $class, $media );
}

/**
 * Get css class list fo the media
 *
 * @param string             $class Additional css classes for the media entry.
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string list of classes for teh media entry.
 */
function psmt_get_media_class( $class = '', $media = null ) {

	$media = psmt_get_media( $media );

	$class_list = "psmt-item psmt-media psmt-media-{$media->type}";

	if ( psmt_is_single_media() ) {
		$class_list .= " psmt-item-single psmt-media-single psmt-media-single-{$media->type}";
	}

	return apply_filters( 'psmt_get_media_class', "{$class_list} {$class}" );
}

/**
 * Print the media anchor html attributes
 *
 * @param array $args any valid html attribute is allowed as key/val pair.
 */
function psmt_media_html_attributes( $args = null ) {
	echo psmt_get_media_html_attributes( $args );
}

/**
 * Build the attributes(prop=val) for the media anchor elemnt
 * It may be useful in adding some extra attributes to the anchor
 *
 * @param array $args any valid html attribute is allowed as key/val pair.
 *
 * @return string
 */
function psmt_get_media_html_attributes( $args = null ) {

	$default = array(
		'class'             => '',
		'id'                => '',
		'title'             => '',
		'data-psmt-context'  => 'gallery',
		'media'             => 0, // pass gallery id or media, not required inside a loop.
		'data-psmt-media-id' => 0,
	);

	$args = wp_parse_args( $args, $default );

	$media = psmt_get_media( $args['media'] );

	if ( ! $media ) {
		return '';
	}

	// if(! $args['id'] )
	//	$args['id'] = 'psmt-media-thumbnail-' . $gallery->id;

	$args['media']             = $media; // we will pass the media object to the filter too.
	$args['data-psmt-media-id'] = psmt_get_media_id( $media );

	$args = (array) apply_filters( 'psmt_media_html_attributes_pre', $args );

	unset( $args['media'] );

	if ( empty( $args['title'] ) ) {
		$args['title'] = psmt_get_media_title( $media );
	}

	return psmt_get_html_attributes( $args ); // may be a filter in future here?
}

/**
 * Print media loop pagination
 */
function psmt_media_pagination() {
	echo psmt_get_media_pagination();
}

/**
 * Get the pagination text
 *
 * @return string
 */
function psmt_get_media_pagination() {

	// check if the current gallery supports playlist, then do not show pagination.
	if ( ! psourcemediathek()->the_media_query || psmt_gallery_supports_playlist( psmt_get_gallery() ) ) {
		return '';
	}

	return "<div class='psmt-paginator'>" . psourcemediathek()->the_media_query->paginate() . '</div>';
}

/**
 * Show the pagination count like showing 1-10 of 20
 */
function psmt_media_pagination_count() {

	if ( ! psourcemediathek()->the_media_query ) {
		return;
	}

	psourcemediathek()->the_media_query->pagination_count();
}

/**
 * Get the next media id based on the given media. It is used for adjacent media.
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return int
 */
function psmt_get_next_media_id( $media ) {

	if ( ! $media ) {
		return 0;
	}

	$media = psmt_get_media( $media );

	$args = array(
		'component'     => $media->component,
		'component_id'  => $media->component_id,
		'object_id'     => $media->id,
		'object_parent' => $media->gallery_id,
		'next'          => true,
	);

	$prev_gallery_id = psmt_get_adjacent_object_id( $args, psmt_get_media_post_type() );

	return $prev_gallery_id;

}

/**
 * Get the previous media id based on the given media.
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return int previous media id.
 */
function psmt_get_previous_media_id( $media ) {

	if ( ! $media ) {
		return 0;
	}

	$media = psmt_get_media( $media );

	$args = array(
		'component'     => $media->component,
		'component_id'  => $media->component_id,
		'object_id'     => $media->id,
		'object_parent' => $media->gallery_id,
		'next'          => false,
	);

	$prev_gallery_id = psmt_get_adjacent_object_id( $args, psmt_get_media_post_type() );

	return $prev_gallery_id;
}

/**
 * Get adjacent media link.
 *
 * @param string $format how to format the link.
 * @param string $link link.
 * @param int    $media_id current media id.
 * @param bool   $previous previous or next to link.
 *
 * @return mixed|null
 */
function psmt_get_adjacent_media_link( $format, $link, $media_id = null, $previous = false ) {

	if ( ! $media_id ) {
		$media_id = psmt_get_current_media_id();
	}

	if ( ! $previous ) {
		$next_media_id = psmt_get_next_media_id( $media_id );
	} else {
		$next_media_id = psmt_get_previous_media_id( $media_id );
	}

	if ( ! $next_media_id ) {
		return;
	}

	$media = psmt_get_media( $next_media_id );

	if ( empty( $media ) ) {
		return;
	}

	$title = psmt_get_media_title( $media );

	$css_class = $previous ? 'psmt-previous' : 'psmt-next'; // css class.

	if ( empty( $title ) ) {
		$title = $previous ? __( 'Vorherige', 'psourcemediathek' ) : __( 'N??chstes', 'psourcemediathek' );
	}

	$date = mysql2date( get_option( 'date_format' ), $media->date_created );
	$rel  = $previous ? 'prev' : 'next';

	$string = "<a href='" . psmt_get_media_permalink( $media ) . "' rel='{$rel}'>";
	$inlink = str_replace( '%title', $title, $link );
	$inlink = str_replace( '%date', $date, $inlink );
	$inlink = $string . $inlink . '</a>';

	$output = str_replace( '%link', $inlink, $format );

	return "<span class='{$css_class}'>{$output}</span>";

}

/**
 * Print next media link.
 *
 * @param string   $format how to format link.
 * @param string   $link Link.
 * @param int|null $media_id current media id.
 */
function psmt_next_media_link( $format = '%link &raquo;', $link = '%title', $media_id = null ) {
	echo psmt_get_adjacent_media_link( $format, $link, $media_id, false );
}

/**
 * Print previous media link.
 *
 * @param string   $format how to format link.
 * @param string   $link Link.
 * @param int|null $media_id current media id.
 */
function psmt_previous_media_link( $format = '&laquo; %link ', $link = '%title', $media_id = null ) {
	echo psmt_get_adjacent_media_link( $format, $link, $media_id, true );
}

/**
 * Stats Related
 * must be used inside the media loop.
 */

/**
 * Print the total media count for the current query
 */
function psmt_total_media_count() {
	echo psmt_get_total_media_count();
}

/**
 * Get the total number of media in current query
 *
 * @return int
 */
function psmt_get_total_media_count() {

	$found = 0;

	if ( psourcemediathek()->the_media_query ) {
		$found = psourcemediathek()->the_media_query->found_posts;
	}

	return apply_filters( 'psmt_get_total_media_count', $found );

}

/**
 * Total media count for user
 */
function psmt_total_media_count_for_member() {
	echo psmt_get_total_media_count_for_member();
}

/**
 * Get total media count for user
 *
 * @todo Implement it?
 *
 * @return int total count.
 */
function psmt_get_total_media_count_for_member() {
	// psmt_get_total_media_for_user() does not exist at the moment.
	$total = function_exists( 'psmt_get_total_media_for_user' ) ? psmt_get_total_media_for_user() : 0;

	return apply_filters( 'psmt_get_total_media_count_for_member', $total );
}

/**
 * Other functions
 */

/**
 * Get The Single media ID
 *
 * @return int
 */
function psmt_get_current_media_id() {
	return psourcemediathek()->current_media->id;
}

/**
 * Get current Media
 *
 * @return PSMT_Media|null
 */
function psmt_get_current_media() {
	return psourcemediathek()->current_media;
}

/**
 * Is it media directory?
 *
 * @todo handle the single media case for root media
 *
 * @return boolean
 */
function psmt_is_media_directory() {

	$action = bp_current_action();

	if ( psmt_is_gallery_directory() && ! empty( $action ) ) {
		return true;
	}

	return false;

}

/**
 * Is Single Media
 *
 * @return boolean
 */
function psmt_is_single_media() {

	if ( psourcemediathek()->the_media_query && psourcemediathek()->the_media_query->is_single() ) {
		return true;
	}

	return false;
}


/**
 * Check if the current action is media editing/management
 *
 * @return boolean
 */
function psmt_is_media_management() {
	return psourcemediathek()->is_editing( 'media' ) && psourcemediathek()->is_action( 'edit' );
}

/**
 * Is it media delete action?
 *
 * @return boolean
 */
function psmt_is_media_delete() {
	return psmt_is_media_management() && psourcemediathek()->is_edit_action( 'delete' );
}

/**
 * Print No media found message.
 *
 * @todo update
 */
function psmt_no_media_message() {
	// detect the type here.
	$type_name = bp_action_variable( 0 );

	// $type_name = media_get_type_name_plural( $type );

	if ( ! empty( $type_name ) ) {
		$message = sprintf( __( 'Es gibt noch keine %s.', 'psourcemediathek' ), strtolower( $type_name ) );
	} else {
		$message = __( 'Es gibt noch keine Galerien.', 'psourcemediathek' );
	}

	echo $message;
}

/**
 * Print media action links.
 *
 * @param PSMT_Media|int|null $media media id or Object.
 */
function psmt_media_action_links( $media = null ) {
	echo psmt_get_media_action_links( $media );
}

/**
 * Get media action links like view/edit/delete/upload to show on individual media
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string action links.
 */
function psmt_get_media_action_links( $media = null ) {

	$links = array();

	$media = psmt_get_media( $media );
	// $links ['view'] = sprintf( '<a href="%1$s" title="view %2$s" class="psmt-view-media">%3$s</a>', psmt_get_media_permalink( $media ), esc_attr( $media->title ), __( 'view', 'psourcemediathek' ) );
	// upload?
	if ( psmt_user_can_edit_media( $media->id ) ) {
		$links['edit'] = sprintf( '<a href="%1$s" title="' . __( 'Bearbeite %2$s', 'psourcemediathek' ) . '">%3$s</a>', psmt_get_media_edit_url( $media ), psmt_get_media_title( $media ), __( 'Bearbeiten', 'psourcemediathek' ) );
	}
	// delete?
	if ( psmt_user_can_delete_media( $media ) ) {
		$links['delete'] = sprintf( '<a href="%1$s" title="' . __( 'L??sche %2$s', 'psourcemediathek' ) . '" class="confirm psmt-confirm psmt-delete psmt-delete-media">%3$s</a>', psmt_get_media_delete_url( $media ), psmt_get_media_title( $media ), __( 'L??schen', 'psourcemediathek' ) );
	}

	return apply_filters( 'psmt_media_actions_links', join( ' ', $links ), $links, $media );

}

/**
 * Get the column class to be assigned to the media grid
 *
 * @param PSMT_Media|int|null $media media id or Object.
 *
 * @return string
 */
function psmt_get_media_grid_column_class( $media = null ) {
	// we are using 1-24 col grid, where 3-24 represents 1/8th and so on.
	$col = psmt_get_option( 'media_columns' );

	return psmt_get_grid_column_class( $col );
}
