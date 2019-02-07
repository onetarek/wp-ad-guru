<?php
/**
 * Content Type Parent Class
 * Register new content type . Settings and functions for a contnet type.
 * Extend this class to create new content type.
 * If you want to add new content type from extensions or other plugins, create a child class of this class 
 * and load that child class file with "adguru_plugins_loaded" action hook.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type' ) ) : 

abstract class ADGURU_Content_Type{

	protected $type = "";
	protected $name = "";
	protected $description = "";
	protected $content_field = "";

	public function __construct(){

		add_action( "adguru_init", array( $this, "register") );
		add_action( "adguru_content_editor_init_{$this->type}", array( $this, "editor_init"), 1, 1 );
		add_action( "adguru_content_editor_{$this->type}", array( $this, "editor"), 1, 2 );
		add_filter( "adguru_content_prepare_to_save_{$this->type}", array( $this, "prepare_to_save" ), 10, 2 );
		add_action( "adguru_content_print_{$this->type}", array( $this, "print_content" ) );
	}

	public function register(){

		if( $this->type == "" ){ return ;}
		if( $this->name == "" ){ $this->name = $this->type ;}
		$args = array(
			"slug" => $this->type,
			"name" => $this->name,
			"description" => $this->description
		);
		adguru_register_content_type( $this->type, $args );
		$this->content_field = "content_".$this->type;
	}

	/**
	 * Get content related data array from $ad object for current ad type
	 * @param object $ad ADGURU_Ad object
	 * @return array  
	 */
	public function get_content( $ad ){

		$content_field = $this->content_field;
		return isset( $ad->{$content_field} ) ? $ad->{$content_field} : array();
	}

	/**
	 * Get content related data array from $ad object for current ad type
	 * @param object $ad ADGURU_Ad object
	 * @param array of content data
	 * @return void  
	 */
	public function set_content( $ad, $content_array ){

		if( !is_array($content_array) ){ return ; }
		$content_field = "content_".$this->type;
		$ad->{$content_field} = $content_array;
	}

	/**
	 * Initilize form builder and other things related to content editor. 
	 */
	public function editor_init( $current_ad_type ){

		$this->_editor_init( $current_ad_type );
	}
	protected function _editor_init( $current_ad_type ){

		//Just a fake function. This function should be overridden in child class.
	}

	/**
	 * Run prepare_to_save function and receive content related data
	 * Add content related data to $ad object
	 * We use only one property of $ad object to save all data related to content
	 * the content field name is content_{content_type}
	 * @param object $ad ADGURU_Ad object
	 * @return void
	 */
	public function prepare_to_save( $ad, $ad_from_db ){

		$content_array = $this->_prepare_to_save( $ad, $ad_from_db );
		if( is_array( $content_array ) )
		{
			$this->set_content( $ad, $content_array );
		}
		return $ad;
	}

	public function editor( $ad, $error_msgs ){

		$this->_editor( $ad, $error_msgs );
	}

	public function print_content( $ad ){
		
		$this->_print_content( $ad );
	}

	/**
	 * Check submitted data from $_POST array
	 * Check for input error and set input error
	 * Prepare an array of content related data
	 * modify $ad object if needed
	 * @param object $ad ADGURU_Ad object
	 * @param object or false $ad_from_db ADGURU_Ad object
	 * @return array of content related data
	 * Additional instructions : 
	 * We use only one property of $ad object to save all data related to content.
	 * Create an associative array of content data and return that
	 */
	abstract protected function _prepare_to_save( $ad, $ad_from_db );

	/**
	 * Render the HTML in Ad editor
	 * @param object $ad ADGURU_Ad object
	 * @param array $error_msgs
	 * @return void 
	 * use $content = $this->get_content( $ad ); to get content related data array
	 */
	abstract protected function _editor( $ad, $error_msgs );

	/** 
	 * Print the output of a content
	 * @param object $ad ADGURU_Ad object
	 * @return void
	 * use $content = $this->get_content( $ad ); to get content related data array
	 */
	abstract protected function _print_content( $ad );

}//end class
endif;


