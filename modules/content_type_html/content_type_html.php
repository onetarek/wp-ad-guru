<?php
/**
 * Content Type HTML
 * Register new content type "HTML". Settings and functions for this contnet type.
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Content_Type_Html' ) ) : 

class ADGURU_Content_Type_Html extends ADGURU_Content_Type{

	public $type = "html";
	public $name = "HTML";
	public $description = "";

	public function __construct(){

		parent::__construct();
		$this->description = __('Any HTML and JavaScript code, JavaScript code must be wrapped with &lt;script&gt; tag.', 'adguru');
	}

	protected function _editor_init( $current_ad_type ){

		include_once( dirname(__FILE__)."/form.php");
	}

	protected function _prepare_to_save( $ad, $ad_from_db ){

		$content = array();
		//content_html FORM 
		$content_html_form = adguru()->form_builder->get_form('content_html_form');
		if( $content_html_form )
		{ 
			$content_html_data = array();
			$submitted_data = $content_html_form->prepare_submitted_data();
			foreach( $submitted_data as $id => $value )
			{
				$key = ADGURU_Helper::str_replace_beginning('content_html_', '', $id );
				$content_html_data[$key] = $value;
			}

			$content_html_data = apply_filters('adguru_ad_prepare_to_save_content_html_data', $content_html_data, $submitted_data );
			/*
			Keep old fields those are not exist with current submitted data. 
			We need this because some fields might be added by extension and extension may be deactivated now
			*/
			$content_field = $this->content_field;
			$old_content = $this->get_content( $ad_from_db );
			if( !empty( $old_content ) )
			{
				$content_html_data = array_merge( $old_content, $content_html_data );
			}

		}

		$content = $content_html_data;

		return $content;	

	}//end prepare_ad_to_save

	protected function _editor( $ad, $error_msgs ){

		$content = $this->get_content( $ad );
		echo '<p>'.$this->description.'</p>';
		adguru_show_content_html_form( $content ); 
	}// editor

	public function _print_content( $ad ){

		$content = $this->get_content( $ad );
		echo isset( $content['html'] )? $content['html'] : "";
	}

}//end class
endif;

new ADGURU_Content_Type_Html();

