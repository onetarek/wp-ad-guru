<?php
/**
 * Modal Popup Ad Class
 * Register Modal Popup management settings and functions
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'ADGURU_Modal_Popup' ) ) : 
class ADGURU_Modal_Popup{  
	/**
	 * Type of ad
	 * WP post type supports max 20 characters,Cannot contain capital letters , spaces and no special chars ( underscore allowed ).
	 * AdGuru reserves 4 characters for prefix adg_
	 * So Ad type must be less than or equal 16 characters. 
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $type = 'modal_popup';

	/**
	 * Singular name of ad . Display Text
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $name;

	/**
	 * Plural name of ad . Display Text
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $plural_name;

	/**
	 * Sort Descriptoin of ad . Display Text
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $description; 

	/**
	 * Is this type of ad be placed in a zone ? 
	 * Optional , default = false
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $use_zone = false;

	/**
	 * Where the ad codes will be loaded when use_zone is false or not defined.
	 * Value is footer or header. 
	 * Optional, default = footer
	 * @var bool
	 * @since 2.0.0
	 **/	 
	 public $script_location = "footer";

	/**
	 * Are multiple slides ( contain ads ) be placed in same zone ?
	 * If multiple slides are set then ads will be shown as carousel/slider 
	 * Optional
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $multiple_slides = false;
	 
	/**
	 * Does this type rotate in a single slide?
	 * Ad will be changed in each page visit.
	 * Optional
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $rotator = true;	 	 
	 
	 /**
	  * Flag for whether modal popup CSS and JS have been printed or not
	  * @var bool
	  * @since 2.0.0
	  */
	 private $common_assets_printed = false;

	 /**
	  * Construction
	  **/
	 public function __construct(){
	 
		$this->name = __( 'Modal Popup', 'adguru' );
		$this->plural_name = __( 'Modal Popups', 'adguru' );
		$this->description = __( 'Modal Popup Ad Management.', 'adguru' );
		
	 	add_action( "adguru_init", array( $this, "register" ) );
	 	add_filter( "adguru_ad_editor_init_{$this->type}", array( $this, "ad_editor_init" ) );
	 	
	 	add_filter( "adguru_ad_prepare_to_save_{$this->type}", array( $this, "prepare_ad_to_save" ), 10, 2 );
		add_action( "adguru_ad_editor_left_after_content_{$this->type}" , array( $this, "ad_editor_after_content" ) , 10, 2 ); //adguru_ad_editor_left_before_content_{$this->type} . params  $ad, $error_msgs
		add_action( "adguru_ad_editor_sidebar_top_{$this->type}" , array( $this, "ad_editor_sidebar_top" ) , 10, 2 ); //adguru_ad_editor_sidebar_top_{$this->type} . params  $ad, $error_msgs
		
		
		add_filter( "adguru_ad_list_columns_{$this->type}", array( $this, "list_table_columns" ) ); #"adguru_ad_list_columns_{$ad_type}"
		//add_filter( "adguru_ad_list_shortable_columns_{$this->type}", array( $this, "list_table_shortable_columns" ) ); #"adguru_ad_list_shortable_columns_{$ad_type}"
		add_filter( "adguru_ad_list_column_output_{$this->type}", array( $this, "list_table_column_output" ), 10, 3 ); #"adguru_ad_list_column_output_{$ad_type}"
		add_filter( "adguru_ad_list_row_actions_{$this->type}", array( $this, "list_table_row_actions" ), 10, 2 ); #"adguru_ad_list_row_actions_{$ad_type}"
		
		add_action( "adguru_ad_manager_all_bottom_{$this->type}" , array( $this, "short_user_guide" ) ); //bottom of the page after the list table: "adguru_ad_manager_{$current_manager_tab}_bottom_{$current_ad_type}"
	 
	 	add_action("adguru_ad_display_{$this->type}", array( $this, "display" ) , 10 , 1);

	 	add_action("adguru_modal_popup_theme_editor_sidebar_top", array( $this, "theme_preview_area" ) , 10 , 2);
	 	add_action("adguru_ad_manager_edit_bottom_{$this->type}", array( $this, "edit_page_bottom" ) , 10 , 1);
	 	add_action("adguru_ad_manager_edit_theme_bottom_{$this->type}", array( $this, "edit_theme_page_bottom" ) , 10 , 1);

	 } 	 	 	 
	

	/**
	 * Register Ad type
	 * @since 2.0.0
	 * @return void
	 **/

	public function register(){
	
		$args = array(
			'slug'          => $this->type,
			'name'			=> $this->name,
			'plural_name'	=> $this->plural_name, 
			'description'   => $this->description,
		);
		$args[ 'use_zone' ] = isset( $this->use_zone ) ? $this->use_zone : false;
		$args[ 'script_location' ]  = isset( $this->script_location ) ? $this->script_location : 'footer';
		$args[ 'multiple_slides' ] = isset( $this->multiple_slides ) ? $this->multiple_slides : false;
		$args[ 'rotator' ]  = isset( $this->rotator ) ? $this->rotator : true;

		adguru_register_ad_type( $this->type, $args );
	
	}
	/**
	 * Initilize form builder and other things related to ad editor. 
	 */
	public function ad_editor_init()
	{
		include_once( dirname(__FILE__)."/editor/form-design.php");
		include_once( dirname(__FILE__)."/editor/form-sizing.php");
		include_once( dirname(__FILE__)."/editor/form-animation.php");
		include_once( dirname(__FILE__)."/editor/form-position.php");
		include_once( dirname(__FILE__)."/editor/form-triggering.php");
		include_once( dirname(__FILE__)."/editor/form-closing.php");
		include_once( dirname(__FILE__)."/editor/form-other.php");
	}

	/**
	 * Prepare Ad Data Before Save to Database
	 * @param array $ad
	 * @return array $ad
	 * @since 2.0.0	 
	 **/ 

	public function prepare_ad_to_save( $ad, $ad_from_db ){
		
		$ad->design_source = trim( $_POST['design_source'] );
		$ad->theme_id = intval( $_POST['theme_id'] );

		include( dirname(__FILE__)."/editor/prepare-to-save-forms.php");
		return $ad;	
	}


	/**
	 * Print Ad New/Edit Form Elements To Left Column of Ad Editor
	 * @param array $ad All data of an ad
	 * @param array $error_msgs messages for error if there is any, error calculated in prepare_ad_to_save()
	 * @return void
	 * @since 2.0.0	 
	 **/
	public function ad_editor_after_content( $ad, $error_msgs ){
		global $adguru_mp_theme_manager;
		if( !isset($ad->design_source) ){ $ad->design_source = 'theme' ; }
		if( !isset($ad->theme_id) ){ $ad->theme_id = $adguru_mp_theme_manager->default_theme_id ; }
		include( dirname(__FILE__)."/editor/edit_form_after_content.php");
	}//end function

	/**
	 * Print Ad New/Edit Form Elements To Right Column of Ad Editor
	 * @param array $ad All data of an ad
	 * @param array $error_msgs messages for error if there is any, error calculated in prepare_ad_to_save()
	 * @return void
	 * @since 2.0.0	 
	 **/
	public function ad_editor_sidebar_top( $ad, $error_msgs ){

		//Preview link
		if( $ad->ID)
		{
		?>
		<div class="postbox">
			<h3 class="hndle"><?php _e('Live Preview', 'adguru')?></h3>
			<div class="inside">
				<div class="main">
						
					<a href="<?php echo $this->get_preview_url( $ad->ID ) ?>" target="_blank"><?php _e('Preview Popup', 'adguru')?></a>

				</div><!-- .main -->
			</div><!-- .inside -->
		</div>
		<?php 
		}
		include_once( dirname(__FILE__)."/theme_builder/preview.php");
	}//end function 

#===============LIST FUNCTIONS====================================================

	/**
	 * Add Columns to List Table
	 * @param array $columns
	 * @return array $columns
	 * @since 2.0.0	 
	 */
	public function list_table_columns( $columns ){

		$columns['size'] = __( 'Size', 'adguru' );
		//$columns['width'] = __( 'Width', 'adguru' );
		return $columns; 
	
	}//end func



	/**
	 * Add Shortable Columns to List Table
	 * @param array $shortable_columns
	 * @return array $shortable_columns
	 * @since 2.0.0
	 **/
	public function list_table_shortable_columns( $shortable_columns ){

			return $shortable_columns;
	}

	/**
	 * Return output of a column cell
	 * @param string $html
	 * @param array $item
	 * @param string $column_name
	 * @return string
	 * @since 2.0.0
	 **/
	  
	public function list_table_column_output( $html, $item, $column_name ){

		switch( $column_name )
		{
			case 'size':
			{
				return $this->get_sizing_text( $item );
				break;
			}
						
			
		}
		return $html ;
		
	}

	/**
	 * Return array of row actions links
	 * @param array $row_actions
	 * @param array $item
	 * @return array
	 * @since 2.0.0
	 **/

	public function list_table_row_actions( $row_actions, $item ){

		$row_actions['preveiw_link'] = '<a href="'.$this->get_preview_url( $item->ID ).'" target="_blank"> Preview Popup</a>';
		return $row_actions;
	}

	/**
	 * Return sizing info text for size column
	 * @param array $item
	 * @return string
	 * @since 2.0.0
	 **/
	public function get_sizing_text( $item ){

		$html = "";
		if( isset($item->sizing ) && is_array( $item->sizing) )
		{
			$sizing = $item->sizing;
			$width = '';
			$height = '';
			if( isset($sizing['mode']) && $sizing['mode'] == 'responsive' )
			{
				$responsive_size = ( isset( $sizing['responsive_size'] ) ) ? $sizing['responsive_size'] : '40';
				$width = ($responsive_size == 'auto' ) ? 'Auto' : $responsive_size."%";
			}
			else
			{
				$width = ( isset( $sizing['custom_width'] ) ) ? $sizing['custom_width'].'px' : '600px';
			}
			if( isset($sizing['auto_height']) && $sizing['auto_height'] == 0 )
			{
				$custom_height = ( isset( $sizing['custom_height'] ) ) ? $sizing['custom_height'] : '400';
				$custom_height_unit = ( isset( $sizing['custom_height_unit'] ) ) ? $sizing['custom_height_unit'] : 'px';
				$height = $custom_height.$custom_height_unit;
			}
			else
			{
				$height = 'Auto';
			}
			$html = $width. ' x '.$height;


		}
		return $html;
	}

	/* 
	 *	Print common CSS and JavaScript for all modal popup
	 *  Print only once
	 * @since 2.0.0
	 */ 
	private function print_common_assets(){

		if( $this->common_assets_printed )
		{
			return ;
		}
		adguru_enqueue_style_in_footer('animate');
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo ADGURU_PLUGIN_URL ?>modules/modal_popup/assets/css/modal-popup.css?var=<?php echo ADGURU_VERSION ?>" >
		<script src="<?php echo ADGURU_PLUGIN_URL ?>modules/modal_popup/assets/js/modal-popup.js?var=<?php echo ADGURU_VERSION ?>"></script>
		<?php 
		$this->common_assets_printed = true;
	}

	/**
	 * Generate HTML/Script to show this ad
	 * @ad object of an ad
	 * @return void
	 * @since 2.0.0
	 * @use with action "adguru_{ad_type}_display"
	 **/
	public function display( $ad ){

		$this->print_common_assets();
		global $adguru_mp_theme_manager;
		//Check if popup theme is being used for this ad, then copy design data from theme and replace current ad design data
		if( isset( $ad->design_source ) && $ad->design_source == 'theme' )
		{
			$theme_id = ( isset($ad->theme_id) ) ? intval( $ad->theme_id ) : 0;
			
			$theme = $adguru_mp_theme_manager->get_theme( $theme_id );
			if( $theme )
			{
				$ad->design = $theme->design;
			}
			else
			{
				$theme_data_arr = json_decode( $adguru_mp_theme_manager->default_theme_data , true );
				$ad->design = $theme_data_arr['meta']['design'];
			}
		}

		$design = ( isset( $ad->design ) && is_array( $ad->design ) ) ? $ad->design : array();
		$sizing = ( isset( $ad->sizing ) && is_array( $ad->sizing ) ) ? $ad->sizing : array();
		$animation = ( isset( $ad->animation ) && is_array( $ad->animation ) ) ? $ad->animation : array();
		$position = ( isset( $ad->position ) && is_array( $ad->position ) ) ? $ad->position : array();
		$closing = ( isset( $ad->closing ) && is_array( $ad->closing ) ) ? $ad->closing : array();
		$triggering = ( isset( $ad->triggering ) && is_array( $ad->triggering ) ) ? $ad->triggering : array();

		//we need container border width and padding value to calculate content max-height with JS
		$sizing['container_border_width'] = 0;
		if( isset($design['container_border_enable']) && $design['container_border_enable'] == 1 )
		{
			$sizing['container_border_width'] = ( isset( $design['container_border_width'] ) ) ? $design['container_border_width'] : 5;
		}
		$sizing['container_padding'] = ( isset( $design['container_padding'] ) ) ? $design['container_padding'] : 0;


		$container_location = ( isset( $position['location'] ) ) ? $position['location'] : 'middle_center';
		$container_location_class = str_replace('_', '-', $container_location );
		
		$close_location = ( isset( $design['close_location'] ) ) ? $design['close_location'] : 'top_right';
		$close_location_class = str_replace('_', '-', $close_location );
		$container_custom_css_class = ( isset( $design['container_custom_css_class'] ) ) ? esc_attr( ' '.$design['container_custom_css_class'] ) : '';
		$close_custom_css_class = ( isset( $design['close_custom_css_class'] ) ) ? esc_attr( ' '.$design['close_custom_css_class'] ) : '';
		$close_button_type = ( isset( $design['close_button_type'] ) ) ? $design['close_button_type'] : 'text';
		if( $close_button_type == 'text' )
		{
			$close_text = ( isset( $design['close_text'] ) ) ? $design['close_text'] : 'X';
			$close_btn_content = $close_text;
		}
		else
		{
			$close_image_source_type = ( isset( $design['close_image_source_type'] ) ) ? $design['close_image_source_type'] : 'builtin';
			if( $close_image_source_type == 'builtin' )
			{
				$close_icon = false;
				if( isset( $design['close_image_name'] ) )
				{
					$close_icon = ADGURU_Helper::get_close_icon( $design['close_image_name'] );
				}
				if(empty($close_icon))
				{
					$close_icon = ADGURU_Helper::get_close_icon( 'core_close_default_png' );
				}
				$close_image_url = isset($close_icon['url'])? $close_icon['url'] : '#';
			}
			else
			{
				$close_image_url = ( isset( $design['close_custom_image_url'] ) ) ? $design['close_custom_image_url'] : '#';
			}
			
			$close_btn_content = '<img src="'.esc_attr($close_image_url).'" alt = "X" />';
			//$close_btn_content = '<img src="'.esc_attr(ADGURU_PLUGIN_URL .'modules/modal_popup/assets/images/close-icons/close-svg-test-1.svg').'" alt = "X" />';
			
		}
		
		$print_content_args = array(
			'wrapper_attributes' => array(
				'id' => 'adguru_modal_popup_content_'.$ad->ID,
				'class' => 'mp-content mp-content-'.$ad->content_type,
				'popup-id' => $ad->ID
				)
		);
		ob_start();
		
		?>
		<div id="adguru_modal_popup_<?php echo $ad->ID ?>" class="adguru-modal-popup hidden" popup-id="<?php echo $ad->ID ?>" data-animation="<?php echo esc_attr(json_encode($animation))?>" data-sizing="<?php echo esc_attr(json_encode($sizing))?>" data-closing="<?php echo esc_attr(json_encode($closing))?>" data-triggering="<?php echo esc_attr(json_encode($triggering))?>">
			<div id="adguru_modal_popup_overlay_<?php echo $ad->ID ?>" class="mp-overlay adguru-modal-popup-overlay" popup-id="<?php echo $ad->ID ?>"></div>
			<div id="adguru_modal_popup_container_wrap_<?php echo $ad->ID ?>" class="mp-container-wrap <?php echo $container_location_class ?>">
				<div id="adguru_modal_popup_conatiner_<?php echo $ad->ID ?>" class="mp-container <?php echo $container_custom_css_class ?>" popup-id="<?php echo $ad->ID ?>">
					<div id="adguru_modal_popup_content_wrap_<?php echo $ad->ID ?>" class="mp-content-wrap mp-content-wrap-<?php echo esc_attr( $ad->content_type );?>" popup-id="<?php echo $ad->ID ?>">
						<?php $ad->print_content( $print_content_args ); ?>
					</div>
					<div id="adguru_modal_popup_close_wrap_<?php echo $ad->ID ?>" class="mp-close-wrap <?php echo $close_location_class ?>"><div id="adguru_modal_popup_close_<?php echo $ad->ID ?>" class="mp-close adguru-modal-popup-close<?php echo $close_custom_css_class ?>" popup-id="<?php echo $ad->ID ?>"><?php echo $close_btn_content ?></div></div>
				</div>
			</div>
			
		</div>
		<style type="text/css"><?php $this->generate_output_css_for_single_popup( $ad ); ?></style>
		<?php 
		$output = ob_get_clean();
		echo $output;
		
	}//end func display

	private function generate_output_css_for_single_popup( $ad ){

		$rules = $this->get_output_css_array_for_single_popup( $ad );
		$ID = $ad->ID;
		echo '#adguru_modal_popup_'.$ID.' .mp-overlay{';
			foreach( $rules['overlay'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';
		echo '#adguru_modal_popup_'.$ID.' .mp-container-wrap{';
			foreach( $rules['container-wrap'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';
		echo '#adguru_modal_popup_'.$ID.' .mp-container{';
			foreach( $rules['container'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';
			echo '#adguru_modal_popup_'.$ID.' .mp-content{';
			foreach( $rules['content'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';
		echo '#adguru_modal_popup_'.$ID.' .mp-close{';
			foreach( $rules['close'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';
		echo '#adguru_modal_popup_'.$ID.' .mp-close-wrap{';
			foreach( $rules['close-wrap'] as $property => $value )
			{
				echo $property.': '.$value.';';
			}
		echo '}';

		do_action('adguru_modal_popup_generate_output_css_for_single_popup', $rules, $ad );

	}
	private function get_output_css_array_for_single_popup( $ad ){

		$ID = $ad->ID;
		$rules = array(
			"overlay" => array(),
			"container-wrap" => array(),
			"container" => array(),
			"content"	=> array(),
			"close" => array(),	
			"close-wrap" => array()
		);
		$design = ( isset( $ad->design ) && is_array( $ad->design ) ) ? $ad->design : array();
		$sizing = ( isset( $ad->sizing ) && is_array( $ad->sizing ) ) ? $ad->sizing : array();
		$animation = ( isset( $ad->animation ) && is_array( $ad->animation ) ) ? $ad->animation : array();
		$position = ( isset( $ad->position ) && is_array( $ad->position ) ) ? $ad->position : array();
		$other = ( isset( $ad->other ) && is_array( $ad->other ) ) ? $ad->other : array(); 
		
		$overlay_z_index = 999999;
		$z_index_by_user = ( isset( $other['z_index'] ) ) ? intval( $other['z_index'] ) : 0;
		
		if( $z_index_by_user != 0 )
		{
			$overlay_z_index = $z_index_by_user;
		}
		else
		{
			$overlay_z_index = $overlay_z_index + $ad->ID;
		}
		$container_z_index = $overlay_z_index + 1;
		
		#OVERLAY
		$hex_color = ( isset($design['overlay_background_color'] ) ) ? $design['overlay_background_color'] : "#444444";
		$opacity = ( isset( $design['overlay_background_opacity'] ) ) ? $design['overlay_background_opacity'] : '75';
		$rules['overlay']['background-color'] = ADGURU_Helper::hexToRgba( $hex_color , $opacity/100 );
		$rules['overlay']['z-index'] = $overlay_z_index;
		
		#CONTAINER
		$rules['container-wrap']['z-index'] = $container_z_index;
		if( isset($sizing['mode']) && $sizing['mode'] == 'responsive' )
		{
			$responsive_size = ( isset( $sizing['responsive_size'] ) ) ? $sizing['responsive_size'] : '40';
			$rules['container']['width'] = ( $responsive_size == 'auto') ? 'auto' : $responsive_size."%";
		}
		else
		{
			$custom_width = ( isset( $sizing['custom_width'] ) ) ? $sizing['custom_width'] : '600';
			$custom_width_unit = ( isset( $sizing['custom_width_unit'] ) ) ? $sizing['custom_width_unit'] : 'px';
			$rules['container']['width'] = $custom_width.$custom_width_unit;
		}
		if( isset($sizing['auto_height']) && $sizing['auto_height'] == 0 )
		{
			$custom_height = ( isset( $sizing['custom_height'] ) ) ? $sizing['custom_height'] : '400';
			$custom_height_unit = ( isset( $sizing['custom_height_unit'] ) ) ? $sizing['custom_height_unit'] : 'px';
			$rules['container']['height'] = $custom_height.$custom_height_unit;
		}
		else
		{
			$rules['container']['height'] = 'auto';
		}
		
		if( isset($sizing['max_width']) && intval( $sizing['max_width'] ) != 0 )
		{
			$max_width = $sizing['max_width'];
			$max_width_unit = ( isset( $sizing['max_width_unit'] ) ) ? $sizing['max_width_unit'] : 'px';
			$rules['container']['max-width'] = $max_width.$max_width_unit;
		}
		if( isset($sizing['min_width']) && intval( $sizing['min_width'] ) != 0 )
		{
			$min_width = $sizing['min_width'];
			$min_width_unit = ( isset( $sizing['min_width_unit'] ) ) ? $sizing['min_width_unit'] : 'px';
			$rules['container']['min-width'] = $min_width.$min_width_unit;
		}
		$container_max_height = 0;
		$container_max_height_unit = 'px';
		if( isset($sizing['max_height']) && intval( $sizing['max_height'] ) != 0 )
		{
			$max_height = $sizing['max_height'];
			$max_height_unit = ( isset( $sizing['max_height_unit'] ) ) ? $sizing['max_height_unit'] : 'px';
			$rules['container']['max-height'] = $max_height.$max_height_unit;
			$container_max_height = $max_height;
			$container_max_height_unit = $max_height_unit;
		}
		if( isset($sizing['min_height']) && intval( $sizing['min_height'] ) != 0 )
		{
			$min_height = $sizing['min_height'];
			$min_height_unit = ( isset( $sizing['min_height_unit'] ) ) ? $sizing['min_height_unit'] : 'px';
			$rules['container']['min-height'] = $min_height.$min_height_unit;
		}
		

		$container_border_width = 0;
		if( isset($design['container_border_enable']) && $design['container_border_enable'] == 1 )
		{
			$container_border_width = ( isset( $design['container_border_width'] ) ) ? $design['container_border_width'] : 5;
			$w = $container_border_width."px";
			$s = ( isset( $design['container_border_style'] ) ) ? $design['container_border_style'] : 'solid';
			$c = ( isset( $design['container_border_color'] ) ) ? $design['container_border_color'] : '#cccccc';
			$rules['container']['border'] = $w.' '.$s.' '.$c;
			$rules['container']['border-radius'] = ( isset( $design['container_border_radius'] ) ) ? $design['container_border_radius']."px" : '0';
		}
		$container_padding = ( isset( $design['container_padding'] ) ) ? $design['container_padding'] : 0;
		$rules['container']['padding'] = $container_padding.'px';

		if( isset($design['container_background_enable']) && $design['container_background_enable'] == 1 )
		{
			$hex_color = ( isset($design['container_background_color'] ) ) ? $design['container_background_color'] : "#ffffff";
			$opacity = ( isset( $design['container_background_opacity'] ) ) ? $design['container_background_opacity'] : '100';
			$rules['container']['background-color'] = ADGURU_Helper::hexToRgba( $hex_color , $opacity/100 );
		}

		if( isset($design['container_box_shadow_enable']) && $design['container_box_shadow_enable'] == 1 )
		{
			$h_offset = ( isset( $design['container_box_shadow_h_offset'] ) ) ? $design['container_box_shadow_h_offset']."px" : '3px';
			$v_offset = ( isset( $design['container_box_shadow_v_offset'] ) ) ? $design['container_box_shadow_v_offset']."px" : '3px';
			$blur_radius = ( isset( $design['container_box_shadow_blur_radius'] ) ) ? $design['container_box_shadow_blur_radius']."px" : '3px';
			$spread = ( isset( $design['container_box_shadow_spread'] ) ) ? $design['container_box_shadow_spread']."px" : '3px';

			$hex_color = ( isset( $design['container_box_shadow_color'] ) ) ? $design['container_box_shadow_color'] : '#444444';
			$opacity = ( isset( $design['container_box_shadow_opacity'] ) ) ? $design['container_box_shadow_opacity'] : '25';

			$color = ADGURU_Helper::hexToRgba( $hex_color , $opacity/100 );
			$inset = ( isset($design['container_box_shadow_inset']) && $design['container_box_shadow_inset'] == 'yes' ) ? ' inset' : '';

			$rules['container']['box-shadow'] = $h_offset.' '.$v_offset.' '.$blur_radius.' '.$spread.' '.$color.$inset;
			$rules['container']['-moz-box-shadow'] = $rules['container']['box-shadow'];
			$rules['container']['-webkit-box-shadow'] = $rules['container']['box-shadow'];
		}
		#CONTENT
		$rules['content']['overflow'] = ( isset($sizing['enable_scrollbar']) && $sizing['enable_scrollbar'] == 1 ) ? 'auto' : 'hidden';
		$rules['content']['height'] = '100%';
		if( $container_max_height != 0 && $container_max_height_unit == 'px' )//for container_max_height_unit == '%' will do nothing here. We will assign the rule using JS.
		{
			$content_max_height = $container_max_height - ( $container_border_width * 2 ) - ( $container_padding * 2 );
			$rules['content']['max-height'] = $content_max_height.'px';
		}

		#CLOSE BUTTON
		$rules['close']['height'] = ( isset( $design['close_height'] ) ) ? $design['close_height']."px" : '20px';
		$rules['close']['width'] = ( isset( $design['close_width'] ) ) ? $design['close_width']."px" : '20px';
		$rules['close']['padding'] = ( isset( $design['close_padding'] ) ) ? $design['close_padding']."px" : '2px';
		if( isset($design['close_border_enable']) && $design['close_border_enable'] == 1 )
		{
			$w = ( isset( $design['close_border_width'] ) ) ? $design['close_border_width']."px" : '3px';
			$s = ( isset( $design['close_border_style'] ) ) ? $design['close_border_style'] : 'solid';
			$c = ( isset( $design['close_border_color'] ) ) ? $design['close_border_color'] : '#cccccc';
			$rules['close']['border'] = $w.' '.$s.' '.$c;
			$rules['close']['border-radius'] = ( isset( $design['close_border_radius'] ) ) ? $design['close_border_radius']."px" : '0';
		}
		$rules['close']['text-align'] = 'center';
		$button_type = ( isset( $design['close_button_type'] ) ) ? $design['close_button_type'] : 'text';
		if( $button_type == 'text')
		{
			$rules['close']['color'] = ( isset( $design['close_color'] ) ) ? $design['close_color'] : '#000000';
			$rules['close']['font-size'] = ( isset( $design['close_font_size'] ) ) ? $design['close_font_size']."px" : '16px';
			$rules['close']['line-height'] = ( isset( $design['close_line_height'] ) ) ? $design['close_line_height']."px" : '16px';

			$font_name = ( isset( $design['close_font_family'] ) ) ? $design['close_font_family'] : 'Arial';
			
			$rules['close']['font-family'] = ($font_name == 'use_from_theme' ) ? 'inherit': ADGURU_Helper::get_font_family_with_fallback( $font_name );
			$rules['close']['font-weight'] = ( isset( $design['close_font_weight'] ) ) ? $design['close_font_weight'] : 'normal';
			$rules['close']['font-style'] = ( isset( $design['close_font_style'] ) ) ? $design['close_font_style'] : 'normal';

			if( isset($design['close_text_shadow_enable']) && $design['close_text_shadow_enable'] == 1 )
			{
				$h_offset = ( isset( $design['close_text_shadow_h_offset'] ) ) ? $design['close_text_shadow_h_offset']."px" : '1px';
				$v_offset = ( isset( $design['close_text_shadow_v_offset'] ) ) ? $design['close_text_shadow_v_offset']."px" : '1px';
				$blur_radius = ( isset( $design['close_text_shadow_blur_radius'] ) ) ? $design['close_text_shadow_blur_radius']."px" : '1px';
				$color = ( isset( $design['close_text_shadow_color'] ) ) ? $design['close_text_shadow_color'] : '#444444';
				$rules['close']['text-shadow'] = $h_offset.' '.$v_offset.' '.$blur_radius.' '.$color;
			}

			$rules['close']['line-height'] = ( isset( $design['close_line_height'] ) ) ? $design['close_line_height']."px" : '16px';

		}
		else
		{
			//rule for button type image
		}
		if( isset($design['close_background_enable']) && $design['close_background_enable'] == 1 )
		{
			$hex_color = ( isset($design['close_background_color'] ) ) ? $design['close_background_color'] : "#ffffff";
			$opacity = ( isset( $design['close_background_opacity'] ) ) ? $design['close_background_opacity'] : '100';
			$rules['close']['background-color'] = ADGURU_Helper::hexToRgba( $hex_color , $opacity/100 );
		}
		

		if( isset($design['close_box_shadow_enable']) && $design['close_box_shadow_enable'] == 1 )
		{
			$h_offset = ( isset( $design['close_box_shadow_h_offset'] ) ) ? $design['close_box_shadow_h_offset']."px" : '3px';
			$v_offset = ( isset( $design['close_box_shadow_v_offset'] ) ) ? $design['close_box_shadow_v_offset']."px" : '3px';
			$blur_radius = ( isset( $design['close_box_shadow_blur_radius'] ) ) ? $design['close_box_shadow_blur_radius']."px" : '3px';
			$spread = ( isset( $design['close_box_shadow_spread'] ) ) ? $design['close_box_shadow_spread']."px" : '3px';

			$hex_color = ( isset( $design['close_box_shadow_color'] ) ) ? $design['close_box_shadow_color'] : '#444444';
			$opacity = ( isset( $design['close_box_shadow_opacity'] ) ) ? $design['close_box_shadow_opacity'] : '25';

			$color = ADGURU_Helper::hexToRgba( $hex_color , $opacity/100 );
			$inset = ( isset($design['close_box_shadow_inset']) && $design['close_box_shadow_inset'] == 'yes' ) ? ' inset' : '';

			$rules['close']['box-shadow'] = $h_offset.' '.$v_offset.' '.$blur_radius.' '.$spread.' '.$color.$inset;
			$rules['close']['-moz-box-shadow'] = $rules['container']['box-shadow'];
			$rules['close']['-webkit-box-shadow'] = $rules['container']['box-shadow'];
		}
		
		$close_location = ( isset( $design['close_location'] ) ) ? $design['close_location'] : 'top_right';
		if( $close_location == 'top_left' )
		{
			$close_top = ( isset( $design['close_top'] ) ) ? $design['close_top'] : 0;
			$close_left = ( isset( $design['close_left'] ) ) ? $design['close_left'] : 0;

			if( $close_top < 0 && ($container_border_width + $close_top )< 0 ){ $close_top_negative = $container_border_width + $close_top; }
			if( $close_left < 0 && ($container_border_width + $close_left )< 0 ){ $close_left_negative = $container_border_width + $close_left; }

			//$close_top = $close_top - $container_border_width;
			//$close_left = $close_left - $container_border_width;
			$rules['close-wrap']['top'] = $close_top.'px';
			$rules['close-wrap']['left'] = $close_left.'px';
			
		}
		elseif( $close_location == 'top_center' )
		{
			$close_top = ( isset( $design['close_top'] ) ) ? $design['close_top'] : 0;	

			if( $close_top < 0 && ($container_border_width + $close_top )< 0 ){ $close_top_negative = $container_border_width + $close_top; }

			//$close_top = $close_top - $container_border_width;
			$rules['close-wrap']['top'] = $close_top.'px';
		}
		elseif( $close_location == 'top_right' )
		{
			$close_top = ( isset( $design['close_top'] ) ) ? $design['close_top'] : 0;
			$close_right = ( isset( $design['close_right'] ) ) ? $design['close_right'] : 0;

			if( $close_top < 0 && ($container_border_width + $close_top )< 0 ){ $close_top_negative = $container_border_width + $close_top; }
			if( $close_right < 0 && ($container_border_width + $close_right )< 0 ){ $close_right_negative = $container_border_width + $close_right; }

			//$close_top = $close_top - $container_border_width;
			//$close_right = $close_right - $container_border_width;
			$rules['close-wrap']['top'] = $close_top.'px';
			$rules['close-wrap']['right'] = $close_right.'px';
		}
		elseif( $close_location == 'middle_left' )
		{
			$close_left = ( isset( $design['close_left'] ) ) ? $design['close_left'] : 0;
			
			if( $close_left < 0 && ($container_border_width + $close_left )< 0 ){ $close_left_negative = $container_border_width + $close_left; }

			//$close_left = $close_left - $container_border_width;
			$rules['close-wrap']['left'] = $close_left.'px';
		}
		elseif( $close_location == 'middle_right' )
		{
			$close_right = ( isset( $design['close_right'] ) ) ? $design['close_right'] : 0;
			
			if( $close_right < 0 && ($container_border_width + $close_right )< 0 ){ $close_right_negative = $container_border_width + $close_right; }
			
			//$close_right = $close_right - $container_border_width;
			$rules['close-wrap']['right'] = $close_right.'px';
		}
		elseif( $close_location == 'bottom_left' )
		{
			$close_left = ( isset( $design['close_left'] ) ) ? $design['close_left'] : 0;
			$close_bottom = ( isset( $design['close_bottom'] ) ) ? $design['close_bottom'] : 0;

			if( $close_left < 0 && ($container_border_width + $close_left )< 0 ){ $close_left_negative = $container_border_width + $close_left; }
			if( $close_bottom < 0 && ($container_border_width + $close_bottom )< 0 ){ $close_bottom_negative = $container_border_width + $close_bottom; }

			//$close_left = $close_left - $container_border_width;
			//$close_bottom = $close_bottom - $container_border_width;
			$rules['close-wrap']['left'] = $close_left.'px';
			$rules['close-wrap']['bottom'] = $close_bottom.'px';

		}
		elseif( $close_location == 'bottom_center' )
		{
			$close_bottom = ( isset( $design['close_bottom'] ) ) ? $design['close_bottom'] : 0;

			if( $close_bottom < 0 && ($container_border_width + $close_bottom )< 0 ){ $close_bottom_negative = $container_border_width + $close_bottom; }

			//$close_bottom = $close_bottom - $container_border_width;
			$rules['close-wrap']['bottom'] = $close_bottom.'px';
		}
		elseif( $close_location == 'bottom_right' )
		{
			$close_right = ( isset( $design['close_right'] ) ) ? $design['close_right'] : 0;
			$close_bottom = ( isset( $design['close_bottom'] ) ) ? $design['close_bottom'] : 0;

			if( $close_right < 0 && ($container_border_width + $close_right )< 0 ){ $close_right_negative = $container_border_width + $close_right; }
			if( $close_bottom < 0 && ($container_border_width + $close_bottom )< 0 ){ $close_bottom_negative = $container_border_width + $close_bottom; }
			
			//$close_right = $close_right - $container_border_width;
			//$close_bottom = $close_bottom - $container_border_width;
			$rules['close-wrap']['right'] = $close_right.'px';
			$rules['close-wrap']['bottom'] = $close_bottom.'px';
		}
		
		#CONTAINER AGAIN
		$location = ( isset( $position['location'] ) ) ? $position['location'] : 'middle_center';
		if( $location == 'top_left' )
		{
			$container_top = ( isset( $position['top'] ) ) ? $position['top'] : 0;
			if( isset( $close_top_negative ) ){ $container_top = max( $container_top, abs($close_top_negative) ); }
			$container_left = ( isset( $position['left'] ) ) ? $position['left'] : 0;
			if( isset( $close_left_negative ) ){ $container_left = max( $container_left, abs($close_left_negative) ); }

			$rules['container-wrap']['top'] = $container_top.'px';
			$rules['container']['margin-left'] = $container_left.'px';
		}
		elseif( $location == 'top_center' )
		{
			$container_top = ( isset( $position['top'] ) ) ? $position['top'] : 0;
			if( isset( $close_top_negative ) ){ $container_top = max( $container_top, abs($close_top_negative) ); }
			$rules['container-wrap']['top'] = $container_top.'px';
		}
		elseif( $location == 'top_right' )
		{
			$container_top = ( isset( $position['top'] ) ) ? $position['top'] : 0;
			if( isset( $close_top_negative ) ){ $container_top = max( $container_top, abs($close_top_negative) ); }
			$container_right = ( isset( $position['right'] ) ) ? $position['right'] : 0;
			if( isset( $close_right_negative ) ){ $container_right = max( $container_right, abs($close_right_negative) ); }
			
			$rules['container-wrap']['top'] = $container_top.'px';
			$rules['container']['margin-right'] = $container_right.'px';
		}
		elseif( $location == 'middle_left' )
		{
			$container_left = ( isset( $position['left'] ) ) ? $position['left'] : 0;
			if( isset( $close_left_negative ) ){ $container_left = max( $container_left, abs($close_left_negative) ); }
			$rules['container']['margin-left'] = $container_left.'px';
		}
		elseif( $location == 'middle_center' )
		{
			//nothing
		}
		elseif( $location == 'middle_right' )
		{
			$container_right = ( isset( $position['right'] ) ) ? $position['right'] : 0;
			if( isset( $close_right_negative ) ){ $container_right = max( $container_right, abs($close_right_negative) ); }
			$rules['container']['margin-right'] = $container_right.'px';
		}
		elseif( $location == 'bottom_left' )
		{
			$container_left = ( isset( $position['left'] ) ) ? $position['left'] : 0;
			if( isset( $close_left_negative ) ){ $container_left = max( $container_left, abs($close_left_negative) ); }
			$container_bottom = ( isset( $position['bottom'] ) ) ? $position['bottom'] : 0;
			if( isset( $close_bottom_negative ) ){ $container_bottom = max( $container_bottom, abs($close_bottom_negative) ); }

			$rules['container']['margin-left'] = $container_left.'px';
			$rules['container-wrap']['bottom'] = $container_bottom.'px';
		}
		elseif( $location == 'bottom_center' )
		{
			$container_bottom = ( isset( $position['bottom'] ) ) ? $position['bottom'] : 0;
			if( isset( $close_bottom_negative ) ){ $container_bottom = max( $container_bottom, abs($close_bottom_negative) ); }
			$rules['container-wrap']['bottom'] = $container_bottom.'px';
		}
		elseif( $location == 'bottom_right' )
		{
			$container_right = ( isset( $position['right'] ) ) ? $position['right'] : 0;
			if( isset( $close_right_negative ) ){ $container_right = max( $container_right, abs($close_right_negative) ); }
			$container_bottom = ( isset( $position['bottom'] ) ) ? $position['bottom'] : 0;
			if( isset( $close_bottom_negative ) ){ $container_bottom = max( $container_bottom, abs($close_bottom_negative) ); }
			$rules['container']['margin-right'] = $container_right.'px';
			$rules['container-wrap']['bottom'] = $container_bottom.'px';
		}


		
		$rules = apply_filters('adguru_modal_popup_output_css_array', $rules, $ad );

		return $rules;
	}//end function

	public function get_preview_url( $adid ){

		return add_query_arg( array(
			    'adguru_preview' => 1,
			    'adtype' => 'modal_popup',
			    'adid' => $adid
			), home_url() );
	}

	public function theme_preview_area( $theme, $error_msgs ){

		include_once( dirname(__FILE__)."/theme_builder/preview.php");
	}
	public function edit_page_bottom( $current_manager_vars ){
		
		echo '<script src="'.ADGURU_PLUGIN_URL.'modules/modal_popup/assets/js/fields.js?var='.ADGURU_VERSION.'"></script>';
		echo '<script src="'.ADGURU_PLUGIN_URL.'modules/modal_popup/assets/js/editor.js?var='.ADGURU_VERSION.'"></script>';
		echo '<script src="'.ADGURU_PLUGIN_URL.'modules/modal_popup/assets/js/preview.js?var='.ADGURU_VERSION.'"></script>';
		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			ADGURU_MP_EDITOR.init();
    	});//jQuery(document) 
		</script>

		<?php
	}

	public function edit_theme_page_bottom( $current_manager_vars ){

		echo '<script src="'.ADGURU_PLUGIN_URL.'modules/modal_popup/assets/js/fields.js?var='.ADGURU_VERSION.'"></script>';
		echo '<script src="'.ADGURU_PLUGIN_URL.'modules/modal_popup/assets/js/preview.js?var='.ADGURU_VERSION.'"></script>';
		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			ADGURU_MP_FIELDS.read_values('design');
			ADGURU_MP_PREIVEW.init();
			ADGURU_MP_PREIVEW.reload();
    	});//jQuery(document) 
		</script>
		<?php
	}
	/**
	 * Extra section at bottom of List table page
	 **/
	public function short_user_guide( $page_args ){
		
	?>
		<br /><br /><br />
		<table class="widefat">
			<thead>
				<tr><th>Usage</th></tr>
			</thead>
			<tr>
				<td>
					To show any modal popup ad in your site, go to <a href="<?php echo add_query_arg( array( "manager_tab" => "links" ), $page_args['base_url']) ?>">Set Modal Popups to pages</a> and set modal popup to appropiate page. 
					<br /><strong> OR</strong><br />
					Add following php code anywhere in your site front-end pages. Replace the word <strong>'ad_id'</strong> with the <strong>id</strong> of the Modal Popup you want to show<br />
					<code>
						&lt;?php if( function_exists( 'adguru_ad' )){ adguru_ad(ad_id); } ?&gt;
					</code><br />
					<strong>Example:</strong><br />
					<code>
						&lt;?php if( function_exists( 'adguru_ad' ) ){ adguru_ad(1); } ?&gt;
					</code>
	
					<br /><br /><strong> OR</strong><br />
					Use following shortcode in your post content<br />
					<code>
						[adguru adid="ad_id"]
					</code><br />
					<strong>Example:</strong><br />	
					<code>
						[adguru adid="1"]
					</code>					
					<br /><br /><br />
				</td>
			</tr>
		</table>
	
	<?php 	
	}


}//end class
endif;

//Run this class
$adguru_modal_popup = new ADGURU_Modal_Popup();
