<?php

//These functions are available only on admin side

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add new tab on settings page
 * @param string $slug, unique string for tab slug
 * @param string $text, dispaly text for the tab
 * Note : use this function within a callback function of action hook "adguru_settings_add".
 */
function adguru_settings_add_tab( $slug , $text )
{
	return adguru()->settings->add_tab( $slug , $text );
		
}

/**
 * Add new section to a settings tab
 * @param string $tab, slug of a tab
 * @param string $slug, unique string for section slug
 * @param string $text, dispaly text for the section
 * Note : use this function within a callback function of action hook "adguru_settings_add".
 */
function adguru_settings_add_section( $tab , $slug, $text )
{
    return adguru()->settings->add_section( $tab , $slug, $text );
}	  

/**
 * Add new section to a settings section
 * @param string $tab, slug of a tab
 * @param string $section, slug of a section
 * @param array $field, detail options of a field
 * Note : use this function within a callback function of action hook "adguru_settings_add".
 */
function adguru_settings_add_field( $tab, $section, $field )
{
	return adguru()->settings->add_field( $tab, $section, $field );
}

