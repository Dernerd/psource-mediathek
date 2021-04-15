<?php
/**
 * Log functions.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Log an entry.
 *
 * @param array $args log fields.
 *
 * @return int|bool
 */
function psmt_log( $args ) {
	return psmt_get_logger()->log( $args );
}

/**
 * Increments the log field value by given number.
 *
 * @param array $args log fields.
 * @param int   $by how many.
 *
 * @return int|boolean
 */
function psmt_incremental_log( $args, $by = 1 ) {
	return psmt_get_logger()->increment( $args, $by );
}

/**
 * Delete all logs
 *
 * @param array $args args.
 *
 * @return bool
 */
function psmt_delete_logs( $args ) {
	return psmt_get_logger()->delete( $args );
}

/**
 * Check if a give log exists
 * If log eists, return the log row else false.
 *
 * @param array $args args.
 *
 * @return bool
 */
function psmt_log_exists( $args ) {

	return psmt_get_logger()->log_exists( $args );
}

/**
 * Get all logs
 *
 * @param array $args associative array.
 *
 * @type int id Log Id
 * @type int $user_id User whose log we want to fetch
 * @type int $item_id
 * @type string $action
 * @type string $value
 *
 * @return array|null
 */
function psmt_get_logs( $args ) {

	return psmt_get_logger()->get( $args );
}
