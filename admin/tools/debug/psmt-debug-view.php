<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<style type="text/css">
	#psmt-admin-debug-textarea{
		height: 500px;
	}
</style>
	<h2 class="psmt-admin-title"><?php _e( 'Systeminformationen', 'psourcemediathek' ); ?></h2>

		<div id="psmt-admin-debuf-info-section-wrapper">
			<?php // form tag to avoid invalid html ?>
			<form action="" method="post" enctype="multipart/form-data" >

				<div id="template">
					<label for="psmt-admin-debug-textarea" class="screen-reader-text"><?php _e( 'Systeminformationen kopieren', 'psourcemediathek' ); ?></label>
					<textarea readonly="readonly" onclick="this.focus();this.select()" id="psmt-admin-debug-textarea" name="psmt-admin-debug-textarea" title="<?php _e( 'Um die Systeminformationen zu kopieren, klicke unten und drücke Strg + C (PC) oder Cmd + C (Mac)..', 'psourcemediathek' ); ?>">
<?php //Non standard indentation needed for plain-text display ?>
<?php echo esc_html( $this->display() ) ?>
					</textarea>
				</div>

			</form>

		</div>
</div>
