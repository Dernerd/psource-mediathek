<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php do_action( 'bp_before_directory_psourcemediathek_page' ); ?>

<div id="buddypress" class="psmt-directory-contents">

	<?php do_action( 'bp_before_directory_psourcemediathek_items' ); ?>

	<div id="psmt-dir-search" class="dir-search" role="search">
		<?php psmt_directory_gallery_search_form(); ?>
	</div><!-- #psmt-dir-search -->

	<?php do_action( 'psmt_before_directory_gallery_tabs' ); ?>

	<form action="" method="post" id="psmt-directory-form" class="dir-form">

		<div class="item-list-tabs" role="navigation">
			<ul>
				<li class="selected" id="psmt-all">
					<a href="<?php echo get_permalink( buddypress()->pages->psourcemediathek->id ); ?>"><?php printf( __( 'Alle Galerien <span>%s</span>', 'psourcemediathek' ), psmt_get_total_gallery_count() ) ?></a>
				</li>

                <?php do_action( 'psmt_directory_types' ) ?>

				<li id="psmt-order-select" class="last filter">

					<?php _e( 'Filtern nach:', 'psourcemediathek' ) ?>
					<select>
						<option value=""><?php _e( 'Alle Galerien', 'psourcemediathek' ) ?></option>

						<?php $active_types = psmt_get_active_types(); ?>

						<?php foreach( $active_types as $type => $type_object ):?>
							<option value="<?php echo $type;?>"><?php echo $type_object->get_label();?> </option>
						<?php endforeach;?>
							
						<?php do_action( 'psmt_gallery_directory_order_options' ) ?>
					</select>
				</li>
					
			</ul>

			
		</div><!-- .item-list-tabs -->

		<div id="psmt-dir-list" class="psmt psmt-dir-list dir-list">
			<?php
				psmt_get_template( 'gallery/loop-gallery.php' );
			?>
		</div><!-- #psmt-dir-list -->

		<?php do_action( 'psmt_directory_gallery_content' ); ?>

		<?php wp_nonce_field( 'directory_psmt', '_wpnonce-psmt-filter' ); ?>

		<?php do_action( 'psmt_after_directory_gallery_content' ); ?>

	</form><!-- #psmt-directory-form -->

	<?php do_action( 'psmt_after_directory_gallery' ); ?>

</div><!-- #buddypress -->

<?php do_action( 'psmt_after_directory_gallery_page' ); ?>
