<?php
/**
 * AD SETUP MANAGER
 * @package     WP AD GURU
 * @since       2.1.0
 * @author oneTarek
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Ad_Setup_Manager' ) ) :

class ADGURU_Ad_Setup_Manager{

	private $current_ad_type;
	private $current_ad_type_args;
	private $current_zone_id;
	private $current_zone;
	private $current_post_id;
	private $page_type_list_html;
	private $ad_zone_links;
	private $ad_zone_link_sets = array();
	private $ads_data = array();
	private $all_allowed_ads;//stores an array of all ads related to this zone and ad type

	 
	public function __construct(){

		//add_action('admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Render Setup manager page
	 * @since 2.1.0
	 */
	public function editor_page(){

		include_once( ADGURU_PLUGIN_DIR."includes/admin/ad-setup-manager/ad-setup-manager-page.php");
	}

	/**
	 *
	 *
	 **/
	private function page_type_item_data_attr( $args ){
		$json = json_encode($args);
		echo ' data-page_type_info_data="'.esc_attr( $json ).'" ';
	}

	/**
	 * Generate HTML for page type list
	 * @since 2.1.0
	 */
	private function get_page_type_list_html(){
		
		if( isset( $this->page_type_list_html ) )
		{
			return $this->page_type_list_html;
		}

		ob_start();	
		$post_types = ADGURU_Helper::get_post_type_list();
		$taxonomies = ADGURU_Helper::get_taxonomy_list();
		?>
		<ul class="page-type-list">
			<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"--", 'taxonomy'=>'--', 'term'=>'--' ) )?>>Default( all )</li>
			<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"home", 'taxonomy'=>'--', 'term'=>'--' ) )?>>Home</li>
			<li>
				<span class="group-name">Single Page</span>
				<ul>
					<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"singular", 'taxonomy'=>'single', 'term'=>'--' ) )?>>Any type post</li>
					<?php
					
					foreach( $post_types as $post_type_slug => $name )
					{?>
						<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"singular", 'taxonomy'=>'single', 'term'=>$post_type_slug, "post_type_name"=>$name ) )?>><?php echo $name ?></li>
					<?php 
					}
					?>
					<li>
						<span class="group-name">Has specific term</span>
						<ul>
						<?php 
						foreach( $taxonomies as $tax_slug => $taxonomy )
						{
							if( $taxonomy->hierarchical )
							{
								$categories = get_categories( array( 'hide_empty'=>0, 'taxonomy'=>$tax_slug ) );
							?>
								<li>
									<span class="group-name"><?php echo $taxonomy->labels->name; ?></span>
									<ul>
									<?php
									  foreach ($categories as $category)
									  {
									  ?>
									  	<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"singular", 'taxonomy'=>$tax_slug, 'term'=>$category->slug, "hierarchical"=>1, "term_name"=>$category->cat_name,"taxonomy_name"=>$taxonomy->labels->singular_name ) )?>><?php echo $category->cat_name ?></li>
									  <?php 
									  }
									?>
									</ul>
								</li>
							<?php  
							}
							else
							{
							?>
								<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"singular", "taxonomy"=>$tax_slug, 'term'=>"--", "hierarchical"=>0,"taxonomy_name"=>$taxonomy->labels->singular_name ) )?>>Specific <strong><?php echo $taxonomy->labels->singular_name; ?></strong></li>
							<?php
							}
						}
						?>
						</ul>
					</li>
				</ul>
			</li>
			<li>
				<span class="group-name">Taxonomy Archive page</span>
				<ul>
					<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"taxonomy", "taxonomy"=>"--","term"=>"--" ) )?>>Any Taxonomy page</li>
				<?php 
				foreach( $taxonomies as $tax_slug => $taxonomy )
				{
					if( $taxonomy->hierarchical )
					{
						$categories = get_categories( array( 'hide_empty'=>0, 'taxonomy'=>$tax_slug ) );
					?>
						<li>
							<span class="group-name"><?php echo $taxonomy->labels->name; ?></span>
							<ul>
								<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"taxonomy", "taxonomy"=>$tax_slug, "term"=>"--", "hierarchical"=>1, "taxonomy_name"=>$taxonomy->labels->singular_name ) )?>>Any <?php echo $taxonomy->labels->singular_name; ?></li>
							<?php
							  foreach ($categories as $category)
							  {
							  ?>
							  	<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"taxonomy", "taxonomy"=>$tax_slug, "term"=>$category->slug, "hierarchical"=>1, "term_name"=>$category->cat_name,"taxonomy_name"=>$taxonomy->labels->singular_name ) )?>><?php echo $category->cat_name ?></li>
							  <?php 
							  }
							?>
							</ul>
						</li>
					<?php  
					}
					else
					{
					?>
					<li>
						<span class="group-name"><?php echo $taxonomy->labels->name; ?></span>
						<ul>
							<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"taxonomy", "taxonomy"=>$tax_slug, "term"=>"--", "hierarchical"=>0, "taxonomy_name"=>$taxonomy->labels->singular_name ) )?>>Any <?php echo $taxonomy->labels->singular_name; ?></li>
							<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"taxonomy", "taxonomy"=>$tax_slug, "term"=>"", "hierarchical"=>0, "taxonomy_name"=>$taxonomy->labels->singular_name ) )?>>Specific <strong><?php echo $taxonomy->labels->singular_name; ?></strong></li>
						</ul>
					</li>
					<?php
					}
				}
				?>
				</ul>
			</li>
			<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"author", 'taxonomy'=>'--', 'term'=>'--' ) )?>>Author Archive Page</li>
			<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"search", 'taxonomy'=>'--', 'term'=>'--' ) )?>>Search Result Page</li>
			<li class="usable" <?php $this->page_type_item_data_attr( array( "page_type"=>"404_not_found", 'taxonomy'=>'--', 'term'=>'--' ) )?>>404 Page</li>
		</ul>
		<?php 
		$html = ob_get_clean();
		$this->page_type_list_html = $html;
		return $html;

	}
	
	/**
	 * Generate HTML for country list select input
	 * @since 2.1.0
	 */
	private function get_country_list_html(){
		ob_start();
		adguru()->html->get_country_list_select_input( '--', array( 'class'=>'country-select' ) );
		$html = ob_get_clean();
		$html = str_replace('Select A Country', 'Any Country', $html );
		return $html;
	}
	
	/**
	 * Generate HTML Template for an ad
	 * @since 2.1.0
	 */
	private function get_ad_html_template(){
		ob_start();
		?>
		<div class="ad" title="{{AD_TYPE_NAME}}" adid="{{AD_ID}}"  adtype="{{AD_TYPE}}">
			<div class="title">{{AD_TITLE}}</div>
			<div class="control-box">
				<span class="percentage-box"><span class="percentage-label">Rotate</span><input type="number" class="percentage" value="{{PERCENTAGE}}" max="100" min="0"> %</span>
				<a class="edit-btn" href="<?php echo admin_url('admin.php?page=adguru_ad_{{AD_TYPE}}&manager_tab=edit&ad_id=')?>{{AD_ID}}" target="_blank" title="Edit this ad"></a>
				<span class="ad-remove-btn" title="Remove this ad"></span>
			</div>
			<div class="more">{{MORE_HTML}}</div>
		</div><!-- /.ad -->
		<?php 
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Generate HTML Template for a slide
	 * @since 2.1.0
	 */
	private function get_slide_html_template(){
		ob_start();
		$slide_text = ( isset( $this->current_ad_type_args['use_zone'] ) && $this->current_ad_type_args['use_zone'] == true ) ? "Slide" : "Set";
		?>
		<div class="slide">
			<span class="slide-delete-btn" title="Delete this slide"></span>
			<div class="slide-header">
				<?php echo $slide_text?> <span class="slide_number">{{SLIDE_NUMBER}}</span>
				<span class="equal-btn" title="Click to fill all rotate fields with equal value"></span>
			</div>
			<div class="ads-box">
				{{ADS_HTML}}
			</div><!-- /.ads-box -->
			<div class="add-ad-btn-box"><span class="add-ad-btn">Add <?php echo $this->current_ad_type_args['name']?></span></div>
		</div><!-- /.slide -->
		<?php 
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Generate HTML Template for a condition set
	 * @since 2.1.0
	 */
	private function get_condition_set_html_template(){
		ob_start();
		$slide_text = ( isset( $this->current_ad_type_args['use_zone'] ) && $this->current_ad_type_args['use_zone'] == true ) ? "Slide" : "Set";
		?>
		<div class="condition-set" id="{{SET_HTML_ID}}">
			<div class="set-header">
				<span class="ec-btn" title="Edit page type"></span>
				<span class="page-type-display-box">{{PAGE_TYPE_DISPLAY_HTML}}</span>
				<div class="cs-box">
					<?php echo $this->get_country_list_html(); ?>
				</div>
				<div class="ac-box">
					<span class="ac-btn"></span>
				</div>
			</div>
			
			<div class="set-body">
				<div class="page-type-list-wrapper">
					<div class="page-type-list-box collapsed">
						<div class="page-type-list-box-inner">
							<span class="title">Select type of page</span>
							<?php echo $this->get_page_type_list_html(); ?>
						</div>
						<div class="open-close-arrow-box"><span class="open-close-arrow"></span></div>
					</div><!-- /.page-type-list-box -->
				</div><!-- /.page-type-list-wrapper -->
				
				<div class="condition-detail">{{CONDITION_DETAIL}}</div>
				
				<div class="slides-box">
					{{SLIDES_HTML}}
				</div><!-- /.slides-box -->
				<div class="add-slide-btn-box"><span class="add-slide-btn">Add new <?php echo $slide_text ?></span></div>
			</div><!-- /.set-body -->
			<div class="set-footer">
				<div class="set-error-msg-box"><!-- Error message will go here --></div>
				<span class="save-btn">Save</span>
				<span class="save-loading hidden"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
				
				<span class="delete-set-loading hidden"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
				<span class="delete-set-btn" title="Delete this set"></span>	
			</div><!-- /.set-footer -->
		</div><!-- /.condition-set -->	
		<?php 
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Render the HTML of modal with Ad list 
	 *
	 */
	
	private function render_ad_list_modal(){ 
		
		$ads = $this->get_all_allowed_ads();
		$all_ad_types = adguru()->ad_types->types;
		$use_zone = ( isset( $this->current_ad_type_args['use_zone'] ) && $this->current_ad_type_args['use_zone'] == 1 ) ? 1 : 0;
		$zone_width = 0;
		$zone_height = 0;
		if( $use_zone )
		{
			$zone = $this->get_current_zone();
			$zone_width = $zone->width;
			$zone_height = $zone->height;
		}

		?>
	
		 <div id="ad_list_modal" title="Insert <?php echo $this->current_ad_type_args['name'] ?>" style="display:none;">
			<div style="padding:5px;">
				<div style="width:470px; float:left;"><strong><?php echo sprintf( __( 'Select a %s and click insert', 'adguru' ) , $this->current_ad_type_args['name'] )?></strong></div>
				<div style="float:right; width:182px; text-align:right;"><input style="width:180px;" placeholder="Search" type="text" size="15" id="search_ad_list" /></div>
				<div style="clear:both"></div>
			</div>
			<div id="ads_list">
			<?php
			
			if( !is_array( $ads ) )
			{
				echo '<span style="color:#cc0000;">';
					echo sprintf( __( 'You have no %s for this zone size', 'adguru' ), $this->current_ad_type_args['name'] ).' <strong>'.$zone_width.'x'.$zone_height.'</strong>';
					echo ' <a href="admin.php?page='.ADGURU_ADMANAGER_PAGE_SLUG_PREFIX.$this->ad_type.'">';
						echo sprintf( __( 'Enter new ad', 'adguru') );
					echo '</a> ' ;
					echo sprintf( __( 'in %s size', 'adguru' ), '<strong>'.$zone_width.'x'.$zone_height.'</strong>' );
				echo '</span>';
			}
			else
			{
				foreach($ads as $ad)
				{
					$ad_type_name =  $all_ad_types[ $ad->type ]['name'];
					$ad_data = $this->get_ad_data( $ad );
					?>
					<div class="ads_list_item" ad_id="<?php echo $ad->ID ?>" ad_type_name="<?php echo $ad_type_name ?>" ad_type="<?php echo $ad->type ?>" ad_name="<?php echo esc_attr( $ad->name ) ?>" data-ad_data="<?php echo esc_attr(json_encode($ad_data))?>" ><span class="ad_name"><?php echo $ad->name ?></span><span class="ad_type"><?php echo $ad_type_name ?></span></div>
					<?php 
				}
			}
			?>
			</div>		
		 </div>
	<?php	
	}//END FUNC	


	/**
	 * Prepare required data before rendering condition sets
	 *
	 * @return void
	 */
	private function prepare(){

		if( !isset($this->current_ad_type ) || !isset($this->current_zone_id ) )
		{
			return;
		}
		//Prepare links
		$links_args = array(
			"zone_id"		=> 	$this->current_zone_id,
			"ad_type"		=> 	$this->current_ad_type
		);
		$ad_zone_links = adguru()->manager->get_ad_zone_links( $links_args );
		if( is_array( $ad_zone_links ) )
		{
			$this->ad_zone_links = $ad_zone_links;
		}

		$this->prepare_ad_zone_link_sets();
		
		#retrieved ads
		$this->ads_data = array();
		$ad_ids = array();
		foreach( $this->ad_zone_links as $link )
		{
			$ad_ids[] = $link->ad_id;
		}
		


		$ads = adguru()->manager->get_ads(array(
			"post_type" => ADGURU_POST_TYPE_PREFIX.$this->current_ad_type,
			"post__in" => $ad_ids,
			"posts_per_page" => -1
		));
		if( is_array($ads) )
		{
			foreach( $ads as $ad )
			{
				$this->ads_data[ $ad->ID ] = $this->get_ad_data( $ad );
			}
			
		}
	}

	/**
	 * Make simple array with required fields of an ad
	 *
	 * @since 2.1.0
	 * @param object $ad
	 * @return array
	 */
	private function get_ad_data( $ad ){
		$data = array(
			"ID" => $ad->ID, 
			"name" => $ad->name, 
			"type" => $ad->type, 
			"description" => $ad->description
		);
		return $data;
	}

	/**
	 * Prepare ad zone link sets array
	 *
	 * @return void
	 */
	private function prepare_ad_zone_link_sets(){

		if( !isset( $this->ad_zone_links ) || !is_array( $this->ad_zone_links ) )
		{
			return;
		}
		$grouped = array();
		$this->ad_zone_link_sets = array();
		foreach( $this->ad_zone_links as $link )
		{
			$key = $link->zone_id . $link->page_type . $link->taxonomy . $link->term . $link->object_id . $link->country_code;
			/*
			Problem : we want default things ( '--' ) to prioritize in sort , but alphabetical order of '--' is lower.
			Solution : convert all '--' in key to '00'
			*/
			$key = str_replace('--', '00', $key ); 
			$grouped[ $key ][] = $link;
		}
		
		ksort($grouped,SORT_LOCALE_STRING);

		foreach( $grouped as $key => $links )
		{
			$page_type_info_data = $this->get_page_type_data_for_a_link( $links[0] );
			$set = array(
				'page_type_info_data' => $page_type_info_data,
				'links' => $links
			);
			$this->ad_zone_link_sets[] = $set;
		}
		
	}

	/**
	 * Get all ads related to this zone size and ad type
	 *
	 * @return array
	 */
	private function get_all_allowed_ads(){

		if( isset( $this->all_allowed_ads ) )
		{
			return $this->all_allowed_ads;
		}

		$args = array(
			"post_type" =>	ADGURU_POST_TYPE_PREFIX.$this->current_ad_type, 
			'posts_per_page' =>	-1		
		);
		$use_zone = ( isset( $this->current_ad_type_args['use_zone'] ) && $this->current_ad_type_args['use_zone'] == 1 ) ? 1 : 0;
		if( $use_zone )
		{	
			$all_ad_types = adguru()->ad_types->types;
			$ptype = array();
			
			foreach( $all_ad_types as $ad_type => $ad_type_args )
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
		$this->all_allowed_ads = is_array( $ads ) ? $ads : array(); 
		return $this->all_allowed_ads;

	}

	/**
	 * Get Current Zone Object
	 */
	private function get_current_zone(){
		if( isset( $this->current_zone ) )
		{
			return $this->current_zone;
		}
		if( $this->current_zone_id == 0 )
		{
			$this->current_zone = false;
		}
		else
		{
			$this->current_zone = adguru()->manager->get_zone( $this->current_zone_id );
		}
		return $this->current_zone;
	}

	/**
	 * Calculate page type data for a link
	 * We need this to detect the page type for a set of links. 
	 * We will use this data in JS code to make initial query data and condition set heading ( page_type_display_html ).
	 */

	private function get_page_type_data_for_a_link( $link ){

		$post_types = ADGURU_Helper::get_post_type_list();
		$taxonomies = ADGURU_Helper::get_taxonomy_list();
		
		$data = array();
		$data['ad_type'] = $link->ad_type;
		$data['zone_id'] = $link->zone_id;
		$data['page_type'] = $link->page_type;
		$data['taxonomy'] = $link->taxonomy;
		$data['term'] = $link->term;
		$data['country_code'] = $link->country_code;
		$data['post_id'] = $link->object_id;
		
		switch( $link->page_type )
		{
			case 'singular' : 
			{
				if( $link->taxonomy == 'single' )
				{
					if( $link->term != '--')
					{
						$post_type = $post_types[ $link->term ];
						$data['post_type_name'] =  $post_type->name;
					}
				}
				else
				{
					
					$taxonomy = $taxonomies[ $link->taxonomy ];
					$data['taxonomy_name'] = $taxonomy->labels->singular_name;
					$data['hierarchical'] = ( $taxonomy->hierarchical ) ? 1 : 0;
					$term = get_term_by('slug', $link->term, $link->taxonomy );
					$data['term_name'] = $term->name;

				}
				break;
			}
			case 'taxonomy' : 
			{
				
				if( $link->taxonomy != '--')
				{
					$taxonomy = $taxonomies[ $link->taxonomy ];
					$data['taxonomy_name'] = $taxonomy->labels->singular_name;
					$data['hierarchical'] = ( $taxonomy->hierarchical ) ? 1 : 0;
					if( $data['hierarchical'] == 1 && $link->term != '--')
					{
						$term = get_term_by('slug', $link->term, $link->taxonomy );
						$data['term_name'] = $term->name;
					}
				}
				
				break;
			}
			

		}//end switch
		
		return $data;
	}


	/**
	 * Print JSON Data and JS file
	 *
	 * @return void
	 */
	private function print_script(){
		$data = array(
			'current_ad_type' => $this->current_ad_type,
			'current_zone_id' => $this->current_zone_id,
			'current_post_id' => $this->current_post_id,
			'ad_zone_link_sets' => $this->ad_zone_link_sets,
			'ads_data' => $this->ads_data,
			'page_type_list_html' => $this->get_page_type_list_html(),
			'country_list_html' => $this->get_country_list_html(),
			'ad_html_template' => $this->get_ad_html_template(),
			'slide_html_template' => $this->get_slide_html_template(),
			'condition_set_html_template' => $this->get_condition_set_html_template()
		);

		$data_json = wp_json_encode( $data );
		?>

		<script>
		var ADGURU_ASM_DATA = <?php echo $data_json ?>
		</script>
		<script src="<?php echo ADGURU_PLUGIN_URL ?>assets/js/ad-setup-manager.js"></script>

		<?php  
	}

}//end class
endif;