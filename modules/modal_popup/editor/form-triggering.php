<?php

$triggering_form_args = array(
	'id' => 'mp_triggering_form',
	'header_callback' => 'adguru_modal_popup_form_triggering_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_triggering_footer_callback',
	'fields' => array(
		'triggering_auto_open_enable' => array(
			'type' 	=> 'checkbox',
			'id'	=> 'triggering_auto_open_enable',
			'label'	=> __("Popup Auto Open", 'adguru' ),
			'default'	=> '1'
		),
		'triggering_auto_open_delay' => array(
			'type' => 'slider',
			'id' => 'triggering_auto_open_delay',
			'label' => __("Auto Open Delay", 'adguru' ),
			'default' => 0,
			'max' => 600,
			'min' => 0,
			'step' => 1,
			'display_text' => 's',
			'help' => 'Number in seconds'
		),
		'triggering_limitation_show_always' => array(
			'type' 	=> 'checkbox',
			'id'	=> 'triggering_limitation_show_always',
			'label'	=> __("Show always", 'adguru' ),
			'default'	=> '0'
		),
		'triggering_limitation_show_always_group' => array(
			'type' => 'group',
			'group_type' => 'row',
			'id' => 'triggering_limitation_show_always_group',
			'fields' => array(
				'triggering_limitation_showing_count' => array(
					'type' => 'number',
					'id' => 'triggering_limitation_showing_count',
					'label' => __("Popup showing count", 'adguru' ),
					'size' => 'small',
					'default' => 1,
					'max' => 50,
					'min' => 1,
					'help' => __( 'Select how many times the popup will be shown for the same user', 'adguru' )
				),
				'triggering_limitation_reset_count_after_days' => array(
					'type' => 'number',
					'id' => 'triggering_limitation_reset_count_after_days',
					'label' => __("Reset count after days", 'adguru' ),
					'size' => 'small',
					'default' => 7,
					'min' => 0,
					'unit_text' => __('days', 'adguru')
				)
			)
		),//end triggering_limitation_show_always_group
		'triggering_limitation_apply_for_individual_page' => array(
			'type' 	=> 'checkbox',
			'id'	=> 'triggering_limitation_apply_for_individual_page',
			'label'	=> __("Apply limitation for each page individually", 'adguru' ),
			'default'	=> '0'
		),
		

	)//end of fields array 
); // end array $triggering_form_args

function adguru_modal_popup_form_triggering_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_triggering_top', $form_obj );
}

function adguru_modal_popup_form_triggering_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
	jQuery(document).on('wpafb-field:change:triggering_auto_open_enable', function(event , args){
	
		var value = args['value'];
		if( value == 0 )
		{
			WPAFB.hideField('triggering_auto_open_delay');
		}
		else
		{
			WPAFB.showField('triggering_auto_open_delay');
		}
	});

	jQuery(document).on('wpafb-field:change:triggering_limitation_show_always', function(event , args){
	
		var value = args['value'];
		if( value == 1 )
		{
			WPAFB.hideFieldGroup('triggering_limitation_show_always_group');
		}
		else
		{
			WPAFB.showFieldGroup('triggering_limitation_show_always_group');
		}
	});
	</script>
	<?php
	do_action('adguru_editor_form_modal_popup_triggering_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$triggering_form_args = apply_filters('adguru_editor_form_modal_popup_triggering_args', $triggering_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$triggering_form_args['header_callback'] = 'adguru_modal_popup_form_triggering_header_callback';
$triggering_form_args['footer_callback'] = 'adguru_modal_popup_form_triggering_footer_callback';
//Create the form object
$triggering_form = adguru()->form_builder->create_form($triggering_form_args);


function adguru_show_modal_popup_triggering_form( $ad )
{

	$triggering_form = adguru()->form_builder->get_form('mp_triggering_form');
	if( $triggering_form )
	{ 
		$triggering_data = array();
		if(! isset($ad->triggering) || !is_array($ad->triggering) )
		{
			$ad->triggering = array();
		}
		else
		{
			foreach( $ad->triggering as $key => $value )
			{
				$id = 'triggering_'.$key;
				$triggering_data[$id] = $value;
			}
			
		}
		$triggering_form->set_data( $triggering_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		if( $triggering_form->get_value('triggering_auto_open_enable') == '0')
		{
			$triggering_form->set_hidden_field('triggering_auto_open_delay');
		}
		if( $triggering_form->get_value('triggering_limitation_show_always') == '1')
		{
			$triggering_form->set_hidden_field('triggering_limitation_show_always_group');
		}

		do_action('adguru_editor_form_modal_popup_triggering_before_render', $triggering_form );
		//render the form
		$triggering_form->render();
		
	}//end if $triggering_form
}




