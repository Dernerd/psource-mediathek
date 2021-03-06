<?php
/**
 * Image setting field.
 *
 * @package psourcemediathek/settings/fields
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Image field type.
 */
class PSMT_Admin_Settings_Field_Image extends PSMT_Admin_Settings_Field {

	/**
	 * Constructor.
	 *
	 * @param array $field field details array.
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
	}

	/**
	 * Render field ui.
	 *
	 * @param array $args args.
	 */
	public function render( $args ) {
	    // Load js.
		wp_enqueue_media();
		wp_enqueue_script( 'psmt_settings_uploader' );

		// attachment url.
		$value = esc_attr( $args['value'] );
		// $size  = $this->get_size();
		// css class.
		$class = '';
		// we need to show this image.
		if ( $value ) {
			$image = "<img src='{$value}' />";
		} else {

			$image = "<img src='' />";
		}

		$id = $args['option_key'];

		?>

        <div class='settings-image-placeholder'>
			<?php
			if ( $value ) {
				$class = 'settings-image-action-visible';
			}

			echo $image;
			?>
            <br/>
            <a href="#" class="delete-settings-image <?php echo $class; ?>"><?php _e( 'Entfernen' ); ?></a>
        </div>

		<?php
		$btn_label      = _x( 'Wählen', 'psourcemediathek settings image field select button label', 'psourcemediathek' );
		$browse_label   = _x( 'Durchsuche', 'psourcemediathek settings image field browse', 'psourcemediathek' );
		$uploader_title = _x( 'Auswählen', 'psourcemediathek settings image field uploader window title', 'psourcemediathek' );

		echo "<input type='hidden' class='hidden-image-url' id='{$id}' name='{$id}' value='{$value}'/>";
		echo "<input type='button' class='button settings-upload-image-button' id='{$id}_button' value='{$browse_label}' data-id='{$id}' data-btn-title='{$btn_label}' data-uploader-title='{$uploader_title}' />";

		echo '<span class="description">' . $this->get_desc() . '</span>';

	}
}
