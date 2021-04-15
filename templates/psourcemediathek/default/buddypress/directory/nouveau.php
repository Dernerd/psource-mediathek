<?php
/**
 * PsourceMediathek directory template for BP Nouveau template pack.
 */
// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;
?>
<nav class="psmt-type-navs main-navs bp-navs dir-navs " role="navigation" >
	<ul class="component-navigation psmt-nav">
		<li id="psmt-all" class="selected" data-bp-scope="all" data-bp-object="psmt">
			<a href="<?php echo esc_url( get_permalink( buddypress()->pages->psourcemediathek->id ) ); ?>">
				<?php printf( __( 'Alle Galerien <span class="count">%s</span>', 'psourcemediathek' ), psmt_get_total_gallery_count() ) ?>
			</a>
		</li>
		<?php do_action( 'psmt_directory_types' ) ?>

	</ul><!-- .component-navigation -->
</nav><!-- end of nav -->

<div class="screen-content">

	<div class="subnav-filters filters no-ajax" id="subnav-filters">

		<div class="subnav-search clearfix">
			<div id="psmt-dir-search" class="dir-search psmt-search bp-search" data-bp-search="psmt">
				<form action="" method="get" class="bp-dir-search-form" id="dir-psmt-search-form" role="search">

					<label for="dir-psmt-search" class="bp-screen-reader-text"><?php _e( 'Galerien durchsuchen...', 'psourcemediathek' );?></label>

					<input id="dir-psmt-search" name="psmt_search" type="search" placeholder="<?php echo  esc_attr( __( 'Galerien durchsuchen...', 'psourcemediathek' ) );?>">

					<button type="submit" id="dir-psmt-search-submit" class="nouveau-search-submit" name="dir_psmt_search_submit">
						<span class="dashicons dashicons-search" aria-hidden="true"></span>
						<span id="button-text" class="bp-screen-reader-text"><?php _e( 'Suche', 'psourcemediathek' );?></span>
					</button>

				</form>
			</div><!-- #psmt-dir-search -->

		</div>

		<div id="comp-filters" class="component-filters clearfix">
			<div id="psmt-order-select" class="last filter">
				<label for="psmt-order-by" class="bp-screen-reader-text">
					<span><?php _e( 'Filtern nach:', 'psourcemediathek' ) ?></span>
				</label>
				<div class="select-wrap">

						<select id="psmt-order-by" data-bp-filter="psmt">
							<option value=""><?php _e( 'Alle Galerien', 'psourcemediathek' ) ?></option>

							<?php $active_types = psmt_get_active_types(); ?>

							<?php foreach( $active_types as $type => $type_object ):?>
								<option value="<?php echo esc_attr( $type );?>"><?php echo $type_object->get_label();?> </option>
							<?php endforeach;?>

							<?php do_action( 'psmt_gallery_directory_order_options' ) ?>
						</select>

					<span class="select-arrow" aria-hidden="true"></span>
				</div>
			</div>
		</div><!-- end of filter -->

	</div><!-- search & filters -->

	<div id="psmt-dir-list" class="psmt dir-list" data-bp-list="psmt">
		<?php psmt_get_template( 'gallery/loop-gallery.php' ); ?>
	</div><!-- #psmt-dir-list -->

</div>