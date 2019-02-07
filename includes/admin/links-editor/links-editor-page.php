<?php

/**
 * Links Editor Page
 *
 * @package     WP AD GURU
 * @author 		oneTarek
 * @since       2.0.0
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

function adguru_links_manager_tabs( $tabs, $current = 'default', $var="tab",  $query_vars=array(), $maintabs=true ) { 

	$zone_id = isset( $_GET['zone_id'] ) ? intval( $_GET['zone_id'] ) : 0 ;
	$page = $_REQUEST['page'];
	$current_manager_tab = isset( $_REQUEST[ 'manager_tab'] ) ? $_REQUEST[ 'manager_tab'] : "links";
	$args = array( "page" => $page , "manager_tab" => $current_manager_tab , "zone_id" => $zone_id );
	foreach( $query_vars as $key => $val )
	{
		$args[ $key ] = $val;
	}
	
	$links = array();
	echo ( $maintabs ) ? '<div id="icon-link-manager" class="icon32"><br></div>' : "";
	
	echo ( $maintabs ) ? '<h2 class="nav-tab-wrapper">' : '<h3 class="nav-tab-wrapper">';
	foreach( $tabs as $tab => $name )
	{
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		$args[ $var ] = $tab;
		$url = add_query_arg( $args ,  'admin.php' );
		echo "<a class='nav-tab$class' href='$url'>$name</a>";
		
	}
	echo ($maintabs) ? '</h2>' : '</h3>';
	
}//end function


global $wpdb;

$zone_id = isset( $_GET['zone_id'] ) ? intval( $_GET['zone_id'] ) : 0 ;

if( $use_zone ){ $msg = sprintf( __("Set %s to Zone", "adguru" ) , $current_ad_type_args['plural_name'] );  } else {  $msg =  sprintf( __("Set %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ); }

echo '<h2 style="text-align:center">'.$msg.'</h2>';	

#Print Zone select dropdown if current ad type use zone
if( $use_zone ){

	$zones = adguru()->manager->get_zones();
	?>
	<style type="text/css">#zone_id_list option.inactive{ color:#cccccc;}</style>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo $page ?>" />
		<input type="hidden" name="manager_tab" value="<?php echo $current_manager_tab ?>" />
		<strong><?php _e( 'Zone', 'adguru' )?> : </strong> 
		<select id="zone_id_list" name="zone_id" onchange="this.form.submit()">
			<option value="0" <?php echo ( $zone_id == 0 ) ? ' selected="selected" ': ""  ?>><?php echo __( "Select A Zone", "adguru" ) ?></option>
			<?php 
			$valid_zone_id = false;
			foreach($zones as $zone)
			{
				$selected = '';
				$class = '';
				if( $zone->active !=1 ){ $class = ' class="inactive" '; }
				if( $zone_id == $zone->ID ){ $selected = ' selected="selected" '; $valid_zone_id = true; }
				echo '<option value="'.$zone->ID.'"'.$class.$selected.'>'.$zone->name.' - '.$zone->width.'x'.$zone->height.'</option>';
			}
			?>
		</select>
	</form>
	
	<?php 

}//end if( $use_zone )

#Run rest of process if current ad type use zone and zoine id is valid , OR run process if current ad type doesn't use zone 
if( ( $zone_id && $valid_zone_id ) || $use_zone == false )
{	
	$tabs = array( 
		'--'			=>	__( 'Default', 'adguru' ), 
		'home'			=> 	__( 'Home' , 'adguru' ), 
		'singular'		=>	__( 'Singular', 'adguru' ),
		'taxonomy' 		=>	__( 'Taxonomy', 'adguru' ),
		'author'		=>	__( 'Author', 'adguru'),
		'404_not_found'	=>	__( '404', 'adguru' ),
		'search' 		=> 	__( 'Search', 'adguru')
	); 
	
	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : "" ;  
	if( $tab == "" || ! array_key_exists( $tab, $tabs ) ){ $tab = "--"; }
	
	adguru_links_manager_tabs( $tabs, $tab );
	
	switch( $tab )
	{
		case "--": #default
		{
			adguru()->html->print_msg( sprintf( __("Set default %s for <strong>all pages</strong> of this website", "adguru" ) , $current_ad_type_args['plural_name'] ) );
			$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"--", "taxonomy"=>"--", "term"=>"--", "post_id"=>0) );
			$links_editor->display();
		
		break;
		}
		case "home":
		{
			adguru()->html->print_msg( sprintf( __("Set default %s only for <strong>home pages</strong>", "adguru" ) , $current_ad_type_args['plural_name'] ) );
			$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"home", "taxonomy"=>"--", "term"=>"--", "post_id"=>0) );
			$links_editor->display();
		
		break;
		}
		case "singular":
		{
	
			include( ADGURU_PLUGIN_DIR."includes/admin/links-editor/tab-block-singular.php" );
		
		break;
		}
		case "taxonomy":
		{
	
			include( ADGURU_PLUGIN_DIR."includes/admin/links-editor/tab-block-taxonomy.php" );
			
		break;
		}
		case "author":
		{
			adguru()->html->print_msg( sprintf( __("Set default %s only for <strong>Author</strong> archive page", "adguru" ) , $current_ad_type_args['plural_name'] ) );
			$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"author", "taxonomy"=>"--", "term"=>"--", "post_id"=>0));
			$links_editor->display();
		
		break;
		}									
		case "404_not_found":
		{
			adguru()->html->print_msg( sprintf( __("Set default %s only for <strong>404 not found</strong> page", "adguru" ) , $current_ad_type_args['plural_name'] ) );
			$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"404_not_found", "taxonomy"=>"--", "term"=>"--", "post_id"=>0) );
			$links_editor->display();
		
		break;
		}
		case "search":
		{
			adguru()->html->print_msg( sprintf( __("Set default %s only for <strong>Search Result</strong> page", "adguru" ) , $current_ad_type_args['plural_name'] ) );
			$links_editor = new ADGURU_Links_Editor(array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"search", "taxonomy"=>"--", "term"=>"--", "post_id"=>0));
			$links_editor->display();
		
		break;
		}								
	
	}#end switch($tab)
	
}#end if($zone_id.............

?>