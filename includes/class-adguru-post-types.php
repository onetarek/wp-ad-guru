<?php
/**
 * ADGURU Post Types Class
 * A class to handle the process of all post types registration 
 *
 * @package WP AD GURU
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Post_Types' ) ) :

class ADGURU_Post_Types{
	
	/**
	 * Store the list of wp post types name used by ADGURU itself 
	 */	
	public $types = array();

	
	public function __construct(){
		
	}
	
	/**
	 * Just a wrapper of register_post_type() function
	 */
	public function register( $post_type , $args ){
		
		register_post_type( $post_type, $args );
		$this->add_type( $post_type );
		
	}
	
	public function add_type( $post_type ){
		$this->types[] = $post_type ;
	}
	

}//end class

endif;