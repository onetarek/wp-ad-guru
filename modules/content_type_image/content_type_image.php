<?php
/**
 * Content Type Image
 * Register new content type "Image". Settings and functions for this contnet type.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type_Image' ) ) : 

class ADGURU_Content_Type_Image extends ADGURU_Content_Type{

	public $type = "image";
	public $name = "Image";
	public $description = "";

	public function __construct(){

		parent::__construct();
		$this->description = __('Link with image', 'adguru');
	}

	protected function _editor_init( $current_ad_type ){
		include_once( dirname(__FILE__)."/form.php");
	}

	protected function _prepare_to_save( $ad , $ad_from_db ){
		$content = array();
		//content_image FORM 
		$content_image_form = adguru()->form_builder->get_form('content_image_form');
		if( $content_image_form )
		{ 
			$content_image_data = array();
			$submitted_data = $content_image_form->prepare_submitted_data();
			foreach( $submitted_data as $id => $value )
			{
				$key = ADGURU_Helper::str_replace_beginning('content_image_', '', $id );
				$content_image_data[$key] = $value;
			}

			$content_image_data = apply_filters('adguru_ad_prepare_to_save_content_image_data', $content_image_data, $submitted_data );
			/*
			Keep old fields those are not exist with current submitted data. 
			We need this because some fields might be added by extension and extension may be deactivated now
			*/
			$content_field = $this->content_field;
			$old_content = $this->get_content( $ad_from_db );
			if( !empty( $old_content ) )
			{
				$content_image_data = array_merge( $old_content, $content_image_data );
			}

		}

		$content = $content_image_data;

		return $content;	

	}//end prepare_ad_to_save

	protected function _editor( $ad, $error_msgs ){
		$content = $this->get_content( $ad );
		echo '<p>'.$this->description.'</p>';
		adguru_show_content_image_form( $content ); 
	}// editor

	protected function _print_content( $ad ){
		$content = $this->get_content( $ad );
		$source_url = isset( $content['source_url'] ) ? $content['source_url'] : "";
		$link_url = isset( $content['link_url'] ) ? $content['link_url'] : "";
		$link_target = isset( $content['link_target'] ) ? $content['link_target'] : "";
		echo '<a href="'.$link_url.'" target="'.$link_target.'" ><img src="'.esc_url( $source_url ).'" class="adguru_content_image" /></a>';
	}


}//end class
endif;

new ADGURU_Content_Type_Image();

