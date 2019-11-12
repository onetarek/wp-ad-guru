<?php

$design_form_args = array(
	'id' => 'zone_design_form',
	'header_callback' => 'adguru_zone_form_design_header_callback',
	'footer_callback' => 'adguru_zone_form_design_footer_callback',
	'fields' => array(

		'design_alignment' => array(
			'type' 	=> 'select',
			'id'	=> 'design_alignment',
			'label'	=> __("Alignment", 'adguru' ),
			'default'	=> 'default',
			'options' => array(
				'default' => __("Default", 'adguru' ),
				'left' => __("Left", 'adguru' ),
				'center' => __("Center", 'adguru' ),
				'right' => __("Right", 'adguru' ),
				'float_left' => __("Float left", 'adguru' ),
				'float_right' => __("Float right", 'adguru' )
				
			),
		),

	)//end of fields array 
); // end array $design_form_args

function adguru_zone_form_design_header_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_design_top', $form_obj );
}

function adguru_zone_form_design_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_design_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$design_form_args = apply_filters('adguru_zone_editor_form_design_args', $design_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$design_form_args['header_callback'] = 'adguru_zone_form_design_header_callback';
$design_form_args['footer_callback'] = 'adguru_zone_form_design_footer_callback';
//Create the form object
$design_form = adguru()->form_builder->create_form($design_form_args);


function adguru_show_zone_design_form( $zone )
{

	$design_form = adguru()->form_builder->get_form('zone_design_form');
	if( $design_form )
	{ 
		
		$design_data = array();
		if(! isset($zone->design) || !is_array($zone->design) )
		{
			$zone->design = array();
		}
		else
		{
			foreach( $zone->design as $key => $value )
			{
				$id = 'design_'.$key;
				$design_data[$id] = $value;
			}
			
		}

		$design_form->set_data( $design_data );
		
		//Before render modify the fields settings, specially update fields hidden status based on the value.

		do_action('adguru_editor_form_zone_design_before_render', $design_form );
		
		//render the form
		$design_form->render();
		
	}//end if $design_form
}


