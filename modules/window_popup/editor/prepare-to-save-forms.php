<?php

//SIZING FORM 
$sizing_form = adguru()->form_builder->get_form('winp_sizing_form');
if( $sizing_form )
{ 
	$sizing_data = array();
	$submitted_data = $sizing_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('sizing_', '', $id );
		$sizing_data[$key] = $value;
	}
	$sizing_data = apply_filters('adguru_ad_prepare_to_save_window_popup_sizing_data', $sizing_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->sizing) && is_array( $ad_from_db->sizing ) )
	{
		$sizing_data = array_merge( $ad_from_db->sizing, $sizing_data );
	}
	$ad->sizing = $sizing_data;
}

//POPUP OPTIONS FORM 
$popup_options_form = adguru()->form_builder->get_form('winp_popup_options_form');
if( $popup_options_form )
{ 
	$popup_options_data = array();
	$submitted_data = $popup_options_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('popup_options_', '', $id );
		$popup_options_data[$key] = $value;
	}
	$popup_options_data = apply_filters('adguru_ad_prepare_to_save_window_popup_popup_options_data', $popup_options_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->popup_options) && is_array( $ad_from_db->popup_options ) )
	{
		$popup_options_data = array_merge( $ad_from_db->popup_options, $popup_options_data );
	}
	$ad->popup_options = $popup_options_data;
}


//TRIGGERING FORM 
$triggering_form = adguru()->form_builder->get_form('winp_triggering_form');
if( $triggering_form )
{ 
	$triggering_data = array();
	$submitted_data = $triggering_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('triggering_', '', $id );
		$triggering_data[$key] = $value;
	}
	$triggering_data = apply_filters('adguru_ad_prepare_to_save_window_popup_triggering_data', $triggering_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->triggering) && is_array( $ad_from_db->triggering ) )
	{
		$triggering_data = array_merge( $ad_from_db->triggering, $triggering_data );
	}
	$ad->triggering = $triggering_data;
}