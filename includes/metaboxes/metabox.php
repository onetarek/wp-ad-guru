<?php
#http://codex.wordpress.org/ThickBox
#http://codex.wordpress.org/Function_Reference/add_meta_box
#http://codex.wordpress.org/Function_Reference/wp_iframe

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Metabox' ) ) :
class ADGURU_Metabox{
	
	public function __construct(){

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );	
		add_action( 'media_upload_adguru_adzonelinks', array( $this, 'media_upload_adzonelinks' ) );	
	}
 
	public function add_metabox(){
	
		if( ! adguru()->user->is_permitted_to('metabox')){ return; }
		
		$post_types = get_post_types( '', 'names' ); 
		$rempost = array( 'attachment', 'revision', 'nav_menu_item' );
		$post_types = array_diff( $post_types, $rempost );	
				
		//$screens = array( 'post', 'page' );
		$screens = $post_types;
		foreach ( $screens as $screen ) 
		{
	
			add_meta_box(
				'adguru_setup',
				__( 'AdGuru Setup', 'adguru' ),
				array( $this, 'metabox' ),
				$screen
			);
		}
	}


	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function metabox( $post ){

		global $wpdb;
		
		add_thickbox();

		$ad_types = adguru()->ad_types->types;
		$SQL = "SELECT * FROM ".ADGURU_LINKS_TABLE." WHERE page_type='singular' AND object_id=".$post->ID;
		$links = $wpdb->get_results($SQL); 
		$banner = false; 
		$mpop = false;
		$wpop = false;

		if( count( $links ) )
		{
			foreach( $links as $link )
			{
				$ad_types[ $link->ad_type ]['has_link'] = true;
			}
		}

		#http://devll.wordpress.com/2009/10/01/jquery-iframe-thickbox-removes-parameter/ 
		#Add all other query parameters before the TB_iframe parameters. Everything after the “TB” is removed from the URL. So rearranging the order fixed the problem.
		do_action("adguru_metabox_top");
		echo '<div class="adguru_metabox_buttons_wrap">';
		foreach( $ad_types as $type => $args )
		{
			$has_link = ( isset( $args['has_link'] ) ) ? true : false;
			?>
			<a href="media-upload.php?type=adguru_adzonelinks&width=800&height=550&p_id=<?php echo $post->ID?>&p_type=<?php echo $post->post_type?>&ad_type=<?php echo $type?>&TB_iframe=true" class="thickbox <?php echo ( $has_link )?'button-primary':'button' ?>"><?php echo $args['name'] ?></a>
			<?php 
			
		}
		echo "<div>";
		do_action("adguru_metabox_bottom");  
	}
	
	public function media_upload_adzonelinks(){
	
		wp_iframe( array( $this, "media_upload_adzonelinks_content" ) );
		
	}
	
	public function print_zone_list_form( $zone_id ){
	?>
		<p>
			<style type="text/css">#zone_id_list option.inactive{ color:#cccccc;}</style>
			<form action="" method="post" >
			<label for="zone_id"><strong><?php _e( 'Zone' ) ?>: </strong></label>
			<select name="zone_id" id="zone_id_list" style="width:300px;"  onchange="this.form.submit()" >
				<option value="0" selected="selected"><?php _e( 'Select a zone' ) ?></option>
				<?php
				$zones = adguru()->manager->get_zones();
				
				foreach($zones as $zone)
				{
					$selected = '';
					$class = '';
					if( $zone->active != 1 ){ $class=' class="inactive" '; }
					if( $zone_id == $zone->ID ){ $selected=' selected="selected" '; $valid_zone_id = true; }
					echo '<option value="'.$zone->ID.'"'.$class.$selected.'>'.$zone->name.' - '.$zone->width.'x'.$zone->height.'</option>';	
				}
				?>
			</select>
			</form>
			
		</p>	
	<?php 
	}

	public function media_upload_adzonelinks_content(){

		global $wpdb;
		$post_type 	= trim( $_GET['p_type'] );
		$post_id 	= intval( $_GET['p_id'] );
		$zone_id 	= isset( $_REQUEST['zone_id'] ) ? intval( $_REQUEST['zone_id'] ) : 0;
		$ad_type 	= trim( $_GET['ad_type'] );
		$ad_types 	= adguru()->ad_types->types;

		if( !array_key_exists( $ad_type, $ad_types ) )
		{
			echo __( "Ad type not found" , 'adguru' ); 
			return;
		}
		
		if( ! $post_id )
		{
			echo __( "This settings page is only for individual post. No post id found.", "adguru");
			return;
		
		}
		
		$post = get_post( $post_id );
		
		if( ! $post )
		{
			echo __( "No post found.", "adguru");
			return;
		}
		
		$current_ad_type_args = $ad_types[ $ad_type ];
		$use_zone =  $current_ad_type_args['use_zone'];	
		
		echo '<div class="wrap" style="margin-left:20px;  margin-bottom:50px;">';
			echo '<div id="icon-link-manager" class="icon32"><br></div><h2>';
			printf( __("Setup %s for this %s", 'adguru' ),$current_ad_type_args['name'], $post_type);
			echo '</h2>';

			if( $use_zone )
			{
				$this->print_zone_list_form( $zone_id );
			}
			
			if( !$use_zone || ( $zone_id && $use_zone ) )
			{
				$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"singular", "taxonomy"=>"--", "term"=>"--", "post_id"=>$post_id) );
				$links_editor->display();				
			
			}
			else
			{
				echo __( "Select a zone", "adguru" );
			}

		echo "</div>";
	
	}//end func

}
endif;

$ADGURU_Metabox = new ADGURU_Metabox();