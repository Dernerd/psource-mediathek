<?php
/**
 * Oembed Media in Lightbox.
 *
 * @package    PsourceMediathek
 * @subpackage templates/default
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

$media = psmt_get_current_media();
if ( ! $media ) {
	return;
}
echo psmt_get_oembed_content( $media->id );
