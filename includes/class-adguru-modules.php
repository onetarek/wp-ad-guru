<?php
/**
 * Modules Class
 *
 * @package WP AD GURU
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Modules' ) ) :

class ADGURU_Modules {

    /**
     * Hold the modules info
     *
     * @var
     */
    public $modules;

    /**
     * initialize
     */
    public function __construct(){

        $this->init();
		$this->load();
    }

    /**
     * Initialize the modules
     *
     * @return void
     */
    private function init(){

        $this->modules = array(
            'banner' => array(
                'title'       => __( 'Banner Ads', 'wp-ad-guru' ),
                'description' => __( 'Banner Ads Management', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/banner/banner.php",
            ),			
            'modal_popup' => array(
                'title'       => __( 'Modal Popups', 'wp-ad-guru' ),
                'description' => __( 'Modal Popups Management', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/modal_popup/main.php",
            ),
            'window_popup' => array(
                'title'       => __( 'Window Popups', 'wp-ad-guru' ),
                'description' => __( 'Window Popups Management', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/window_popup/window_popup.php",
            ),
            'content_type_html' => array(
                'title'       => __( 'Content Type : HTML', 'wp-ad-guru' ),
                'description' => __( 'Any HTML and JavaScript code', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_html/content_type_html.php",
            ),
            'content_type_image' => array(
                'title'       => __( 'Content Type : Image', 'wp-ad-guru' ),
                'description' => __( 'Link with image', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_image/content_type_image.php",
            ),
            'content_type_iframe' => array(
                'title'       => __( 'Content Type : iFrame', 'wp-ad-guru' ),
                'description' => __( 'Link in iFrame', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_iframe/content_type_iframe.php",
            ),
            'content_type_wysiwyg' => array(
                'title'       => __( 'Content Type : WYSIWYG', 'wp-ad-guru' ),
                'description' => __( 'Create content using WYSIWYG editor', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_wysiwyg/content_type_wysiwyg.php",
            ),
            'content_type_url' => array(
                'title'       => __( 'Content Type : URL', 'wp-ad-guru' ),
                'description' => __( 'Set url for window popup', 'wp-ad-guru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_url/content_type_url.php",
            )			
			
        );
    }
	
	/**
	 * Load Module Files
	 */
	private function load(){
        
		foreach( $this->modules as $key => $module)
        {
			if( file_exists( $module['file'] ) ){ require_once $module['file']; }
		}
	
	} 


}//END CLASS
endif;
