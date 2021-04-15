<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Groups Component Gallery list(PsourceMediathek landing page) template
 *  Used by /groups/group_name/psourcemediathek/
 */
?>
<div role="navigation" id="subnav" class="item-list-tabs no-ajax psmt-group-nav">
	<ul>
		<?php do_action( 'psmt_group_nav' ); ?>
	</ul>
</div>
<div class="psmt-container psmt-clearfix" id="psmt-container">

	<div class="psmt-breadcrumbs psmt-clearfix"><?php psmt_gallery_breadcrumb(); ?></div>
	<?php
	if ( psmt_user_can_view_storage_stats( bp_loggedin_user_id(), 'groups', bp_get_current_group_id() ) ) {
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
		$template = 'buddypress/groups/gallery/manage.php';
	} elseif ( psmt_is_media_management() ) {
		$template = 'buddypress/groups/media/manage.php';
	} elseif ( psmt_is_single_media() ) {
		$template = 'buddypress/groups/media/single.php';
	} elseif ( psmt_is_single_gallery() ) {
		$template = 'buddypress/groups/gallery/single.php';
	} elseif ( psmt_is_gallery_home() ) {
		$template = 'gallery/loop-gallery.php';
	} else {
		$template = 'gallery/404.php';// not found.
	}

	$template = psmt_locate_template( array( $template ), false );

	$template = apply_filters( 'psmt_groups_gallery_located_template', $template );

	if ( is_readable( $template ) ) {
		include $template;
	}
	unset( $template );
	?>
</div>  <!-- end of psmt-container -->
