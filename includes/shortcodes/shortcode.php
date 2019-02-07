<?php
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

function adguru_shortcode_handler($atts)
{
	extract( shortcode_atts( array(
			'adid' 	=> '0',
			'zoneid' => '0',
	), $atts ) );

	$zoneid = intval( $zoneid );
	$adid = intval( $adid );
	
	if( $zoneid )
	{ 
		return adguru()->server->show_zone( $zoneid , true ); 
	}

	if( $adid )
	{ 
		return adguru()->server->show_ad( $adid, true ); 
	}
	
	return "";
}

add_shortcode( 'adguru', 'adguru_shortcode_handler' );
add_shortcode( 'ADGURU', 'adguru_shortcode_handler' );
