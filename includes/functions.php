<?php
// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

#=====================================INTERNAL AND ADDON FUNCTOINS ==============================================

function adguru_set_ad_input_error( $field, $msg )
{
	adguru()->error->set_ad_input_error( $field , $msg );
}

function adguru_get_ad_input_error()
{
	return adguru()->error->get_ad_input_error();
}

function adguru_set_zone_input_error( $field, $msg )
{
	adguru()->error->set_zone_input_error( $field , $msg );
}

function adguru_get_zone_input_error()
{
	return adguru()->error->get_zone_input_error();
}

function adguru_html_redirect( $redirect_to )
{
	echo '<META http-equiv="refresh" content="0;URL='.$redirect_to.'">';
	echo '<div><p>This page is redirecting.... If you can see me , <a href="'.$redirect_to.'">please click here</a> to redirect manually</p></div>';
}


/**
 * Get an option value
 *
 * Return a value from the global $adguru_options array. If the specified key not existing in the array then return $default
 * @param string $key option key name
 * @param mixed $default
 * @return mixed
 * @since 2.0.0
 */
function adguru_get_option( $key, $default = NULL )
{
	global $adguru_options;
	if( !is_array( $adguru_options ) )
	{
		$adguru_options = adguru_get_settings();
	}
	$value = isset( $adguru_options[ $key ] ) ? $adguru_options[ $key ] : $default;
	$value = apply_filters( 'adguru_get_option', $key, $value , $default );
	return apply_filters( "adguru_get_option_{$key}", $value, $default );
}

/**
 * Update an option
 * Updates an adguru setting value in both the db and the global variable.
 * Warning: Passing in an null string value will remove
 *          the key from the adguru_options array.
 * 
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 * @return bool true if updated, false if not.
 * @since 2.0.0
 */
function adguru_update_option( $key, $value = NULL )
{

	#If no key return
	if ( empty( $key ) )
	{
		return false;
	}

	if ( $value === NULL )
	{
		$removed = adguru_delete_option( $key );
		return $removed;
	}

	#Pull the current settings
	$options = adguru_get_settings();

	#Let other developers alter the value coming in
	$value = apply_filters( 'adguru_update_option', $key, $value );
	$value = apply_filters( "adguru_update_option_{$key}", $value );

	#Try to update the value
	$options[ $key ] = $value;
	$did_update = adguru_update_settings( $options );

	#If it updated, update the global adguru option variable
	if ( $did_update )
	{
		global $adguru_options;
		$adguru_options = $options;
	}

	return $did_update;
}

/**
 * Delete an option
 *
 * Removes an adguru setting value in both the db and the global variable.
 *
 * @param string $key The key to delete
 * @return bool true if updated, false if not.
 * @since 2.0.0
 */
function adguru_delete_option( $key )
{

	#If no key return
	if ( empty( $key ) )
	{
		return false;
	}

	#Pull the current settings
	$options = adguru_get_settings();

	#Try to update the value
	if( isset( $options[ $key ] ) )
	{
		unset( $options[ $key ] );
	}
	
	$did_update = adguru_update_settings( $options );

	#If it updated, update the global variable
	if ( $did_update )
	{
		global $adguru_options;
		$adguru_options = $options;
	}

	return $did_update;
}

/**
 * Get All Settings
 *
 * @since 2.0.0
 * @return array ADGURU settings
 */
function adguru_get_settings()
{
	$settings = get_option( ADGURU_OPTIONS_FIELD_NAME , array() );
	return apply_filters( 'adguru_get_settings', $settings );
}

/**
 * Save All Settings to option table
 *
 * @since 2.0.0
 * @param array $settings
 * @return bool . True if option value has changed, false if not or if update failed.
 */
function adguru_update_settings( $settings )
{
	$settings = apply_filters( 'adguru_get_settings', $settings );
	return update_option( ADGURU_OPTIONS_FIELD_NAME, $settings );
}

/**
 * Add new ad type settings to regirster
 * This function must be called within adguru_init hook
 * @since 2.0.0
 * @param string $ad_type
 * @param array $args ad type settings
 * @return void
 */
 function adguru_register_ad_type( $ad_type, $args )
 {
 	adguru()->ad_types->add( $ad_type, $args );
 }

 /**
 * Add new content type settings to regirster
 * This function must be called within adguru_init hook
 * @since 2.0.0
 * @param string $content_type
 * @param array $args content type settings
 * @return void
 */
 function adguru_register_content_type( $content_type, $args )
 {
 	adguru()->content_types->register( $content_type, $args );
 }

 /**
  * A global variable to store requested stylesheet names to load in footer
  */
 global $adguru_requested_stylesheet_for_footer;
 		$adguru_requested_stylesheet_for_footer = array();
 
 /** 
  * Request to load a stylesheet in footer
  * @param string $stylesheet_name
  */
 function adguru_enqueue_style_in_footer( $stylesheet_name )
 {
 	global $adguru_requested_stylesheet_for_footer;
 	if( !in_array($stylesheet_name, $adguru_requested_stylesheet_for_footer ) )
 	{
 		$adguru_requested_stylesheet_for_footer[] = $stylesheet_name;
 	}
 }

 /** 
  * Load requested stylesheet in footer
  * @param string $stylesheet_name
  */
 function adguru_load_styles_in_footer()
 {
 	$stylesheet_list = array(
 		'animate' => array( 'url' => ADGURU_PLUGIN_URL.'assets/css/animate.min.css' )
 	);
 	global $adguru_requested_stylesheet_for_footer;
 	foreach( $adguru_requested_stylesheet_for_footer as $stylesheet )
 	{
 		if( isset( $stylesheet_list[ $stylesheet ] ) )
 		{
 			$url = $stylesheet_list[ $stylesheet ]['url'];
 			echo '<link rel="stylesheet" type="text/css" href="'.esc_url( $url ).'" >';
 		}
 	}

 }

#=====================================PUBLIC FUNCTIONS==========================================================
function adguru_ad( $ad_id ){ adguru()->server->show_ad( $ad_id ); }
function adguru_zone( $zone_id ){ adguru()->server->show_zone( $zone_id ); }



