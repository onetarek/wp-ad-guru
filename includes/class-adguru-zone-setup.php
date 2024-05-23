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
			'name'               => __('Zone', 'adguru'),
			'singular_name'      => __('Zone', 'adguru'),
			'menu_name'          => __('Zones', 'adguru'),
			'add_new'            => __( 'Add New Zone', 'adguru' ),
			'add_new_item'       => __( 'Add New Zone', 'adguru' ),
			'new_item'           => __( 'New Zone', 'adguru' ),
			'edit_item'          => __( 'Edit Zone', 'adguru' ),
			'view_item'          => __( 'View Zone', 'adguru' ),
			'all_items'          => __( 'All Zones', 'adguru' ),
			'search_items'       => __( 'Search Zone', 'adguru' ),
			'parent_item_colon'  => __( 'Parent Zone', 'adguru' ),
			'not_found'          => __( 'No zone found', 'adguru' ),
			'not_found_in_trash' => __( 'No zone found in Trash', 'adguru' ),
		);		

		$this->post_type_args = array(
			'labels'             => $labels,
			'description'        => __( 'A container to hold ads in frontend', 'adguru' ),
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