<?php

$animation_form_args = array(
	'id' => 'mp_animation_form',
	'header_callback' => 'adguru_modal_popup_form_animation_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_animation_footer_callback',
	'fields' => array(
		'animation_opening_animation_type' => array(
			'type' 	=> 'select',
			'id'	=> 'animation_opening_animation_type',
			'label'	=> __("Opening Animation Type", 'adguru' ),
			'default'	=> 'none',
			'options' => array(
				'none' => __("None", 'adguru' ),
				'bounce' => __("Bounce", 'adguru' ),
				'swing' => __("Swing", 'adguru' ),
				'fadeIn' => __("Fade In", 'adguru' ),
				'slideInDown' => __("Slide In Down", 'adguru' ),
				'slideInUp' => __("Slide In Up", 'adguru' ),
			),
		),
		'animation_opening_animation_speed' => array(
			'type' => 'select',
			'id' => 'animation_opening_animation_speed',
			'label' => __("Opening Animation Speed", 'adguru' ),
			'default' => 'normal',
			'options' => array(
				'normal' => __("Normal - 1s", 'adguru' ),
				'slow' => __("Slow - 2s", 'adguru' ),
				'slower' => __("Slower - 3s", 'adguru' ),
				'fast' => __("Fast - 800ms", 'adguru' ),
				'faster' => __("Faster - 500ms", 'adguru' )
			)
		),
		'animation_closing_animation_type' => array(
			'type' 	=> 'select',
			'id'	=> 'animation_closing_animation_type',
			'label'	=> __("Closing Animation Type", 'adguru' ),
			'default'	=> 'none',
			'options' => array(
				'none' => __("None", 'adguru' ),
				'bounceOut' => __("Bounce Out", 'adguru' ),
				'fadeOut' => __("Fade Out", 'adguru' ),
				'slideOutDown' => __("Slide Out Down", 'adguru' ),
				'slideOutUp' => __("Slide Out Up", 'adguru' ),
			),
		),
		'animation_closing_animation_speed' => array(
			'type' => 'select',
			'id' => 'animation_closing_animation_speed',
			'label' => __("Closing Animation Speed", 'adguru' ),
			'default' => 'normal',
			'options' => array(
				'normal' => __("Normal - 1s", 'adguru' ),
				'slow' => __("Slow - 2s", 'adguru' ),
				'slower' => __("Slower - 3s", 'adguru' ),
				'fast' => __("Fast - 800ms", 'adguru' ),
				'faster' => __("Faster - 500ms", 'adguru' )
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




