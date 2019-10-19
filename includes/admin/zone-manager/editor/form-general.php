<?php

$general_form_args = array(
	'id' => 'zone_general_form',
	'header_callback' => 'adguru_zone_form_general_header_callback',
	'footer_callback' => 'adguru_zone_form_general_footer_callback',
	'fields' => array(
		'general_place' => array(
			'type' 	=> 'select',
			'id'	=> 'general_place',
			'label'	=> __("Placement", 'adguru' ),
			'default'	=> 'none',
			'options' => array(
				'none' => __("None", 'adguru' ),
				'before_post' => __("Before Post", 'adguru' ),
				'after_post' => __("Aefore Post", 'adguru' ),
				'before_content' => __("Before Content", 'adguru' ),
				'after_content' => __("After Content", 'adguru' ),
				
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
		if(! isset($zone->general) || !is_array($zone->general) )
		{
			$zone->general = array();
		}
		else
		{
			foreach( $zone->general as $key => $value )
			{
				$id = 'animation_'.$key;
				$general_data[$id] = $value;
			}
			
		}
		$general_form->set_data( $general_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_modal_popup_animation_before_render', $general_form );
		//render the form
		$general_form->render();
		
	}//end if $animation_form
}




