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

		//Return false if post__in exists with empty value
		//wp query does not include post__in parameter if that is empty and returns posts regurdless of post__in condition.
		if( isset( $args['post__in'] ) && empty( $args['post__in'] ) )
		{
			return false;
		}

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
	 * @since 2.1.0 Completely refactored. $args parameter is optional. Supports None, single and multiple fields in $args 
	 * @param array $args
	 * @return mixed array if $wpdb return array, false otherwise
	 *
	 * Available $args keys are zone_id, page_type, taxonomy, term, object_id, country_code, ad_id
	 * $args items can be both array or non array
	 * example $args = array(
	 * 		'zone_id' => 2,
	 *		'page_type' => array('home' ,'--'),
	 *		'......' => '.......',	
	 * 		)
	 *
	 *	If $args is blank then this function will return all links
	 */
	public function get_ad_zone_links( $args = array() ){
		
		global $wpdb;
		$query = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE 1=1";
		$vars = array();
		
		#query with zone_id
		if( isset( $args['zone_id'] ) )
		{
			$zone_id = $args['zone_id'];
			$zone_id_arr = array();
			$zone_id_placeholders = array();
			if( is_array( $zone_id ) )
			{
				foreach($zone_id as $item )
				{
					$zone_id_arr[] = intval( $item );
					$zone_id_placeholders[] = "%d";
				}
			}
			else
			{
				$zone_id_arr[] = intval( $zone_id );
				$zone_id_placeholders[] = "%d";
			}
			if( count( $zone_id_arr ) )
			{
				$query.=" AND zone_id IN (".implode(", ", $zone_id_placeholders).")";
				$vars = array_merge( $vars, $zone_id_arr );
			}
			
		}

		#query with ad_type
		if( isset( $args['ad_type'] ) )
		{
			$ad_type = $args['ad_type'];
			$ad_type_arr = array();
			$ad_type_placeholders = array();
			if( is_array( $ad_type ) )
			{
				foreach( $ad_type as $item )
				{
					$s = trim( $item );
					if( $s )
					{
						$ad_type_arr[] = $s;
						$ad_type_placeholders[] = "%s";
					}
					
				}
			}
			else
			{
				$s = trim( $ad_type );
				if( $s )
				{
					$ad_type_arr[] = $s;
					$ad_type_placeholders[] = "%s";
				}
				
			}
			if( count( $ad_type_arr ) )
			{
				$query.=" AND ad_type IN (".implode(", ", $ad_type_placeholders).")";
				$vars = array_merge( $vars, $ad_type_arr );
			}
			
		}

		#query with page_type
		if( isset( $args['page_type'] ) )
		{
			$page_type = $args['page_type'];
			$page_type_arr = array();
			$page_type_placeholders = array();
			if( is_array( $page_type ) )
			{
				foreach( $page_type as $item )
				{
					$s = trim( $item );
					if( $s )
					{
						$page_type_arr[] = $s;
						$page_type_placeholders[] = "%s";
					}
					
				}
			}
			else
			{
				$s = trim( $page_type );
				if( $s )
				{
					$page_type_arr[] = $s;
					$page_type_placeholders[] = "%s";
				}
				
			}
			if( count( $page_type_arr ) )
			{
				$query.=" AND page_type IN (".implode(", ", $page_type_placeholders).")";
				$vars = array_merge( $vars, $page_type_arr );
			}
			
		}
		
		#query with taxonomy
		if( isset( $args['taxonomy'] ) )
		{
			$taxonomy = $args['taxonomy'];
			$taxonomy_arr = array();
			$taxonomy_placeholders = array();
			if( is_array( $taxonomy ) )
			{
				foreach( $taxonomy as $item )
				{
					$s = trim( $item );
					if( $s )
					{
						$taxonomy_arr[] = $s;
						$taxonomy_placeholders[] = "%s";
					}
					
				}
			}
			else
			{
				$s = trim( $taxonomy );
				if( $s )
				{
					$taxonomy_arr[] = $s;
					$taxonomy_placeholders[] = "%s";
				}
				
			}
			if( count( $taxonomy_arr ) )
			{
				$query.=" AND taxonomy IN (".implode(", ", $taxonomy_placeholders).")";
				$vars = array_merge( $vars, $taxonomy_arr );
			}
			
		}

		#query with term
		if( isset( $args['term'] ) )
		{
			$term = $args['term'];
			$term_arr = array();
			$term_placeholders = array();
			if( is_array( $term ) )
			{
				foreach( $term as $item )
				{
					$s = trim( $item );
					if( $s )
					{
						$term_arr[] = $s;
						$term_placeholders[] = "%s";
					}
					
				}
			}
			else
			{
				$s = trim( $term );
				if( $s )
				{
					$term_arr[] = $s;
					$term_placeholders[] = "%s";
				}
				
			}
			if( count( $term_arr ) )
			{
				$query.=" AND term IN (".implode(", ", $term_placeholders).")";
				$vars = array_merge( $vars, $term_arr );
			}
			
		}

		#query with object_id
		if( isset( $args['object_id'] ) )
		{
			$object_id = $args['object_id'];
			$object_id_arr = array();
			$object_id_placeholders = array();
			if( is_array( $object_id ) )
			{
				foreach($object_id as $item )
				{
					$object_id_arr[] = intval( $item );
					$object_id_placeholders[] = "%d";
				}
			}
			else
			{
				$object_id_arr[] = intval( $object_id );
				$object_id_placeholders[] = "%d";
			}
			if( count( $object_id_arr ) )
			{
				$query.=" AND object_id IN (".implode(", ", $object_id_placeholders).")";
				$vars = array_merge( $vars, $object_id_arr );
			}
			
		}

		#query with country_code
		if( isset( $args['country_code'] ) )
		{
			$country_code = $args['country_code'];
			$country_code_arr = array();
			$country_code_placeholders = array();
			if( is_array( $country_code ) )
			{
				foreach( $country_code as $item )
				{
					$s = trim( $item );
					if( $s )
					{
						$country_code_arr[] = $s;
						$country_code_placeholders[] = "%s";
					}
					
				}
			}
			else
			{
				$s = trim( $country_code );
				if( $s )
				{
					$country_code_arr[] = $s;
					$country_code_placeholders[] = "%s";
				}
				
			}
			if( count( $country_code_arr ) )
			{
				$query.=" AND country_code IN (".implode(", ", $country_code_placeholders).")";
				$vars = array_merge( $vars, $country_code_arr );
			}
			
		}

		#query with ad_id
		if( isset( $args['ad_id'] ) )
		{
			$ad_id = $args['ad_id'];
			$ad_id_arr = array();
			$ad_id_placeholders = array();
			if( is_array( $ad_id ) )
			{
				foreach($ad_id as $item )
				{
					$ad_id_arr[] = intval( $item );
					$ad_id_placeholders[] = "%d";
				}
			}
			else
			{
				$ad_id_arr[] = intval( $ad_id );
				$ad_id_placeholders[] = "%d";
			}
			if( count( $ad_id_arr ) )
			{
				$query.=" AND ad_id IN (".implode(", ", $ad_id_placeholders).")";
				$vars = array_merge( $vars, $ad_id_arr );
			}
			
		}

		if( count( $vars ) )
		{
			$prepared_query = $wpdb->prepare( $query, $vars );
		}
		else
		{
			$prepared_query = $query;
		}
		
		$links = $wpdb->get_results( $prepared_query );
		
		if( is_array($links) )
		{
			//convert some string data of int fields back to original. WordPress wpdb converts all number fields to string.
			//https://stackoverflow.com/questions/40103136/why-are-int-columns-returned-as-string-when-using-wordpresss-wpdb-to-get-datab
			
			$new_links = array();
			foreach( $links as $link )
			{
				$link->zone_id 		= intval( $link->zone_id );
				$link->object_id 	= intval( $link->object_id );
				$link->slide 		= intval( $link->slide );
				$link->ad_id 		= intval( $link->ad_id );
				$link->percentage 	= intval( $link->percentage );
				$new_links[] 		= $link;
			}
			return $new_links;

		}
		else
		{
			return false;
		}
		
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