<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 *
 * Single photo view
 *
 */
$media = psmt_get_current_media();
?>

<img src="<?php psmt_media_src( psmt_get_selected_single_media_size(), $media ); ?>" alt="<?php psmt_media_title( $media ); ?>" class="psmt-large"/>