<?php

//GENERAL FORM 
$general_form = adguru()->form_builder->get_form('zone_general_form');
if( $general_form )
{ 
	$general_data = array();
	$submitted_data = $general_form->prepare_submitted_data();
	write_log($submitted_data);
	//return;
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('general_', '', $id );
		$general_data[$key] = $value;
	}
	//IMPORTANT : in this from , Data items will not be saved as an array in a single meta field. All array keys will create individual meta field.
	$general_data = apply_filters('adguru_zone_prepare_to_save_general_data', $general_data, $submitted_data );
	
	foreach( $general_data as $key => $value )
	{
		$zone->{$key} = $value;
	}

	write_log($zone);
}
