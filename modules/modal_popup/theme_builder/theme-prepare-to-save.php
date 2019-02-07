<?php
//DESIGN FORM
$design_form = adguru()->form_builder->get_form('mp_design_form');
if( $design_form )
{ 
	$design_data = array();
	$submitted_data = $design_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('design_', '', $id );
		$design_data[$key] = $value;
	}
	$design_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_design_data', $design_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $theme_from_db && isset($theme_from_db->design) && is_array( $theme_from_db->design ) )
	{
		$design_data = array_merge( $theme_from_db->design, $design_data );
	}
	$theme->design = $design_data;
}