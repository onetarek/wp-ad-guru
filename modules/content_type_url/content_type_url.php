<?php
/**
 * Content Type URL
 * Register new content type "url". Settings and functions for this contnet type.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type_Url' ) ) : 

class ADGURU_Content_Type_Url extends ADGURU_Content_Type{

	public $type = "url";
	public $name = "URL";
	public $description = "";

	public function __construct(){

		parent::__construct();
		$this->description = __('Set url for window popup', 'adguru');
	}

	protected function _editor_init( $current_ad_type ){
		
		include_once( dirname(__FILE__)."/form.php");
	}
	
	protected function _prepare_to_save( $ad , $ad_from_db ){

		$content = array();
		//content_url FORM 
		$content_url_form = adguru()->form_builder->get_form('content_url_form');
		if( $content_url_form )
		{ 
			$content_url_data = array();
			$submitted_data = $content_url_form->prepare_submitted_data();
			foreach( $submitted_data as $id => $value )
			{
				$key = ADGURU_Helper::str_replace_beginning('content_url_', '', $id );
				$content_url_data[$key] = $value;
			}

			$content_url_data = apply_filters('adguru_ad_prepare_to_save_content_url_data', $content_url_data, $submitted_data );
			/*
			Keep old fields those are not exist with current submitted data. 
			We need this because some fields might be added by extension and extension may be deactivated now
			*/
			$content_field = $this->content_field;
			$old_content = $this->get_content( $ad_from_db );
			if( !empty( $old_content ) )
			{
				$content_url_data = array_merge( $old_content, $content_url_data );
			}

		}

		$content = $content_url_data;

		return $content;	

	}//end prepare_ad_to_save

	protected function _editor( $ad, $error_msgs ){

		$content = $this->get_content( $ad );
		echo '<p>'.$this->description.'</p>';
		adguru_show_content_url_form( $content ); 
	}// editor

	protected function _print_content( $ad ){

		$content = $this->get_content( $ad );
		//this type of content does not have any output.
	}


}//end class
endif;

new ADGURU_Content_Type_Url();

