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
	private $page_type_list_html;
	private $taxonomy_list;
	private $ad_zone_links;
	private $ad_zone_link_sets = array();
	private $ads_data = array();

	 
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
	 * Generate HTML for page type list
	 * @since 2.1.0
	 */
	private function get_page_type_list_html(){
		
		if( isset( $this->page_type_list_html ) )
		{
			return $this->page_type_list_html;
		}

		ob_start();	
		$post_types = $this->get_post_type_list();
		$taxonomies = $this->get_taxonomy_list();
		?>
		<ul class="page-type-list">
			<li class="usable">Default( all )</li>
			<li class="usable">Home</li>
			<li>
				<span class="group-name">Single Page</span>
				<ul>
					<li class="usable">Any type post</li>
					<?php
					
					foreach( $post_types as $key => $name )
					{?>
						<li class="usable"><?php echo $name ?></li>
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
									  	<li class="usable"><?php echo $category->cat_name ?></li>
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
								<li class="usable">Specific <strong><?php echo $taxonomy->labels->singular_name; ?></strong></li>
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
					<li class="usable">Any Taxonomy page</li>
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
								<li class="usable">Any <?php echo $taxonomy->labels->singular_name; ?></li>
							<?php
							  foreach ($categories as $category)
							  {
							  ?>
							  	<li class="usable"><?php echo $category->cat_name ?></li>
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
							<li class="usable">Any <?php echo $taxonomy->labels->singular_name; ?></li>
							<li class="usable">Specific <strong><?php echo $taxonomy->labels->singular_name; ?></strong></li>
						</ul>
					</li>
					<?php
					}
				}
				?>
				</ul>
			</li>
			<li class="usable">Author Archive Page</li>
			<li class="usable">Search Result Page</li>
			<li class="usable">404 Page</li>
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
		return $html;
	}
	
	/**
	 * Generate HTML Template for an ad
	 * @since 2.1.0
	 */
	private function get_ad_html_template(){
		ob_start();
		?>
		<div class="ad">
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
		?>
		<div class="slide">
			<span class="slide-delete-btn" title="Delete this slide"></span>
			<div class="slide-header">
				Slide <span class="slide_number">{{SLIDE_NUMBER}}</span>
				<span class="equal-btn" title="Click to fill all rotate fields with equal value"></span>
			</div>
			<div class="ads-box">
				{{ADS_HTML}}
			</div><!-- /.ads-box -->
			<div class="add-ad-btn-box"><span class="add-ad-btn">Add new banner</span></div>
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
		?>
		<div class="condition-set" id="{{SET_HTML_ID}}">
			<div class="set-header">
				<span class="ec-btn" title="Edit page type"></span>
				<span class="page-type-display-box">{{PAGE_TYPE_DISPLAY_HTML}}<span>
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
				<div class="add-slide-btn-box"><span class="add-slide-btn">Add new slide</span></div>
			</div><!-- /.set-body -->
			<div class="set-footer">
				<div class="set-error-msg-box">Error message will go here<!-- Error message will go here --></div>
				<span class="save-btn">Save</span>
				<span class="save-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
				
				<span class="delete-set-loading"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading32.gif" height="32" /></span>
				<span class="delete-set-btn" title="Delete this set"></span>	
			</div><!-- /.set-footer -->
		</div><!-- /.condition-set -->	
		<?php 
		$html = ob_get_clean();
		return $html;
	}


	/**
	 * Retrieve all registred post types.
	 * @since 2.1.0
	 */

	private function get_post_type_list(){
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

		return $post_types;

	}

	/**
	 * Retrieve registred taxonomies those have real user facing usages.
	 * @since 2.1.0
	 */
	private function get_taxonomy_list(){

		if( isset( $this->taxonomy_list ) )
		{
			return $this->taxonomy_list;
		}

		$taxonomies = get_taxonomies(array(), 'objects');
		
		$remTax = array( "nav_menu", "link_category", "post_format", "single", "Single" ); #we remove "single" because it a reserve word for this plugin. This word "Single" we are using to store as a taxonomy for when  post types are stored as terms.	
		
		foreach( $taxonomies as $key => $taxobj )
		{
			if( in_array($key, $remTax ) )#remove taxonomies those are being used only for internal usages. Those object/post_types does not have show UI.
			{
				unset( $taxonomies[ $key ] );
				continue;
			}
			
			if( !isset( $taxobj->object_type ) || !is_array( $taxobj->object_type ) )
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
		$this->taxonomy_list = $taxonomies;
		return $taxonomies;
	}

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
				$ad_data = array(
					"ID" => $ad->ID, 
					"name" => $ad->name, 
					"type" => $ad->type, 
					"description" => $ad->description
				);

				$this->ads_data[ $ad->ID ] = $ad_data;
			}
			
		}
		//write_log($this->ads);
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
			$grouped[ $link->zone_id . $link->page_type . $link->taxonomy . $link->term . $link->object_id . $link->country_code ][] = $link;
		}
		foreach( $grouped as $key => $set )
		{
			$this->ad_zone_link_sets[] = $set;
		}
		
		//write_log($this->grouped_ad_zone_links);
	}

	/**
	 * Get an ad data from previously retrieved ads list
	 *
	 * @param int $id ad id
	 * @return array
	 */
	private function get_ad_data( $id ){

		if( isset( $this->ads_data[$id] ) )
		{
			return $this->ads_data[$id];
		}
		else
		{
			return false;
		}
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