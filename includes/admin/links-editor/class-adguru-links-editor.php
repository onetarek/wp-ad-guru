<?php 
/**
 * Links Editor Class
 *
 * @package     WP AD GURU
 * @author 		oneTarek
 * @since       2.0.0
 */


// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Links_Editor' ) ) :

class ADGURU_Links_Editor{
	
	public  $ad_type;
	public  $ad_type_args;
	public  $multiple_slides;
	public  $rotator;
	public  $use_zone;
	
	public 	$zone_id;
	public 	$zone; //detail object of a zone
	public  $zone_width;
	public  $zone_height;
	
	public  $post_id;
	public 	$page_type;
	public  $taxonomy;
	public  $term;
	public  $object_id;
	
	
	public $ads;//store an array of all ads related to this zone and ad type
	
	public $geo_rotator = true;
	
	public $all_ad_types;//store all registered ad type details.
	 
	/**
	 * Constructor
	 *
	 * @param array $args , Array of settings
	 * @param int $post_id
	 * @return void
	 * @since 2.0.0
	 **/
	public function __construct( $arg=array(), $post_id=0 ){
		
		$this->ad_type_args 	=  $arg['ad_type_args'];
		$this->use_zone 		= ( bool ) $this->ad_type_args['use_zone'] ;
		$this->multiple_slides 	= ( bool ) $this->ad_type_args['multiple_slides'] ;
		$this->rotator 			= ( bool ) $this->ad_type_args['rotator'] ;

		
		$this->ad_type = $this->ad_type_args['slug'];
		$this->zone_id = intval( $arg['zone_id'] );
		$this->post_id = intval( $arg['post_id'] );
		
		if( $arg['page_type']== "" ) 	$arg['page_type'] 	= "--";
		if( $arg['taxonomy'] == "" ) 	$arg['taxonomy'] 	= "--";
		if( $arg['term'] == "" ) 		$arg['term'] 		= "--";
		
		$this->page_type 	= $arg['page_type']; 
		$this->taxonomy 	= $arg['taxonomy']; 
		$this->term 		= $arg['term']; 
		$this->object_id 	= $post_id;

		if( $this->zone_id )
		{
			$this->zone = adguru()->manager->get_zone( $this->zone_id );
		}

		$this->zone_width 	= ( $this->zone ) ? $this->zone->width  : 0; 
		$this->zone_height 	= ( $this->zone ) ? $this->zone->height : 0; 
		
		$this->all_ad_types = adguru()->ad_types->types;
		
	}//END FUNC
	
	/**
	 * Get all related ads and store into $this->ads
	 * @return void
	 * @since 2.0.0
	 */
	 function prepare_related_ads(){
			
		$args = array(
			"post_type" =>	ADGURU_POST_TYPE_PREFIX.$this->ad_type, 
			'posts_per_page' =>	-1		
		);
	 
		if( $this->use_zone )
		{	
			$ptype = array();
			
			foreach( $this->all_ad_types as $ad_type => $ad_type_args )
			{
				if( $ad_type_args['use_zone'] )
				{
					$ptype[] = ADGURU_POST_TYPE_PREFIX.$ad_type;
				}
			}
			$args['post_type'] = $ptype;
			/*
			$args['meta_query'] = array(
					array(
						'key' => '_width',
						'value' => $this->zone_width,
					),
					array(
						'key' => '_height',
						'value' => $this->zone_height,
					)						
				);
			*/  
		}
		
		$ads = adguru()->manager->get_ads( $args , true ); //true for id_as_key	
		$this->ads = is_array( $ads ) ? $ads : array(); 
	 
	 }//end func

	/**
	 * Render Links Editor
	 * @return void
	 * @since 2.0.0
	 **/		
		
	public function display(){
	
		if( $this->ad_type =="" ) { return; }
		if( $this->use_zone && !is_object( $this->zone ) )
		{
			echo "<div>Zone not found for zone id ".$this->zone_id."</div>";
			return;
		}
		
		global $wpdb;
		
		$this->prepare_related_ads();
		
		$this->print_scripts();
		
		$all_country_list = ADGURU_Helper::get_country_list();
		$country_code_list = array('--');#we need default country -- at the begining of array
		$country_list = array();
		$ad_zone_link_set = array(
			"--" =>
				array(
					array(
						array()
					)
				)
			);
		
		
		$links_args = array(
			"zone_id"		=> 	$this->zone_id,
			"ad_type"		=> 	$this->ad_type,
			"page_type"		=> 	$this->page_type,
			"taxonomy"		=> 	$this->taxonomy,
			"term"			=> 	$this->term,
			"object_id"		=> 	$this->post_id
			
		);
		$ad_zone_links = adguru()->manager->get_ad_zone_links( $links_args );		
			
		if( $ad_zone_links )
		{
		
			foreach( $ad_zone_links as $links )
			{
				if( $links->country_code != "--" )
				{
					$country_code_list[] = $links->country_code;
				}
			}			
			
			$country_code_list = array_unique( $country_code_list );
			
			foreach( $country_code_list as $code )
			{ 
				$country_list[ $code ] = $all_country_list[ $code ]; 
			}
			
			$ad_zone_link_set = array();
			foreach( $country_list as $code => $name )
			{
				
				$ad_zone_link_set[ $code ] = array( array() );
			}
			
			foreach( $ad_zone_links as $links )
			{
				$ad_item = array(
					"id"			=>	$links->ad_id, 
					"name"			=>	isset( $this->ads[ $links->ad_id ] ) ? $this->ads[ $links->ad_id ]->name." --- ".$links->ad_type : "<span style='color:red'>Not Found : ".$links->ad_type."</span>",
					"ad_type"		=>	$links->ad_type, 
					"percentage"	=>	$links->percentage
					);
				$slide = $links->slide;
				$ad_zone_link_set[ $links->country_code ][ $slide-1 ][] = $ad_item;
			}
		
		
		}
		else
		{
			//
		}#end if($ad_zone_links

		$country_list["--"] = "Default";

		?>	

		<?php $this->ad_list_modal(); ?>
		<div id="links_editor" style="margin-top:10px;">	
			<div id="links_editor_header" style="display:none;">
			<h2><?php _e( 'Manage ads for this zone', 'adguru' ) ?></h2>
			</div>
			<div id="links_editor_body">
			<?php
				$table_style =  ( $this->geo_rotator )? "width: 650px;" : "width: 504px;";
			?>
			<table id="links_editor_table" class="widefat" style="<?php echo $table_style; ?>">
				<thead>
					<tr>
						<?php if( $this->geo_rotator ){?><th width="146"><?php _e( 'Country', 'adguru' ) ?></th><?php }?>
						<th width="504">
						<?php 
						if( $this->zone_id )
						{
							echo sprintf( __( '%s set for the <strong>zone( %s )</strong> on this <strong>page</strong>', 'adguru' ), $this->ad_type_args['name'], $this->zone->name );
						}
						else
						{
							echo sprintf( __( '%s set for this <strong>page</strong>', 'adguru' ), $this->ad_type_args['name'] );
						}
						?>
						</th>
					</tr>
				</thead>
				
				<tr>
					<?php if( $this->geo_rotator ){?>
					<td style=" background:#F3F3F3;">
						<ul id="ctab_set">
						<?php 
						$i=0;
						foreach($country_list as $code=>$name)
						{
							$i++;
							$selected=($code=="--")?' class="selected"':'';
							echo '<li tabid="'.$i.'" code="'.$code.'"'.$selected.'>'.$name.'</li>';
						}?>
						</ul>
						<span class="add_country_btn" id="add_new_country_btn"><?php _e( 'Add new country', 'adguru' )?></span>
					</td>
					<?php }?>
					<td>								
						<div id="ad_zone_link_set_wrap">
							<?php 
							$j=0;
							foreach( $country_list as $country_code => $country_name){ $j++; ?>
							<div class="ctab_box ad_zone_link_set" tabid="<?php echo $j?>" id="ctab_box_<?php echo $j?>" <?php if( $j == 1 ) echo ' style="display:block"';?>>
								<div class="ctab_box_head">
									<?php if( $j!=1 && ADGURU_GEO_LOCATION_ENABLED == false ){?>
									<div style="color:red"><?php _e( 'Geo location feature is not enabled', 'adguru' )?></div>
									<?php } ?>
									<div class="ctab_box_title"><?php if($j==1){ _e( 'Default: for all country', 'adguru' );} else { echo $country_name; } ?></div>
									<div class="ctab_control_box" tabid="<?php echo $j?>"><?php if($j!=1){ ?><span class="remove_country_btn" title="<?php echo esc_attr( __( 'Remove this country', 'adguru' ) ) ?>">&nbsp;</span><?php } ?></div>
									<div class="clear"></div>
								</div>
								<?php
								$selected = $country_code;
								if( $j==1 )
								{
									adguru()->html->get_country_list_select_input( $selected, array('class'=>'country_name', 'style'=>'display:none', 'onchange'=>"adgLinksEditor.checkDuplicateCountry(this)" ) );
								}
								else
								{
									adguru()->html->get_country_list_select_input( $selected, array('class'=>'country_name', 'onchange'=>"adgLinksEditor.checkDuplicateCountry(this)" ) );
								}							
								
								$slide_set_arr = $ad_zone_link_set[ $country_code ];
								$this->generate_ad_slide_set( $slide_set_arr );	

								?>
							</div>
							<?php }?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="button" class="save_ad_zone_links_btn button-primary" value="<?php echo esc_attr( __( 'Save Change', 'adguru' ) )?>" style="width:100px;" onclick="" />
						<span class="ad_zone_link_msg"></span>
						<span class="ad_zone_link_loading" style="float:right; display:none;"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading.gif" alt="loading.." /></span>
					</td>
				</tr>
			</table>
			</div><!--#links_editor_body-->
		</div><!--#links_editor-->
		<?php 
		if( $this->post_id == 0 )
		{
			$this->user_guide_section();
		}//end if $this->post_id
	
	
	}//END FUNC	DISPLAY

	/**
	 * Print Links Editor JS and CSS
	 * @return void
	 * @since 2.0.0
	 **/		
		
	private function print_scripts(){
		
		$js_vars 					= array();
		$js_vars['ad_type'] 		= $this->ad_type;
		$js_vars['multiple_slides'] = $this->multiple_slides;
		$js_vars['rotator'] 		= $this->rotator;
		
		$js_vars['use_zone']		= $this->use_zone;
		$js_vars['zone_id'] 		= $this->zone_id;
		$js_vars['zone_width'] 		= $this->zone_width;
		$js_vars['zone_height'] 	= $this->zone_height;
		
		$js_vars['post_id'] 		= $this->post_id;
		$js_vars['page_type'] 		= $this->page_type;
		$js_vars['taxonomy'] 		= $this->taxonomy;
		$js_vars['term'] 			= $this->term;
		$js_vars['object_id'] 		= $this->object_id;
		
		$js_vars = apply_filters("adguru_links_editor_js_vars" , $js_vars, $this->ad_type );
		
	
		?>
		<script type="text/javascript">
			adgLinksEditorVars = <?php echo json_encode( $js_vars );?>;//LEVARS stands for Links Editor JS Vars
		</script>
		<script type="text/javascript" src="<?php echo ADGURU_PLUGIN_URL; ?>assets/js/ad_zone_links_controller.js?var=<?php echo ADGURU_VERSION ?>"></script>	
		<script type="text/javascript">
		
			function get_country_list_html()
			{
			<?php
				$country_list= ADGURU_Helper::get_country_list();
				echo 'var html=\'<select onchange ="adgLinksEditor.checkDuplicateCountry(this)"  class="country_name">'; 
				$cn=0;
				foreach($country_list as $slug=>$name)
				{
				$cn++;
				if($cn==7){echo '<optgroup label="- - - - - - - - - - - - - - - - - - - - - - - -">';}	
				echo '<option value="'.$slug.'">'.addslashes($name).'</option>';
				}
				echo '</optgroup></select>\'';
		   ?>			
			return html;
			}
										
			</script>
			<link rel="stylesheet" href="<?php echo ADGURU_PLUGIN_URL; ?>assets/css/ad_zone_links_controller.css?var=<?php echo ADGURU_VERSION ?>" />
			
	<?php	
	}//END FUNC			
	
	/**
	 * Render the HTML of Ad list modal
	 *
	 */
	
	private function ad_list_modal(){ ?>
	
		 <div id="ad_list_modal" title="Insert <?php echo $this->ad_type_args['name'] ?>" style="display:none;">
			<div>
			<div style="width:240px; float:left;"><strong><?php echo sprintf( __( 'Select a %s and click insert', 'adguru' ) , $this->ad_type_args['name'] )?></strong></div>
			<div style="float:right; width:200px; margin-right:22px; text-align:right;"><input style="width:180px;" placeholder="Search" type="text" size="15" id="search_ad_list" /></div>
			</div>
			<div style="clear:both"></div>
			
				<div id="ads_list">
				<?php
				
				if( !is_array( $this->ads ))
				{
					echo '<span style="color:#cc0000;">';
						echo sprintf( __( 'You have no %s for this zone size', 'adguru' ), $this->ad_type_args['name'] ).' <strong>'.$this->zone_width.'x'.$this->zone_height.'</strong>';
						echo ' <a href="admin.php?page='.ADGURU_ADMANAGER_PAGE_SLUG_PREFIX.$this->ad_type.'">';
							echo sprintf( __( 'Enter new ad', 'adguru') );
						echo '</a> ' ;
						echo sprintf( __( 'in %s size', 'adguru' ), '<strong>'.$this->zone_width.'x'.$this->zone_height.'</strong>' );
					echo '</span>';
				}
				else
				{
					foreach($this->ads as $ad)
					{
						$ad_type_name =  $this->all_ad_types[ $ad->type ]['name'];
						
						echo '<div class="ads_list_item" ad_id="'.$ad->ID.'" ad_type_name="'.$ad_type_name.'" ad_type="'.$ad->type.'" ad_name="'.esc_attr( $ad->name ).'" ><span class="ad_name">'.$ad->name.'</span><span class="ad_type">'.$ad_type_name.'</span></div>';
					}
				}
				?>
				</div>		
		 </div>
	<?php	
	}//END FUNC	
		
	/**
	 * Render HTML of ad slide set
	 **/

	 private function generate_ad_slide_set( $slide_set_arr ){ ?>
		<div>	
			<div class="ad_slide_set_box">
				<?php $i = 0; foreach( $slide_set_arr as $slide ){ $i++; ?>
				<?php if( $this->multiple_slides ){ echo '<h3>'.__('Slide', 'adguru').' '.$i.'</h3>'; } ?>
				<div class="ad_slide">
					<div class="slide_header">
						<div class="sl_hd_left"><?php echo ( $this->use_zone ) ? __('Ads', 'adguru' ) : $this->ad_type_args['name']; echo ' '.__('Name', 'adguru' ) ?></div>
						<div class="sl_hd_middle"><span class="equal_button" title="<?php echo esc_attr( __( 'Set all percentage fields equal', 'adguru' ) ) ?>"></span></div>
						<div class="sl_hd_right">&nbsp;</div>
						<div class="clear"></div>
					</div>
					<div class="ad_set">
						<?php foreach( $slide as $ad ){ if( isset( $ad['id'] ) ){ ?>
						<div class="ad_item" ad_id="<?php echo $ad['id'] ?>">
							<div class="ad_item_left" title="<?php echo esc_attr( $this->all_ad_types[$ad['ad_type']]['name'] ) ?>"><?php echo $ad['name'] ?></div>
							<div class="ad_item_middle"><input type="text" size="3" class="percentage" value="<?php echo esc_attr( $ad['percentage'] ) ?>" /> %</div>
							<div class="ad_item_right"><span class="remove_ad_btn" title="<?php echo esc_attr( __( 'Remove this ad', 'adguru' ) ) ?>"></span></div>
							<div class="clear"></div>
						</div>
						<?php } }?>
					</div>
					<div class="slide_footer">
						<div class="sl_ft_left"><span class="add_ad_btn" onclick="adgLinksEditor.showAdListModal(this)"><?php echo sprintf( __( 'Add new %s', 'adguru'), $this->ad_type_args['name'] ) ?></span></div>
						<div class="sl_ft_middle">&nbsp;</span></div>
						<div class="sl_ft_right">&nbsp;</div>
						<div class="clear"></div>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php if( $this->multiple_slides ){ ?>
			<div style="margin-top:10px; margin-bottom:10px;"><span class="add_slide_btn"><?php echo __( 'Add new slide', 'adguru' ) ?></span></div>
			<?php }?>
		</div>	 
	 <?php 
	 }#end generate_ad_slide_set($slide_set_arr)

	public function user_guide_section(){
	?>
		<div style="background:#F0F0F0; width:700px; padding:0px 5px 15px 5px; margin-top:40px; border:1px solid #DFDFDF;">
			<h2><?php _e( 'User Guide for This Page', 'adguru') ?></h2>
			<img src="<?php echo ADGURU_PLUGIN_URL;?>assets/images/ad_zone_links_editor_guide.jpg" /><br />
	
			<?php _e( '<strong>1. Country List:</strong> You can set ads for individual country where your visitor come from. Country name <strong>Default</strong> means all country.', 'adguru') ?> <br /><br />
			<?php _e( '<strong>2. Slide:</strong> You are able to show multiple ads in same place like a courosel/slider. If you have only one slide then the ad will shown normally. If you have one more slide then the ads will be shown in a carousel. Ads will be changed in each 5 seconds.', 'adguru') ?><br /><br />
			<?php _e( '<strong>3. New Slide Button:</strong> After clicking on this buton a new slide will be added.', 'adguru') ?>  <br /><br />
			<?php _e( '<strong>4. An Ad:</strong> This is an ad item you added for this slide. A slide may contains one more ads. But on the front-end only one ad is shown for a slide. If you have one more ads (eg. 4 ads) then ads is roteated based on its percentage you set.', 'adguru') ?><br /><br />
			<?php _e( '<strong>5. New Ad Button:</strong> By clicking on this button a popup box is shown with a list of all ads you created. This list shows all ads those are match in size with the ad zone you selected. Select an ad and click <strong>Insert</strong> button.', 'adguru') ?><br /><br />
			<?php _e( '<strong>6. Rotator Percentage:</strong> Number of percentage, how many times this ad will be shown in 100 call. A slide can contain one more ads. Total of percentage value of all ads in same slide must be 100. If you have only one ad then keep it 100%.', 'adguru') ?><br /><br />
			<?php _e( '<strong>7. Equal Button:</strong> To auto fill rotator percentage value click on this button.', 'adguru') ?><br /><br />
			<?php _e( '<strong>8. Remove ad button:</strong> To remove an ad item click on this button.', 'adguru') ?><br />
		</div>	
	<?php 
	}//END FUNC	 
	 
}// end class 
endif;