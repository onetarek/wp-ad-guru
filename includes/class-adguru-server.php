<?php
/**
 * ADGURU Server Class
 * A class to perpare and serve appropriate ads for front-end
 *
 * @package WP AD GURU
 * @author oneTarek
 * @since 2.0.0
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

class ADGURU_Server {
	public $options;
	public $zones; #store object array of all active zones
	public $ad_zone_links; #store all ad and zone links for current and default post.
	public $ads; #store object array of all required ads for current and default post mentioned in ad zone links table.
	public $all_country_list; #stroe all country code and name
	public $page_type;#which type of page is being visited currently.
	public $visitor_country_code; #store visitor's country code
	public $instance_number=0; #index of current zone or ad block is being shown in page.
	
	public $current_taxonomy; #store the name of current taxonomy
	public $current_term; #store the slug of current term
	public $current_post_taxonomies_terms;#store an array of taxonomy and terms of current single post.

	public $current_page_info; #all information about current visited page

	public function __construct(){
			
		add_action('wp_head', array( $this, 'start_inserter' ) );
		
	}#end __construct
	

	public function start_inserter(){
		ADGURU_Inserter::instance();
	}

	#some common functoins=========================================================================


	public function get_ad( $id ){

		$id = intval( $id );
		
		if( !$id )
		{ 
			return false;
		}

		if( isset( $this->ads[ $id ] ) )
		{
			return $this->ads[$id];
		}
		else
		{
			$ad = adguru()->manager->get_ad( $id );
			if( $ad )
			{
				$this->ads[$id] = $ad;
				return $ad;
			}
			else
			{
				return false;
			}			
		}
	}

	public function get_zone( $id ){

		$id = intval( $id );

		if( !$id )
		{ 
			return false; 
		}
		
		$zones = $this->get_zones();
		
		if( isset( $zones[ $id ] ) )
		{
			return $zones[ $id ];
		}
		else
		{
			$zone = adguru()->manager->get_zone( $id );
			if( !$zone )
			{ 
				return false; 
			}
			else
			{ 
				$this->zones[ $id ] = $zone; 
				return $zone; 
			}		
		}
		
		return false;	
	}
			
	public function get_zones(){

		if( !isset( $this->zones ) )
		{ 
			$this->zones = adguru()->manager->get_active_zones( true ); //true for ID as kye
		}
		return $this->zones;
	}
	
	private function generate_current_page_info(){
		$info = array();
		$info['url'] = ADGURU_Helper::current_page_url();		
		#Taking decision based on which type of page is being visited currently 
		$page_type = "default";
		$info['page_type'] = "default";

		if( is_home() || is_front_page( ) )
		{ 
			$page_type = "home"; 
			$info['page_type'] = "home";
		}
		elseif( is_singular() )
		{ 
			$page_type = "singular"; 
			$info['page_type'] = "singular";

			global $post;
			$info['post_type'] = $post->post_type;
			$info['post_id'] = $post->ID;
		}
		elseif( is_category() )
		{
			$page_type = "category";
			$info['page_type'] = "category";

			$this->current_taxonomy = "category";
			$info['taxonomy'] = "category";

			$thisCat = get_category( get_query_var('cat'),false );

			$this->current_term = $thisCat->slug;
			$info['term'] = $thisCat->slug;
		}
		elseif( is_tag() )
		{
			$page_type = "tag";
			$info['page_type'] = "tag";

			$this->current_taxonomy = "post_tag"; 
			$info['taxonomy'] = "post_tag";

			$term = get_query_var('tag');
			$this->current_term = $term;	
			$info['term'] = $term;
		}
		elseif( is_tax() )
		{
			$page_type = "custom_taxonomy";
			$info['page_type'] = "custom_taxonomy";

			$taxonomy = get_query_var('taxonomy');
			$this->current_taxonomy = $taxonomy;
			$info['taxonomy'] = $taxonomy;

			$term = get_query_var('term');
			$this->current_term = $term;	
			$info['term'] = $term;
		
		}#Note that when used without the $taxonomy parameter, is_tax() returns false on category archives and tag archives. You should use is_category() and is_tag() respectively when checking for category and tag archives. 
		elseif( is_date() )
		{
			#not ready yet. Right now use as 'default'
			$page_type = "default"; //Have to change later.
			$info['page_type'] = "date";
		}
		elseif( is_search() )
		{ 
			$page_type = "search"; 
			$info['page_type'] = "search";
		}
		elseif( is_author() )
		{ 
			$page_type = "author";
			$info['page_type'] = "author"; 
		}
		elseif( is_404() )
		{ 
			$page_type = "404_not_found"; 
			$info['page_type'] = "404_not_found";
		}
		else
		{ 
			$page_type = "default"; 
			$info['page_type'] = "default";
		}									
		#End Taking decision 
		
		$this->page_type = $page_type;
		$this->current_page_info = $info;

		return $info;
	}

	public function get_current_page_info(){
		if( isset( $this->current_page_info ) )
		{
			return $this->current_page_info;
		}
		$this->generate_current_page_info();
		return $this->current_page_info;
	}


	public function get_page_type(){

		if( $this->page_type != "" ) 
		{
			return $this->page_type;
		}	
		$this->generate_current_page_info();
		return $this->page_type;	
	}
		
	public function generate_ad_zone_and_links_data(){

		global $wpdb;
		
		#$ad_zone_links array structure[zone_id][ad_type][page_type][taxonomy][term][object_id][country_code][slide number]=$ad_item_array("id"=>1, "percentage"=>50);
		
		$ad_zone_links = array();
		#Actual structure of final $ad_zone_links array 
		#Comment out folowing array sructure since 2.0. With this default array item cause an issue. Just keeping following codes for refrence. 
		#DO NOT DELETE !
		#Since 2.0 array structue has been changed a bit. Altered level of zone_id and ad_type. Now ad_type is child of zone_id. 
		/*
		$ad_zone_links=array( 
							0=>array( #zone_id
								"modal_popup"=>array(#ad_type key 'banner' changed to 'modal_popup' since 2.0
									"--"=>array( #page_type
										"--"=>array( #taxonomy
											"--"=>array( #term
												"0"=>array( #object_id
													"--"=>array( #country_code
														array( #slide number
															array( "id"=>0, "percentage"=>100 ) #ad_item array
															)
														)
													)
												)
											)
										)
									)	
								)
							); //end of $ad_zone_links
		*/						
		
		$page_type = $this->get_page_type();
		if( ADGURU_GEO_LOCATION_ENABLED )
		{
			$visitor_contry_code = ADGURU_Helper::get_visitor_country_code();
		}
		else
		{
			$visitor_contry_code = "";
		}
		
		$zones = $this->get_zones();
		
		$zone_id_list = array(0);
		
		foreach( $zones as $zone )
		{ 
			$zone_id_list[] = $zone->ID; 
		}

		$zone_id_in = implode(",", $zone_id_list );

		if( $visitor_contry_code == "" || $visitor_contry_code == "--" )
		{ 
			$country_code_in = "'--'"; 
		}
		else
		{ 
			$country_code_in = "'--','".$visitor_contry_code."'";
		}
				
		switch( $page_type )
		{
			case "home":
			{
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('home','--') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "singular":
			{
				global $post;
				$object_id_in = "0,".$post->ID;
				$taxes = array("--","single");
				$terms = array("--", $post->post_type);
				
				$taxonomies = get_taxonomies(array("object_type"=>array( $post->post_type ) )); 
				$remTax = array("nav_menu","link_category","post_format","single", "Single"); #we remove "single" because it a reserve word for this plugin. This word "Single" we are using to store as a taxonomy for when  post types are stored as terms.
				$taxonomies = array_diff( $taxonomies,$remTax);
				if( $post->post_type == "post" )
				{
					$taxonomies_terms = array( "category"=>array(), "post_tag"=>array() );
				}
				else
				{
					$taxonomies_terms = array();
				}
				if( count( $taxonomies ) )
				{
					foreach( $taxonomies as $tx )
					{
						$taxes[] = $tx;
					}

					$post_terms = wp_get_post_terms( $post->ID, $taxonomies );
					
					foreach( $post_terms as $t )
					{
						$terms[] = $t->slug;
						$taxonomies_terms[$t->taxonomy][] = $t->slug;
					}
				}

				$this->current_post_taxonomies_terms = $taxonomies_terms;
				$taxes_in = "'".implode("', '", $taxes)."'";
				$terms_in = "'".implode("', '", $terms)."'";
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('--','singular') AND taxonomy IN(".$taxes_in.") AND term IN(".$terms_in.") AND object_id IN(".$object_id_in.") AND country_code IN(".$country_code_in.")";
				break;
			}			
			case "category":
			{
				$category = $this->current_term;
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('--','taxonomy') AND taxonomy IN('--', 'category') AND term IN('--','".$category."') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "tag":
			{
				$tag = $this->current_term;
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('--','taxonomy') AND taxonomy IN('--', 'post_tag') AND term IN('--','".$tag."') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "custom_taxonomy":
			{
			 	$taxonomy = $this->current_taxonomy;
				$term = $this->current_term;
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('--','taxonomy') AND taxonomy IN('--', '".$taxonomy."') AND term IN('--', '".$term."') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "search":
			{
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('search','--') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "author":
			{
				$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('author','--') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "404_not_found":
			{
				$SQL="SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('404_not_found','--') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}
			case "default":#not necessery
			{
				$SQL="SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE zone_id IN (".$zone_id_in.") AND page_type IN('--') AND object_id IN(0) AND country_code IN(".$country_code_in.")";
				break;
			}															
									
		}#end switch
		
		$ad_zone_links_raw = $wpdb->get_results( $SQL );
		 	
		$ad_id_list = array();
		 
		if( $ad_zone_links_raw )
		{			
			
			$types = adguru()->ad_types->types;
			$use_zone_types = array();

			foreach( $types as $key => $args )
			{
				if( $args['use_zone'] ){ $use_zone_types[] = $key; }
			}
			
			foreach( $ad_zone_links_raw as $links )
			{
				$ad_id_list[] = $links->ad_id;
				$ad_item = array( "id" => $links->ad_id, "percentage" => $links->percentage );
				$slide = $links->slide;
				#combine all $use_zone_types ad_types to "--", because a single zone can contain multiple types of ads. 
				$ad_type = ( in_array( $links->ad_type, $use_zone_types ) ) ? "--" : $links->ad_type;
				$ad_zone_links[ $links->zone_id ][$ad_type][ $links->page_type ][ $links->taxonomy ][ $links->term ][ $links->object_id ][ $links->country_code ][ $slide-1 ][] = $ad_item;
			}
		}
		  
		$this->ad_zone_links = $ad_zone_links; 
		if( count( $ad_id_list ) )
		{
			$args = array(
				'post_type'=> adguru()->ad_types->post_types, 
				'post__in' => $ad_id_list
			);
			$this->ads = adguru()->manager->get_ads( $args , true );
		
		}
		else
		{
			$this->ads = array();
		}
	
	}#end function generate_ad_zone_and_links_data
	
	public function get_ad_zone_links(){

		if( !isset( $this->ad_zone_links) )
		{
			$this->generate_ad_zone_and_links_data();
		}

		return $this->ad_zone_links;
	
	}
	
	#$ad_zone_links array structure[zone_id][ad_type][page_type][taxonomy][term][object_id][country_code][slide number]=$ad_item_array("id"=>1, "percentage"=>50);
	public function get_appropiate_ad_links( $zone_id = 0, $ad_type = "--" ){

		$ad_zone_links = $this->get_ad_zone_links();
		$zone_id = intval( $zone_id );
		if( !isset( $ad_zone_links[ $zone_id ] ) ){ return false; }
		
		if( $zone_id )
		{
			$zones = $this->get_zones();
			if( !isset( $zones[ $zone_id] ) ){ return false; }
			if( !isset( $ad_zone_links[ $zone_id ]['--'] ) ){ return false; }	
			$czl = $ad_zone_links[ $zone_id ]['--']; // czl = current_zone_links
		}
		else
		{
			if( !isset( $ad_zone_links[ 0 ][ $ad_type ] ) ){ return false; }
			$czl = $ad_zone_links[ 0 ][ $ad_type ]; // czl = current_zone_links
		}		
		
		
		if( ADGURU_GEO_LOCATION_ENABLED )
		{
			$visitor_contry_code = ADGURU_Helper::get_visitor_country_code();
		}
		else
		{
			$visitor_contry_code = "";
		}
		
		$links = array();

		$page_type = $this->get_page_type();		
		switch( $page_type )
		{
			case "home":
			{
				if( isset( $czl[ "home" ][ "--" ][ "--" ][ 0 ][ $visitor_contry_code ] ) )
				{
					$links = $czl[ "home" ][ "--" ][ "--" ][ 0 ][ $visitor_contry_code ];
				}
				elseif( isset( $czl[ "home" ][ "--" ][ "--" ][ 0 ][ "--" ] ) )
				{
					$links = $czl[ "home" ][ "--" ][ "--" ][ 0 ][ "--" ];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}
				
				break;
			}
			case "singular":
			{

				global $post;
				$found = false;
				#check individual post ID
				if( isset( $czl["singular"]["--"]["--"][$post->ID][$visitor_contry_code] ) )
				{
					$links = $czl["singular"]["--"]["--"][$post->ID][$visitor_contry_code];
					$found = true;
				}
				elseif( isset( $czl["singular"]["--"]["--"][$post->ID]["--"] ) )
				{
					$links = $czl["singular"]["--"]["--"][$post->ID]["--"];
					$found = true; 
				}

				#check terms and taxonomy
				if( !$found && is_array( $this->current_post_taxonomies_terms ) && count( $this->current_post_taxonomies_terms ) )
				{	
					
					foreach( $this->current_post_taxonomies_terms as $tax => $terms )
					{
						foreach( $terms as $t )
						{							
							if( isset( $czl["singular"][$tax][$t][0][$visitor_contry_code] ) )
							{
								$links = $czl["singular"][$tax][$t][0][$visitor_contry_code];
								$found = true;
							}
							elseif( isset( $czl["singular"][$tax][$t][0]["--"] ) )
							{
								$links = $czl["singular"][$tax][$t][0]["--"];
								$found = true;
							}													
						}
						if( $found ){ break; }
					}
				
				}
				
				#check post type
				if( !$found )
				{

					if( isset( $czl["singular"]["single"][$post->post_type][0][$visitor_contry_code] ) )
					{
						$links = $czl["singular"]["single"][$post->post_type][0][$visitor_contry_code];
						$found = true;
					}
					elseif( isset( $czl["singular"]["single"][$post->post_type][0]["--"] ) )
					{
						$links = $czl["singular"]["single"][$post->post_type][0]["--"];
						$found = true;
					}
					elseif( isset( $czl["singular"]["single"]["--"][0][$visitor_contry_code] ) )
					{
						$links = $czl["singular"]["single"]["--"][0][$visitor_contry_code];
						$found = true;
					}
					elseif( isset( $czl["singular"]["single"]["--"][0]["--"] ) )
					{
						$links = $czl["singular"]["single"]["--"][0]["--"];
						$found = true;
					}					
					
				}
				
				#check default
				if( !$found )
				{
					if( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
					{
						$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
					}								
					elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
					{
						$links = $czl["--"]["--"]["--"][0]["--"];
					}
				}				
			
			break;
			}
			case "category":
			{

				if( isset( $czl["taxonomy"]['category'][$this->current_term][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]['category'][$this->current_term][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]['category'][$this->current_term][0]["--"] ) )
				{
					$links = $czl["taxonomy"]['category'][$this->current_term][0]["--"];
				}
				elseif( isset( $czl["taxonomy"]['category']["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]['category']["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]['category']["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"]['category']["--"][0]["--"];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}				
				
				break;
			}
			case "tag":
			{

				if( isset( $czl["taxonomy"]['post_tag'][$this->current_term][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]['post_tag'][$this->current_term][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]['post_tag'][$this->current_term][0]["--"] ) )
				{
					$links = $czl["taxonomy"]['post_tag'][$this->current_term][0]["--"];
				}
				elseif( isset( $czl["taxonomy"]['post_tag']["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]['post_tag']["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]['post_tag']["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"]['post_tag']["--"][0]["--"];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}
			
				break;
			}			
			case "custom_taxonomy":
			{

				if( isset( $czl["taxonomy"][$this->current_taxonomy][$this->current_term][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"][$this->current_taxonomy][$this->current_term][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"][$this->current_taxonomy][$this->current_term][0]["--"] ) )
				{
					$links = $czl["taxonomy"][$this->current_taxonomy][$this->current_term][0]["--"];
				}
				elseif( isset( $czl["taxonomy"][$this->current_taxonomy]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"][$this->current_taxonomy]["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"][$this->current_taxonomy]["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"][$this->current_taxonomy]["--"][0]["--"];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0][$visitor_contry_code];
				}
				elseif( isset( $czl["taxonomy"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["taxonomy"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}

			
				break;
			}
			case "search":
			{
				if( isset( $czl["search"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["search"]["--"]["--"][0][$visitor_contry_code];
				}
				if( isset( $czl["search"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["search"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}								

				break;
			}												
			case "author":
			{

				if( isset( $czl["author"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["author"]["--"]["--"][0][$visitor_contry_code];
				}
				if( isset( $czl["author"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["author"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}	
							
				break;
			}
			case "404_not_found":
			{

				if( isset( $czl["404_not_found"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["404_not_found"]["--"]["--"][0][$visitor_contry_code];
				}
				if( isset( $czl["404_not_found"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["404_not_found"]["--"]["--"][0]["--"];
				}
				elseif( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}
			
				break;
			}
			case "default":
			{

				if( isset( $czl["--"]["--"]["--"][0][$visitor_contry_code] ) )
				{
					$links = $czl["--"]["--"]["--"][0][$visitor_contry_code];
				}								
				elseif( isset( $czl["--"]["--"]["--"][0]["--"] ) )
				{
					$links = $czl["--"]["--"]["--"][0]["--"];
				}
			
				break;
			}								
		}#end switch( $page_type)
		return $links;
		
		
	}#end function get_appropiate_ad_links	
	
	
	public function get_ad_by_percentage_probability( $ad_set ){ #$ad_set=array(array('id'=>1,'percentage'=>30),array('id'=>1,'percentage'=>70 ) )

		if( !is_array( $ad_set ) ){ return false; }
		if( count( $ad_set) == 0 ){ return false; }
		
		$ad_set_count = count( $ad_set );
		
		if( $ad_set_count == 0 ){ return false; }
		if( $ad_set_count == 1 ){ return $ad_set[0]['id']; }#we don't check percentage when only one item is given
		#create percentage probabilty
		$total_percentage = 0;

		foreach( $ad_set as $set )
		{
			if( $set['id'] == "" ){ return false; }
			$total_percentage+=intval( $set['percentage'] );
		}
		
		if( $total_percentage < 100 ){ $ad_set[0]['percentage']+=100-$total_percentage; } #when total percentage will not 100 then we give the extra to the first item
	
		$items = array();

		foreach( $ad_set as $set )
		{
			$per = intval( $set['percentage'] );
			$items[] = array( 'per'=>$per, 'id' => $set['id'] );
		}
		#$items=array(array('per'=>30,'id'=>1), array('per'=>50,'id'=>2), array('per'=>20,'id'=>5 ) ); #total 100%
		$id_list = array();
		
		$i = 0; $p = 0;
		foreach( $items as $item )
		{
			$key = $item['per'];
			$id = $item['id'];
			for( $i; $i<$p+$key; $i++)
			{
				$id_list[] = $id;	
			}
			$p = $p+$key;
		}
		$rand = mt_rand(0,99);
		$target_id = $id_list[$rand];
		return $target_id;
	
	}#end function get_ad_by_percentage_probability	
	

	/**
	 * Print ads in <head>
	 * OR
	 * Print ads in footer before </body>
	 * @param string $script_location , value "footer" or "head"
	 * @return void
	 */ 

	public function print_header_footer_ads( $script_location = "footer" ){
		//check if modal popup or window popup preview request
		if( isset( $_GET['adguru_preview'] ) && $_GET['adguru_preview']==1 && isset( $_GET['adtype'] ) && isset( $_GET['adid'] ) )
		{
			if( $_GET['adtype']=='modal_popup' )
			{

				$modal_popup_id = intval( $_GET['adid'] );
				if( $modal_popup_id )
				{
					$ad = $this->get_ad( $modal_popup_id );
					if( $ad )
					{ 
						echo '<script>var ADGURU_MODAL_POPUP_PREVIEW_MODE = true;</script>';
						$ad->display(); 
					}
				}
			}
			elseif( $_GET['adtype']=='window_popup' )
			{

				$window_popup_id = intval( $_GET['adid'] );
				if( $window_popup_id )
				{
					$ad = $this->get_ad( $window_popup_id );
					if( $ad )
					{ 
						echo '<script>var ADGURU_WINDOW_POPUP_PREVIEW_MODE = true;</script>';
						$ad->display(); 
					}
				}
			}

			return false;
		}

		$types = adguru()->ad_types->types;
		$allowed_ad_types = array();
		foreach( $types as $key => $args )
		{
			if( $args['use_zone'] == false)
			{ 	
				if( isset( $args['script_location'] ) && $args['script_location'] == $script_location )
				{	
					$allowed_ad_types[] = $key; 
				}
			
			}
		}
		
		foreach( $allowed_ad_types as $ad_type )
		{
			$links = $this->get_appropiate_ad_links( 0 , $ad_type);
			#play with links
			if( ! is_array( $links ) ){ continue; }
			$tot_slide = count( $links );
			if( $tot_slide == 0 ) { continue; }
			foreach( $links as $ad_set )
			{
				$ad_id = intval( $this->get_ad_by_percentage_probability( $ad_set ) );			
				
				if( $ad_id )
				{ 
					$ad = $this->get_ad( $ad_id );
					if( $ad )
					{
						$ad->display();
					}				
				}
			}
			
		}
	
	}//end func

	/**
	 * Show or Return ad output 
	 * @param int $ad_id
	 * @param bool $ret , default = false. If true then no echo, return string.
	 * @return void or string
	 */
	 
	 public function show_ad( $ad_id, $ret = false ){

		if( !intval( $ad_id ) ){ return false; }
		
		$ad = $this->get_ad( $ad_id );

		if( !$ad ){ return false; }

		$output = $ad->display( true );
		
		if( $ret ){ return $output; } else { echo $output; }	 
		 
	 }//end func
	 
	/**
	 * Show or Return zone output 
	 * @param int $zone_id
	 * @param bool $ret , default = false. If true then no echo, return string. 
	 * @return void or string
	 */
	 
	 public function show_zone( $zone_id , $ret = false ){

		$zone_id = intval( $zone_id );
		if( ! $zone_id ) { return false; }
		
		$zones = $this->get_zones();
		
		$output = "";
		
		if( !isset( $zones[ $zone_id ] ) )
		{
			$output =__("Zone not found or deactivated.", "adguru" )."Zone id : ".$zone_id; 
		}
		else
		{
			$current_zone = $zones[ $zone_id ];
			$output = $current_zone->display(true);//true for return output
		}
			
		if( $ret ){ return $output; } else { echo $output; }	 
	 
	 }//end func

}#end class