<?php
/**
 * ADGURU_Inserter class
 * responsible to insert zones to pages automatically
 * @since 2.2.0
 * @author oneTarek
 */

//Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Inserter' ) ) :

/**
 * ADGURU_Inserter Class.
 *
 * @since 2.2.0
 */
final class ADGURU_Inserter{
	/** Singleton Class/

	/**
	 * @var ADGURU_Inserter The one true ADGURU_Inserter
	 * @since 2.2.0
	 */
	private static $instance;

	/**
	  * Stores all active zone object
	  * @var object
	  * @since 2.2.0
	  */
	private $zones;

	/**
	  * Stores information about current visited page
	  * @var array
	  * @since 2.2.0
	  */
	private $current_page_info;

	/**
	  * Stores places as key and zones array as value
	  * @var array
	  * @since 2.2.0
	  */
	private $possible_places;

	/**
	 * Main ADGURU_Inserter Instance.
	 *
	 * Insures that only one instance of ADGURU_Inserter exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 2.2.0
	 * @static
	 * @staticvar array $instance
	 * @return object|ADGURU_Inserter The one true ADGURU_Inserter
	 */
	public static function instance(){
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ADGURU_Inserter ) )
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

		$this->zones = adguru()->server->get_zones();
		$this->current_page_info = adguru()->server->get_current_page_info();
		$this->prepare();
		$this->add_hooks();
    }

    private function prepare(){

    	$this->possible_places = array();
    	//write_log( $this->zones );
    	foreach( $this->zones as $zone )
    	{
    		if( $zone->is_auto_insert_possible( $this->current_page_info ) )
    		{
    			$place = $zone->get_auto_insert_place();
    			if( ! isset( $this->possible_places[ $place ] ) )
    			{
    				$this->possible_places[ $place ] = array();
    			}
    			$this->possible_places[ $place ][] = $zone;
    		}
    	}

    	//write_log( $this->places );
    }
	
	private function add_hooks(){

		//VALID PLACES : 'before_post', 'between_posts', 'after_post', 'before_content', 'after_content', 'before_comments', 'between_comments', 'after_comments', 'footer'

		
		if( isset( $this->possible_places[ 'before_post' ] ) )
		{
			add_action('loop_start', array( $this, 'hook_before_post' ), -100 );
		}

		if( isset( $this->possible_places[ 'between_posts' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'after_post' ] ) )
		{
			add_action('loop_end', array( $this, 'hook_after_post' ), 100 );
		}

		if( isset( $this->possible_places[ 'before_content' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'after_content' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'before_comments' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'between_comments' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'after_comments' ] ) )
		{
			
		}

		if( isset( $this->possible_places[ 'footer' ] ) )
		{
			
		}
		

	}	


	public function hook_before_post(){
		if( !isset( $this->possible_places['before_post'] ) || !is_array($this->possible_places['before_post'] ) )
		{
			return;
		}

		foreach( $this->possible_places['before_post'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}

	}

	public function hook_after_post(){
		if( !isset( $this->possible_places['after_post'] ) || !is_array($this->possible_places['after_post'] ) )
		{
			return;
		}

		foreach( $this->possible_places['after_post'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}
	}


	
	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 2.2.0
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
	 * @since 2.2.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup(){
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'adguru' ), '2.0' );
	}	
	
	
}//end class 

endif;
