<?php

$visibility_form_args = array(
	'id' => 'zone_visibility_form',
	'header_callback' => 'adguru_zone_form_visibility_header_callback',
	'footer_callback' => 'adguru_zone_form_visibility_footer_callback',
	'fields' => array(
		'visibility_show_on_screen_size' => array(
			'type' 	=> 'radio',
			'id'	=> 'visibility_show_on_screen_size',
			'label'	=> __( 'Show on screen size', 'adguru' ),
			'items_direction' => 'horizontal',
			'default'	=> 'all',
			'options' => array(
				'all' => __( 'All screen', 'adguru' ),
				'custom' => __( 'Custom', 'adguru' )
				
			),
		),

		'visibility_screen_size_group' => array(
			'type' 	=> 'group',
			'group_type' => 'row',
			'id' 	=> 'visibility_screen_size_group',
			'fieldset' => array( 'legend' => __( 'Condition', 'adguru' ) ),
			'fields'=> array(
				'visibility_screen_min_width' => array(
					'type' => 'number',
					'id' => 'visibility_screen_min_width',
					'label'	=> __( 'Screen min width', 'adguru' ),
					'default' => '0',
					'size' => 'small',
					'min' => 0,
					'unit_text' => 'px',
					'desc' => __( 'Set minimum screen width. 0 for no limit.', 'adguru' )
				),

				'visibility_screen_max_width' => array(
					'type' => 'number',
					'id' => 'visibility_screen_max_width',
					'label'	=> __( 'Screen max width', 'adguru' ),
					'default' => '0',
					'size' => 'small',
					'min' => 0,
					'unit_text' => 'px',
					'desc' => __( 'Set maximum screen width. 0 for no limit.', 'adguru' )
				),

				'visibility_screen_help_text' => array(
					'type' => 'html',
					'id' => 'visibility_screen_help_text',
					'single_column' => true,
					'value'	=> '<div style="color:#999999">
								<strong>' . __( 'Some common breakpoints for widths of devices', 'adguru' ) . '</strong>
								<ul>
									<li>320px — 480px : Mobile devices</li>
									<li>481px — 768px : iPads, Tablets</li>
									<li>769px — 1024px : Small screens, laptops</li>
									<li>1025px — 1200px : Desktops, large screens</li>
									<li>1201px and more :  Extra large screens, TV</li>
								</ul>
								</div>',
				)
			)
		),

	)//end of fields array 
); // end array $visibility_form_args

function adguru_zone_form_visibility_header_callback( $form_obj )
{
	do_action( 'adguru_editor_form_zone_visibility_top', $form_obj );
}

function adguru_zone_form_visibility_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
		jQuery(document).on('wpafb-field:change:visibility_show_on_screen_size', function( event, args ){
		
			var value = args['value'];

			if( value == 'custom' )
			{
				WPAFB.showFieldGroup( 'visibility_screen_size_group' );
			}
			else if( value == 'all' )
			{
				WPAFB.hideFieldGroup( 'visibility_screen_size_group' );
			}
		});
	</script>
<?php
	do_action( 'adguru_editor_form_zone_visibility_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$visibility_form_args = apply_filters( 'adguru_zone_editor_form_visibility_args', $visibility_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$visibility_form_args['header_callback'] = 'adguru_zone_form_visibility_header_callback';
$visibility_form_args['footer_callback'] = 'adguru_zone_form_visibility_footer_callback';
//Create the form object
$visibility_form = adguru()->form_builder->create_form( $visibility_form_args );


function adguru_show_zone_visibility_form( $zone )
{

	$visibility_form = adguru()->form_builder->get_form( 'zone_visibility_form' );
	if( $visibility_form )
	{ 
		
		$visibility_data = array();
		if(! isset( $zone->visibility ) || !is_array( $zone->visibility ) )
		{
			$zone->visibility = array();
		}
		else
		{
			foreach( $zone->visibility as $key => $value )
			{
				$id = 'visibility_'.$key;
				$visibility_data[$id] = $value;
			}
			
		}

		$visibility_form->set_data( $visibility_data );
		
		//Before render modify the fields settings, specially update fields hidden status based on the value.

		$visibility_show_on_screen_size = $visibility_form->get_value( 'visibility_show_on_screen_size' );
		if( $visibility_show_on_screen_size == 'all' )
		{
			$visibility_form->set_hidden_field( 'visibility_screen_size_group' );
		}
		

		do_action( 'adguru_editor_form_zone_visibility_before_render', $visibility_form );
		
		//render the form
		$visibility_form->render();
		
	}//end if $visibility_form
}


