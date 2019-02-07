<?php

$popup_options_form_args = array(
	'id' => 'winp_popup_options_form',
	'header_callback' => 'adguru_window_popup_form_popup_options_header_callback',
	'footer_callback' => 'adguru_window_popup_form_popup_options_footer_callback',
	'fields' => array(
		'popup_options_window_title' => array(
			'type' 	=> 'text',
			'id'	=> 'popup_options_window_title',
			'label'	=> __("Window Title", 'adguru' ),
			'size' => 'medium',
		),
		"popup_options_window_options" => array(
			'type' => 'multicheck',
			'id'   => 'popup_options_window_options',
			'label' => __("Window Options", 'adguru' ),
			'default' => array(
				'titlebar' => 1,
				'location' => 0,
				'menubar' => 0,
				'resizable' => 1,
				'scrollbars' => 0,
				'status' => 0,
				'toolbar' => 0
				),
			'on_off_values' => array('1', '0'),
			'options' => array(
				'titlebar' => 'Titlebar',
				'location' => 'Location',
				'menubar' => 'Menubar',
				'resizable' => 'Resizable',
				'scrollbars' => 'Scrollbars',
				'status'=> 'Statusbar',
				'toolbar'=> 'Toolbar',
				),
			'items_direction' => 'vertical',
			
		),

	)//end of fields array 
); // end array $popup_options_form_args

function adguru_window_popup_form_popup_options_header_callback( $form_obj )
{
	do_action('adguru_editor_form_window_popup_popup_options_top', $form_obj );
}

function adguru_window_popup_form_popup_options_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_popup_options_window_popup_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$popup_options_form_args = apply_filters('adguru_editor_form_window_popup_popup_options_args', $popup_options_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$popup_options_form_args['header_callback'] = 'adguru_window_popup_form_popup_options_header_callback';
$popup_options_form_args['footer_callback'] = 'adguru_window_popup_form_popup_options_footer_callback';
//Create the form object
$popup_options_form = adguru()->form_builder->create_form($popup_options_form_args);


function adguru_show_window_popup_popup_options_form( $ad )
{

	$popup_options_form = adguru()->form_builder->get_form('winp_popup_options_form');
	if( $popup_options_form )
	{ 
		$popup_options_data = array();
		if(! isset($ad->popup_options) || !is_array($ad->popup_options) )
		{
			$ad->popup_options = array();
		}
		else
		{
			foreach( $ad->popup_options as $key => $value )
			{
				$id = 'popup_options_'.$key;
				$popup_options_data[$id] = $value;
			}
			
		}
		$popup_options_form->set_data( $popup_options_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_window_popup_popup_options_before_render', $popup_options_form );

		//render the form
		$popup_options_form->render();
		
	}//end if $popup_options_form
}




