<?php
$general_form_page_type_detail = adguru_get_page_types_multicheck_options_detail();

$general_form_args = array(
	'id' => 'zone_general_form',
	'header_callback' => 'adguru_zone_form_general_header_callback',
	'footer_callback' => 'adguru_zone_form_general_footer_callback',
	'fields' => array(
		'general_place' => array(
			'type' 	=> 'radio',
			'id'	=> 'general_place',
			'label'	=> __("Automatic Insert", 'adguru' ),
			'items_direction' => 'vertical',
			'default'	=> 'none',
			'options' => array(
				'none' => __("Disabled (  Do not insert automatically )", 'adguru' ),
				'before_posts' => __("Before Posts", 'adguru' ),
				'between_posts' => __("Between Posts", 'adguru' ),
				'after_posts' => __("After Posts", 'adguru' ),
				'before_content' => __("Before Content", 'adguru' ),
				'after_content' => __("After Content", 'adguru' ),
				'before_comments' => __("Before Comments", 'adguru' ),
				'between_comments' => __("Between Comments", 'adguru' ),
				'after_comments' => __("After Comments", 'adguru' ),
				'footer' => __("After Footer", 'adguru' ),
				
			),
		),

		'general_page_types_group' => array(
			'type'	=> 'group',
			'group_type' => 'vertical',
			'id' => 'general_page_types_group',
			'label' => __("Page Types", 'adguru' ),
			'help' => __("Select type of pages where you want to insert this zone", 'adguru' ),
			'fields' => array(
				'general_page_types_misc' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'general_page_types_misc',
					'label'	=> '',
					'fieldset' => array( 'legend'=> 'Misc pages' ),
					'items_direction' => 'vertical',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $general_form_page_type_detail['misc_type_page_defaults'],
					'options' => $general_form_page_type_detail['misc_type_page_options'],
				),

				'general_page_types_single' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'general_page_types_single',
					'fieldset' => array( 'legend'=> sprintf('<strong>%s</strong>', __("Single pages", 'adguru' ) ) ),
					'label'	=> '',
					'items_direction' => 'horizontal',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $general_form_page_type_detail['single_type_page_defaults'],
					'options' => $general_form_page_type_detail['single_type_page_options'],
				),
				'general_page_types_archive' => array(
					'type' 	=> 'multicheck',
					'id'	=> 'general_page_types_archive',
					'fieldset' => array( 'legend'=> sprintf('<strong>%s</strong>', __("Archive pages", 'adguru' ) ) ),
					'label'	=> '',
					'items_direction' => 'horizontal',
					'on_off_values' => array( "1", "0" ),
					'default'	=> $general_form_page_type_detail['archive_type_page_defaults'],
					'options' => $general_form_page_type_detail['archive_type_page_options'],
				),
			)
		),//end of general_page_types_group

		

	)//end of fields array 
); // end array $general_form_args

function adguru_zone_form_general_header_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_general_top', $form_obj );
}

function adguru_zone_form_general_footer_callback( $form_obj )
{
	do_action('adguru_editor_form_zone_general_bottom', $form_obj );
}

//Apply filters so that extensions can add/modify fields settings
$general_form_args = apply_filters('adguru_zone_editor_form_general_args', $general_form_args );
//reassign header and footer callback to prevent modification the value of  header and  footer callback key.
$general_form_args['header_callback'] = 'adguru_zone_form_general_header_callback';
$general_form_args['footer_callback'] = 'adguru_zone_form_general_footer_callback';
//Create the form object
$general_form = adguru()->form_builder->create_form($general_form_args);


function adguru_show_zone_general_form( $zone )
{

	$general_form = adguru()->form_builder->get_form('zone_general_form');
	if( $general_form )
	{ 
		$general_data = array();
		
		//IMPORTANT : in this from , Data items are not be saved as an array in a single meta field. All array keys are stored as individual meta field.

		$field_list = $general_form->get_field_list();
		foreach( $field_list as $id => $opts ){
			if( $opts['real_field'] != 1 ) { continue; }
			$key = ADGURU_Helper::str_replace_beginning('general_', '', $id );
			if( isset($zone->{$key} ) )
			{
				$general_data[ $id ] = $zone->{$key};
			}

		}
		//set merged page type related merged multicheck data for 3 fields
		if( isset( $zone->auto_insert_to_pages ) )
		{
			$auto_insert_to_pages = $zone->auto_insert_to_pages;
			$general_data[ 'general_page_types_misc' ] = $auto_insert_to_pages;
			$general_data[ 'general_page_types_single' ] = $auto_insert_to_pages;
			$general_data[ 'general_page_types_archive' ] = $auto_insert_to_pages;

		}
		
		$general_form->set_data( $general_data );
		//Before render modify the fields settings, specially update fields hidden status based on the value.
		do_action('adguru_editor_form_zone_general_before_render', $general_form );
		//render the form
		$general_form->render();
		
	}//end if $animation_form
}

function adguru_get_page_types_multicheck_options_detail(){
	$detail = array();
	$post_types = ADGURU_Helper::get_post_type_list();
	$taxonomy_list = ADGURU_Helper::get_taxonomy_list();

	$misc_type_page_options = array();
	$misc_type_page_defaults = array();

	$misc_type_page_options['home'] = __('Home page', 'adguru');
	$misc_type_page_defaults['home'] = 1;
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

	$archive_type_page_options['archive_search'] =  __('Search', 'adguru');
	$archive_type_page_defaults['archive_search'] = 0;

	$archive_type_page_options['archive_date'] =  __('Year/Month/Date', 'adguru');
	$archive_type_page_defaults['archive_date'] = 0;

	$detail['misc_type_page_options'] = $misc_type_page_options;
	$detail['misc_type_page_defaults'] = $misc_type_page_defaults;
	$detail['single_type_page_options'] = $single_type_page_options;
	$detail['single_type_page_defaults'] = $single_type_page_defaults;
	$detail['archive_type_page_options'] = $archive_type_page_options;
	$detail['archive_type_page_defaults'] = $archive_type_page_defaults;
	
	return $detail;

}


