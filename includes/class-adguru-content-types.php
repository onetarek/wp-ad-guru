<?php
/**
 * A class to handle the process of all content type registration
 * @author oneTarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Types' ) ) :

class ADGURU_Content_Types{
	
	public $types = array();

	
	public function __construct(){
		
		add_action( 'adguru_init_process', array( $this, 'init' ) );
	}

	/**
	 * Collect content_type settings
	 * This function must be called within adguru_init hook
	 */
	public function register( $content_type , $args ){
		if( !array_key_exists( $content_type, $this->types ) )
		{
			if( !isset( $args['slug'] ) || $args['slug'] != $content_type )
			{
				$args['slug'] = $content_type;
			}
			$this->types[ $content_type ] = $args;
		}
		
	}
	
	public function init(){
	
		$this->types = apply_filters( "adguru_content_types" , $this->types );
		//Check valid settings
		$temp_arr = array();
		foreach( $this->types as $type => $args )
		{
			if( !isset( $args['slug'] ) || trim($args['slug'] ) == "" )
			{
				continue;
			}
			if( !isset( $args['name'] ) || trim($args['name'] ) == "" )
			{
				continue;
			}

			$temp_arr[ $type ] = $args;
		}
		$this->types = $temp_arr;
	}
	
	
}//end class

endif;