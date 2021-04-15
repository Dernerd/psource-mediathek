<?php
/**
 * Oembed Media View
 *
 * @package    PsourceMediathek
 * @subpackage templates/default
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.4.0
 */

$media = psmt_get_current_media();
if ( ! $media ) {
	return;
}
echo psmt_get_oembed_content( $media->id );
