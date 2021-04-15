<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//PsourceMediathek page not found template

?>
<?php do_action( 'psmt_before_404' ); ?>
	<div class="psmt-container">
		<div class="psmt-error">
			<h3><?php _e( 'Entschuldigung, hier ist nichts zu sehen!', 'psourcemediathek' ); ?></h3>
		</div>
	</div>
<?php do_action( 'psmt_after_404' ); ?>