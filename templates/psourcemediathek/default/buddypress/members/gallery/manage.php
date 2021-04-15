<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This template loads appropriate template file for the current Edit gallery or Edit media action
 */

?>

<div class="psmt-menu psmt-menu-open  psmt-menu-horizontal psmt-gallery-admin-menu">
	<?php psmt_gallery_admin_menu( psmt_get_current_gallery(), psmt_get_current_edit_action() ); ?>
</div>
<hr/>
<?php do_action( 'psmt_after_gallery_admin_menu' ); ?>
<?php
$template = '';
if ( psmt_is_gallery_add_media() ) {
	$template = 'gallery/manage/add-media.php';
} elseif ( psmt_is_gallery_edit_media() ) {
	$template = 'gallery/manage/edit-media.php';
} elseif ( psmt_is_gallery_reorder_media() ) {
	$template = 'gallery/manage/reorder-media.php';
} elseif ( psmt_is_gallery_settings() ) {
	$template = 'gallery/manage/settings.php';
} elseif ( psmt_is_gallery_delete() ) {
	$template = 'gallery/manage/delete.php';
}

$template = apply_filters( 'psmt_get_gallery_management_template', $template );
// load it.
if ( $template ) {
	psmt_get_template( $template );
}
unset( $template );// do not let the global litter.
do_action( 'psmt_load_gallery_management_template' );
