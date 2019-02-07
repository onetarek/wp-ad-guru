<?php
/**
 * Content Type iFrame
 * Register new content type "iFrame". Settings and functions for this contnet type.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type_Iframe' ) ) : 

class ADGURU_Content_Type_Iframe extends ADGURU_Content_Type{

	public $type = "iframe";
	public $name = "iFrame";
	public $description = "";

	public function __construct(){

		parent::__construct();
		$this->description = __('Show any webpage in an iFrame', 'adguru');
	}

	protected function _editor_init( $current_ad_type ){

		include_once( dirname(__FILE__)."/form.php");
	}

	protected function _prepare_to_save( $ad, $ad_from_db ){

		$content = array();
		//content_iframe FORM 
		$content_iframe_form = adguru()->form_builder->get_form('content_iframe_form');
		if( $content_iframe_form )
		{ 
			$content_iframe_data = array();
			$submitted_data = $content_iframe_form->prepare_submitted_data();
			foreach( $submitted_data as $id => $value )
			{
				$key = ADGURU_Helper::str_replace_beginning('content_iframe_', '', $id );
				$content_iframe_data[$key] = $value;
			}

			$content_iframe_data = apply_filters('adguru_ad_prepare_to_save_content_iframe_data', $content_iframe_data, $submitted_data );
			/*
			Keep old fields those are not exist with current submitted data. 
			We need this because some fields might be added by extension and extension may be deactivated now
			*/
			$content_field = $this->content_field;
			$old_content = $this->get_content( $ad_from_db );
			if( !empty( $old_content ) )
			{
				$content_iframe_data = array_merge( $old_content, $content_iframe_data );
			}

		}

		$content = $content_iframe_data;

		return $content;	

	}//end prepare_ad_to_save

	protected function _editor( $ad, $error_msgs ){

		$content = $this->get_content( $ad );
		echo '<p>'.$this->description.'</p>';
		adguru_show_content_iframe_form( $content );
	}// editor

	protected function _print_content( $ad ){
		
		$content = $this->get_content( $ad );
		$source_url = isset( $content['source_url'] ) ? $content['source_url'] : "";
		$scrolling = isset( $content['scrolling'] ) ? $content['scrolling'] : "yes";
		echo '<iframe src="'.esc_attr( $source_url ).'" scrolling="'.esc_attr($scrolling).'" frameborder="0" ></iframe>';
	}


}//end class
endif;

new ADGURU_Content_Type_Iframe();

