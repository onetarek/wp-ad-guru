<?php

$content_html_form_args = array(
	'id' => 'content_html_form',
	'header_callback' => 'adguru_content_html_form_header_callback',
	'footer_callback' => 'adguru_content_html_form_footer_callback',
	'validation_callback' => 'adguru_content_html_form_validation_callback',
	'fields' => array(
		'content_html_html' => array(
			'type' => 'textarea',
			'id' => 'content_html_html',
			'label' => __('HTML/JavaScript Code', 'adguru'),
			'default' => '',
			'size' => 'medium',
			'placeholder' => __('Enter HTML or JavaScript code here', 'adguru'),
		)
	)//end of fields array 
); // end array $content_html_form_args

function adguru_content_html_form_header_callback( $form_obj )
{
	do_action('adguru_content_editor_form_html_main_top', $form_obj );
}

function adguru_content_html_form_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_html_main_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$content_html_form_args = apply_filters('adguru_content_editor_form_html_main_args', $content_html_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$content_html_form_args['header_callback'] = 'adguru_content_html_form_header_callback';
$content_html_form_args['footer_callback'] = 'adguru_content_html_form_footer_callback';
//Create the form object
$content_html_form = adguru()->form_builder->create_form($content_html_form_args);


function adguru_show_content_html_form( $content )
{

	$content_html_form = adguru()->form_builder->get_form('content_html_form');
	if( $content_html_form )
	{ 
		$content_html_data = array();
		if(!is_array($content) )
		{
			$content = array();
		}
		else
		{
			foreach( $content as $key => $value )
			{
				$id = 'content_html_'.$key;
				$content_html_data[$id] = $value;
			}
			
		}
		$content_html_form->set_data( $content_html_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_content_html_before_render', $content_html_form );

		//render the form
		$content_html_form->render();
		
	}//end if $content_html_form
}


function adguru_content_html_form_validation_callback( $data, $args, $form )
{
	
	$id = $args['id'];
	$value = $data['value'];
	$error = '';
	switch( $id )
	{
		case 'content_html_html':
		{
			if( $value == "")
			{
				$error = __('HTML code field is blank', 'adguru');
			}
			break;
		}

	}
	if( $error != '')
	{
		adguru_set_ad_input_error( $id , $error ); 
		$data['error'] = $error;
	} 
	return $data;
}


