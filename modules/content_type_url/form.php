<?php

$content_url_form_args = array(
	'id' => 'content_url_form',
	'header_callback' => 'adguru_content_url_form_header_callback',
	'footer_callback' => 'adguru_content_url_form_footer_callback',
	'validation_callback' => 'adguru_content_url_form_validation_callback',
	'fields' => array(
		'content_url_url' => array(
			'type' => 'url',
			'id' => 'content_url_url',
			'label' => __('URL', 'adguru'),
			'default' => '',
			'size' => 'medium',
			'placeholder' => __('Enter a valid url', 'adguru'),
		)
	)//end of fields array 
); // end array $content_url_form_args

function adguru_content_url_form_header_callback( $form_obj )
{
	do_action('adguru_content_editor_form_url_main_top', $form_obj );
}

function adguru_content_url_form_footer_callback( $form_obj )
{ 
	do_action('adguru_editor_form_url_main_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$content_url_form_args = apply_filters('adguru_content_editor_form_url_main_args', $content_url_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$content_url_form_args['header_callback'] = 'adguru_content_url_form_header_callback';
$content_url_form_args['footer_callback'] = 'adguru_content_url_form_footer_callback';
//Create the form object
$content_url_form = adguru()->form_builder->create_form($content_url_form_args);


function adguru_show_content_url_form( $content )
{

	$content_url_form = adguru()->form_builder->get_form('content_url_form');
	if( $content_url_form )
	{ 
		$content_url_data = array();
		if(!is_array($content) )
		{
			$content = array();
		}
		else
		{
			foreach( $content as $key => $value )
			{
				$id = 'content_url_'.$key;
				$content_url_data[$id] = $value;
			}
			
		}
		$content_url_form->set_data( $content_url_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_content_url_before_render', $content_url_form );

		//render the form
		$content_url_form->render();
		
	}//end if $content_url_form
}


function adguru_content_url_form_validation_callback( $data, $args, $form )
{
	
	$id = $args['id'];
	$value = $data['value'];
	$error = '';
	switch( $id )
	{
		case 'content_url_url':
		{
			if( $value == "")
			{
				$error = __('URL field is blank', 'adguru');
			}
			elseif(false == ADGURU_Helper::is_valid_url( $value ) ) 
			{
				$error = __('URL is not valid', 'adguru');
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


