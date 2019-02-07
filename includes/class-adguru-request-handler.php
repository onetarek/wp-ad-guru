<?php
/**
 * ADGURU Request Handler Class
 * A class to handle all http requst via admin_action__requestaction hook for adguru plugin.
 *
 * @package WP AD GURU
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Request_Handler' ) ) :

class ADGURU_Request_Handler{
	
	public function __construct(){
		
		add_action( 'admin_action_adguru_delete_ad', array($this, 'handle_delete_ad') );//https://developer.wordpress.org/reference/hooks/admin_action__requestaction/

	}

	public function handle_delete_ad(){

		if( wp_get_referer() == false )
		{
			 wp_safe_redirect( get_home_url() );
			 exit;
		}
		
		check_admin_referer( 'adguru_delete_ad', 'adguru_delete_ad_nonce' );
		$delete_id 	=  isset( $_GET['delete_id'] ) ? intval( $_GET['delete_id'] ) : 0;
		$deleted_ad = false;
		$refurl = wp_get_referer();
		$refurl = remove_query_arg( 'msg', $refurl );
		
		if( $delete_id )
		{
			if( adguru()->user->is_permitted_to('delete_ad') )
			{
				$deleted_ad = adguru()->manager->delete_ad( $delete_id );
				if( $deleted_ad )
			    {
			    	$refurl = add_query_arg( array('msg'=>'deleted' ) , $refurl );
			    }
		    }
		} 

		wp_safe_redirect( $refurl );
		exit;
	}


}
endif;