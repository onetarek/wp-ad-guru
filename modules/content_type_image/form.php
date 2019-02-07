<?php

$content_image_form_args = array(
	'id' => 'content_image_form',
	'header_callback' => 'adguru_content_image_form_header_callback',
	'footer_callback' => 'adguru_content_image_form_footer_callback',
	'validation_callback' => 'adguru_content_image_form_validation_callback',
	'fields' => array(
		'content_image_source_url' => array(
			'type' => 'image',
			'id' => 'content_image_source_url',
			'label' => __('Image source Url', 'adguru'),
			'default' => '',
			'size' => 'medium',
			'placeholder' => __('Enter image url', 'adguru'),
		),
		'content_image_link_url' => array(
			'type' => 'url',
			'id' => 'content_image_link_url',
			'label' => __('Image link Url', 'adguru'),
			'default' => '',
			'size' => 'medium',
			'placeholder' => __('Enter image link url', 'adguru'),
		),
		'content_image_link_target' => array(
			'type' => 'select',
			'id' => 'content_image_link_target',
			'label' => __('Link target', 'adguru'),
			'default'	=> '_blank',
			'options' => array(
				'_blank'=> '_blank',
				'_self' => '_self',
				'_parent' => '_parent',
				'_top' => '_top'
				),
		),
		

	)//end of fields array 
); // end array $content_image_form_args

function adguru_content_image_form_header_callback( $form_obj )
{
	do_action('adguru_content_editor_form_image_main_top', $form_obj );
}

function adguru_content_image_form_footer_callback( $form_obj )
{ 
	do_action('adguru_editor_form_image_main_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$content_image_form_args = apply_filters('adguru_content_editor_form_image_main_args', $content_image_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$content_image_form_args['header_callback'] = 'adguru_content_image_form_header_callback';
$content_image_form_args['footer_callback'] = 'adguru_content_image_form_footer_callback';
//Create the form object
$content_image_form = adguru()->form_builder->create_form($content_image_form_args);


function adguru_show_content_image_form( $content )
{

	$content_image_form = adguru()->form_builder->get_form('content_image_form');
	if( $content_image_form )
	{ 
		$content_image_data = array();
		if(!is_array($content) )
		{
			$content = array();
		}
		else
		{
			foreach( $content as $key => $value )
			{
				$id = 'content_image_'.$key;
				$content_image_data[$id] = $value;
			}
			
		}
		$content_image_form->set_data( $content_image_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_content_image_before_render', $content_image_form );

		//render the form
		$content_image_form->render();
		
	}//end if $content_image_form
}


function adguru_content_image_form_validation_callback( $data, $args, $form )
{
	
	$id = $args['id'];
	$value = $data['value'];
	$error = '';
	switch( $id )
	{
		case 'content_image_source_url':
		{
			if( $value == "")
			{
				$error = __('Image source url is blank', 'adguru');
			}
			elseif(false == ADGURU_Helper::is_valid_url( $value ) ) 
			{
				$error = __('Image source url is not valid', 'adguru');
			}
			break;
		}
		case 'content_image_link_url':
		{
			if( $value == "")
			{
				$error = __('Image link url is blank', 'adguru');
			}
			elseif($value != '#' && false == ADGURU_Helper::is_valid_url( $value ) ) 
			{
				$error = __('Image link url is not valid', 'adguru');
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


