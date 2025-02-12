<?php

$animation_form_args = array(
	'id' => 'mp_animation_form',
	'header_callback' => 'adguru_modal_popup_form_animation_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_animation_footer_callback',
	'fields' => array(
		'animation_opening_animation_type' => array(
			'type' 	=> 'select',
			'id'	=> 'animation_opening_animation_type',
			'label'	=> __("Opening Animation Type", 'wp-ad-guru' ),
			'default'	=> 'none',
			'options' => array(
				'none' => __("None", 'wp-ad-guru' ),
				'bounce' => __("Bounce", 'wp-ad-guru' ),
				'swing' => __("Swing", 'wp-ad-guru' ),
				'fadeIn' => __("Fade In", 'wp-ad-guru' ),
				'slideInDown' => __("Slide In Down", 'wp-ad-guru' ),
				'slideInUp' => __("Slide In Up", 'wp-ad-guru' ),
			),
		),
		'animation_opening_animation_speed' => array(
			'type' => 'select',
			'id' => 'animation_opening_animation_speed',
			'label' => __("Opening Animation Speed", 'wp-ad-guru' ),
			'default' => 'normal',
			'options' => array(
				'normal' => __("Normal - 1s", 'wp-ad-guru' ),
				'slow' => __("Slow - 2s", 'wp-ad-guru' ),
				'slower' => __("Slower - 3s", 'wp-ad-guru' ),
				'fast' => __("Fast - 800ms", 'wp-ad-guru' ),
				'faster' => __("Faster - 500ms", 'wp-ad-guru' )
			)
		),
		'animation_closing_animation_type' => array(
			'type' 	=> 'select',
			'id'	=> 'animation_closing_animation_type',
			'label'	=> __("Closing Animation Type", 'wp-ad-guru' ),
			'default'	=> 'none',
			'options' => array(
				'none' => __("None", 'wp-ad-guru' ),
				'bounceOut' => __("Bounce Out", 'wp-ad-guru' ),
				'fadeOut' => __("Fade Out", 'wp-ad-guru' ),
				'slideOutDown' => __("Slide Out Down", 'wp-ad-guru' ),
				'slideOutUp' => __("Slide Out Up", 'wp-ad-guru' ),
			),
		),
		'animation_closing_animation_speed' => array(
			'type' => 'select',
			'id' => 'animation_closing_animation_speed',
			'label' => __("Closing Animation Speed", 'wp-ad-guru' ),
			'default' => 'normal',
			'options' => array(
				'normal' => __("Normal - 1s", 'wp-ad-guru' ),
				'slow' => __("Slow - 2s", 'wp-ad-guru' ),
				'slower' => __("Slower - 3s", 'wp-ad-guru' ),
				'fast' => __("Fast - 800ms", 'wp-ad-guru' ),
				'faster' => __("Faster - 500ms", 'wp-ad-guru' )
			)
		)
		

	)//end of fields array 
); // end array $animation_form_args

function adguru_modal_popup_form_animation_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_animation_top', $form_obj );
}

function adguru_modal_popup_form_animation_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_animation_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$animation_form_args = apply_filters('adguru_modal_popup_editor_form_animation_args', $animation_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$animation_form_args['header_callback'] = 'adguru_modal_popup_form_animation_header_callback';
$animation_form_args['footer_callback'] = 'adguru_modal_popup_form_animation_footer_callback';
//Create the form object
$animation_form = adguru()->form_builder->create_form($animation_form_args);


function adguru_show_modal_popup_animation_form( $ad )
{

	$animation_form = adguru()->form_builder->get_form('mp_animation_form');
	if( $animation_form )
	{ 
		$animation_data = array();
		if(! isset($ad->animation) || !is_array($ad->animation) )
		{
			$ad->animation = array();
		}
		else
		{
			foreach( $ad->animation as $key => $value )
			{
				$id = 'animation_'.$key;
				$animation_data[$id] = $value;
			}
			
		}
		$animation_form->set_data( $animation_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_modal_popup_animation_before_render', $animation_form );
		//render the form
		$animation_form->render();
		
	}//end if $animation_form
}




