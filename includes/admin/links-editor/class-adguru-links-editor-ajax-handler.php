<?php 
/**
 * Links Editor Ajax Handler Class
 *
 * @package     WP AD GURU
 * @author 		oneTarek
 * @since       2.0.0
 */


// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Links_Editor_Ajax_Handler' ) ) :

class ADGURU_Links_Editor_Ajax_Handler{
	
	/**
	 * Constructor
	 *
	 * @param none
	 * @return void
	 * @since 2.0.0
	 **/
	public function __construct(){
		
	add_action( 'wp_ajax_adguru_save_ad_zone_links', array( $this, 'save_ad_zone_links' ) );

		
	}//END FUNC 
	
	/**
	 * Check User Permission
	 * @param string $action
	 * @return void or exit with response JSON
	 * @since 2.0.0
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
	  * @since 2.0.0
	  */
	 private function throw_error_response( $msg ){
	 	$response = array();
		$response['status'] = 'fail';
		$response['message'] = $msg;
		echo json_encode($response);
		exit;
	 }
	 
	 public function save_ad_zone_links(){
	 
	 	$this->check_permission( 'save_ad_zone_links' );
		
		$response = array();
		global $wpdb;
		$zone_id = isset( $_POST['zone_id'] ) ? intval( $_POST['zone_id'] ) : 0;
		
		$ad_type= isset( $_POST['ad_type'] ) ? trim( $_POST['ad_type'] ) : "";
		
		$all_ad_types = adguru()->ad_types->types;
		$use_zone = false;
		
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
		

		$set 		= array();
		$post_id 	= intval( $_POST['post_id'] );
		$page_type 	= trim( $_POST['page_type'] ); if( $page_type == "" ) $page_type = "--";
		$taxonomy 	= trim( $_POST['taxonomy'] ); if( $taxonomy == "" ) $taxonomy = "--";
		$term 		= trim( $_POST['term'] ); if( $term == "" ) $term = "--";
		$object_id	= 0;
		
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
		
		$ad_zone_link_set = $_POST['ad_zone_link_set']; 
		$ad_ids = array();
		foreach( $ad_zone_link_set as $link_set_item )
		{
			$country_code = isset( $link_set_item['country_code'] ) ? $link_set_item['country_code'] : "";
			if( $country_code == "" ) { continue; }
			$ad_slide_set = isset( $link_set_item['ad_slide_set'] ) ? $link_set_item['ad_slide_set'] : "";
			if( $ad_slide_set == "" ) { continue; }
			$slide = 0;
			if( is_array( $ad_slide_set ) && count( $ad_slide_set ) )
			{
				foreach( $ad_slide_set as $ad_slide )
				{
					$slide++;
					foreach( $ad_slide as $ad )
					{
						$ad_id = $ad['ad_id'];
						$ad_ids[] = $ad_id ;
						$percentage = intval( $ad['percentage'] );
						$xx = array( "ad_type" => $ad_type, "zone_id"=>$zone_id, "page_type"=>$page_type, "taxonomy"=>$taxonomy, "term"=>$term, "object_id"=>$object_id, "country_code"=>$country_code, "slide"=>$slide, "ad_id"=>$ad_id, "percentage"=>$percentage );
						$set[]=$xx;
					}
				}
			}
		
		}#end foreach($ad_zone_link_set
		$ad_ids = array_unique( $ad_ids );
		$args = array(
				'post_type'=> adguru()->ad_types->post_types, 
				'post__in' => $ad_ids
			);
		$ads = adguru()->manager->get_ads( $args , true);
		
		#at first delete all exisiting record for this zone_id and post_id
		#delete all type of ads for this zone_id.
		if( $use_zone )
		{
			$wpdb->query("DELETE FROM ".ADGURU_LINKS_TABLE." WHERE zone_id=".$zone_id." AND page_type='".$page_type."' AND taxonomy='".$taxonomy."' AND term='".$term."' AND object_id=".$object_id);	
		}
		else
		{
			$wpdb->query("DELETE FROM ".ADGURU_LINKS_TABLE." WHERE ad_type='".$ad_type."' AND zone_id=".$zone_id." AND page_type='".$page_type."' AND taxonomy='".$taxonomy."' AND term='".$term."' AND object_id=".$object_id);
		}
		
		#insert new record. here we are using multiple query for all new record, but we can inseart all at once. 
		foreach($set as $s)
		{
			if( !isset( $ads[ $s['ad_id'] ] ) ) {  continue; }
			$ad = $ads[ $s['ad_id'] ];
			
			$SQL="INSERT INTO ".ADGURU_LINKS_TABLE." (ad_type, zone_id, page_type, taxonomy, term, object_id, country_code, slide, ad_id, percentage) 
				VALUES(
					'".$ad->type."', 
					".$s['zone_id'].",  
					'".$s['page_type']."', 
					'".$s['taxonomy']."', 
					'".$s['term']."', 
					".$s['object_id'].", 
					'".$s['country_code']."', 
					".$s['slide'].", 
					".$s['ad_id'].", 
					".$s['percentage']."   	
					)";
					
			$res = $wpdb->query($SQL);		
		}
			
		$response['status'] = 'success';
		$response['message'] = "Saved";				
		echo json_encode($response);
		exit;	 
	 }//end func 
	  
	  
}// end class

new ADGURU_Links_Editor_Ajax_Handler();

endif;
