<?php
/**
 * Media size control field.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Media size setting with hight, width, crop options.
 */
class PSMT_Admin_Settings_Field_Media_Size extends PSMT_Admin_Settings_Field {

	/**
	 * Used as key in settings.
	 *
	 * @var string
	 */
	private $key = '';

	/**
	 * Internal option name.
	 *
	 * @var string
	 */
	private $_option_name;

	/**
	 * Any extra data for the field.
	 *
	 * @var mixed|array
	 */
	private $extra;

	/**
	 * Field Constructor
	 *
	 * @param array $field field settings.
	 */
	public function __construct( $field ) {

		parent::__construct( $field );
	}

	/**
	 * Override name.
	 *
	 * @return string
	 */
	public function get_name() {
		return parent::get_name() ;//. '-' . $this->key;
	}

	/**
	 * Render the field on settings page.
	 *
	 * @param array $args callback args.
	 */
	public function render( $args ) {
		$this->callback_media_size( $args );
	}

	/**
	 * Callback for rendering media size field in settings screen.
	 *
	 * @param array $args callback render args.
	 */
	public function callback_media_size( $args ) {

		$value = $args['value'];

		$options = $this->get_options();

		if ( empty( $value ) ) {
			$value = $options;
		}

		$crop = empty( $value['crop'] ) ? 0 : 1;
		$width = absint( $value['width'] );
		$height = absint( $value['height'] );
		echo '<div class="psmt-media-size-field-wrapper">';
			printf( '<label for="%1$s[width]" class="psmt-settings-media-size-field-label">%2$s</label> <input type="number" class="psmt-settings-media-size-field" id="%1$s[width]" name="%1$s[width]" value="%3$s" />', esc_attr( $args['option_key'] ),_x( 'Breite:', 'Admin Einstellungen für Mediengröße', 'psourcemediathek'), $width );
			printf( '<label for="%1$s[height]" class="psmt-settings-media-size-field-label">%2$s</label> <input type="number" class="psmt-settings-media-size-field" id="%1$s[height]" name="%1$s[height]" value="%3$s" />', esc_attr( $args['option_key'] ),_x( 'Höhe:', 'Admin Einstellungen für Mediengröße', 'psourcemediathek'), $height );
			printf( '<label for="%1$s[crop]" class="psmt-settings-media-size-field-label">%2$s</label><input type="checkbox" class="psmt-settings-media-size-field" id="%1$s[crop]" name="%1$s[crop]" value="1" %3$s/>',esc_attr( $args['option_key'] ), _x('Zuschneiden:', 'Admin Einstellungen für Mediengröße', 'psourcemediathek' ), checked( $crop, 1, false ) );
		echo'</div>';
		printf( '<span class="description"> %s </span>', $this->get_desc() );
	}

}
