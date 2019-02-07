<?php

$position_form_args = array(
	'id' => 'mp_position_form',
	'header_callback' => 'adguru_modal_popup_form_position_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_position_footer_callback',
	'fields' => array(
		'position_location' => array(
			'type' 	=> 'select',
			'id' 	=> 'position_location',
			'label' => __('Location', 'adguru' ),
			'default'	=> 'middle_center',
			'options' => array(
				'top_left' 		=> 'Top Left',
				'top_center' 	=> 'Top Center',
				'top_right' 	=> 'Top Right',
				'middle_left' 	=> 'Middle Left',
				'middle_center' => 'Middle Center',
				'middle_right' 	=> 'Middle Right',
				'bottom_left' 	=> 'Bottom Left',
				'bottom_center' => 'Bottom Center',
				'bottom_right' 	=> 'Bottom Right'
			),
			'help' => __('Choose the location of popup in the screen', 'adguru' ),
		),
		'position_top' => array(
			'type' 	=> 'slider',
			'id' 	=> 'position_top',
			'label' => __('Top', 'adguru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 400,
			'step' 	=> 1,
			'display_text' => 'px',
			'hidden' => true,
		),
		'position_left' => array(
			'type' 	=> 'slider',
			'id' 	=> 'position_left',
			'label' => __('Left', 'adguru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 400,
			'step' 	=> 1,
			'display_text' => 'px',
			'hidden' => true,
		),
		'position_right' => array(
			'type' 	=> 'slider',
			'id' 	=> 'position_right',
			'label' => __('Right', 'adguru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 400,
			'step' 	=> 1,
			'display_text' => 'px',
			'hidden' => true,
		),
		'position_bottom' => array(
			'type' 	=> 'slider',
			'id' 	=> 'position_bottom',
			'label' => __('Bottom', 'adguru' ),
			'default'	=> 0,
			'min' 	=> 0,
			'max' 	=> 400,
			'step' 	=> 1,
			'display_text' => 'px',
			'hidden' => true,
		),
		

	)//end of fields array 
); // end array $position_form_args

function adguru_modal_popup_form_position_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_position_top', $form_obj );
}

function adguru_modal_popup_form_position_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">

		jQuery(document).on('wpafb-field:change:position_location', function(event , args){
				
			var value = args['value'];
			WPAFB.hideField('position_top');
			WPAFB.hideField('position_left');
			WPAFB.hideField('position_right');
			WPAFB.hideField('position_bottom');

			if( value == 'top_left')
			{
				WPAFB.showField('position_top');
				WPAFB.showField('position_left');
			}
			else if( value == 'top_center')
			{
				WPAFB.showField('position_top'); 
			}
			else if( value == 'top_right')
			{
				WPAFB.showField('position_top');
				WPAFB.showField('position_right');
			}
			else if( value == 'middle_left')
			{
				WPAFB.showField('position_left');
			}
			else if( value == 'middle_right')
			{
				WPAFB.showField('position_right');
			}
			else if( value == 'bottom_left')
			{
				WPAFB.showField('position_left');
				WPAFB.showField('position_bottom'); 
			}
			else if( value == 'bottom_center')
			{
				WPAFB.showField('position_bottom'); 
			}
			else if( value == 'bottom_right')
			{
				WPAFB.showField('position_bottom');
				WPAFB.showField('position_right');
			}

		});

	</script>
	<?php 
	do_action('adguru_editor_form_modal_popup_position_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$position_form_args = apply_filters('adguru_editor_form_modal_popup_position_args', $position_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$position_form_args['header_callback'] = 'adguru_modal_popup_form_position_header_callback';
$position_form_args['footer_callback'] = 'adguru_modal_popup_form_position_footer_callback';
//Create the form object
$position_form = adguru()->form_builder->create_form($position_form_args);


function adguru_show_modal_popup_position_form( $ad )
{

	$position_form = adguru()->form_builder->get_form('mp_position_form');
	if( $position_form )
	{ 
		$position_data = array();
		if(! isset($ad->position) || !is_array($ad->position) )
		{
			$ad->position = array();
		}
		else
		{
			foreach( $ad->position as $key => $value )
			{
				$id = 'position_'.$key;
				$position_data[$id] = $value;
			}
			
		}
		$position_form->set_data( $position_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		$position_location= $position_form->get_value('position_location');
		if( $position_location == 'top_left')
		{
			$position_form->set_hidden_field('position_top', false ); // false to show the field
			$position_form->set_hidden_field('position_left', false );
		}
		elseif( $position_location == 'top_center')
		{
			$position_form->set_hidden_field('position_top', false ); 
		}
		elseif( $position_location == 'top_right')
		{
			$position_form->set_hidden_field('position_top', false ); 
			$position_form->set_hidden_field('position_right', false );
		}
		elseif( $position_location == 'middle_left')
		{
			$position_form->set_hidden_field('position_left', false ); 
		}
		elseif( $position_location == 'middle_right')
		{
			$position_form->set_hidden_field('position_right', false ); 
		}
		elseif( $position_location == 'bottom_left')
		{
			$position_form->set_hidden_field('position_left', false );
			$position_form->set_hidden_field('position_bottom', false ); 
		}
		elseif( $position_location == 'bottom_center')
		{
			$position_form->set_hidden_field('position_bottom', false ); 
		}
		elseif( $position_location == 'bottom_right')
		{
			$position_form->set_hidden_field('position_bottom', false ); 
			$position_form->set_hidden_field('position_right', false );
		}
		
		do_action('adguru_editor_form_modal_popup_position_before_render', $position_form );

		//render the form
		$position_form->render();
		
	}//end if $position_form
}




