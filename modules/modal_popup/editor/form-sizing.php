<?php

$sizing_form_args = array(
	'id' => 'mp_sizing_form',
	'header_callback' => 'adguru_modal_popup_form_sizing_header_callback',
	'footer_callback' => 'adguru_modal_popup_form_sizing_footer_callback',
	'fields' => array(
		'sizing_mode' => array(
			'type' 	=> 'radio',
			'id'	=> 'sizing_mode',
			'label'	=> __("Sizing Mode", 'adguru' ),
			'default'	=> 'responsive',
			'options' => array('responsive'=>__("Responsive", 'adguru' ), 'custom'=>__("Custom", 'adguru' )),
		),
		'sizing_responsive_size' => array(
			'type' 	=> 'select',
			'id'	=> 'sizing_responsive_size',
			'label'	=> __("Responsive Size", 'adguru' ),
			'default'	=> '40',
			'options' => array(
				//'auto'=>__("Auto", 'adguru' ), //Has issue with this option. We will think about this later.
				'10'=>__("10%", 'adguru' ),
				'20'=>__("20%", 'adguru' ),
				'30'=>__("30%", 'adguru' ),
				'40'=>__("40%", 'adguru' ),
				'50'=>__("50%", 'adguru' ),
				'60'=>__("60%", 'adguru' ),
				'70'=>__("70%", 'adguru' ),
				'80'=>__("80%", 'adguru' ),
				'90'=>__("90%", 'adguru' ),
				'100'=>__("100%", 'adguru' ),
				),
		),
		'sizing_custom_width_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_custom_width_group',
			'label' => __("Custom Width", 'adguru' ),
			'help' => __("Custom width of the popup container. Note : this value includes padding and border width value", 'adguru' ),
			'fields' => array(
				'sizing_custom_width' => array(
					'type' => 'number',
					'id' => 'sizing_custom_width',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_custom_width_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_custom_width_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_custom_width_group
		"sizing_auto_height" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'sizing_auto_height',
			'label' => __('Auto Adjusted Height', 'adguru' ),
			'default'	=> "1",
		),
		'sizing_custom_height_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_custom_height_group',
			'label' => __("Custom Height", 'adguru' ),
			'help' => __("Custom height of the popup container. Note : this value includes padding and border width value", 'adguru' ),
			'fields' => array(
				'sizing_custom_height' => array(
					'type' => 'number',
					'id' => 'sizing_custom_height',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_custom_height_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_custom_height_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_custom_width_group
		'sizing_max_width_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_max_width_group',
			'label' => __("Max Width", 'adguru' ),
			'help' => __("Maximum width of the popup container. Note : this value includes padding and border width value. Keep this 0 if you don't want to use max-width property", 'adguru' ),
			'fields' => array(
				'sizing_max_width' => array(
					'type' => 'number',
					'id' => 'sizing_max_width',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_max_width_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_max_width_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_max_width_group
		'sizing_min_width_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_min_width_group',
			'label' => __("Min Width", 'adguru' ),
			'help' => __("Minimum width of the popup container. Note : this value includes padding and border width value. Keep this 0 if you don't want to use min-width property", 'adguru' ),
			'fields' => array(
				'sizing_min_width' => array(
					'type' => 'number',
					'id' => 'sizing_min_width',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_min_width_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_min_width_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_max_width_group
		'sizing_max_height_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_max_height_group',
			'label' => __("Max Height", 'adguru' ),
			'help' => __("Maximum height of the popup container. Note : this value includes padding and border width value.  Keep this 0 if you don't want to use max-height property", 'adguru' ),
			'fields' => array(
				'sizing_max_height' => array(
					'type' => 'number',
					'id' => 'sizing_max_height',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_max_height_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_max_height_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_max_width_group
		'sizing_min_height_group' => array(
			'type'	=> 'group',
			'group_type' => 'horizontal',
			'id' => 'sizing_min_height_group',
			'label' => __("Min Height", 'adguru' ),
			'help' => __("Minimum height of the popup container. Note : this value includes padding and border width value.  Keep this 0 if you don't want to use min-height property", 'adguru' ),
			'fields' => array(
				'sizing_min_height' => array(
					'type' => 'number',
					'id' => 'sizing_min_height',
					'default' => '0',
					'size' => 'small',
					'min' => 0,
				),
				'sizing_min_height_unit' => array(
					'type' 	=> 'select',
					'id'	=> 'sizing_min_height_unit',
					'default'	=> 'px',
					'options' => array(
						'px'=> 'px',
						'%' => '%'
						),
				)
			)
		),//end of sizing_max_width_group
		"sizing_enable_scrollbar" => array(
			'type' 	=> 'checkbox',
			'id' 	=> 'sizing_enable_scrollbar',
			'label' => __('Enable Scrollbar', 'adguru' ),
			'default'	=> "1",
		),
		

	)//end of fields array 
); // end array $sizing_form_args

function adguru_modal_popup_form_sizing_header_callback( $form_obj )
{
	do_action('adguru_editor_form_modal_popup_sizing_top', $form_obj );
}

function adguru_modal_popup_form_sizing_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
		jQuery(document).on('wpafb-field:change:sizing_mode', function(event , args){
		
			var value = args['value'];
			if( value == 'responsive' )
			{
				WPAFB.showField('sizing_responsive_size');
				WPAFB.hideFieldGroup('sizing_custom_width_group');
			}
			else
			{
				WPAFB.hideField('sizing_responsive_size');
				WPAFB.showFieldGroup('sizing_custom_width_group');
			}
		});

		jQuery(document).on('wpafb-field:change:sizing_auto_height', function(event , args){
		
			var value = args['value'];
			if( value == 1 )
			{
				WPAFB.hideFieldGroup('sizing_custom_height_group');
			}
			else
			{
				WPAFB.showFieldGroup('sizing_custom_height_group');
			}
		});


	</script>
	<?php 
	do_action('adguru_editor_form_sizing_modal_popup_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$sizing_form_args = apply_filters('adguru_editor_form_modal_popup_sizing_args', $sizing_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$sizing_form_args['header_callback'] = 'adguru_modal_popup_form_sizing_header_callback';
$sizing_form_args['footer_callback'] = 'adguru_modal_popup_form_sizing_footer_callback';
//Create the form object
$sizing_form = adguru()->form_builder->create_form($sizing_form_args);


function adguru_show_modal_popup_sizing_form( $ad )
{

	$sizing_form = adguru()->form_builder->get_form('mp_sizing_form');
	if( $sizing_form )
	{ 
		$sizing_data = array();
		if(! isset($ad->sizing) || !is_array($ad->sizing) )
		{
			$ad->sizing = array();
		}
		else
		{
			foreach( $ad->sizing as $key => $value )
			{
				$id = 'sizing_'.$key;
				$sizing_data[$id] = $value;
			}
			
		}
		$sizing_form->set_data( $sizing_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		if( $sizing_form->get_value('sizing_mode') == 'responsive')
		{
			$sizing_form->set_hidden_field('sizing_custom_width_group');
		}
		else
		{
			$sizing_form->set_hidden_field('sizing_responsive_size');
		}

		if( $sizing_form->get_value('sizing_auto_height') == '1')
		{
			$sizing_form->set_hidden_field('sizing_custom_height_group');
		}
		
		do_action('adguru_editor_form_modal_popup_sizing_before_render', $sizing_form );

		//render the form
		$sizing_form->render();
		
	}//end if $sizing_form
}




