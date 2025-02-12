<?php
/**
 * A class to handle the process of Zone registration
 * @author oneTarek
 * @since 2.0.0
 **/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Zone_Setup' ) ) :

class ADGURU_Zone_Setup{
	
	public $post_type;
	public $post_type_args;
	
	public function __construct(){

		$this->post_type = ADGURU_POST_TYPE_PREFIX.'zone';
		add_action( 'init', array( $this, 'register' ) );
	}
		
	public function register(){
		
		$labels = array(
			'name'               => __('Zone', 'wp-ad-guru'),
			'singular_name'      => __('Zone', 'wp-ad-guru'),
			'menu_name'          => __('Zones', 'wp-ad-guru'),
			'add_new'            => __( 'Add New Zone', 'wp-ad-guru' ),
			'add_new_item'       => __( 'Add New Zone', 'wp-ad-guru' ),
			'new_item'           => __( 'New Zone', 'wp-ad-guru' ),
			'edit_item'          => __( 'Edit Zone', 'wp-ad-guru' ),
			'view_item'          => __( 'View Zone', 'wp-ad-guru' ),
			'all_items'          => __( 'All Zones', 'wp-ad-guru' ),
			'search_items'       => __( 'Search Zone', 'wp-ad-guru' ),
			'parent_item_colon'  => __( 'Parent Zone', 'wp-ad-guru' ),
			'not_found'          => __( 'No zone found', 'wp-ad-guru' ),
			'not_found_in_trash' => __( 'No zone found in Trash', 'wp-ad-guru' ),
		);		

		$this->post_type_args = array(
			'labels'             => $labels,
			'description'        => __( 'A container to hold ads in frontend', 'wp-ad-guru' ),
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
		

		adguru()->post_types->register( $this->post_type, $this->post_type_args );

	}
	
	
}//end class

endif;