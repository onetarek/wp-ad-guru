<?php
$inserter_form_page_type_detail = adguru_get_page_types_multicheck_options_detail();

$inserter_form_args = array(
	'id' => 'zone_inserter_form',
	'header_callback' => 'adguru_zone_form_inserter_header_callback',
	'footer_callback' => 'adguru_zone_form_inserter_footer_callback',
	'fields' => array(
		'inserter_enabled' => array(
			'type' 	=> 'radio',
			'id'	=> 'inserter_enabled',
			'label'	=> __("Enable", 'adguru' ),
			'items_direction' => 'horizontal',
			'default'	=> '0',
			'options' => array(
				'1' => __("Enable", 'adguru' ),
				'0' => __("Disable", 'adguru' )
				
			),
		),

		'inserter_place' => array(
			'type' 	=> 'radio',
			'id'	=> 'inserter_place',
			'label'	=> __("Place", 'adguru' ),
			'items_direction' => 'vertical',
			'default'	=> 'none',
			'options' => array(
				'none' => __("Nowhere", 'adguru' ),
				'before_post' => __("Before Post", 'adguru' ),
				'between_posts' => __("Between Posts", 'adguru' ),
				'after_post' => __("After Post", 'adguru' ),
				'before_content' => __("Before Content", 'adguru' ),
				'after_content' => __("After Content", 'adguru' ),
				'before_comments' => __("Before Comments", 'adguru' ),
				'between_comments' => __("Between Comments", 'adguru' ),
				'before_comment_form' => __("Before Comment Form", 'adguru' ),
				'after_comment_form' => __("After Comment Form", 'adguru' ),
				'before_footer' => __("Before Footer", 'adguru' ),
				'after_footer' => __("After Footer", 'adguru' ),
				
			),
		),

		'inserter_after_post_numbers' => array(
			'type'	=> 'text',
			'id'	=> 'inserter_after_post_numbers',
			'label'	=> __("After Post number(s)", 'adguru'),
			'desc' => __( 'Add post number(s) in the loop after which you want to show this zone. You can use multiple numbers separated by comma', 'adguru'),
			'default'	=> 0,
			'size'  => 'medium',

		),

		'inserter_after_comment_numbers' => array(
			'type'	=> 'text',
			'id'	=> 'inserter_after_comment_numbers',
			'label'	=> __("After Comment number(s)", 'adguru'),
			'desc' => __( 'Add comment number(s) in the loop after which you want to show this zone. You can use multiple numbers separated by comma', 'adguru'),
			'default'	=> 0,
			'size'  => 'medium',
		),

		'inserter_page_types_group' => array(
			'type'	=> 'group',
			'group_type' => 'vertical',
			'id' => 'inserter_page_types_group',
			'label' => __("Pages", 'adguru' ),
			'help' => __("Select type of pages where you want to insert this zone", 'adguru' ),
			'fields' => array(
				'inserter_page_types_misc' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'inserter_page_types_misc',
					'label'	=> '',
					'fieldset' => array( 'legend'=> 'Misc pages' ),
					'items_direction' => 'vertical',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $inserter_form_page_type_detail['misc_type_page_defaults'],
					'options' => $inserter_form_page_type_detail['misc_type_page_options'],
				),

				'inserter_page_types_single' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'inserter_page_types_single',
					'fieldset' => array( 'legend'=> sprintf('<strong>%s</strong>', __("Single pages", 'adguru' ) ) ),
					'label'	=> '',
					'items_direction' => 'horizontal',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $inserter_form_page_type_detail['single_type_page_defaults'],
					'options' => $inserter_form_page_type_detail['single_type_page_options'],
				),
				'inserter_page_types_archive' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'inserter_page_types_archive',
					'fieldset' => array( 'legend'=> sprintf('<strong>%s</strong>', __("Archive pages", 'adguru' ) ) ),
					'label'	=> '',
					'items_direction' => 'horizontal',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $inserter_form_page_type_detail['archive_type_page_defaults'],
					'options' => $inserter_form_page_type_detail['archive_type_page_options'],
				),
			)
		),//end of inserter_page_types_group

		

	)//end of fields array 
); // end array $inserter_form_args

function adguru_zone_form_inserter_header_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_inserter_top', $form_obj );
}

function adguru_zone_form_inserter_footer_callback( $form_obj )
{
	?>
	<script type="text/javascript">
		jQuery(document).on('wpafb-field:change:inserter_place', function(event , args){
		
			var value = args['value'];
			
			WPAFB.hideField('inserter_after_post_numbers');
			WPAFB.hideField('inserter_after_comment_numbers');

			if( value == 'between_posts' )
			{
				WPAFB.showField('inserter_after_post_numbers');
				
			}
			else if( value == 'between_comments' )
			{
				WPAFB.showField('inserter_after_comment_numbers');
			}
		});


	</script>
	<?php
	do_action('adguru_editor_form_zone_inserter_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$inserter_form_args = apply_filters('adguru_zone_editor_form_inserter_args', $inserter_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$inserter_form_args['header_callback'] = 'adguru_zone_form_inserter_header_callback';
$inserter_form_args['footer_callback'] = 'adguru_zone_form_inserter_footer_callback';
//Create the form object
$inserter_form = adguru()->form_builder->create_form($inserter_form_args);


function adguru_show_zone_inserter_form( $zone )
{

	$inserter_form = adguru()->form_builder->get_form('zone_inserter_form');
	if( $inserter_form )
	{ 
		
		$inserter_data = array();
		if(! isset($zone->inserter) || !is_array($zone->inserter) )
		{
			$zone->inserter = array();
		}
		else
		{
			foreach( $zone->inserter as $key => $value )
			{
				$id = 'inserter_'.$key;
				$inserter_data[$id] = $value;
			}
			
		}

		/* 
		KEEPING THIS UNNECESSARY DISABLED CODE FOR FUTURE REFERENCE
		WE CAN USE THIS TECHNIQUE FOR ANY OTHER FORM
		//IMPORTANT : in this from , Data items are not be saved as an array in a single meta field. All array keys are stored as individual meta field.
		$field_list = $inserter_form->get_field_list();
		foreach( $field_list as $id => $opts ){
			if( $opts['real_field'] != 1 ) { continue; }
			$key = ADGURU_Helper::str_replace_beginning('inserter_', '', $id );
			if( isset($zone->{$key} ) )
			{
				$inserter_data[ $id ] = $zone->{$key};
			}

		}
		*/

		//set merged page type related merged multicheck data for 3 fields
		if( isset( $inserter_data['inserter_page_types'] ) )
		{
			$inserter_data[ 'inserter_page_types_misc' ] = $inserter_data['inserter_page_types'];
			$inserter_data[ 'inserter_page_types_single' ] = $inserter_data['inserter_page_types'];
			$inserter_data[ 'inserter_page_types_archive' ] = $inserter_data['inserter_page_types'];
			unset( $inserter_data['inserter_page_types'] );

		}

		
		$inserter_form->set_data( $inserter_data );
		
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		$inserter_form->set_hidden_field('inserter_after_post_numbers');//hide
		$inserter_form->set_hidden_field('inserter_after_comment_numbers');//hide

		$inserter_place = $inserter_form->get_value('inserter_place');
		if( $inserter_place == 'between_posts')
		{
			$inserter_form->set_hidden_field('inserter_after_post_numbers', false);//unhide
		}
		elseif( $inserter_place == 'between_comments' )
		{
			$inserter_form->set_hidden_field('inserter_after_comment_numbers', false);//unhide
		}
		

		do_action('adguru_editor_form_zone_inserter_before_render', $inserter_form );
		
		//render the form
		$inserter_form->render();
		
	}//end if $inserter_form
}

function adguru_get_page_types_multicheck_options_detail(){
	$detail = array();
	$post_types = ADGURU_Helper::get_post_type_list();
	$taxonomy_list = ADGURU_Helper::get_taxonomy_list();

	$misc_type_page_options = array();
	$misc_type_page_defaults = array();

	$misc_type_page_options['home'] = __('Home page', 'adguru');
	$misc_type_page_defaults['home'] = 1;
	
	$misc_type_page_options['search'] =  __('Search result page', 'adguru');
	$misc_type_page_defaults['search'] = 0;

	$misc_type_page_options['404_not_found'] = __('404 page', 'adguru');
	$misc_type_page_defaults['404_not_found'] = 0;


	$single_type_page_options = array();
	$single_type_page_defaults = array();

	$single_type_page_options['single_any'] = __('Any single page', 'adguru');
	$single_type_page_defaults['single_any'] = 1;
	foreach( $post_types as $type => $name )
	{
		$single_type_page_options[ 'single_'.$type ] = $name;
		$single_type_page_defaults[ 'single_'.$type ] = 0;
	}

	$archive_type_page_options = array();
	$archive_type_page_defaults = array();
	$archive_type_page_options['archive_any'] =  __('Any archive page', 'adguru');
	$archive_type_page_defaults['archive_any'] = 1;
	foreach( $taxonomy_list as $key => $tax )
	{
		$archive_type_page_options['archive_'.$key] = $tax->label;
		$archive_type_page_defaults['archive_'.$key] = 0;
	}

	$archive_type_page_options['archive_author'] =  __('Author', 'adguru');
	$archive_type_page_defaults['archive_author'] = 0;

	$archive_type_page_options['archive_date'] =  __('Year/Month/Day', 'adguru');
	$archive_type_page_defaults['archive_date'] = 0;

	$detail['misc_type_page_options'] = $misc_type_page_options;
	$detail['misc_type_page_defaults'] = $misc_type_page_defaults;
	$detail['single_type_page_options'] = $single_type_page_options;
	$detail['single_type_page_defaults'] = $single_type_page_defaults;
	$detail['archive_type_page_options'] = $archive_type_page_options;
	$detail['archive_type_page_defaults'] = $archive_type_page_defaults;
	
	return $detail;

}


