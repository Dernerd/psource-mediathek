<?php
class PSMT_Media_Debugger {


	/**
	 * PSMT_Media_Debugger constructor.
	 */
	public function __construct() {

		$this->setup();

	}

	private function setup() {
		add_filter( 'attachment_fields_to_edit', array( $this, 'info' ), 10, 2 );
	}

	public function info( $fields, $post ) {

		$html = $this->get_html( $post );

		$fields['psmt-media-debug-info'] = array(
			'label'     => __( 'Debug-Informationen', 'psourcemediathek' ),
			'input'     => 'html',
			'html'      => $html,
		);

		return $fields;
	}


	public function get_html( $media ) {

		$media = psmt_get_media( $media );

		$html = '<table>';
		$html .='<tr><th>' . __( 'Component', 'psourcemediathek' ) . '</th><td>' . $media->component .'</td></tr>';
		$html .='<tr><th>' . __( 'Type', 'psourcemediathek' ) . '</th><td>' . psmt_get_type_singular_name( $media->type ) .'</td></tr>';
		$html .='<tr><th>' . __( 'Status', 'psourcemediathek' ) . '</th><td>' . $media->status .'</td></tr>';
		$html .='<tr><th>' . __( 'Ist verwaistes Medium', 'psourcemediathek' ) . '</th><td>' . $this->get_yesno( $media->is_orphan ) .'</td></tr>';
		$html .='<tr><th>' . __( 'Speichermethode', 'psourcemediathek' ) . '</th><td>' . psmt_get_storage_method( $media->id ) .'</td></tr>';
		$html .='<tr><th>' . __( 'Gültige Medien?', 'psourcemediathek' ) . '</th><td>' . $this->get_yesno( $media->is_psmt_media ) .'</td></tr>';

		$html .= '</table>';

		return $html;
	}

	public function get_yesno( $anything ) {

		if ( $anything ) {
			return __ ( 'Ja', 'mdiapress' );
		}

		return __( 'Nein', 'psourcemediathek' );
	}
}

new PSMT_Media_Debugger();