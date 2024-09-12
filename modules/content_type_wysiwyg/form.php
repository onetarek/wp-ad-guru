<?php

$content_wysiwyg_form_args = array(
	'id' => 'content_wysiwyg_form',
	'header_callback' => 'adguru_content_wysiwyg_form_header_callback',
	'footer_callback' => 'adguru_content_wysiwyg_form_footer_callback',
	'validation_callback' => 'adguru_content_wysiwyg_form_validation_callback',
	'fields' => array(
		'content_wysiwyg_html' => array(
			'type' => 'editor',
			'id' => 'content_wysiwyg_html',
			'label' => __('Create your own', 'adguru'),
			'default' => '',
			'size' => 'large',
			'settings' => array('editor_height' => 300), //Do not use 'px' like '300px', use only integer value.
			'desc' => 'You can use <a href="https://codex.wordpress.org/Shortcode_API" target="_blank">Shortcodes</a> and <a href="https://codex.wordpress.org/Embeds" target="_blank">Embeds</a>',
		)
	)//end of fields array 
); // end array $content_wysiwyg_form_args

function adguru_content_wysiwyg_form_header_callback( $form_obj )
{
	do_action('adguru_content_editor_form_wysiwyg_main_top', $form_obj );
}

function adguru_content_wysiwyg_form_footer_callback( $form_obj )
{ 
	do_action('adguru_editor_form_wysiwyg_main_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$content_wysiwyg_form_args = apply_filters('adguru_content_editor_form_wysiwyg_main_args', $content_wysiwyg_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$content_wysiwyg_form_args['header_callback'] = 'adguru_content_wysiwyg_form_header_callback';
$content_wysiwyg_form_args['footer_callback'] = 'adguru_content_wysiwyg_form_footer_callback';
//Create the form object
$content_wysiwyg_form = adguru()->form_builder->create_form($content_wysiwyg_form_args);


function adguru_show_content_wysiwyg_form( $content )
{

	$content_wysiwyg_form = adguru()->form_builder->get_form('content_wysiwyg_form');
	if( $content_wysiwyg_form )
	{ 
		$content_wysiwyg_data = array();
		if(!is_array($content) )
		{
			$content = array();
		}
		else
		{
			foreach( $content as $key => $value )
			{
				$id = 'content_wysiwyg_'.$key;
				$content_wysiwyg_data[$id] = $value;
			}
			
		}
		$content_wysiwyg_form->set_data( $content_wysiwyg_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		
		do_action('adguru_editor_form_content_wysiwyg_before_render', $content_wysiwyg_form );

		//render the form
		$content_wysiwyg_form->render();
		
	}//end if $content_wysiwyg_form
}


function adguru_content_wysiwyg_form_validation_callback( $data, $args, $form )
{
	
	$id = $args['id'];
	$value = $data['value'];
	$error = '';
	switch( $id )
	{
		case 'content_wysiwyg_html':
		{
			if( $value == "")
			{
				$error = __('WYSIWYG Editor field is blank', 'adguru');
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


