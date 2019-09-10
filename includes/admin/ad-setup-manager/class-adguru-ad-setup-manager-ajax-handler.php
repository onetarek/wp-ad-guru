<?php 
/**
 * Ad Setup Manager Ajax Handler Class
 *
 * @package     WP AD GURU
 * @author 		oneTarek
 * @since       2.1.0
 */


// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Ad_Setup_Manager_Ajax_Handler' ) ) :

class ADGURU_Ad_Setup_Manager_Ajax_Handler{
	
	/**
	 * Constructor
	 *
	 * @param none
	 * @return void
	 * @since 2.1.0
	 **/
	public function __construct(){
		
		add_action( 'wp_ajax_adguru_save_ad_links', array( $this, 'save_ad_links' ) );
		add_action( 'wp_ajax_adguru_get_term_data', array( $this, 'get_term_data' ) );
		add_action( 'wp_ajax_adguru_delete_condition_set', array( $this, 'delete_condition_set' ) );

		
	}//END FUNC 
	
	/**
	 * Check User Permission
	 * @param string $action
	 * @return void or exit with response JSON
	 * @since 2.1.0
	 */ 
	 private function check_permission( $action ){
	 	if( adguru()->user->is_permitted_to( $action ) )
		{
			return;
		}
		else
		{
			$this->throw_error_response( __( 'No permission, either your are not permitted for this action or you are not logged in', 'adguru' ) );
		}	 
	 }//end func
	 
	 /**
	  * Exit with response JSON with error message
	  * @param string $msg
	  * @return void
	  * @since 2.1.0
	  */
	 private function throw_error_response( $msg ){
	 	$response = array();
		$response['status'] = 'fail';
		$response['message'] = $msg;
		wp_send_json( $response );
		return;
	 }
	 
	 public function save_ad_links(){
	 	
	 	$this->check_permission( 'save_ad_links' );
		
		$response = array();
		global $wpdb;
		$zone_id = isset( $_POST['zone_id'] ) ? intval( $_POST['zone_id'] ) : 0;
		
		$ad_type= isset( $_POST['ad_type'] ) ? trim( $_POST['ad_type'] ) : "";
		
		$all_ad_types = adguru()->ad_types->types;
		$use_zone = false;

		$initial_query_data = isset( $_POST['initial_query_data'] ) ? $_POST['initial_query_data'] : false;
		if( !is_array( $initial_query_data ) )
		{
			$this->throw_error_response( __( 'Initial query data is required.', 'adguru' ) );
		}
		
		if( $ad_type != "" && isset( $all_ad_types[ $ad_type ] ) )
		{
			$ad_type_args = $all_ad_types[ $ad_type ];
			$use_zone = isset( $ad_type_args['use_zone'] ) ? ( bool ) $ad_type_args['use_zone'] : false;
			
			if( $use_zone && $zone_id == 0 )
			{
				$this->throw_error_response( __( 'This type of ad needs zone, no zone id given.', 'adguru' ) );
			} 
			
			
		}
		else
		{
			$this->throw_error_response( __( 'Given ad type is not valid', 'adguru' ) );
		}
		
		if( $zone_id == 0 && $ad_type == '' )
		{
			$this->throw_error_response( __( 'Ad type is required', 'adguru' ) );
		}

		$set 		= array();
		$slides 	= ( isset( $_POST['slides'] ) && is_array( $_POST['slides'] ) ) ? $_POST['slides'] : array();
		$post_id 	= intval( $_POST['post_id'] );
		$page_type 	= trim( $_POST['page_type'] );	if( $page_type == "" ){ $page_type = "--"; }
		$taxonomy 	= trim( $_POST['taxonomy'] ); 	if( $taxonomy == "" ){ $taxonomy = "--"; }
		$term 		= trim( $_POST['term'] ); 		if( $term == "" ){ $term = "--"; }
		$object_id	= 0;

		$country_code = trim( $_POST['country_code'] ) ; if( $country_code == "" ){ $country_code = "--"; }
		

		if( $post_id )
		{
			$page_type	= "singular";
			$taxonomy	= "--";
			$term		= "--";
			$object_id	= $post_id;
			
		}
		else
		{
			switch($page_type)
			{
				case "--":
				{
					$page_type	= "--";
					$taxonomy	= "--";
					$term		= "--";	
					$object_id	= 0;				
					break;
				}
				case "home":
				{
					$page_type	= "home";
					$taxonomy	= "--";
					$term		= "--";	
					$object_id	= 0;					
					break;
				}
				case "singular":
				{
					#no change					
					break;
				}
				case "taxonomy":
				{
					#no change
					break;
				}					
				case "author":
				{
					#no change
					break;
				}
				case "404":
				{
					#no change
					break;
				}
				case "search":
				{
					#no change
					break;
				}					
								
			}#end switch($page_type)
		
		}#end if($post_id)
		
		$ad_ids = array();
		$slide_num = 0;
		foreach( $slides as $slide )
		{
	
			if( !is_array($slide) || count($slide) == 0 ){ continue;}
			
			$slide_num++;
			
			foreach( $slide as $ad )
			{
				
				$ad_id = $ad['ad_id'];
				$ad_ids[] = $ad_id ;
				$percentage = intval( $ad['percentage'] );
				$adType = $ad['ad_type'];
				$xx = array( "ad_type" => $adType, "zone_id"=>$zone_id, "page_type"=>$page_type, "taxonomy"=>$taxonomy, "term"=>$term, "object_id"=>$object_id, "country_code"=>$country_code, "slide"=>$slide_num, "ad_id"=>$ad_id, "percentage"=>$percentage );
				$set[]=$xx;
				
			}
			
		
		}#end foreach($slides as $slide

		$ad_ids = array_unique( $ad_ids );
		$args = array(
				'post_type'=> adguru()->ad_types->post_types, 
				'post__in' => $ad_ids
			);
		$ads = adguru()->manager->get_ads( $args , true);
		
		#at first delete all exisiting record for this zone_id and post_id

		$this->delete_ad_links_by_initial_query_data( $initial_query_data );

		
		
		#insert new record. here we are using multiple query for all new record, but we can inseart all at once. 
		foreach($set as $s)
		{
			if( !isset( $ads[ $s['ad_id'] ] ) ) {  continue; }
			$ad = $ads[ $s['ad_id'] ];
			
			$SQL= $wpdb->prepare("INSERT INTO ".ADGURU_LINKS_TABLE." ( ad_type, zone_id, page_type, taxonomy, term, object_id, country_code, slide, ad_id, percentage ) 
				VALUES( %s,%d, %s, %s, %s, %d, %s, %d, %d, %d )", 
					$ad->type, $s['zone_id'], $s['page_type'], $s['taxonomy'] , $s['term'], $s['object_id'], $s['country_code'], $s['slide'], $s['ad_id'], $s['percentage']
			);

			$res = $wpdb->query($SQL);		
		}
			
		$response['status'] = 'success';
		$response['message'] = "Saved";				
		wp_send_json( $response );
		return;	 
	 }//end func 
	 
	 #Delete all exisiting record for this zone_id and post_id
	 #Delete all type of ads for this zone_id.
	 private function delete_ad_links( $args ){
	 	
	 	global $wpdb;
	 	if( $args['zone_id'] == 0 && $args['ad_type'] == "" )
	 	{
	 		return false;
	 	}

	 	if( $args['zone_id'] == 0 )
	 	{
			$SQL = $wpdb->prepare("DELETE FROM ".ADGURU_LINKS_TABLE." WHERE ad_type=%s AND zone_id=%d AND page_type=%s AND taxonomy=%s AND term=%s AND object_id=%d AND country_code=%s",
	 		   $args['ad_type'], $args['zone_id'], $args['page_type'], $args['taxonomy'],$args['term'],  $args['object_id'], $args['country_code'] );
	 	
	 	}
	 	else
	 	{
	 		$SQL = $wpdb->prepare("DELETE FROM ".ADGURU_LINKS_TABLE." WHERE zone_id=%d AND page_type=%s AND taxonomy=%s AND term=%s AND object_id=%d AND country_code=%s",
	 		   $args['zone_id'], $args['page_type'], $args['taxonomy'],$args['term'],  $args['object_id'], $args['country_code'] );
	 	
	 	}
	 	$res = $wpdb->query( $SQL );
	 	return $res;
	 }

	 public function get_term_data(){
	 	$response = array();
	 	$response['status'] = 'success';
	 	$response['exist'] = 0;
	 	$taxonomy = isset( $_GET['taxonomy'] ) ? trim($_GET['taxonomy']) : "";
	 	$term = isset( $_GET['term'] ) ? trim($_GET['term']) : "";
	 	if( $taxonomy == "" || $term == "")
	 	{
	 		wp_send_json( $response );
			return;
	 	}
	 	$t = term_exists( $term, $taxonomy );
	 	if( $t )
	 	{	
	 		$response['exist'] = 1;
	 		$response['term_data'] = get_term( $t['term_id'] );
	 	}
	 	wp_send_json( $response );
		return;	
	 }//end func

	 public function delete_condition_set(){
	 	$response = array();
	 	$initial_query_data = isset( $_POST['initial_query_data'] ) ? $_POST['initial_query_data'] : false;
		if( !is_array( $initial_query_data ) )
		{
			$this->throw_error_response( __( 'Initial query data is required.', 'adguru' ) );
		}
		else
		{
			$success = $this->delete_ad_links_by_initial_query_data( $initial_query_data );
			if( $success )
			{
				$response['status'] = 'success';
				$response['message'] = "deleted";
				wp_send_json( $response );
				return;
			}
			else
			{
				$this->throw_error_response( __( 'Delete operation has been failed based on given initial query data.', 'adguru' ) ); 
			}
			
		}

	 }

	 private function delete_ad_links_by_initial_query_data( $initial_query_data ){
	 	if( !( isset( $initial_query_data['new_entry'] ) && $initial_query_data['new_entry'] == 1 ) )
		{


			$prev_zone_id = $initial_query_data['zone_id'];
			$prev_page_type = $initial_query_data['page_type'];
			$prev_taxonomy = $initial_query_data['taxonomy'];
			$prev_term = $initial_query_data['term'];
			$prev_object_id = isset($initial_query_data['object_id']) ? $initial_query_data['object_id'] : 0;
			$prev_country_code = $initial_query_data['country_code'];
			$prev_ad_type = $initial_query_data['ad_type'];

			#delete all type of ads for this zone_id.
			$del_args = array(
				'zone_id' => $prev_zone_id,
				'page_type' => $prev_page_type,
				'taxonomy' => $prev_taxonomy,
				'term' => $prev_term,
				'object_id' => $prev_object_id,
				'country_code' => $prev_country_code,
				'ad_type' => $prev_ad_type
			);
			
			$this->delete_ad_links( $del_args );
			return true;
		}
		return false;
	 }
	  
}// end class

new ADGURU_Ad_Setup_Manager_Ajax_Handler();

endif;
