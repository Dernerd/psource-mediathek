<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="psmt-container psmt-clearfix psmt-members-component" id="psmt-container">
	<div class="psmt-breadcrumbs psmt-clearfix"><?php psmt_gallery_breadcrumb(); ?></div>
	<?php
	if ( psmt_user_can_view_storage_stats( bp_loggedin_user_id(), 'members', bp_displayed_user_id() ) ) {
		psmt_display_space_usage();
	}
	?>
	<?php
	// IMPORTANT: Template loading
	// Please do not modify the code below unless you know what you are doing.
	$template = '';
	if ( psmt_is_gallery_create() ) {
		$template = 'gallery/create.php';
	} elseif ( psmt_is_gallery_management() ) {
		$template = 'buddypress/members/gallery/manage.php';
	} elseif ( psmt_is_media_management() ) {
		$template = 'buddypress/members/media/manage.php';
	} elseif ( psmt_is_single_media() ) {
		$template = 'buddypress/members/media/single.php';
	} elseif ( psmt_is_single_gallery() ) {
		$template = 'buddypress/members/gallery/single.php';
	} elseif ( psmt_is_gallery_home() ) {
		$template = 'gallery/loop-gallery.php';
	} else {
		$template = 'gallery/404.php';// not found.
	}

	$template = psmt_locate_template( array( $template ), false );
	// filter on located template.
	$template = apply_filters( 'psmt_member_gallery_located_template', $template );

	if ( is_readable( $template ) ) {
		include $template;
	}
	unset( $template );
	// you can modify anything after this.
	?>
</div>  <!-- end of psmt-container -->
