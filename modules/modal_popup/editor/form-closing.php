<?php

$closing_form_args = array(
	'id' => 'mp_closing_form',
	'header_callback' => 'adguru_modal_popup_form_closing_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_closing_footer_callback',
	'fields' => array(
		'closing_close_on_overlay_click' => array(
			'type' 	=> 'checkbox',
			'id'	=> 'closing_close_on_overlay_click',
			'label'	=> __("Close on overlay click", 'adguru' ),
			'default'	=> '0',
			'help' => __('Close the popup on overlay click', 'adguru')
		),

	)//end of fields array 
); // end array $closing_form_args

function adguru_modal_popup_form_closing_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_closing_top', $form_obj );
}

function adguru_modal_popup_form_closing_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_closing_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$closing_form_args = apply_filters('adguru_editor_form_modal_popup_closing_args', $closing_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$closing_form_args['header_callback'] = 'adguru_modal_popup_form_closing_header_callback';
$closing_form_args['footer_callback'] = 'adguru_modal_popup_form_closing_footer_callback';
//Create the form object
$closing_form = adguru()->form_builder->create_form($closing_form_args);


function adguru_show_modal_popup_closing_form( $ad )
{

	$closing_form = adguru()->form_builder->get_form('mp_closing_form');
	if( $closing_form )
	{ 
		$closing_data = array();
		if(! isset($ad->closing) || !is_array($ad->closing) )
		{
			$ad->closing = array();
		}
		else
		{
			foreach( $ad->closing as $key => $value )
			{
				$id = 'closing_'.$key;
				$closing_data[$id] = $value;
			}
			
		}
		$closing_form->set_data( $closing_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_modal_popup_closing_before_render', $closing_form );
		//render the form
		$closing_form->render();
		
	}//end if $closing_form
}




