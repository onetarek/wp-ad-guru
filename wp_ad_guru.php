<?php
/**
 * Plugin Name: WP Ad Guru
 * Description: An essential set of advertising and marketing tools. Manage banner ad, modal popup and window popup. Ad zones, ad rotator, GeoLocation tracker, ads carousel-slider, different ads by multiple conditions of visited page.
 * Plugin URI: http://wpadguru.com
 * Author: oneTarek
 * Author URI: http://onetarek.com
 * Version: 2.5.4
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

//Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_Ad_Guru' ) ) :

/**
 * Main WP_Ad_Guru Class.
 *
 * @since 2.0.0
 */
final class WP_Ad_Guru{
	/** Singleton Class */

	/**
	 * @var WP_Ad_Guru The one true WP_Ad_Guru
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * ADGURU Modules Object.
	 *
	 * @var object|ADGURU_Modules
	 * @since 2.0.0
	 */
	public $modules;
	
	/**
	 * ADGURU HTML Element Helper Object.
	 *
	 * @var object|ADGURU_HTML_Elements
	 * @since 2.0.0
	 */
	public $html;

	/**
	 * ADGURU Emails Object.
	 *
	 * @var object|ADGURU_Emails
	 * @since 2.0.0
	 */
	public $emails;	

	/**
	 * ADGURU API Object.
	 *
	 * @var object|ADGURU_API
	 * @since 2.0.0
	 */
	public $api;	
	
	/**
	 * ADGURU Error Object
	 * @var object|ADGURU_Error
	 * @since 2.0.0
	 */
	public $error;
	 
	/**
	 * ADGURU User Object
	 * @var object|ADGURU_User
	 * @since 2.0.0
	 */
	public $user;		
	
	/**
	 * ADGURU Manager Object
	 * @var object|ADGURU_Manager
	 * @since 2.0.0
	 */
	public $manager; 
		
	/**
	 * ADGURU Server Object
	 * @var object|ADGURU_Server
	 * @since 2.0.0
	 */
	public $server; 

	/**
	 * ADGURU Post Types Object
	 * @var object|ADGURU_Post_Types
	 * @since 2.0.0
	 */
	public $post_types; 
		 
	/**
	 * ADGURU Ad Types Object
	 * @var object|ADGURU_Ad_Types
	 * @since 2.0.0
	 */
	public $ad_types; 	 

	/**
	 * ADGURU Content Types Object
	 * @var object|ADGURU_Content_Types
	 * @since 2.0.0
	 */
	public $content_types; 

	/**
	 * ADGURU Menu Object
	 * @var object|ADGURU_Menu
	 * @since 2.0.0
	 */
	public $menu; 

	/**
	 * ADGURU Ad Editor Object
	 * @var object|ADGURU_Ad_Editor
	 * @since 2.0.0
	 */
	public $ad_editor;
	
	/**
	 * ADGURU Settings Object
	 * @var object|WPAFB_Main
	 * @since 2.0.0
	 */
	public $form_builder; 

	/**
	 * ADGURU Form Builder Object
	 * @var object|ADGURU_Settings
	 * @since 2.0.0
	 */
	public $settings; 
		 
	/**
	 * ADGURU Admin Notice Object
	 * @var object|ADGURU_Admin_Notice
	 * @since 2.0.0
	 */
	public $admin_notice;

	/**
	 * ADGURU Request Handler Object
	 * @var object|ADGURU_Request_Handler
	 * @since 2.0.0
	 */
	public $request_handler;

	/**
	 * ADGURU Migrator
	 * @var object|ADGURU_Migrator
	 * @since 2.0.0
	 */
	public $migrator; 
	
	/**
	 * Flag for migration neded or not
	 **/
	public $migration_needed = false;

	/**
	 * Ad setup manager
	 * @var object|ADGURU_Ad_Setup_Manager
	 * @since 2.1.0
	 */
	public $ad_setup_manager;


	/**
	 * Main WP_Ad_Guru Instance.
	 *
	 * Insures that only one instance of WP_Ad_Guru exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 2.0.0
	 * @static
	 * @staticvar array $instance
	 * @uses WP_Ad_Guru::load_textdomain() load the language files.
	 * @see ADGURU()
	 * @return object|WP_Ad_Guru The one true WP_Ad_Guru
	 */
	public static function instance(){
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Ad_Guru ) )
		{
			self::$instance = new self();
			
		}
		return self::$instance;
	}	

    /**
     * Private constructor so nobody else can instance it
     *
     */
    private function __construct(){

		define( 'ADGURU_OPTIONS_FIELD_NAME', 'adguru_settings' );
		global $adguru_options;
		$adguru_options = get_option( ADGURU_OPTIONS_FIELD_NAME , array() );
		
		$this->migration_needed = get_option( "adguru_migration_needed", 0 );
		
		$this->define_constants();
		add_action( 'plugins_loaded', array( $this , 'plugins_loaded' ) );
		
		$this->includes();

		if( $this->migration_needed )
		{
			$this->migrator   = new ADGURU_Migrator();
		}
		$this->error      = new ADGURU_Error();
		$this->user		  = new ADGURU_User();
		$this->modules    = new ADGURU_Modules();
		$this->api        = new ADGURU_API();
		$this->html       = new ADGURU_HTML_Elements();
		$this->emails     = new ADGURU_Emails();
		$this->post_types = new ADGURU_Post_Types();
		
		new ADGURU_Zone_Setup();
		
		$this->ad_types   = new ADGURU_Ad_Types();
		$this->content_types   = new ADGURU_Content_Types();
		$this->manager	  = new ADGURU_Manager();
		$this->server     = new ADGURU_Server();
		if ( is_admin() )
		{
			$this->menu     = new ADGURU_Menu();
			$this->admin_notice = new ADGURU_Admin_Notice();
			$this->settings = new ADGURU_Settings();
			$this->form_builder = wpafb();
			$this->ad_setup_manager = new ADGURU_Ad_Setup_Manager();
			new ADGURU_Zone_Edit_Manager();
		}
		#for geo location feature strat session here before outputing anything to the browser.
		if( session_id() == "" && !defined( 'DOING_CRON' ) && !isset( $_GET['doing_wp_cron'] ) ){ session_start(); }
		
		$this->request_handler = new ADGURU_Request_Handler();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );	
		
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );	
		
		add_action( 'deleted_post', array( $this, 'delete_post_action' ) );	
		
		add_action( 'init', array( $this, 'init_hook' ) );	

		// Loaded action
		do_action( 'adguru_loaded' );
    }
		
	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @return void
	 */
	public function __clone(){
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'adguru' ), '2.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup(){
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'adguru' ), '2.0' );
	}	

	/**
	 * Define plugin constants.
	 *
	 * @access private
	 * @since 2.0.0
	 * @return void
	 */
	private function define_constants(){

		global $wpdb;
		global $adguru_options;
		define( 'ADGURU_VERSION', '2.5.4' );
		define( 'ADGURU_DOCUMENTAION_URL', 'http://wpadguru.com/documentation/' );
		define( 'ADGURU_PLUGIN_FILE', __FILE__ );
		define( 'ADGURU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); // Plugin Directory
		define( 'ADGURU_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // with forward slash (/). Plugin URL (for http requests).
		define( 'ADGURU_PLUGIN_SLUG', 'adguru' );
		define( 'ADGURU_ADMANAGER_PAGE_SLUG_PREFIX' , ADGURU_PLUGIN_SLUG.'_ad_' );
		define( 'ADGURU_COOKIE_PREFIX', "adguru_" );
		define( 'ADGURU_POST_TYPE_PREFIX','adg_');
		
		#adguru tables
		define( 'ADGURU_LINKS_TABLE',$wpdb->prefix.'adguru_ad_links' );	

		define('ADGURU_GEO_LOCATION_ENABLED', ( isset( $adguru_options['enable_geo_location'] ) && $adguru_options['enable_geo_location'] == 'on' )? true : false );
			
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 2.0.0
	 * @return void
	 */
	private function includes(){

		global $adguru_options;
	
		#INCLUDE REQUIRED FILES
		if( $this->migration_needed )
		{
			require_once( ADGURU_PLUGIN_DIR."includes/migrator/class-adguru-migrator.php" );
		}
		
		require_once( ADGURU_PLUGIN_DIR."includes/install/install.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-helper.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/functions.php" );
				
		$adguru_options = adguru_get_settings();
		
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-error.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-user.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-modules.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-api.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-html-elements.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-emails.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-post-types.php" );
		
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-zone.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-zone-setup.php" );

		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-ad-types.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-content-types.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-content-type.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-ad.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-manager.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-server.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/widgets/widget.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/shortcodes/shortcode.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-request-handler.php" );
		require_once( ADGURU_PLUGIN_DIR."includes/class-adguru-inserter.php" );
		
		if ( is_admin() )
		{
			#INCLUDE ADMIN RELATED FILES HERE
			require_once( ADGURU_PLUGIN_DIR."libs/wp-admin-form-builder/main.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/class-adguru-admin-notice.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/settings/class-adguru-settings.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/class-adguru-menu.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/zone-manager/class-zone-edit-manager.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/links-editor/class-adguru-links-editor.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/links-editor/class-adguru-links-editor-ajax-handler.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/ad-setup-manager/class-adguru-ad-setup-manager.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/admin/ad-setup-manager/class-adguru-ad-setup-manager-ajax-handler.php" );
			require_once( ADGURU_PLUGIN_DIR."includes/metaboxes/metabox.php" );	
			require_once( ADGURU_PLUGIN_DIR."includes/functions.php" );

		}	
	
	}

	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function load_textdomain(){
		load_plugin_textdomain( 'adguru', false, ADGURU_PLUGIN_DIR."languages/" );
	}

	/**
	 * Run within wp plugins_loaded action 
	 * @since 2.0.0
	 * @return void
	 */
	public function plugins_loaded(){
		$this->load_textdomain();
		#use "adguru_plugins_loaded" hook in extensions or other plugins to load files those are depended on adGuru core files.
		do_action( "adguru_plugins_loaded" );
	}

	/**
	 * Function to trigger some action hooks
	 * This function is called on WP init action hook
	 * @access public 
	 * @since 2.0.0
	 * @return void
	 */
	public function init_hook(){
		do_action( 'adguru_init' );
		do_action( 'adguru_init_process' );
		do_action( 'adguru_init_end' );
	}

	/**
	 * Enqueue required CSS and JS files in admin area
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts(){

		if( strpos( $_SERVER[ 'REQUEST_URI' ], ADGURU_PLUGIN_SLUG ) ) #to ensure that current plugin page is being shown.
		{
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-ui-accordion', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-ui-dialog', array( 'jquery' ) );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			//$jquery_css_base = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css';
			$jquery_css_base = ADGURU_PLUGIN_URL.'assets/css/jquery-ui-themes/smoothness/jquery-ui.min.css';
			wp_enqueue_style ( 'jquery-ui-standard-css', $jquery_css_base );
			wp_enqueue_media();
			
			wp_enqueue_script( 'adguru-admin', ADGURU_PLUGIN_URL.'assets/js/admin.js', array( 'jquery' ), ADGURU_VERSION );
			wp_enqueue_style ( 'adguru-admin-css', ADGURU_PLUGIN_URL.'assets/css/admin.css', array(), ADGURU_VERSION );	
			
			// Values to pass into JS script.
			//Unfortunately wp_localize_script() casts all scalars (simple types) in the passed-in array to strings. A way around it is to put your arguments in an array within the passed-in array. https://wordpress.stackexchange.com/questions/186155/how-do-you-pass-a-boolean-value-to-wp-localize-script/186191#186191
			$js_vars = array(
				'ajaxUrl' 	=> admin_url( 'admin-ajax.php' ),
				'assetsUrl' => ADGURU_PLUGIN_URL.'assets',
				'options' 	=> array(
					'geoLocationEnabled' => ADGURU_GEO_LOCATION_ENABLED,
				),
			);
		
			// Pass them in.
			wp_localize_script(
				'adguru-admin', // script handle
				'adGuruAdminVars', // name of JS object that will contain our values
				$js_vars
			);			
			
			do_action( 'adguru_admin_enqueue_scripts' );
		}   
	}
	
	/**
	 * Enqueue required CSS and JS files in front-end
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue_scripts(){
	
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'adguru-simple-carousel', ADGURU_PLUGIN_URL.'assets/js/simple.carousel_edited.js', array( 'jquery' ), ADGURU_VERSION );
		wp_enqueue_script( 'adguru', ADGURU_PLUGIN_URL.'assets/js/adguru.js', array( 'jquery' ), ADGURU_VERSION );
		wp_enqueue_style ( 'adguru-css', ADGURU_PLUGIN_URL.'assets/css/adguru.css', array(), ADGURU_VERSION );	
		
		// Values to pass into JS script.
		$js_vars = array(
			'ajaxUrl' 		=> admin_url( 'admin-ajax.php' ),
			'assetsUrl' 	=> ADGURU_PLUGIN_URL.'assets',
			'cookiePrefix' 	=> ADGURU_COOKIE_PREFIX,
			'options' 		=> array(
				'geoLocationEnabled' => ADGURU_GEO_LOCATION_ENABLED,
			),
		);
	
		// Pass them in.
		wp_localize_script(
			'adguru-simple-carousel', // script handle
			'adGuruVars', // name of JS object that will contain our values
			$js_vars
		);		
		
	}
	
	/**
	 * Function to be called in admin_head
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function admin_head(){ return;
		if( strpos( $_SERVER['REQUEST_URI'], ADGURU_PLUGIN_SLUG ) ) #to ensure that current plugin page is being shown.
		{
			//print something here
		}
	}
	
	/**
	 * Function to be called in wp_head
	 * Print all required scripts for adguru
	 * Print ad codes those are required to load in <head>
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	
	public function wp_head(){
		do_action( 'adguru_wp_head_top' );
		$this->server->print_header_footer_ads( "head" );		
		do_action( 'adguru_wp_head_bottom' );
	}

	/**
	 * Function to be called in wp_footer
	 * Print ad codes those are required to load in footer before </body> tag
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	
	public function wp_footer(){
		do_action( 'adguru_wp_footer_top' );
		$this->server->print_header_footer_ads( "footer" );	
		adguru_load_styles_in_footer();	
		do_action( 'adguru_wp_footer_bottom' );
	}		

	/**
	 * Delete all ad links for post when post is deleted.
	 * @param int $post_id
	 * @use_with deleted_post action
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */ 
	public function delete_post_action( $post_id ){
		global $wpdb;
		$wpdb->query( "DELETE FROM ".ADGURU_LINKS_TABLE." WHERE page_type='singular' AND object_id=".$post_id );
	}
	
	
}//end class 

endif;

/**
 * The main function for that returns WP_Ad_Guru
 *
 * The main function responsible for returning the one true WP_Ad_Guru
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $adguru = adguru(); ?>
 *
 * @since 2.0.0
 * @return object|WP_Ad_Guru The one true WP_Ad_Guru Instance.
 */
function adguru()
{
	return WP_Ad_Guru::instance();
}

// Get ADGURU Running.
adguru();
