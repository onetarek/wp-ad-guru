<?php
/**
 * A class to handle all input error message
 * @author oneTarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Error' ) ) :

class ADGURU_Error{

	public $ad_input_error;
	public $zone_input_error;
	
	public function __construct(){
		
	}
	
	public function set_ad_input_error( $field , $msg){

		$this->ad_input_error[ $field ] = $msg;
	}
	public function get_ad_input_error(){
		return $this->ad_input_error;
	}	

	public function set_zone_input_error( $field , $msg){

		$this->zone_input_error[ $field ] = $msg;
	}
	public function get_zone_input_error(){
		
		return $this->zone_input_error;
	}		

}

endif;