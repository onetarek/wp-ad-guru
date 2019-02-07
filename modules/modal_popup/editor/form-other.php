<?php

$other_form_args = array(
	'id' => 'mp_other_form',
	'header_callback' => 'adguru_modal_popup_form_other_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_other_footer_callback',
	'fields' => array(
		'other_z_index' => array(
			'type' 	=> 'text',
			'id'	=> 'other_z_index',
			'size'	=> 'medium',
			'label'	=> __("Popup z-index", 'adguru' ),
			'placeholder' => 'Ex : 999999',
			'default'	=> '',
			'help' => __("CSS z-index value for the popup", 'adguru' ),
		)
		

	)//end of fields array 
); // end array $other_form_args

function adguru_modal_popup_form_other_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_other_top', $form_obj );
}

function adguru_modal_popup_form_other_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_other_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$other_form_args = apply_filters('adguru_editor_form_modal_popup_other_args', $other_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$other_form_args['header_callback'] = 'adguru_modal_popup_form_other_header_callback';
$other_form_args['footer_callback'] = 'adguru_modal_popup_form_other_footer_callback';
//Create the form object
$other_form = adguru()->form_builder->create_form($other_form_args);


function adguru_show_modal_popup_other_form( $ad )
{

	$other_form = adguru()->form_builder->get_form('mp_other_form');
	if( $other_form )
	{ 
		$other_data = array();
		if(! isset($ad->other) || !is_array($ad->other) )
		{
			$ad->other = array();
		}
		else
		{
			foreach( $ad->other as $key => $value )
			{
				$id = 'other_'.$key;
				$other_data[$id] = $value;
			}
			
		}
		$other_form->set_data( $other_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_modal_popup_other_before_render', $other_form );
		//render the form
		$other_form->render();
		
	}//end if $other_form
}




