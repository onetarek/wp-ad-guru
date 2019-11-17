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
 	 * Stores the number of current post in wp main query loop
 	 */
	private $current_post_number_in_loop = 0;

	/**
 	 * Stores the number of current comment ( where depth is 0 ) in wp list comment loop
 	 */
	private $current_comment_number_in_loop = 0;


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
    }
	
	private function add_hooks(){

		//VALID PLACES : 'before_post', 'between_posts', 'after_post', 'before_content', 'after_content', 'before_comments', 'between_comments', 'before_comment_form', 'after_comment_form', 'before_footer', 'after_footer'

		
		if( isset( $this->possible_places[ 'before_post' ] ) )
		{
			add_action('loop_start', array( $this, 'hook_before_post' ), -100 , 1 ); # loop_start : https://developer.wordpress.org/reference/hooks/loop_start/
		}

		if( isset( $this->possible_places[ 'between_posts' ] ) )
		{
			add_action('the_post', array( $this, 'hook_between_posts' ), -100 , 2 ); #the_post https://developer.wordpress.org/reference/hooks/the_post/
		}

		if( isset( $this->possible_places[ 'after_post' ] ) )
		{
			add_action('loop_end', array( $this, 'hook_after_post' ), 100 , 1 ); # loop_end : https://developer.wordpress.org/reference/hooks/loop_end/
		}

		if( isset( $this->possible_places[ 'before_content' ] ) )
		{
			
			add_filter( 'the_content', array( $this, 'hook_before_content' ), 10, 1 ); #the_content : https://developer.wordpress.org/reference/hooks/the_content/
		}

		if( isset( $this->possible_places[ 'after_content' ] ) )
		{
			add_filter( 'the_content', array( $this, 'hook_after_content' ), 10, 1 ); #the_content : https://developer.wordpress.org/reference/hooks/the_content/
		}

		if( isset( $this->possible_places[ 'before_comments' ] ) )
		{
			add_filter('comments_template' , array( $this, 'hook_before_comments' ), -100, 1 );
		}

		if( isset( $this->possible_places[ 'between_comments' ] ) )
		{
			add_filter('wp_list_comments_args' , array( $this, 'filter_wp_list_comments_args' ), 100, 1 );
		}

		if( isset( $this->possible_places[ 'before_comment_form' ] ) )//same for place after_comments
		{
			add_action('comment_form_before', array( $this, 'hook_before_comment_form' ), -100 );
		}

		if( isset( $this->possible_places[ 'after_comment_form' ] ) )//same for place after_comments
		{
			add_action('comment_form_after', array( $this, 'hook_after_comment_form' ), -100 );
		}

		if( isset( $this->possible_places[ 'before_footer' ] ) )
		{
			add_action( 'get_footer', array( $this, 'hook_before_footer' )); #get_footer : https://codex.wordpress.org/Plugin_API/Action_Reference/get_footer
		}

		if( isset( $this->possible_places[ 'after_footer' ] ) )
		{
			add_action( 'wp_footer', array( $this, 'hook_after_footer' ));
		}
		

	}	


	public function hook_before_post( $wp_query ){

		if( ! $wp_query->is_main_query() )
		{
			return;
		}
		if( !isset( $this->possible_places['before_post'] ) || !is_array($this->possible_places['before_post'] ) )
		{
			return;
		}

		foreach( $this->possible_places['before_post'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}

	}

	public function hook_after_post($wp_query){

		if( ! $wp_query->is_main_query() )
		{
			return;
		}

		if( !isset( $this->possible_places['after_post'] ) || !is_array($this->possible_places['after_post'] ) )
		{
			return;
		}

		foreach( $this->possible_places['after_post'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}
	}

	public function hook_between_posts( $post, $wp_query ){

		if( ! $wp_query->is_main_query() )
		{
			return;
		}

		$this->current_post_number_in_loop++;

		if( !isset( $this->possible_places['between_posts'] ) || !is_array($this->possible_places['between_posts'] ) )
		{
			return;
		}

		foreach( $this->possible_places['between_posts'] as $zone )
		{
			if( $zone->is_auto_insert_possible_before_post( $this->current_post_number_in_loop ) )
			{
				adguru()->server->show_zone( $zone->ID );
			}
		}
	}

	public function hook_before_content( $content ){

		// Check if we're inside the main loop in a single post page.
	    if ( ! ( is_single() && in_the_loop() && is_main_query() ) ) {
	        return $content;
	    }
	 
		if( !isset( $this->possible_places['before_content'] ) || !is_array($this->possible_places['before_content'] ) )
		{
			return $content;
		}
		$ad_contents = '';
		foreach( $this->possible_places['before_content'] as $zone )
		{
			$ad_contents = $ad_contents. adguru()->server->show_zone( $zone->ID , true );
		}
		return $ad_contents.$content;
	}

	public function hook_after_content( $content ){

		// Check if we're inside the main loop in a single post page.
	    if ( ! ( is_single() && in_the_loop() && is_main_query() ) ) {
	        return $content;
	    }
	 
		if( !isset( $this->possible_places['after_content'] ) || !is_array($this->possible_places['after_content'] ) )
		{
			return $content;
		}
		$ad_contents = '';
		foreach( $this->possible_places['after_content'] as $zone )
		{
			$ad_contents = $ad_contents. adguru()->server->show_zone( $zone->ID , true );
		}
		return $content.$ad_contents;
	}


	
	public function hook_before_comments( $theme_template ){

		if( !isset( $this->possible_places['before_comments'] ) || !is_array($this->possible_places['before_comments'] ) )
		{
			return;
		}

		foreach( $this->possible_places['before_comments'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}

		return $theme_template;
	}


	public function filter_wp_list_comments_args ($args) {
		$args ['end-callback'] = array( $this, 'hook_between_comments');
		return $args;
	}

	public function hook_between_comments($comment, $args, $depth){
	
		if( $depth == 0 )
		{
			$this->current_comment_number_in_loop++;
		}

		if( !isset( $this->possible_places['between_comments'] ) || !is_array($this->possible_places['between_comments'] ) )
		{
			return;
		}

		foreach( $this->possible_places['between_comments'] as $zone )
		{
			if( $zone->is_auto_insert_possible_before_comment( $this->current_comment_number_in_loop ) )
			{
				adguru()->server->show_zone( $zone->ID );
			}
		}


	}

	public function hook_before_comment_form(){

		if( !isset( $this->possible_places['before_comment_form'] ) || !is_array($this->possible_places['before_comment_form'] ) )
		{
			return;
		}

		foreach( $this->possible_places['before_comment_form'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}
	}

	public function hook_after_comment_form(){

		if( !isset( $this->possible_places['after_comment_form'] ) || !is_array($this->possible_places['after_comment_form'] ) )
		{
			return;
		}

		foreach( $this->possible_places['after_comment_form'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}
	}

	public function hook_before_footer(){

		if( !isset( $this->possible_places['before_footer'] ) || !is_array($this->possible_places['before_footer'] ) )
		{
			return;
		}

		foreach( $this->possible_places['before_footer'] as $zone )
		{
			adguru()->server->show_zone( $zone->ID );
		}
	}
	public function hook_after_footer(){

		if( !isset( $this->possible_places['after_footer'] ) || !is_array($this->possible_places['after_footer'] ) )
		{
			return;
		}

		foreach( $this->possible_places['after_footer'] as $zone )
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
