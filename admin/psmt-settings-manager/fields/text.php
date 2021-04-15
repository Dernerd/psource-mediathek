<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * For example
 * Here is the text field rendering
 */
class PSMT_Admin_Settings_Field_Text extends PSMT_Admin_Settings_Field {
    
    
    public function __construct( $field ) {
		
        parent::__construct( $field );
    }
    
    
    public function render( $args ) {
		
        parent::render( $args );
    }
}
