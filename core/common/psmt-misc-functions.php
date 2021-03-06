<?php

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common DB Queries and helpers for queries
 */
/**
 * Get an array of Gallery ids or Media ids based on other params
 *
 * @param array  $args {
 *  Options for fetching object ids.
 *
 * @type string|array $component comma separated sting or array of components eg 'groups,members' or array('groups', 'members' )
 * @type int $component_id numeric component id (user id or group id)
 * @type string|array $status comma separated list or array of statuses e.g. 'public,private,friends' or array ( 'public', 'private', 'friends' )
 * @type string|array $type comma separated list or array of media types e.g 'audio,video,photo' or array ( 'audio', 'video', 'photo' )
 * }
 *
 * @param string $post_type name of post type.
 *
 * @return mixed array of gallery or media post ids
 */
function psmt_get_object_ids( $args, $post_type ) {

	global $wpdb;

	$post_type_sql = '';

	$sql = array();

	$default = array(
		'component'    => '',
		'component_id' => false,
		'status'       => '',
		'type'         => '',
		'post_status'  => 'publish',
	);

	// if component is set to user, we can simply avoid component query
	// may be next iteration someday.
	$args = wp_parse_args( $args, $default );

	$status       = $args['status'];
	$component    = $args['component'];
	$component_id = $args['component_id'];
	$type         = $args['type'];
	$post_status  = $args['post_status'];

	if ( ! $status ) {
		if ( $component && $component_id ) {
			$status = psmt_get_accessible_statuses( $component, $component_id, get_current_user_id() );
		} else {
			$status = array_keys( psmt_get_active_statuses() );
		}
	}

	if ( ! $component ) {
		$component = array_keys( psmt_get_active_components() );
	}

	if ( ! $type ) {
		$type = array_keys( psmt_get_active_types() );
	}

	// do we have a component set.
	if ( $component ) {
		$sql [] = psmt_get_tax_sql( $component, psmt_get_component_taxname() );
	}


	// do we have a component set?
	if ( $status ) {
		$sql [] = psmt_get_tax_sql( $status, psmt_get_status_taxname() );
	}

	// for type, repeat it.
	if ( $type ) {
		$sql [] = psmt_get_tax_sql( $type, psmt_get_type_taxname() );
	}

	$post_type_sql = $wpdb->prepare( "SELECT DISTINCT ID as object_id FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s", $post_type, $post_status );

	// if a user or group id is given.
	if ( $component_id ) {
		$post_type_sql = $wpdb->prepare( "SELECT DISTINCT p.ID  as object_id FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type= %s AND p.post_status = %s AND pm.meta_key=%s and pm.meta_value=%d", $post_type, $post_status, '_psmt_component_id', $component_id );
	}
	$new_sql = $join_sql = '';
	// let us generate inner sub queries.
	if ( $sql ) {
		$join_sql = ' (' . join( ' AND object_id IN (', $sql );
	}

	$sql_count = count( $sql );
	// we need to append the ) for closing the sub queries.
	for ( $i = 0; $i < $sql_count; $i ++ ) {
		$join_sql .= ')';
	}

	$new_sql = $post_type_sql;

	// if the join sql is present, let us append it.
	if ( $join_sql ) {
		$new_sql .= ' AND ID IN ' . $join_sql;
	}

	return $wpdb->get_col( $new_sql );
}

/**
 *  Get total galleries|media based on other parameters
 *
 * @param array  $args {
 *  Options to get the object count.
 *
 * @type string|array $component comma separated sting or array of components eg 'groups,members' or array('groups', 'members' )
 * @type int $component_id numeric component id (user id or group id)
 * @type string|array $status comma separated list or array of statuses e.g. 'public,private,friends' or array ( 'public', 'private', 'friends' )
 * @type string|array $type comma separated list or array of media types e.g 'audio,video,photo' or array ( 'audio', 'video', 'photo' )
 * }
 *
 * @param string $post_type name of post type.
 *
 * @return int total no of posts
 */
function psmt_get_object_count( $args, $post_type ) {

	global $wpdb;

	$sql = array();

	$default = array(
		'component'    => '',
		'component_id' => '',
		'status'       => '',
		'type'         => '',
		'user_id'      => '',
		'post_status'  => 'publish',
		'meta_query'   => null,
	);

	// if component is set to user, we can simply avoid component query
	// may be next iteration someday.
	$args = wp_parse_args( $args, $default );

	$status       = $args['status'];
	$component    = $args['component'];
	$component_id = $args['component_id'];
	$type         = $args['type'];
	$user_id      = $args['user_id'];
	$post_status  = $args['post_status'];
	$meta_args =  empty( $args['meta_query'] ) ? array(): $args['meta_query'];

	$where = array();
	$join = array();

	if ( ! $status ) {
		if ( $component && $component_id ) {
			$status = psmt_get_accessible_statuses( $component, $component_id, get_current_user_id() );
		} else {
			$status = array_keys( psmt_get_active_statuses() );
		}
	}

	if ( ! $component ) {
		$component = array_keys( psmt_get_active_components() );
	}

	if ( ! $type ) {
		$type = array_keys( psmt_get_active_types() );
	}

	// do we have a component set.
	if ( $component ) {
		$sql [] = psmt_get_tax_sql( $component, psmt_get_component_taxname() );
	}

	// do we have a component set.
	if ( $status ) {
		$sql [] = psmt_get_tax_sql( $status, psmt_get_status_taxname() );
	}

	// for type, repeat it.
	if ( $type ) {
		$sql [] = psmt_get_tax_sql( $type, psmt_get_type_taxname() );
	}

	// we need to find all the object ids which are present in these terms
	// since mysql does not have intersect clause and inner join will be causing too large data set
	// let us use another apprioach for now
	// in our case
	// there are 3 taxonomies
	// so we will be looking for the objects appearing thrice.
	$tax_object_sql = ' (SELECT DISTINCT t.object_id FROM (' . join( ' UNION ALL ', $sql ) . ') AS t GROUP BY object_id HAVING count(*) >=3 )';

	$where['post_type']   = $wpdb->prepare( 'p.post_type = %s', $post_type );
	$where['post_status'] = $wpdb->prepare( 'p.post_status = %s', $post_status );

	$select_clause = 'SELECT COUNT( DISTINCT p.ID ) as total';
	$from_clause   = "FROM {$wpdb->posts} p";

	// if a user or group id is given.
	if ( $component_id ) {
		$meta_args [] = array(
			'key'     => '_psmt_component_id',
			'value'   => $component_id,
			'compare' => '=',
		);
	}

	if ( $user_id ) {
		$where['post_author'] = $wpdb->prepare( 'p.post_author = %d ', $user_id );
	}

	// If the join sql is present, let us append it.
	if ( $tax_object_sql ) {
		$where['tax_where'] = 'p.ID IN ' . $tax_object_sql;
	}


	if ( $meta_args ) {
		$meta_query = new WP_Meta_Query( $meta_args );
		$meta_sql   = $meta_query->get_sql( 'post', 'p', 'ID' );
		if ( $meta_sql ) {
			$where['meta_where'] = preg_replace( '/^\sAND/', '', $meta_sql['where'] );
			$join['meta_join']   = $meta_sql['join'];
		}
	}

	$join  = array_filter( $join );
	$where = array_filter( $where );

	$join_clause  = join( ' ', $join );
	$where_clause = join( ' AND ', $where );

	return $wpdb->get_var( "$select_clause {$from_clause} {$join_clause} WHERE {$where_clause}" );
}

/**
 * Get adjacent object(media|gallery) ids.
 *
 * @param array  $args {
 *  Array of options.
 *
 * }
 * @param string $post_type name of post type.
 *
 * @return null|string
 */
function psmt_get_adjacent_object_id( $args, $post_type ) {

	global $wpdb;

	$post_type_sql = '';

	$sql = array();

	$default = array(
		'component'     => '',
		'component_id'  => '',
		'status'        => psmt_get_accessible_statuses( psmt_get_current_component(), psmt_get_current_component_id() ),
		'type'          => '',
		'post_status'   => 'any',
		'next'          => true,
		'object_id'     => '', // given post id.
		'object_parent' => 0,
	);

	if ( psmt_get_gallery_post_type() === $post_type ) {
		// for gallery, the default post type should be published status.
		$default['post_status'] = 'publish';
	}

	// if component is set to user, we can simply avoid component query
	// may be next iteration someday.
	$args = wp_parse_args( $args, $default );

	$status        = $args['status'];
	$component     = $args['component'];
	$component_id  = $args['component_id'];
	$type          = $args['type'];
	$post_status   = $args['post_status'];
	$object_parent = $args['object_parent'];
	$object_id     = $args['object_id'];

	// is gallery sorted?
	$sorted = $object_parent && psmt_is_gallery_sorted( $object_parent );
	// whether we are looking for next post or previous post.
	if ( $args['next'] ) {
		$op =  $sorted ? '<' : '>';
	} else {
		$op = $sorted ? '>' : '<';
	}

	// do we have a component set.
	if ( $component ) {
		$sql [] = psmt_get_tax_sql( $component, psmt_get_component_taxname() );
	}

	// do we have a component set.
	if ( $status ) {
		$sql [] = psmt_get_tax_sql( $status, psmt_get_status_taxname() );
	}

	// for type, repeat it.
	if ( $type ) {
		$sql [] = psmt_get_tax_sql( $type, psmt_get_type_taxname() );
	}


	/*
	 $term_object_sql = "SELECT object_id FROM (
	  (SELECT DISTINCT value FROM table_a)
	  UNION ALL
	  (SELECT DISTINCT value FROM table_b)
	  ) AS t1 GROUP BY value HAVING count(*) >= 2;
	 */
	$post_type_sql = $wpdb->prepare( "SELECT DISTINCT ID as object_id FROM {$wpdb->posts} WHERE post_type = %s ", $post_type );

	// if a user or group id is given.
	if ( $component_id ) {
		$post_type_sql = $wpdb->prepare( "SELECT DISTINCT p.ID  as object_id FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type= %s  AND pm.meta_key=%s and pm.meta_value=%d", $post_type, '_psmt_component_id', $component_id );
	}

	$post_status_sql = '';

	if ( $post_status && 'any' !== $post_status ) {
		$post_status_sql = $wpdb->prepare( ' AND post_status =%s', $post_status );
	}

	$new_sql = $join_sql = '';
	// let us generate inner sub queries.
	if ( $sql ) {
		$join_sql = ' (' . join( ' AND object_id IN (', $sql );
	}

	$sql_count = count( $sql );
	// we need to append the ) for closing the sub queries.
	for ( $i = 0; $i < $sql_count; $i ++ ) {
		$join_sql .= ')';
	}


	$new_sql = $post_type_sql . $post_status_sql;

	// if the join sql is present, let us append it.
	if ( $join_sql ) {
		$new_sql .= ' AND ID IN ' . $join_sql;
	}

	// for next/prev
	// sorted gallery
	// or by date.
	$post   = get_post( $object_id );

	if ( $sorted ) {
		$new_sql .= $wpdb->prepare( " AND p.menu_order $op %d ", $post->menu_order );
	} else {
		$new_sql .= $wpdb->prepare( " AND p.ID $op %d ", $object_id );
	}

	if ( $object_parent ) {
		$new_sql .= $wpdb->prepare( ' AND post_parent = %d ', $object_parent );
	}

	$oreder_by_clause = '';

	if ( $sorted ) {
		$oreder_by_clause = ' ORDER BY p.menu_order ';
	} else {
		$oreder_by_clause = 'ORDER BY p.ID';
	}

	if ( ! $args['next'] ) {
		// for previous
		// find the last element les than give.
		$oreder_by_clause .= $sorted ? ' ASC ' :' DESC ';
	} else {
		$oreder_by_clause .= $sorted ? ' DESC ' : ' ASC ';
	}

	if ( ! empty( $new_sql ) ) {
		$new_sql .= $oreder_by_clause . ' LIMIT 0, 1';
	}

	return $wpdb->get_var( $new_sql );
}

/**
 * Generate sql for tax queries where terms slugs and taxonomy is given
 *
 * @param array  $terms array of terms.
 * @param string $taxonomy name of taxonomy.
 *
 * @return string|boolean
 */
function psmt_get_tax_sql( $terms, $taxonomy ) {

	// for type, repeat it.
	if ( $terms ) {
		// if the comma separated terms are given, convert it to array.
		$terms = psmt_string_to_array( $terms );

		// get the term_taxonomy ids array for component.
		$term_tax_ids = psmt_get_tt_ids( $terms, $taxonomy );

		$objects_in_terms_sql = psmt_get_objects_in_terms_sql( $term_tax_ids );

		return $objects_in_terms_sql;
	}

	return false;
}

/**
 * Return an array of Terms Ids for given term slugs (an array of slugs can be passed)
 *
 * @param string|array $terms terms.
 * @param string       $taxonomy name of taxonomy the terms belong to.
 *
 * @return array of term ids.
 */
function psmt_get_term_ids( $terms, $taxonomy ) {

	$terms_data = psmt_get_terms_data( $taxonomy );

	if ( ! is_array( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	$ids = array();

	foreach ( $terms as $term ) {

		if ( ! empty( $terms_data[ $term ] ) ) {
			$ids[] = $terms_data[ $term ]->get_id();
		}
	}

	return $ids;
}

/**
 * Get an array of term_taxonomy_ids for the terms under given taxonomy
 *
 * @param array|string $terms array of terms.
 * @param string       $taxonomy name of taxonomy.
 *
 * @return  array
 */
function psmt_get_tt_ids( $terms, $taxonomy ) {

	$terms_data = psmt_get_terms_data( $taxonomy );

	if ( ! is_array( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	$ids = array();

	foreach ( $terms as $term ) {

		if ( ! empty( $terms_data[ $term ] ) ) {
			$ids[] = $terms_data[ $term ]->get_tt_id();
		}
	}

	return $ids;
}

/**
 * Returns sql for finding objects in given term taxonomy ids
 *  We Use it when finding media/galleries for particular term
 *
 * @param array $term_taxonomy_ids array of numeric term taxonomy ids.
 *
 * @return WP_Error|string
 */
function psmt_get_objects_in_terms_sql( $term_taxonomy_ids ) {

	global $wpdb;

	if ( ! is_array( $term_taxonomy_ids ) ) {
		$term_taxonomy_ids = (array) $term_taxonomy_ids;
	}

	$term_taxonomy_ids = array_map( 'intval', $term_taxonomy_ids );


	$term_taxonomy_ids = "'" . implode( "', '", $term_taxonomy_ids ) . "'";

	$sql = "SELECT DISTINCT tr.object_id AS object_id  FROM $wpdb->term_relationships as tr WHERE tr.term_taxonomy_id IN ($term_taxonomy_ids) ";

	return $sql;
}


/**
 * Get all terms data for the given taxonomy.
 *
 * @param string $taxonomy the name of actual WordPress taxonomy we use for various psmt related things.
 *
 * @return PSMT_Taxonomy[]
 */
function psmt_get_terms_data( $taxonomy ) {

	$data = array();

	if ( empty( $taxonomy ) ) {
		return $data;
	}

	switch ( $taxonomy ) {

		case psmt_get_status_taxname():

			$data = psourcemediathek()->statuses;
			break;

		case psmt_get_type_taxname():
			$data = psourcemediathek()->types;
			break;

		case psmt_get_component_taxname():
			$data = psourcemediathek()->components;
			break;
	}

	return $data;
}
