<?php


//DESIGN FORM
$design_form = adguru()->form_builder->get_form('zone_design_form');
if( $design_form )
{ 
	$design_data = array();
	$submitted_data = $design_form->prepare_submitted_data();
	
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('design_', '', $id );
		$design_data[$key] = $value;
	}
	
	$design_data = apply_filters('adguru_zone_prepare_to_save_design_data', $design_data, $submitted_data );
	
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $zone_from_db && isset($zone_from_db->design) && is_array( $zone_from_db->design ) )
	{
		$design_data = array_merge( $zone_from_db->design, $design_data );
	}
	$zone->design = $design_data;
}

//VISIBILITY FORM
$visibility_form = adguru()->form_builder->get_form('zone_visibility_form');
if( $visibility_form )
{ 
	$visibility_data = array();
	$submitted_data = $visibility_form->prepare_submitted_data();
	
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('visibility_', '', $id );
		$visibility_data[$key] = $value;
	}
	
	$visibility_data = apply_filters('adguru_zone_prepare_to_save_visibility_data', $visibility_data, $submitted_data );
	
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $zone_from_db && isset($zone_from_db->visibility) && is_array( $zone_from_db->visibility ) )
	{
		$visibility_data = array_merge( $zone_from_db->visibility, $visibility_data );
	}
	$zone->visibility = $visibility_data;
}

//INSERTER FORM 
$inserter_form = adguru()->form_builder->get_form('zone_inserter_form');
if( $inserter_form )
{ 
	$inserter_data = array();
	$submitted_data = $inserter_form->prepare_submitted_data();
	
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('inserter_', '', $id );
		$inserter_data[$key] = $value;
	}
	
	$inserter_data = apply_filters('adguru_zone_prepare_to_save_inserter_data', $inserter_data, $submitted_data );
	
	//merge 3 page types related multicheck data to save in single field
	$inserter_data['page_types']  = array_merge($inserter_data[ 'page_types_misc' ], $inserter_data[ 'page_types_single' ], $inserter_data[ 'page_types_archive' ] );
	unset($inserter_data[ 'page_types_misc' ], $inserter_data[ 'page_types_single' ], $inserter_data[ 'page_types_archive' ]);
	
	
	/*
		KEEPING THIS UNNECESSARY DISABLED CODE FOR FUTURE REFERENCE
		WE CAN USE THIS TECHNIQUE FOR ANY OTHER FORM
		//IMPORTANT : in this from , Data items will not be saved as an array in a single meta field. All array keys will create individual meta field.
	foreach( $inserter_data as $key => $value )
	{
		if( ADGURU_Helper::is_valid_variable_name( $key ) )
		{
			$zone->{$key} = $value;
		}
		
	}
	*/
	
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $zone_from_db && isset($zone_from_db->inserter) && is_array( $zone_from_db->inserter ) )
	{
		$inserter_data = array_merge( $zone_from_db->inserter, $inserter_data );
	}
	$zone->inserter = $inserter_data;
}

