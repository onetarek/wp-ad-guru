<?php
/**
 * This file is jsut a piece of a long process. It is included in links-editor-page.php
 * Can use all variables form links-editor-page.php and ad-manager-page.php 	
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

#retrieve all registred post types.
$post_types = get_post_types( '', 'names' ); 		

#remove post types those are used for internal usage by WordPress.
$rempost = array( 'attachment', 'revision', 'nav_menu_item' );
$post_types = array_diff( $post_types, $rempost );	

#remove post types those are being used by ADGURU itself.
$post_types = array_diff( $post_types, adguru()->post_types->types );	

#remove post types those has no UI, means those are beings used for internal usages only.
foreach( $post_types as $key => $val )
{
	$ptobj = get_post_type_object( $key );
	if( !$ptobj->show_ui ) 
	{ 
		unset( $post_types[ $key ] );
	}
	else
	{
		#capitalize first char of name
		$post_types[ $key ]	= ucfirst( $val );
	}
}

$tabs = array_merge( array( '--'=>"Any Type Post" ), $post_types, array( 'specific_term'=>"Specific Term" ) );
$tab2 = isset( $_GET['tab2'] ) ? $_GET['tab2'] : ""; if( $tab2 == "" || !array_key_exists( $tab2 , $tabs ) ) { $tab2 = "--"; }
$post_type = $tab2;

?>
	<div style="padding-left:20px;">
	<?php 
	adguru_links_manager_tabs( $tabs, $tab2 , 'tab2', array( 'tab'=>'singular' ), false ); 
	if( $tab2 != "specific_term" )
	{
		if( $post_type == "--" )
		{
			$msg = sprintf( __("Set default %s only for <strong>any type</strong> of single page", "adguru" ) , $current_ad_type_args['plural_name'] ) ;
		}
		else
		{
			$msg = sprintf( __("Set default %s for a single page where post type is <strong>%s</strong>", "adguru" ) , $current_ad_type_args['plural_name'], $post_type ) ;
		}
					
		adguru()->html->print_msg($msg);
		$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"singular", "taxonomy"=>"single","term"=>$post_type, "post_id"=>0) );
		$links_editor->display();
	}
	else
	{
	?>
		<div style="padding-left:20px;">
		<?php 
		
		$taxonomies = get_taxonomies(array()); 
		$remTax = array( "nav_menu", "link_category", "post_format", "single", "Single" ); #we remove "single" because it a reserve word for this plugin. This word "Single" we are using to store as a taxonomy for when  post types are stored as terms.
		$taxonomies = array_diff($taxonomies,$remTax);
		
		#remove taxonomies those are being used only for interlan usages. Those object/post_types does not have show UI.
		
		foreach( $taxonomies as $key => $val )
		{
			$taxobj = get_taxonomy( $key );
			$taxonomies[ $key ] = $taxobj->labels->name; 
			
			if( !isset( $taxobj->object_type ) && !is_array( $taxobj->object_type ) )
			{ 
				unset( $taxonomies[ $key ] );
				continue;  
			}

			foreach( $taxobj->object_type  as $object_type )
			{
				$ptobj = get_post_type_object( $object_type );
				if( !$ptobj->show_ui )
				{ 
					unset( $taxonomies[ $key ] ); 
					break;
				}
			}
		}		
		
								
		$tab3 = isset( $_GET['tab3'] ) ? $_GET['tab3'] : "" ; if( $tab3 == "" || !array_key_exists( $tab3, $taxonomies ) ){ $tab3 = "category"; }
		
		adguru_links_manager_tabs( $taxonomies, $tab3 , 'tab3', array( 'tab2'=>$tab2,'tab'=>'singular' ), false ); 
		
		$taxonomy_name = $taxonomies[$tab3];
		$taxonomy_slug = $tab3;
		$taxonomy = get_taxonomy( $taxonomy_slug );
		
		$selected_term_slug = isset( $_GET['term_slug'] ) ? trim( $_GET['term_slug'] ) : "";
		
		if( $selected_term_slug != "" )
		{
			$selected_term_check = term_exists( $selected_term_slug, $taxonomy_slug );
			
			if( $selected_term_check && is_array( $selected_term_check ) )
			{
				$selected_term_exists = true;
				$selected_term = get_term( $selected_term_check['term_id'], $taxonomy_slug );#user may input term name instead of term slug, so we ensuring the term slug.
				$selected_term_slug = $selected_term->slug;
				$selected_term_name = $selected_term->name;
			}
			else
			{
				$selected_term_exists = false;
			}
		}
		else
		{
			$selected_term_exists = false;
		}#end if($selected_term_slug!="")


		if( $taxonomy->hierarchical )
		{
		  $categories = get_categories( array( 'hide_empty'=>0, 'taxonomy'=>$taxonomy_slug ) ); 

		?>
		<form action="admin.php" method="get">
			<input type="hidden" name="page" value="<?php echo $page ?>" />
			<input type="hidden" name="manager_tab" value="<?php echo $current_manager_tab ?>" />
			<input type="hidden" name="zone_id" value="<?php echo $zone_id ?>" />
			<input type="hidden" name="tab3" value="<?php echo $tab3 ?>" />
			<input type="hidden" name="tab2" value="specific_term" />
			<input type="hidden" name="tab" value="singular" />						
						
		<select name="term_slug"  onchange="this.form.submit()"> 
		 <option value=""><?php echo sprintf( __( 'Select a %s', 'adguru' ), $taxonomy_name ); ?></option> 
		 <?php 
		  foreach ($categories as $category)
		  {
			$option = '<option value="'.$category->slug.'"'; $option .= ($category->slug==$selected_term_slug)? ' selected="selected" ':''; $option .= '>';
			$option .= $category->cat_name;
			$option .= ' ('.$category->category_count.')';
			$option .= '</option>';
			echo $option;
		  }
		 ?>
		</select>
		</form>
		<?php 	
			
			if($selected_term_exists)
			{
				$msg = sprintf( __("Set default %s for a single post which is in <strong>%s %s</strong> of single page", "adguru" ) , $current_ad_type_args['plural_name'], $selected_term_slug, $taxonomy_name );
				adguru()->html->print_msg( $msg );
				$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"singular", "taxonomy"=>$taxonomy_slug,"term"=>$selected_term_slug, "post_id"=>0) );
				$links_editor->display();
			}				
		}
		else
		{
			if( $selected_term_exists )
			{
				$msg = sprintf( __("Set default %s for a single post which is in <strong>%s %s</strong> of single page", "adguru" ) , $current_ad_type_args['plural_name'], $selected_term_slug, $taxonomy_name );
				adguru()->html->print_msg( $msg );
				$links_editor = new ADGURU_Links_Editor( array( "ad_type_args"=>$current_ad_type_args, "zone_id"=>$zone_id, "page_type"=>"singular", "taxonomy"=>$taxonomy_slug, "term"=>$selected_term_slug, "post_id"=>0) );
				$links_editor->display();
				echo "<br><br>";
			}
			else
			{
				if(isset($_GET['term_slug']))
				{
					echo '<span style="color:#ff0000;">'; echo __( "Your given term does not exists. Enter a valid term slug", "adguru" ); echo '</span><br><br>';
				}
			}
		  
		  
		  
		  $sql = "SELECT DISTINCT term FROM ".ADGURU_LINKS_TABLE." WHERE zone_id=".$zone_id." AND ad_type='".$current_ad_type."' AND page_type='singular' AND taxonomy='".$taxonomy_slug."'";
		  $used_term_list = $wpdb->get_results($sql);
		  
		  ?>
		 <form action="admin.php" method="get">
			<input type="hidden" name="page" value="<?php echo $page ?>" />
			<input type="hidden" name="manager_tab" value="<?php echo $current_manager_tab ?>" />
			<input type="hidden" name="zone_id" value="<?php echo $zone_id ?>" />
			<input type="hidden" name="tab3" value="<?php echo $tab3 ?>" />
			<input type="hidden" name="tab2" value="specific_term" />
			<input type="hidden" name="tab" value="singular" />
			<?php echo __( "Add new term slug", "adguru" )?> :
			<input type="text" size="15"  name="term_slug" /><input type="submit" class="button" name="add_term" value="<?php echo esc_attr( __( 'Add and Select', 'adguru' ) )?>" />
		  </form><br />
		  <?php echo __( "OR click on any previously used term below to edit.", "adguru" ) ?>
		  <div id="used_term_list">
			<?php 
				
				if(count($used_term_list))
				{
					$link_args = array( "page" => $page , "manager_tab" => $current_manager_tab, "zone_id" =>$zone_id,"tab"=>"singular", "tab2"=>"specific_term", "tab3"=>$tab3 );
					$link = add_query_arg( $link_args , "admin.php" );
					
					foreach($used_term_list as $t)
					{
						echo '<a href="'.$link.'&term_slug='.$t->term.'">'.$t->term.'</a>';
					}
				}
				else
				{
					echo __( 'You did not use any term for this taxonomy and zone yet', 'adguru' );
				}
			?>
		  </div><!--used_term_list-->
		   
		  <?php 												  				
		}#if($taxonomy->hierarchical)
	?>
	</div>
<?php }#end of if($tab2!="specific_term") ?>	
</div>
<?php 	