<?php
/**
 * Content Type WYSIWYG
 * Register new content type "WYSIWYG". Settings and functions for this contnet type.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type_Wysiwyg' ) ) : 

class ADGURU_Content_Type_Wysiwyg extends ADGURU_Content_Type{

	public $type = "wysiwyg";
	public $name = "WYSIWYG Editor";
	public $description = "Create content using WYSIWYG editor";

	public function __construct(){

		parent::__construct();
		$this->description = __('Create content using WYSIWYG editor', 'adguru');
	}

	protected function _editor_init( $current_ad_type ){

		include_once( dirname(__FILE__)."/form.php");
	}
	
	protected function _prepare_to_save( $ad , $ad_from_db){

		$content = array();
		//content_wysiwyg FORM 
		$content_wysiwyg_form = adguru()->form_builder->get_form('content_wysiwyg_form');
		if( $content_wysiwyg_form )
		{ 
			$content_wysiwyg_data = array();
			$submitted_data = $content_wysiwyg_form->prepare_submitted_data();
			foreach( $submitted_data as $id => $value )
			{
				$key = ADGURU_Helper::str_replace_beginning('content_wysiwyg_', '', $id );
				$content_wysiwyg_data[$key] = $value;
			}

			$content_wysiwyg_data = apply_filters('adguru_ad_prepare_to_save_content_wysiwyg_data', $content_wysiwyg_data, $submitted_data );
			/*
			Keep old fields those are not exist with current submitted data. 
			We need this because some fields might be added by extension and extension may be deactivated now
			*/
			$content_field = $this->content_field;
			$old_content = $this->get_content( $ad_from_db );
			if( !empty( $old_content ) )
			{
				$content_wysiwyg_data = array_merge( $old_content, $content_wysiwyg_data );
			}

		}

		$content = $content_wysiwyg_data;

		return $content;	

	}//end prepare_ad_to_save

	protected function _editor( $ad, $error_msgs ){

		$content = $this->get_content( $ad );
		echo '<p>'.$this->description.'</p>';
		adguru_show_content_wysiwyg_form( $content ); 
	}// editor

	protected function _print_content( $ad ){
		
		$content = $this->get_content( $ad );
		$own_html = isset( $content['html'] ) ? $content['html'] : "";
		echo apply_filters('the_content', $own_html );
	}


}//end class
endif;

new ADGURU_Content_Type_Wysiwyg();

