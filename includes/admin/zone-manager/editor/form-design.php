<?php

$design_form_args = array(
	'id' => 'zone_design_form',
	'header_callback' => 'adguru_zone_form_design_header_callback',
	'footer_callback' => 'adguru_zone_form_design_footer_callback',
	'fields' => array(

		'design_wrapper' => array(
			'type' 	=> 'radio',
			'id'	=> 'design_wrapper',
			'label'	=> __("Wrapper", 'adguru' ),
			'items_direction' => 'horizontal',
			'default'	=> '1',
			'options' => array(
				'1' => __("Use wrapper", 'adguru' ),
				'0' => __("No wrapper", 'adguru' )
				
			),
			'help' => __('Extra &lt;DIV&gt; element will wrap the zone html to set the alignment , margin etc.', 'adguru'),
		),
		'design_alignment' => array(
			'type' 	=> 'select',
			'id'	=> 'design_alignment',
			'label'	=> __("Alignment", 'adguru' ),
			'default'	=> 'center',
			'options' => array(
				'none' => __("None", 'adguru' ),
				'left' => __("Left", 'adguru' ),
				'center' => __("Center", 'adguru' ),
				'right' => __("Right", 'adguru' ),
				'float_left' => __("Float left", 'adguru' ),
				'float_right' => __("Float right", 'adguru' )
				
			),
		),

	)//end of fields array 
); // end array $design_form_args

function adguru_zone_form_design_header_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_design_top', $form_obj );
}

function adguru_zone_form_design_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
		jQuery(document).on('wpafb-field:change:design_wrapper', function(event , args){
		
			var value = args['value'];

			if( value == 1 )
			{
				WPAFB.showField('design_alignment');
				
			}
			else if( value == 0 )
			{
				WPAFB.hideField('design_alignment');
			}
		});


	</script>
	<?php
	do_action('adguru_editor_form_zone_design_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$design_form_args = apply_filters('adguru_zone_editor_form_design_args', $design_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$design_form_args['header_callback'] = 'adguru_zone_form_design_header_callback';
$design_form_args['footer_callback'] = 'adguru_zone_form_design_footer_callback';
//Create the form object
$design_form = adguru()->form_builder->create_form($design_form_args);


function adguru_show_zone_design_form( $zone )
{

	$design_form = adguru()->form_builder->get_form('zone_design_form');
	if( $design_form )
	{ 
		
		$design_data = array();
		if(! isset($zone->design) || !is_array($zone->design) )
		{
			$zone->design = array();
		}
		else
		{
			foreach( $zone->design as $key => $value )
			{
				$id = 'design_'.$key;
				$design_data[$id] = $value;
			}
			
		}

		$design_form->set_data( $design_data );
		
		//Before render modify the fields settings, specially update fields hidden status based on the value.

		$design_wrapper = $design_form->get_value('design_wrapper');
		if( $design_wrapper == 0)
		{
			$design_form->set_hidden_field('design_alignment');
		}
		

		do_action('adguru_editor_form_zone_design_before_render', $design_form );
		
		//render the form
		$design_form->render();
		
	}//end if $design_form
}


