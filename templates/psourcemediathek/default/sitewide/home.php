<?php
/**
 * @package PsourceMediathek
 *
 * Sitewide gallery single gallery home page
 */
?>
<?php

$gallery = psmt_get_current_gallery();
if ( psmt_user_can_upload( $gallery->component, $gallery->component_id, $gallery ) ) :?>
	<div class="psmt-menu psmt-menu-open  psmt-menu-horizontal psmt-gallery-admin-menu">
		<?php psmt_gallery_admin_menu( psmt_get_current_gallery(), psmt_get_current_edit_action() ); ?>
	</div>
	<hr/>
<?php endif; ?>
<div class="psmt-container psmt-clearfix psmt-sitewide-component" id="psmt-container">
	<div class="psmt-breadcrumbs"><?php psmt_gallery_breadcrumb(); ?></div>
	<?php
	if ( is_super_admin() ) {
		psmt_display_space_usage();
	}
	?>
	<?php
	// main file loaded by PsourceMediathek
	// it loads the requested file.
	$template = '';
	if ( psmt_is_gallery_create() ) {
		$template = 'gallery/create.php';
	} elseif ( psmt_is_gallery_management() ) {
		$template = 'sitewide/gallery/manage.php';
	} elseif ( psmt_is_media_management() ) {
		$template = 'sitewide/media/manage.php';
	} elseif ( psmt_is_single_media() ) {
		$template = 'sitewide/media/single.php';
	} elseif ( psmt_is_single_gallery() ) {
		$template = 'sitewide/gallery/single.php';
	} elseif ( psmt_is_gallery_home() ) {
		$template = 'gallery/loop-gallery.php';
	} else {
		$template = 'gallery/404.php';// not found.
	}

	$template = apply_filters( 'psmt_get_sitewide_gallery_template', $template );

	psmt_get_template( $template );
	unset( $template );

	?>
	<?php setup_postdata( psmt_get_current_gallery() ); ?>
</div>  <!-- end of psmt-container -->
