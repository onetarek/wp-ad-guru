<?php

$content_iframe_form_args = array(
	'id' => 'content_iframe_form',
	'header_callback' => 'adguru_content_iframe_form_header_callback',
	'footer_callback' => 'adguru_content_iframe_form_footer_callback',
	'validation_callback' => 'adguru_content_iframe_form_validation_callback',
	'fields' => array(
		'content_iframe_source_url' => array(
			'type' => 'url',
			'id' => 'content_iframe_source_url',
			'label' => __('Link in iFrame', 'adguru'),
			'default' => '',
			'size' => 'medium',
			'placeholder' => __('Enter url', 'adguru'),
		),
		'content_iframe_scrolling' => array(
			'type' => 'select',
			'id' => 'content_iframe_scrolling',
			'label' => __('Use Scolling', 'adguru'),
			'default'	=> 'yes',
			'options' => array(
				'yes'=> 'Yes',
				'no' => 'No'
				),
		),
		

	)//end of fields array 
); // end array $content_iframe_form_args

function adguru_content_iframe_form_header_callback( $form_obj )
{
	do_action('adguru_content_editor_form_iframe_main_top', $form_obj );
}

function adguru_content_iframe_form_footer_callback( $form_obj )
{ 
	do_action('adguru_editor_form_iframe_main_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$content_iframe_form_args = apply_filters('adguru_content_editor_form_iframe_main_args', $content_iframe_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$content_iframe_form_args['header_callback'] = 'adguru_content_iframe_form_header_callback';
$content_iframe_form_args['footer_callback'] = 'adguru_content_iframe_form_footer_callback';
//Create the form object
$content_iframe_form = adguru()->form_builder->create_form($content_iframe_form_args);


function adguru_show_content_iframe_form( $content )
{

	$content_iframe_form = adguru()->form_builder->get_form('content_iframe_form');
	if( $content_iframe_form )
	{ 
		$content_iframe_data = array();
		if(!is_array($content) )
		{
			$content = array();
		}
		else
		{
			foreach( $content as $key => $value )
			{
				$id = 'content_iframe_'.$key;
				$content_iframe_data[$id] = $value;
			}
			
		}
		$content_iframe_form->set_data( $content_iframe_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_content_iframe_before_render', $content_iframe_form );

		//render the form
		$content_iframe_form->render();
		
	}//end if $content_iframe_form
}


function adguru_content_iframe_form_validation_callback( $data, $args, $form )
{
	
	$id = $args['id'];
	$value = $data['value'];
	$error = '';
	switch( $id )
	{
		case 'content_iframe_source_url':
		{
			if( $value == "")
			{
				$error = __('iFrame source url is blank', 'adguru');
			}
			elseif(false == ADGURU_Helper::is_valid_url( $value ) ) 
			{
				$error = __('iFrame source url is not valid', 'adguru');
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


