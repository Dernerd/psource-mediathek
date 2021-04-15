<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="psmt-menu psmt-menu-open psmt-menu-horizontal psmt-media-admin-menu">

	<?php psmt_media_menu( psmt_get_current_media(), psmt_get_current_edit_action() ); ?>
</div>
<hr/>
<?php
$template = '';
if ( psmt_is_media_delete() ) {
	$template = 'gallery/media/manage/delete.php';
} elseif ( psmt_is_media_management() ) {
	$template = 'gallery/media/manage/edit.php';
}

$template = apply_filters( 'psmt_get_media_management_template', $template );
// load it.
if ( $template ) {
	psmt_get_template( $template );
}
unset( $template );// don't let the global litter unintentionally.

do_action( 'psmt_load_media_management_template' );
