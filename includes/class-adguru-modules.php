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
                'title'       => __( 'Banner Ads', 'adguru' ),
                'description' => __( 'Banner Ads Management', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/banner/banner.php",
            ),			
            'modal_popup' => array(
                'title'       => __( 'Modal Popups', 'adguru' ),
                'description' => __( 'Modal Popups Management', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/modal_popup/main.php",
            ),
            'window_popup' => array(
                'title'       => __( 'Window Popups', 'adguru' ),
                'description' => __( 'Window Popups Management', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/window_popup/window_popup.php",
            ),
            'content_type_html' => array(
                'title'       => __( 'Content Type : HTML', 'adguru' ),
                'description' => __( 'Any HTML and JavaScript code', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_html/content_type_html.php",
            ),
            'content_type_image' => array(
                'title'       => __( 'Content Type : Image', 'adguru' ),
                'description' => __( 'Link with image', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_image/content_type_image.php",
            ),
            'content_type_iframe' => array(
                'title'       => __( 'Content Type : iFrame', 'adguru' ),
                'description' => __( 'Link in iFrame', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_iframe/content_type_iframe.php",
            ),
            'content_type_wysiwyg' => array(
                'title'       => __( 'Content Type : WYSIWYG', 'adguru' ),
                'description' => __( 'Create content using WYSIWYG editor', 'adguru' ),
                'file'    => ADGURU_PLUGIN_DIR."modules/content_type_wysiwyg/content_type_wysiwyg.php",
            ),
            'content_type_url' => array(
                'title'       => __( 'Content Type : URL', 'adguru' ),
                'description' => __( 'Set url for window popup', 'adguru' ),
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
