<?php

$sizing_form_args = array(
	'id' => 'winp_sizing_form',
	'header_callback' => 'adguru_window_popup_form_sizing_header_callback',
	'footer_callback' => 'adguru_window_popup_form_sizing_footer_callback',
	'fields' => array(
		'sizing_mode' => array(
			'type' 	=> 'radio',
			'id'	=> 'sizing_mode',
			'label'	=> __("Sizing Mode", 'adguru' ),
			'default'	=> 'custom',
			'options' => array('custom'=> __("Custom", 'adguru' ), 'full'=>__("Full Screen", 'adguru' ), 'responsive'=>__("Responsive", 'adguru' )),
			'disabled' => array('full', 'responsive')
		),
		'sizing_custom_width' => array(
			'type' => 'number',
			'id' => 'sizing_custom_width',
			'label' => __("Custom Width", 'adguru' ),
			'default' => '500',
			'size' => 'small',
			'min' => 100,
			'unit_text' => 'px'
		),
		'sizing_custom_height' => array(
			'type' => 'number',
			'id' => 'sizing_custom_height',
			'label' => __("Custom Height", 'adguru' ),
			'default' => '500',
			'size' => 'small',
			'min' => 100,
			'unit_text' => 'px'
		)

	)//end of fields array 
); // end array $sizing_form_args

function adguru_window_popup_form_sizing_header_callback( $form_obj )
{
	do_action('adguru_editor_form_window_popup_sizing_top', $form_obj );
}

function adguru_window_popup_form_sizing_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_sizing_window_popup_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$sizing_form_args = apply_filters('adguru_editor_form_window_popup_sizing_args', $sizing_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$sizing_form_args['header_callback'] = 'adguru_window_popup_form_sizing_header_callback';
$sizing_form_args['footer_callback'] = 'adguru_window_popup_form_sizing_footer_callback';
//Create the form object
$sizing_form = adguru()->form_builder->create_form($sizing_form_args);


function adguru_show_window_popup_sizing_form( $ad )
{

	$sizing_form = adguru()->form_builder->get_form('winp_sizing_form');
	if( $sizing_form )
	{ 
		$sizing_data = array();
		if(! isset($ad->sizing) || !is_array($ad->sizing) )
		{
			$ad->sizing = array();
		}
		else
		{
			foreach( $ad->sizing as $key => $value )
			{
				$id = 'sizing_'.$key;
				$sizing_data[$id] = $value;
			}
			
		}
		$sizing_form->set_data( $sizing_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_window_popup_sizing_before_render', $sizing_form );

		//render the form
		$sizing_form->render();
		
	}//end if $sizing_form
}




