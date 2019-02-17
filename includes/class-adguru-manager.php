<?php
/**
 * Ad Guru Manager Class
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Manager' ) ) :

class ADGURU_Manager{
	
	public function __construct(){
		
	}
	
	/**
	 * Save ad post data 
	 * Insert new or update existing
	 * @param array() $ad 
	 * @return int $id of ad
	 */ 
	public function save_ad( $ad ){
	
		if( ! isset( $ad->type ) || $ad->type == "" ) return 0;
		$ad_id = isset( $ad->ID ) ? intval( $ad->ID ) : 0;
		#if $ad_id = 0 then insert new post otherwise update existing post
		$postarr = array(
			"ID" => $ad_id, 
			"post_type" => $ad->post_type,
			"post_title" => $ad->name,
			"post_excerpt" => $ad->description,
			"post_status" => 'publish',
			"meta_input" => $ad->meta
		);
		$post_id = wp_insert_post( $postarr );
		return ( is_wp_error( $post_id) ) ? 0 : $post_id;		
		
	}//END FUNC
	
	/**
	 * Get Ads
	 *
	 * Retrieves an array of all ads of a specific type.
	 *
	 * @since 2.0.0
	 * @param array $args Query arguments
	 * @param bool $id_as_key optional
	 * @param pointer $wp_query_obj optional. Call by reference. Pass a true variable to receive the WP_Query object used in this function. 
	 * @return mixed array if ads exist, false otherwise
	 */
	public function get_ads( $args = array() , $id_as_key = false, &$wp_query_obj = null ){

		if( !isset( $args['post_type'] ) || $args['post_type'] == "" ) 
		{
			return false;
		}

		$defaults = array(
			'posts_per_page' => 30,
			'paged'          => null,
			'post_status'    => array( 'publish' )
		);
	
		$args = wp_parse_args( $args, $defaults );
		if( $wp_query_obj )
		{
			$wp_query_obj = new WP_Query;
			$posts = $wp_query_obj->query( $args );
		}
		else
		{
			$posts = get_posts( $args );
		}
		
		if ( $posts )
		{
			$ads = array();
			
			foreach( $posts as $p )
			{

				$ad = new ADGURU_Ad( str_replace( ADGURU_POST_TYPE_PREFIX, "", $p->post_type ) );

				$ad->ID = $p->ID;
				$ad->name = $p->post_title;
				$ad->description = $p->post_excerpt;
			
				$metas = get_post_custom( $p->ID );
				//Take only first value . It simillar with useing single = true with get post meta
				foreach( $metas as $key => $val )
				{
					$ad->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
				}
				
				if( $id_as_key ){ $ads[ $p->ID ] = $ad; } else{ $ads[] = $ad; }
					
			}
			
			return $ads; 		
		}//end if
	
	
		return false;
	}//end func


	/**
	 * Get a single Ad
	 *
	 * Retrieves an array of a single zone.
	 *
	 * @since 2.0.0
	 * @param int $ad_id
	 * @return mixed array if ad exist, false otherwise
	 */
	public function get_ad( $ad_id ){

		if( ! $ad_id ) return false;
	
		$p = get_post( $ad_id );
		if ( $p )
		{		
			
			$ad = new ADGURU_Ad( str_replace( ADGURU_POST_TYPE_PREFIX, "", $p->post_type ) );
			$metas = array();
			$ad->ID = $p->ID;
			$ad->name = $p->post_title;
			$ad->description = $p->post_excerpt;
			$metas = get_post_custom( $p->ID );
			//Take only first value . It simillar with useing single = true with get post meta
			foreach( $metas as $key => $val )
			{
				$ad->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
			}
			return $ad; 		
		}//end if
	
	
		return false;
	}//END FUNC
	

	/**
	 * Save zone post data 
	 * Insert new or update existing
	 * @param array() $zone 
	 * @return int $id of zone
	 */ 
	public function save_zone( $zone ){
	
		$zone_id = isset( $zone->ID ) ? intval( $zone->ID ) : 0;
		
		$postarr = array(
			"ID" => $zone_id, 
			"post_type" => ADGURU_POST_TYPE_PREFIX.'zone',
			"post_title" => $zone->name,
			"post_excerpt" => $zone->description,
			"post_status" => 'publish',
			"meta_input" => $zone->meta
		);
		$post_id = wp_insert_post( $postarr );
		return ( is_wp_error( $post_id) ) ? 0 : $post_id;		
		
	}//END FUNC


	/**
	 * Get Zones
	 *
	 * Retrieves an array of all zones.
	 *
	 * @since 2.0.0
	 * @param array $args Query arguments
	 * @return mixed array if zones exist, false otherwise
	 */
	public function get_zones( $args = array(), $id_as_key = false ){

		$defaults = array(
			'post_type'		 => ADGURU_POST_TYPE_PREFIX.'zone',
			'posts_per_page' => -1,
			'paged'          => null,
			'post_status'    => array( 'publish' )
		);
	
		$args = wp_parse_args( $args, $defaults );
	
		$posts = get_posts( $args );
		if ( $posts )
		{
			$zones = array();
			
			foreach( $posts as $p )
			{
			
				$zone = new ADGURU_Zone();
				$zone->ID = $p->ID;
				$zone->name = $p->post_title;
				$zone->description = $p->post_excerpt;
				$metas = get_post_custom( $p->ID );
				//Take only first value . It simillar with useing single = true with get post meta
				foreach( $metas as $key => $val )
				{
					$zone->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
				}
				
				if( $id_as_key ){ $zones[ $p->ID ] = $zone; } else{ $zones[] = $zone; }			
					
			}
			
			return $zones; 		
		}//end if
	
	
		return array();
		
	}//END FUNC


	/**
	 * Get a single Zone
	 *
	 * Retrieves an array of a single zone.
	 *
	 * @since 2.0.0
	 * @param int $zone_id
	 * @return mixed array if zone exist, false otherwise
	 */
	public function get_zone( $zone_id ){

		if( ! $zone_id ) return false;
	
		$p = get_post( $zone_id );
		if ( $p && $p->post_type == ADGURU_POST_TYPE_PREFIX.'zone' )
		{		
			
			$zone = new ADGURU_Zone();
			$zone->ID = $p->ID;
			$zone->name = $p->post_title;
			$zone->description = $p->post_excerpt;
			$metas = get_post_custom( $p->ID );
			//Take only first value . It simillar with useing single = true with get post meta
			foreach( $metas as $key => $val )
			{
				$zone->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
			}			
			
			return $zone; 		
		}//end if
	
	
		return false;
	}//END FUNC

	/**
	 * Get All Active zone
	 * This function just a wrapper of get_zones() function
	 */
	 
	 public function get_active_zones( $id_as_key = false ){

		$args['meta_query'] = array(
			array(
				'key' => '_active',
				'value' => 1,
				'compare' => '='
			)						
		); 				
		
		return $this->get_zones( $args , $id_as_key);
	 
	 }
	 
	 
	/**
	 * Get Ad Zone Links
	 *
	 * Retrieves an array of links of ad and zone.
	 *
	 * @since 2.0.0
	 * @param array $args
	 * @return mixed array if $wpdb return array, false otherwise
	 */
	public function get_ad_zone_links( $args ){
		
		if( !isset( $args['zone_id'] ) && !isset( $args['ad_type'] ) ){ return false; }
		if( !isset( $args['page_type'] ) )	{ return false; }
		if( !isset( $args['taxonomy'] ) )	{ return false; }
		if( !isset( $args['term'] ) )		{ return false; }
		if( !isset( $args['object_id'] ) )	{ return false; }
		
		$zone_id = isset( $args['zone_id'] ) ? intval( $args['zone_id'] ) : 0;
		$ad_type = isset( $args['ad_type'] ) ? $args['ad_type'] : "";
		
		if( $ad_type == "" && $zone_id == 0  ) { return false; }
				
		
		global $wpdb;
		if( $zone_id )
		{
		#get all links of all type of ads for this zone.
		$query = $wpdb->prepare( 
			"SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id=%d AND page_type=%s AND taxonomy=%s AND term=%s AND object_id=%d",
			 $args['zone_id'],
			 $args['page_type'],
			 $args['taxonomy'],
			 $args['term'],
			 $args['object_id']			
			 );
		}
		else
		{
		#get all links of current ad type which has no zone id.
		$query = $wpdb->prepare( 
			"SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id=%d AND ad_type=%s AND page_type=%s AND taxonomy=%s AND term=%s AND object_id=%d",
			 $args['zone_id'],
			 $args['ad_type'],
			 $args['page_type'],
			 $args['taxonomy'],
			 $args['term'],
			 $args['object_id']			
			 );		
		}		
		
		$links = $wpdb->get_results( $query );
		return is_array( $links ) ? $links : false;
		
	}//END FUNC	

	/**
	 * Get Links for an ad
	 *
	 * Retrieves an array of links for a specific ad.
	 *
	 * @since 2.0.0
	 * @param int $ad_id
	 * @return array
	 */
	public function get_links_for_an_ad( $ad_id ){

		$ad_id = intval( $ad_id );
		
		if( !$ad_id )
		{ 
			return array(); 
		}

		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE ad_id=%d", $ad_id );
		$links = $wpdb->get_results( $query );
		return is_array( $links ) ? $links : array();
	}

	/** 
	 * Delete all links for an ad
	 * @param int|object ad. ad id or ad object
	 * @return none
	 **/
	public function delete_links_for_an_ad( $ad ){

		if( is_a($ad, 'ADGURU_Ad') )
		{
			$ad_id = $ad->ID;
		}
		else
		{
			$ad_id = intval( $ad );
			
			if( !$ad_id )
			{ 
				return; 
			}

			$ad = $this->get_ad( $ad_id );
		}

		global $wpdb;
		$query = $wpdb->prepare( "DELETE FROM ".ADGURU_LINKS_TABLE." WHERE ad_id=%d", $ad_id );
		$links = $wpdb->get_results( $query );
		do_action("adguru_delete_links_for_an_ad", $ad );
	}

	/** 
	 * Delete an ad
	 * @param int|object ad. ad id or ad object
	 * @return none
	 **/
	public function delete_ad( $ad ){

		if( is_a($ad, 'ADGURU_Ad') )
		{
			$ad_id = $ad->ID;
		}
		else
		{
			$ad_id = intval( $ad );
			if( !$ad_id )
			{ 
				return false; 
			}
			
			$ad = $this->get_ad( $ad_id );
			if( !$ad )
			{ 
				return false; 
			}
		}
		$this->delete_links_for_an_ad( $ad );
		
		$p = wp_delete_post( $ad_id, true );//force delete.
		if( !$p )
		{ 
			return false;
		}
		
		do_action("adguru_delete_ad", $ad );
		return $ad;
	}

	
}//end class

endif;