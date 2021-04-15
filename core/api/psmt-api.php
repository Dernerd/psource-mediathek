<?php
/**
 * PsourceMediathek API
 *
 * @package psourcemediathek
 */

// No direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Register a New gallery or media status
 *
 * @param array|string $args {
 *  Various options to create new status.
 *
 *  @type boolean 'media' does this status applies to media?
 *  @type boolean 'gallery' does this status applies to gallery?
 *  @type string  'key'     the unique string to identify this status eg. public|private|friends etc
 *  @type string  'label'   the actual readable name of this status
 *  @type string  'description'  description for this status
 * }
 */
function psmt_register_status( $args ) {

	$default = array(
		'media'            => true, // enable this status for media?
		'gallery'          => true, // enable this status for gallery?
		'key'              => '',
		'label'            => '',
		'labels'           => array(), // singular_name, plural_name.
		'description'      => '',
		'callback'         => '', // callback to test for this status access.
		'activity_privacy' => '',
	);

	$args = wp_parse_args( $args, $default );

	$key = $args['key'];

	if ( empty( $key ) || empty( $args['label'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'You must provide valid key and label for privacy', 'psourcemediathek' ), '1.0' );
	}

	$psourcemediathek = psourcemediathek();

	// if it was not already registered.
	if ( ! isset( $psourcemediathek->statuses[ $key ] ) ) {

		// internally we store the status as _status name(slug is made by prefixing underscore to the key).
		$term_slug = psmt_underscore_it( $key );

		$taxonomy = psmt_get_status_taxname();

		// if the terms does not exists, add it.
		if ( ! psmt_term_exists( $term_slug, $taxonomy ) ) {

			wp_insert_term( $args['label'], $taxonomy, array(
				'slug'        => $term_slug,
				'description' => $args['description'],
			) );
		}

		// create an status object.
		$status_object = new PSMT_Status( array(
			'key'    => $key,
			'label'  => $args['label'],
			'labels' => $args['labels'],
		) );


		$status_object->callback = $args['callback'];

		if ( empty( $args['activity_privacy'] ) ) {
			// in case the activity privacy is not specified for this status, we use the status slug as privacy.
			$args['activity_privacy'] = $key;
		}

		/**
		 * We use activity privacy to add privacy to activities for the media/gallery with the given status
		 * Another goal is map PsourceMediathek status to the privacy offered by BP Activity Privacy plugin
		 * When true privacy comes to BuddyPress, we will map to that.
		 */
		$status_object->activity_privacy = $args['activity_privacy'];

		// let us store a reference in all the global statuses.
		$psourcemediathek->statuses[ $key ] = $status_object;

		// if this privacy applies to gallery.
		if ( $args['gallery'] ) {
			$psourcemediathek->gallery_statuses[ $key ] = $status_object;
		}
		// does this status applies to media too?
		if ( $args['media'] ) {
			$psourcemediathek->media_statuses[ $key ] = $status_object;
		}
	}
}

/**
 * De register a previously registered Status(or privacy)
 *
 * @param string $status status key.
 *
 * @return boolean true on success false if the privacy was not found in the registered list
 */
function psmt_deregister_status( $status ) {

	$psourcemediathek = psourcemediathek();

	if ( isset( $psourcemediathek->statuses[ $status ] ) ) {
		unset( $psourcemediathek->statuses[ $status ] );

		// it could be registered for media or gallery or both, let us remove that.
		if ( isset( $psourcemediathek->gallery_statuses[ $status ] ) ) {
			unset( $psourcemediathek->gallery_statuses[ $status ] );
		}

		if ( isset( $psourcemediathek->media_statuses[ $status ] ) ) {
			unset( $psourcemediathek->media_statuses[ $status ] );
		}

		// we do not remove the taxonomy term and leave it for future.
		return true; // successfully deregistered.
	}

	return false; // no such privacy exists.
}

/**
 *  Register a new Gallery Type
 *
 * @param array $args {
 *  Type options.
 *
 * @type string $key unique identifier for the type.
 * @type string $label Label for the type.
 * @type array  $labels array of singular, plural names.('singular_name'=>'some_name', 'name'=> 'some name').
 * @type string $description optional. description for the type.
 * @type string $extensions Comma separated list of extensions(.jpg,.gif etc)
 *
 * }
 */
function psmt_register_type( $args ) {

	$default = array(
		'key'         => '',
		'label'       => '',
		'labels'      => array(),
		'description' => '',
		'extensions'  => '', // allowed file extensions as array of filetypes.
	);

	$args = wp_parse_args( $args, $default );

	$key = $args['key'];

	if ( empty( $key ) || empty( $args['label'] ) || empty( $args['extensions'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'You must provide valid key, label and extensions for gallery/media type', 'psourcemediathek' ), '1.0.0' );
	}

	$psourcemediathek = psourcemediathek();
	// if it was not already registered.
	if ( ! isset( $psourcemediathek->types[ $key ] ) ) {

		$term_slug  = psmt_underscore_it( $key );
		$taxonomy   = psmt_get_type_taxname();
		$extensions = psmt_string_to_array( $args['extensions'] );

		// if the terms does not exists, add it.
		if ( ! psmt_term_exists( $term_slug, $taxonomy ) ) {

			wp_insert_term( $args['label'], $taxonomy, array(
				'slug'        => $term_slug,
				'description' => $args['description'],
			) );
		}

		$type_object = new PSMT_Type( array(
			'key'        => $key,
			'label'      => $args['label'],
			'labels'     => $args['labels'],
			'extensions' => $extensions,
		) );

		$psourcemediathek->types[ $key ] = $type_object;
	}

}

/**
 * De register a previously registered gallery type
 *
 * @param string $key slug of the type eg: photo|audio|video.
 *
 * @return boolean true if success false if the type was not found in registered list
 */
function psmt_deregister_type( $key ) {

	$psourcemediathek = psourcemediathek();

	if ( isset( $psourcemediathek->types[ $key ] ) ) {
		unset( $psourcemediathek->types[ $key ] );

		return true;
	}

	return false;
}

/**
 * Register a new Associated/Supported component
 *
 * @param array $args array of args for registering component.
 */
function psmt_register_component( $args ) {

	$default = array(
		'key'         => '',
		'label'       => '',
		'labels'      => array(),
		'description' => '',
	);

	$args = wp_parse_args( $args, $default );

	$key = $args['key'];

	if ( empty( $key ) || empty( $args['label'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'You must provide valid key and label for associated component.', 'psourcemediathek' ), '1.0.0' );
	}

	$psourcemediathek = psourcemediathek();
	// if it was not already registered.
	if ( ! isset( $psourcemediathek->components[ $key ] ) ) {
		$term_slug = psmt_underscore_it( $key );
		$taxonomy  = psmt_get_component_taxname();

		// if the terms does not exists, add it.
		if ( ! psmt_term_exists( $term_slug, $taxonomy ) ) {
			wp_insert_term( $args['label'], $taxonomy, array(
				'slug'        => $term_slug,
				'description' => $args['description'],
			) );
		}

		$component_object = new PSMT_Component( array(
			'key'    => $key,
			'label'  => $args['label'],
			'labels' => $args['labels'],
		) );

		$psourcemediathek->components[ $key ] = $component_object;
	}
}

/**
 * De register a previously registered associated component
 *
 * @param string $key slug of the component eg members/groups/site/events.
 *
 * @return boolean true if success false if component does not exists in registered list
 */
function psmt_deregister_component( $key ) {

	$psourcemediathek = psourcemediathek();

	if ( isset( $psourcemediathek->components[ $key ] ) ) {
		unset( $psourcemediathek->components[ $key ] );

		return true;
	}

	return false;
}

/**
 * Media Sizes
 */

// cover image: 2 dimensions(for audio, video,gallery can we allow setting up different cover image sizes?)
// media size( original, we will store+ register thumb, mid, large, can we make it apllicable for different media type?).
/**
 * Register a new media Size
 *
 * @param mixed $args {
 *  Media size settings.
 *
 * @type string $name the name for the media size
 * @type int $width the width of the image
 * @type int $height required, height of the image
 * @type boolean $crop optional, whether to crop or resize
 *
 * }
 *
 * @return boolean
 */
function psmt_register_media_size( $args ) {
	/*
	  $default = array(
	  'name'=> 'thumb',
	  'label'=>'Thumbnail',
	  'width'=> 200,
	  'height'=> 200,
	  'crop'=> true,
	  'type'=> 'audio,video,photo'//allow multiple types
	  );
	 */
	$name = isset( $args['name'] ) ? $args['name'] : '';
	$type = isset( $args['type'] ) ? $args['type'] : '';
	$crop = isset( $args['crop'] ) ? $args['crop'] : false;

	if ( ! $name || ! $args['width'] || ! $args['height'] || ! $type ) {
		return false; // unable to register.
	}

	$mp    = psourcemediathek();
	$types = psmt_string_to_array( $type );

	foreach ( $types as $media_type ) {
		$mp->media_sizes[ $media_type ][ $name ] = array(
			'height' => absint( $args['height'] ),
			'width'  => absint( $args['width'] ),
			'crop'   => $crop,
			'label'  => $args['label'],
		);
	}

	return true; // successfully registered.
}

/**
 * Deregister an already registered media size
 *
 * @param array $args {
 *  Array, size configuration.
 *
 *  @type string $name required, the name of  the registered media size.
 *  @type string|array type(s) for which to be deregistered. e.g 'audio,video,photo' or 'audio,photo' or array('audio', 'photo').
 * }
 *
 * @return boolean
 */
function psmt_deregister_media_size( $args ) {

	$name = isset( $args['name'] ) ? $args['name'] : '';
	$type = isset( $args['type'] ) ? $args['type'] : '';

	if ( ! $name || ! $type ) {
		return false; // can not de register.
	}

	$mp = psourcemediathek();

	$types = psmt_string_to_array( $type );
	// remove the size setting for each type.
	foreach ( $types as $media_type ) {
		unset( $mp->media_sizes[ $media_type ][ $name ] );
	}

	return true;
}

/**
 * Get the registered media size.
 *
 * @param string $name name of the media size.
 * @param string $media_type which type of media it will apply to.
 *
 * @return array {
 *
 * @type int $width
 * @type int $height
 * @type boolean $crop
 *
 * }
 */
function psmt_get_media_size( $name = 'thumbnail', $media_type = 'photo' ) {

	$mp = psourcemediathek();

	if ( isset( $mp->media_sizes[ $media_type ][ $name ] ) ) {
		return $mp->media_sizes[ $media_type ][ $name ];
	}
	// if we are here, this media type or size was not registerd
	// check if default is registered, return that.
	if ( isset( $mp->media_sizes['default'][ $name ] ) ) {
		return $mp->media_sizes['default'][ $name ];
	}

	return array(); // no size error.
}

/**
 * Get all registered media sizes for the emdia type.
 *
 * @param string $media_type media type name.
 *
 * @return boolean|mixed array of mixed array{
 *
 * @type int $width
 * @type int $height
 * @type boolean $crop
 *
 * }
 */
function psmt_get_media_sizes( $media_type = 'photo' ) {

	$mp = psourcemediathek();

	$sizes = array();

	if ( isset( $mp->media_sizes[ $media_type ] ) ) {
		$sizes = $mp->media_sizes[ $media_type ];
	}

	// if we are here, this media type or size was not registerd
	// check if default is registered, return that.
	if ( ! $sizes && isset( $mp->media_sizes['default'] ) ) {
		$sizes = $mp->media_sizes['default'];
	}

	return apply_filters( 'psmt_get_media_sizes', $sizes ); // no size error.
}

/**
 * Register a media view
 *
 * Media View is used to render single media object contents/display.
 *
 * @param string         $type media type.
 * @param string         $storage storage method.
 * @param PSMT_Media_View $view media view.
 *
 * @return boolean
 */
function psmt_register_media_view( $type, $storage, $view ) {
	// storage should be set to 'default' for the default fallback handler.
	if ( ! $type || ! $storage || ! is_a( $view, 'PSMT_Media_View' ) ) {
		return false;
	}

	$mp = psourcemediathek();

	$mp->media_views[ $type ][ $storage ] = $view;

	return true;
}

/**
 * De regiser the media view.
 *
 * @param string $type media type.
 * @param string $storage storage method name.
 *
 * @return boolean
 */
function psmt_deregister_media_view( $type, $storage ) {

	if ( ! $type || ! $storage ) {
		return false;
	}

	$mp = psourcemediathek();

	unset( $mp->media_views[ $type ][ $storage ] );

	return true;
}

/**
 * Get registered view for this media type.
 *
 * @param PSMT_Media $media media object.
 *
 * @return PSMT_Media_View|Null
 */
function psmt_get_media_view( $media ) {

	if ( ! $media ) {
		$media = psmt_get_media();
	}

	$type = $media->type;

	$storage = psmt_get_storage_method( $media->id );

	if ( ! $type || ! $storage ) {
		return null;
	}

	$mp = psourcemediathek();

	$view = null;

	if ( isset( $mp->media_views[ $type ][ $storage ] ) ) {
		$view = $mp->media_views[ $type ][ $storage ];
	} elseif ( isset( $mp->media_views[ $type ]['default'] ) ) {
		// Fallback to default.
		$view = $mp->media_views[ $type ]['default'];
	}

	return apply_filters( 'psmt_get_media_view', $view, $media );
}

/**
 * Register a new gallery view
 *
 * @param string           $type photo|audio|video etc.
 * @param PSMT_Gallery_View $view Gallery View.
 *
 * @return boolean
 */
function psmt_register_gallery_view( $type, $view ) {

	if ( ! $type || ! is_a( $view, 'PSMT_Gallery_View' ) ) {
		return false;
	}

	psourcemediathek()->gallery_views[ $type ][ $view->get_id() ] = $view;

	return true;
}

/**
 * De register a gallery view
 *
 * @param string $type type name.
 * @param string $view_id view identifier.
 *
 * @return boolean
 */
function psmt_deregister_gallery_view( $type, $view_id ) {

	if ( ! $type || ! $view_id || ! is_string( $view_id ) ) {
		return false;
	}

	$psmt = psourcemediathek();

	unset( $psmt->gallery_views[ $type ][ $view_id ] );

	return true;
}

/**
 * Get gallery view.
 *
 * @param PSMT_Gallery $gallery Gallery object.
 * @param string      $view_id name of the view.
 *
 * @return boolean|PSMT_Gallery_View
 */
function psmt_get_gallery_view( $gallery, $view_id = '' ) {

	// we always need a gallery to generate gallery view.
	$type      = $gallery->type;
	$component = $gallery->component;

	if ( ! $type ) {
		return false;
	}

	// if view id is not given, get the single associated view.
	if ( ! $view_id ) {
		$view_id = psmt_get_gallery_meta( $gallery->id, '_psmt_view', true );
	}
	// if there was no view found, let us fallback to default.
	if ( ! $view_id ) {
		// fallback to the current component view.
		$view_id = psmt_get_component_gallery_view( $component, $type );
	}

	// if view id is still not found, lets fallback to default.
	if ( ! $view_id ) {
		$view_id = 'default';
	}

	// if we are here, we know the view_id and the type.
	$psmt = psourcemediathek();

	$view = null;
	if ( isset( $psmt->gallery_views[ $type ][ $view_id ] ) ) {
		$view = $psmt->gallery_views[ $type ][ $view_id ];
	} else {
		// we will be here if the view type is not registered now but was used, return default view.
		$view = $psmt->gallery_views[ $type ]['default'];// return default view when view not found.
	}

	return apply_filters( 'psmt_get_gallery_view', $view, $gallery );
}

// adding component support for multiple things.
/**
 * Register a component feature or override existing feature
 *
 * @param string $component ( e.g groups | members etc).
 * @param string $feature name of the feature.
 * @param mixed  $value value of the feature.
 *
 * @return boolean|PSMT_features
 * @todo someday drop it in favor of better name like psmt_component_add_feature
 */
function psmt_component_register_feature( $component, $feature, $value ) {

	if ( ! psmt_is_registered_component( $component ) ) {
		return false;
	}

	return psourcemediathek()->components[ $component ]->add_support( $feature, $value );
}

/**
 * Unregister a component feature
 * If you dont pass a value, all the feature value will be removed
 *
 * @param string $component name of the component.
 * @param string $feature feature name.
 * @param mixed  $value optional. Value to be removed.
 *
 * @return PSMT_Features|boolean
 *
 * @todo someday drop it in favor of better name like psmt_component_remove_feature
 */
function psmt_component_deregister_feature( $component, $feature, $value = null ) {

	if ( ! psmt_is_registered_component( $component ) ) {
		return false;
	}

	return psourcemediathek()->components[ $component ]->remove_support( $feature, $value );
}

/**
 * Check if component supports a particular feature e.g "friends only privacy or audio type"
 *
 * @param string $component any of the registered components (e.g groups|members etc).
 * @param string $feature feature name ( e.g status, type etc).
 * @param mixed  $value the feature value we are checking against.
 *    ( For example $feature='status' and $value ='groupsonly' means we are checking if the component supports friendsonly privacy level or not).
 *
 * @return boolean true if the feature is supported else false
 */
function psmt_component_supports_feature( $component, $feature, $value = null ) {

	return psourcemediathek()->components[ $component ]->supports( $feature, $value );
}

/**
 * Register the support for an status by the component
 *
 * E.g  psmt_component_add_status_support( 'members', 'private');
 *        psmt_component_add_status_support( 'members', 'public' );
 * means that the members component supports two privacy levels private/public.
 *
 * This must be called on/after psmt_init
 *
 * @param string $component componetn name.
 * @param string $status status name.
 *
 * @return PSMT_Features
 */
function psmt_component_add_status_support( $component, $status ) {
	return psmt_component_register_feature( $component, 'status', $status );
}

/**
 * Remove the support for a given status by the comonent
 *
 * @param string $component component name.
 * @param string $status status name.
 *
 * @return PSMT_Features
 */
function psmt_component_remove_status_support( $component, $status ) {
	return psmt_component_deregister_feature( $component, 'status', $status );
}

/**
 * Check if component supports given feature.
 *
 * @param string $component component name.
 * @param string $status name.
 *
 * @return bool
 */
function psmt_component_supports_status( $component, $status ) {
	return psourcemediathek()->components[ $component ]->supports( 'status', $status );
}

/**
 * Add the support for a media/gallery type by a component
 *
 * @param string $component component name.
 * @param string $type name.
 *
 * @return PSMT_Features
 */
function psmt_component_add_type_support( $component, $type ) {
	return psmt_component_register_feature( $component, 'type', $type );
}

/**
 * Remove the support for a given type the component
 *
 * @param string $component component name.
 * @param string $type name.
 *
 * @return PSMT_Features
 */
function psmt_component_remove_type_support( $component, $type ) {
	return psmt_component_deregister_feature( $component, 'type', $type );
}

/**
 * Does the given component supports the type
 *
 * @param string $component component name(e.g. 'members', 'groups' etc ).
 * @param string $type media type(audio,photo,video,doc etc).
 *
 * @return boolean
 */
function psmt_component_supports_type( $component, $type ) {
	return psmt_is_registered_component( $component ) && psourcemediathek()->components[ $component ]->supports( 'type', $type );
}

/**
 * Get all supported types for the given component.
 *
 * @param string $component component name.
 *
 * @return mixed
 */
function psmt_component_get_supported_types( $component ) {

	$option = $component . '_active_types';

	return psmt_get_option( $option, array() );
}

/**
 * Get the logger.
 *
 * @return PSMT_Logger
 */
function psmt_get_logger() {

	if ( ! class_exists( 'PSMT_DB_Logger' ) ) {
		psourcemediathek()->load_logger();
	}

	return PSMT_DB_Logger::get_instance();
}
