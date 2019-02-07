<?php
/**
 * Main Class
 * @package WP Admin Form Builder
 * @author oneTarek
 * @since 1.0.0
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( "WPAFB_Main" ) ):
final class WPAFB_Main{
	
	/**
	 * @var instance of WPAFB_Main
	 */
	private static $instance;
	
	/**
	 *@var array of all form instance
	 */
	private $forms = array();

	public function __construct(){

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action("admin_footer", array( $this, "admin_footer") );
	}

	/**
	 * Create instance of WPAFB_Form class , store $forms array and returns new instance\
	 * If form has already been created and stored in $forms array then return form there instead of creating new one.
	 * @param array $args of new form
	 * @return false or object. if there is no form id provided in $args array , returns false otherwise returns instance of WPAFB_Form class. 
	 */

	public function create_form( $args ){

		if( !isset( $args['id'] ) || trim( $args['id'] ) == "" )
		{
			return false;
		}
		$form_id = trim( $args['id'] );
		if( isset( $this->forms[$form_id] ) )
		{
			return $this->forms[$form_id];
		}
		$this->forms[$form_id] = new WPAFB_Form( $args );
		return $this->forms[$form_id];
	}

	/**
	 * Get previously created form
	 * @param string $formid
	 * @return false or object of WPAFB_Form class. if there is no form created before for the provided id , return false.  
	 */
	public function get_form( $id ){

		if( $id == "" )
		{
			return false;
		}
		if( isset( $this->forms[$id] ) && $this->forms[$id] instanceof WPAFB_Form )
		{
			return $this->forms[$id];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Main WPAFB_Main Instance.
	 *
	 * Insures that only one instance of WPAFB_Main exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0.0
	 * @static
	 * @staticvar array $instance
	 * @see wpafb()
	 * @return object|WPAFB_Main The one true WPAFB_Main
	 */
	public static function instance(){

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPAFB_Main ) )
		{
			self::$instance = new self();
			
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function __clone(){
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup(){
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}

	/**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts(){
    	wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-slider', array('jquery') );
        wp_enqueue_style ( 'wpafb-style-css', WPAFB_URL.'/assets/css/wpafb-style.css', array(), WPAFB_VERSION );
        wp_enqueue_script( 'wpafb-script', WPAFB_URL.'/assets/js/wpafb-script.js', array('jquery'), WPAFB_VERSION );	
    }

	/**
     * Print something to admin footer
     */
    function admin_footer(){
        //do something here.
    }

}//end class

endif;

/**
 * The main function for that returns WPAFB_Main
 *
 * The main function responsible for returning the one true WPAFB_Main
 * instance to functions everywhere.
 *
 * Use this function like a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpafb = wpafb(); ?>
 *
 * @since 1.0.0
 * @return object|WPAFB_Main The one true WPAFB_Main Instance.
 */
function wpafb()
{
	return WPAFB_Main::instance();
}

// Get WPAFB_Main Running.
wpafb();
