<?php

$general_form_args = array(
	'id' => 'zone_general_form',
	'header_callback' => 'adguru_zone_form_general_header_callback',
	'footer_callback' => 'adguru_zone_form_general_footer_callback',
	'fields' => array(
		'general_place' => array(
			'type' 	=> 'radio',
			'id'	=> 'general_place',
			'label'	=> __("Automatic Insert", 'adguru' ),
			'items_direction' => 'vertical',
			'default'	=> 'none',
			'options' => array(
				'none' => __("Disabled (  Do not insert automatically )", 'adguru' ),
				'before_posts' => __("Before Posts", 'adguru' ),
				'between_posts' => __("Between Posts", 'adguru' ),
				'after_posts' => __("After Posts", 'adguru' ),
				'before_content' => __("Before Content", 'adguru' ),
				'after_content' => __("After Content", 'adguru' ),
				'before_comments' => __("Before Comments", 'adguru' ),
				'between_comments' => __("Between Comments", 'adguru' ),
				'after_comments' => __("After Comments", 'adguru' ),
				'footer' => __("After Footer", 'adguru' ),
				
			),
		),

		'general_page_types' => array(
			'type' 	=> 'multicheck',
			'id'	=> 'general_page_types',
			'label'	=> __("Page types", 'adguru' ),
			'items_direction' => 'horizontal',
			'on_off_values' => array( "1", "0" ),
			'default'	=> array(
				'any_page' => 1,
				'singel_post' => 0,
				'taxonomy_archive' => 0,
				'date_archive' => 0,
				'search_result' => 0,
				'author_archive' => 0
			),
			'options' => array(
				'any_page' => __("Any type page", 'adguru' ),
				'singel_post' => __("Single Post", 'adguru' ),
				'taxonomy_archive' => __("Taxonomy archive", 'adguru' ),
				'date_archive' => __("Date archive", 'adguru' ),
				'search_result' => __("Search result", 'adguru' ),
				'author_archive' => __("Author archive", 'adguru' )
				
			),
		),

		

	)//end of fields array 
); // end array $animation_form_args

function adguru_zone_form_general_header_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_general_top', $form_obj );
}

function adguru_zone_form_general_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_general_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$general_form_args = apply_filters('adguru_zone_editor_form_general_args', $general_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$general_form_args['header_callback'] = 'adguru_zone_form_general_header_callback';
$general_form_args['footer_callback'] = 'adguru_zone_form_general_footer_callback';
//Create the form object
$general_form = adguru()->form_builder->create_form($general_form_args);


function adguru_show_zone_general_form( $zone )
{

	$general_form = adguru()->form_builder->get_form('zone_general_form');
	if( $general_form )
	{ 
		$general_data = array();
		
		
		//IMPORTANT : in this from , Data items are not be saved as an array in a single meta field. All array keys are stored as individual meta field.

		$field_list = $general_form->get_field_list();
		foreach( $field_list as $id => $opts ){
			if( $opts['real_field'] != 1 ) { continue; }
			$key = ADGURU_Helper::str_replace_beginning('general_', '', $id );
			if( isset($zone->{$key} ) )
			{
				$general_data[ $id ] = $zone->{$key};
			}

		}
write_log($general_data);
		$general_form->set_data( $general_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_modal_popup_animation_before_render', $general_form );
		//render the form
		$general_form->render();
		
	}//end if $animation_form
}




