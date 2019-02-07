<?php
/**
 * A class to handle the process of all ad type registration
 * @author oneTarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Ad_Types' ) ) :

class ADGURU_Ad_Types{
	
	public $types = array();
	/**
	 * Store the list of ad types name with adguru post type prefix 
	 */
	public $post_types = array();
	
	public function __construct(){
		
		add_action( 'adguru_init_process', array( $this, 'init' ) );
	}

	/**
	 * Collect ad_type settings
	 * This function must be called within adguru_init hook
	 */
	public function add( $ad_type , $args ){
		if( !array_key_exists( $ad_type, $this->types ) )
		{
			if( !isset( $args['slug'] ) || $args['slug'] != $ad_type )
			{
				$args['slug'] = $ad_type;
			}
			$this->types[ $ad_type ] = $args;
		}
		
	}
	
	public function init(){
	
		$this->types = apply_filters( "adguru_ad_types" , $this->types );
		//Check valid settings
		$temp_arr = array();
		foreach( $this->types as $type => $args )
		{
			if( !isset( $args['slug'] ) || trim($args['slug'] ) == "" || $args['slug'] != $type )
			{
				continue;
			}

			/*
			 * WP post type supports max 20 characters,Cannot contain capital letters , spaces and no special chars ( underscore allowed ).
			 * AdGuru reserves 4 characters for prefix adg_
			 * So Ad type must be less than or equal 16 characters.
			 */		 			
			if( strlen( $type ) > 16 )
			{
				continue;
			}

			$defaults = array(
				"use_zone" 			=> false, 	#Is this type of ad be placed in a zone ? 
				"multiple_slides"	=> false, 	#Are multiple slides ( contain ads ) be placed in same zone ?
				"rotator"			=> true 	#Does this type ad rotate in a single slide? Ad will be changed in each page visit.
			);
			$args = wp_parse_args( $args, $defaults );
			if( $args['use_zone'] == false && !isset( $args[ 'script_location'] ) )
			{
				$args[ 'script_location'] = "footer";
			}
			
			$temp_arr[ $type ] = $args;
		}
		$this->types = $temp_arr;

		$this->register();	
		
	}
	
	public function register(){
	
		foreach( $this->types as $type => $args )
		{
			adguru()->post_types->register( ADGURU_POST_TYPE_PREFIX.$type, $this->make_post_type_args( $args ) );
			$this->post_types[]=ADGURU_POST_TYPE_PREFIX.$type;
			
		}
	}
	
	private function make_post_type_args( $args ){

		$labels = array(
			'name'               => $args['name'],
			'singular_name'      => $args['name'],
			'menu_name'          => $args['plural_name'],
			'add_new'            => sprintf( _x( 'Add New', '%s', 'adguru' ), $args['name'] ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'adguru' ), $args['name'] ),
			'new_item'           => sprintf( __( 'New %s', 'adguru' ), $args['name'] ),
			'edit_item'          => sprintf( __( 'Edit %s', 'adguru' ), $args['name'] ),
			'view_item'          => sprintf( __( 'View %s', 'adguru' ), $args['name'] ),
			'all_items'          => sprintf( __( 'All %s', 'adguru' ), $args['plural_name'] ),
			'search_items'       => sprintf( __( 'Search %s', 'adguru' ), $args['plural_name'] ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'adguru' ), $args['plural_name'] ),
			'not_found'          => sprintf( __( 'No %s found.', 'adguru' ), $args['plural_name'] ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'adguru' ), $args['plural_name'] ),
		);		

		$post_type_args = array(
			'labels'             => $labels,
			'description'        => $args['description'],
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);
		return $post_type_args;		
	
	}//end func
	
}//end class

endif;