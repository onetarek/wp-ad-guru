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
	if( $ad_from_db && isset($ad_from_db->design) && is_array( $ad_from_db->design ) )
	{
		$design_data = array_merge( $ad_from_db->design, $design_data );
	}
	$ad->design = $design_data;
}

//SIZING FORM 
$sizing_form = adguru()->form_builder->get_form('mp_sizing_form');
if( $sizing_form )
{ 
	$sizing_data = array();
	$submitted_data = $sizing_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('sizing_', '', $id );
		$sizing_data[$key] = $value;
	}
	$sizing_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_sizing_data', $sizing_data, $submitted_data );
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

//ANIMATION FORM 
$animation_form = adguru()->form_builder->get_form('mp_animation_form');
if( $animation_form )
{ 
	$animation_data = array();
	$submitted_data = $animation_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('animation_', '', $id );
		$animation_data[$key] = $value;
	}
	$animation_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_animation_data', $animation_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->animation) && is_array( $ad_from_db->animation ) )
	{
		$animation_data = array_merge( $ad_from_db->animation, $animation_data );
	}
	$ad->animation = $animation_data;
}


//POSITION FORM 
$position_form = adguru()->form_builder->get_form('mp_position_form');
if( $position_form )
{ 
	$position_data = array();
	$submitted_data = $position_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('position_', '', $id );
		$position_data[$key] = $value;
	}
	$position_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_position_data', $position_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->position) && is_array( $ad_from_db->position ) )
	{
		$position_data = array_merge( $ad_from_db->position, $position_data );
	}
	$ad->position = $position_data;
}


//TRIGGERING FORM 
$triggering_form = adguru()->form_builder->get_form('mp_triggering_form');
if( $triggering_form )
{ 
	$triggering_data = array();
	$submitted_data = $triggering_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('triggering_', '', $id );
		$triggering_data[$key] = $value;
	}
	$triggering_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_triggering_data', $triggering_data, $submitted_data );
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

//CLOSING FORM 
$closing_form = adguru()->form_builder->get_form('mp_closing_form');
if( $closing_form )
{ 
	$closing_data = array();
	$submitted_data = $closing_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('closing_', '', $id );
		$closing_data[$key] = $value;
	}
	$closing_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_closing_data', $closing_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->closing) && is_array( $ad_from_db->closing ) )
	{
		$closing_data = array_merge( $ad_from_db->closing, $closing_data );
	}
	$ad->closing = $closing_data;
}

//OTHER FORM
$other_form = adguru()->form_builder->get_form('mp_other_form');
if( $other_form )
{ 
	$other_data = array();
	$submitted_data = $other_form->prepare_submitted_data();
	foreach( $submitted_data as $id => $value )
	{
		$key = ADGURU_Helper::str_replace_beginning('other_', '', $id );
		$other_data[$key] = $value;
	}
	$other_data = apply_filters('adguru_ad_prepare_to_save_modal_popup_other_data', $other_data, $submitted_data );
	/*
	Keep old fields those are not exist with current submitted data. 
	We need this because some fields might be added by extension and extension may be deactivated now
	*/
	if( $ad_from_db && isset($ad_from_db->other) && is_array( $ad_from_db->other ) )
	{
		$other_data = array_merge( $ad_from_db->other, $other_data );
	}
	$ad->other = $other_data;
}
