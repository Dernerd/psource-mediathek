<?php

/**
 * Get the term id from term slug without going through extra database query
 *
 * We fetch it from our stored array of PSMT_Terms in psourcemediathek object
 *
 * @see core/class-psmt-taxonomy.php for implemetation details
 *
 * For Internal use only
 *
 * @access private
 *
 * @param string $term_slug term slug.
 * @param string $psmt_terms_list which terms list to use.
 *
 * @return int term id
 */
function psmt_get_term_id_by_slug( $term_slug, $psmt_terms_list ) {

	// if the status id is given we scan into psourcemediathek->statuses array for it.
	$term_id = 0; // non existant.

	if ( ! $term_slug || ! is_string( $term_slug ) ) {
		return $term_id;
	}

	$psmt = psourcemediathek();

	if ( ! isset( $psmt->{$psmt_terms_list} ) ) {
		return $term_id;
	}

	$psmt_terms = $psmt->{$psmt_terms_list};

	foreach ( $psmt_terms as $psmt_term ) {

		if ( $psmt_term->get_slug() === $term_slug ) {
			$term_id = $psmt_term->get_id();
			break;
		}
	}

	return absint( $term_id );

}

/**
 * Get the term_slug from term id without going through extra database query
 * We fetch it from our stored array of PSMT_Terms in psourcemediathek object
 *
 * For Internal use only
 *
 * @access private
 *
 * @param int    $term_id term id for which we want the term slug.
 * @param string $psmt_terms_list name of the terms list to scan.
 *
 * @return string
 */
function psmt_get_term_slug( $term_id, $psmt_terms_list ) {

	// if the status id is given we scan into psourcemediathek()->statuses array for it
	$slug = ''; // non existant.
	if ( ! $term_id || ! is_numeric( $term_id ) ) {
		return $slug;
	}


	$psmt = psourcemediathek();

	if ( ! isset( $psmt->{$psmt_terms_list} ) ) {
		return $slug;
	}


	$psmt_terms = $psmt->{$psmt_terms_list};//

	foreach ( $psmt_terms as $psmt_term ) {

		if ( $psmt_term->get_id() === $term_id ) {
			$slug = $psmt_term->get_slug();
			break;
		}
	}

	return $slug;
}

/**
 * Get the slug(private|public etc) for the status term_id
 *
 * @param int $status_id internal status term id.
 *
 * @return string the status slug.
 */
function psmt_get_status_term_slug( $status_id ) {
	return psmt_get_term_slug( $status_id, 'statuses' );
}

/**
 * Get the Type slug( photo, video etc) by type id
 *
 * @param int $type_id the internal term id.
 *
 * @return string type slug.
 */
function psmt_get_type_term_slug( $type_id ) {
	return psmt_get_term_slug( $type_id, 'types' );
}

/**
 * Get the component slug(members|groups) etc by the component id
 *
 * @param int $component_id internal component term id.
 *
 * @return string component slug
 */
function psmt_get_component_term_slug( $component_id ) {
	return psmt_get_term_slug( $component_id, 'components' );
}

/**
 * Get the status Object for given key.
 *
 * @param string $key status key(private|public etc).
 *
 * @return PSMT_Status|Boolean
 */
function psmt_get_status_object( $key ) {

	if ( ! $key ) {
		return false;
	}

	if ( is_numeric( $key ) ) {
		$key = psmt_get_status_term_slug( $key );
	}

	$psmt = psourcemediathek();

	if ( $key && isset( $psmt->statuses[ $key ] ) && is_a( $psmt->statuses[ $key ], 'PSMT_Status' ) ) {
		return $psmt->statuses[ $key ];
	}

	return false;
}

/**
 * Get the component object.
 *
 * @param string $key Component name(members|groups etc).
 *
 * @return PSMT_Component|boolean
 */
function psmt_get_component_object( $key ) {

	if ( ! $key ) {
		return false;
	}

	if ( is_numeric( $key ) ) {
		$key = psmt_get_component_term_slug( $key );
	}

	$psmt = psourcemediathek();

	if ( isset( $psmt->components[ $key ] ) && is_a( $psmt->components[ $key ], 'PSMT_Component' ) ) {
		return $psmt->components[ $key ];
	}

	return false;
}

/**
 * Get the type object.
 *
 * @param string|int $key type name( members|groups  etc).
 *
 * @return PSMT_Type|boolean
 */
function psmt_get_type_object( $key ) {

	if ( ! $key ) {
		return false;
	}

	if ( is_numeric( $key ) ) {
		$key = psmt_get_type_term_slug( $key );
	}

	$psmt = psourcemediathek();

	if ( isset( $psmt->types[ $key ] ) && is_a( $psmt->types[ $key ], 'PSMT_Type' ) ) {
		return $psmt->types[ $key ];
	}

	return false;
}


/**
 * Get allowed file extensions for this type as array
 *
 * @param string $type audio|photo|video etc.
 *
 * @return array( 'jpg', 'gif', ..)//allowed extensions for a given type
 */
function psmt_get_allowed_file_extensions( $type ) {

	if ( ! psmt_is_registered_type( $type ) ) {
		// should we only do it for active types?
		return array();
	}

	$type_object = psmt_get_type_object( $type );

	return $type_object->get_allowed_extensions();
}

/**
 * Get the list of allowed file extensions
 *
 * @param string $type type name(photo, video etc).
 * @param string $separator separator used while creating the list.
 *
 * @return string
 */
function psmt_get_allowed_file_extensions_as_string( $type, $separator = ',' ) {

	$extensions = psmt_get_allowed_file_extensions( $type );

	if ( empty( $extensions ) ) {
		return '';
	}

	return join( $separator, $extensions );
}

/** Let us improve the performance*/

/**
 * Cache all terms used by PsourceMediathek to avoid the query overhead
 *
 * We know that we won't have more than 10-15 terms, so It is perfectly ok to store them in cache
 * in future, we may only want to include few fields
 */
function _psmt_cache_all_terms() {

	$taxonomies = _psmt_get_all_taxonomies();

	$args = array( 'hide_empty' => false );

	$terms = get_terms( $taxonomies, $args );

	$new_terms = _psmt_build_terms_array( $terms );

	foreach ( $taxonomies as $tax ) {

		if ( empty( $new_terms[ $tax ] ) ) {
			// avoid cache miss causing recursion in _psmt_get_all_terms.
			$new_terms[ $tax ] = array();
		}
	}

	foreach ( $new_terms as $taxonomy => $tax_terms ) {
		wp_cache_set( 'psmt_taxonomy_' . $taxonomy, $tax_terms, 'psmt' );
	}
}

/**
 * Cache individual term
 *
 * @param WP_Term $term term object to cache.
 */
function _psmt_cache_term( $term ) {

	$taxonomy = $term->taxonomy;

	$terms = _psmt_get_terms( $taxonomy );

	$terms[ psmt_strip_underscore( $term->slug ) ] = $term;

	wp_cache_set( 'psmt_taxonomy_' . $taxonomy, $terms, 'psmt' );
}

/**
 * Get the terms from cache.
 *
 * @param string|int $slug_or_id term slug or id.
 * @param string     $taxonomy taxonomy to which this term belongs to.
 *
 * @return bool|string|WP_Term
 */
function _psmt_get_term( $slug_or_id, $taxonomy ) {

	$term = '';

	if ( ! $slug_or_id ) {
		return false;
	}

	$terms = _psmt_get_terms( $taxonomy );

	if ( is_numeric( $slug_or_id ) ) {
		foreach ( $terms as $term_item ) {

			if ( $slug_or_id === $term_item->term_id ) {
				$term = $term_item;
				break;
			}
		}
	} else {
		$term = isset( $terms[ $slug_or_id ] ) ? $terms[ $slug_or_id ] : '';
	}

	return $term;
}

/**
 * Get all terms in the given taxonomy.
 *
 * @param string $taxonomy taxonomy name.
 *
 * @return bool|mixed
 */
function _psmt_get_terms( $taxonomy ) {

	if ( ! $taxonomy || ! in_array( $taxonomy, _psmt_get_all_taxonomies() ) ) {
		return false;
	}

	$terms = wp_cache_get( 'psmt_taxonomy_' . $taxonomy, 'psmt' );

	if ( false !== $terms ) {
		return $terms;
	}

	// if we are here, It is a cache miss.
	_psmt_cache_all_terms();

	return _psmt_get_terms( $taxonomy );

}

/**
 * Rebuilds the default terms array keyed by taxonomy/slug
 *
 * @param array $terms array of terms.
 *
 * @return array
 */
function _psmt_build_terms_array( &$terms ) {

	$new_terms = array();

	foreach ( $terms as $term ) {
		$new_terms[ $term->taxonomy ][ psmt_strip_underscore( $term->slug ) ] = $term;
	}

	return $new_terms;
}

/**
 * Get an array of the names of psmt core taxonomies ( literally psmt-status, psmt-component, psmt-type )
 *
 * @return array of PsourceMediathek used taxonomies.
 */
function _psmt_get_all_taxonomies() {

	return apply_filters( 'psmt_get_all_taxonomies', array(
		psmt_get_status_taxname(),
		psmt_get_type_taxname(),
		psmt_get_component_taxname(),
	) );
}

/**
 * Translates our terminology to internal taxonomy( for e.f component translates to psmt-component and so on )
 *
 * @param string $name name of the aliases for the terms.
 *
 * @return string taxonomy name
 */
function psmt_translate_to_taxonomy( $name ) {

	$tax_name = '';
	/**
	 * @todo Think about the possibility to name the functions dynamically like psmt_get_{$name}_taxname() for flexibility
	 */
	if ( 'component' === $name ) {
		$tax_name = psmt_get_component_taxname();
	} elseif ( 'type' === $name ) {
		$tax_name = psmt_get_type_taxname();
	} elseif ( 'status' === $name ) {
		$tax_name = psmt_get_status_taxname();
	}

	return $tax_name;
}
