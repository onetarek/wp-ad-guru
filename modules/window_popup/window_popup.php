<?php
/**
 * Window Popup Ad Class
 * Register Window Popup management settings and functions
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'ADGURU_Window_Popup' ) ) : 
class ADGURU_Window_Popup{  
	/**
	 * Type of ad
	 * WP post type supports max 20 characters,Cannot contain capital letters , spaces and no special chars ( underscore allowed ).
	 * AdGuru reserves 4 characters for prefix adg_
	 * So Ad type must be less than or equal 16 characters. 
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $type = 'window_popup';

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
	  * Flag for whether window popup CSS and JS have been printed or not
	  * @var bool
	  * @since 2.0.0
	  */
	 private $common_assets_printed = false;

	 /**
	  * Construction
	  **/
	 public function __construct(){
	 
		$this->name = __( 'Window Popup', 'adguru' );
		$this->plural_name = __( 'Window Popups', 'adguru' );
		$this->description = __( 'Window Popup Ad Management.', 'adguru' );
		
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

		//Popup content via admin ajax
		add_action( 'wp_ajax_adguru_window_popup_content', array( $this, 'popup_content') );
		add_action( 'wp_ajax_nopriv_adguru_window_popup_content', array( $this, 'popup_content') );
		
		
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
		include_once( dirname(__FILE__)."/editor/form-sizing.php");
		include_once( dirname(__FILE__)."/editor/form-popup-options.php");
		include_once( dirname(__FILE__)."/editor/form-triggering.php");
	}

	/**
	 * Prepare Ad Data Before Save to Database
	 * @param array $ad
	 * @return array $ad
	 * @since 2.0.0	 
	 **/ 

	public function prepare_ad_to_save( $ad, $ad_from_db ){
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
		if( !isset($ad->design_source) ){ $ad->design_source = 'theme' ; }
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
			<h3 class="hndle">Preview</h3>
			<div class="inside">
				<div class="main">
						
					<a href="<?php echo $this->get_preview_url( $ad->ID ) ?>" target="_blank"> Preview Popup</a>

				</div><!-- .main -->
			</div><!-- .inside -->
		</div>
		<?php 
		}
	}//end function

#===============LIST FUNCTIONS====================================================

	/**
	 * Add Columns to List Table
	 * @param array $columns
	 * @return array $columns
	 * @since 2.0.0	 
	 */
	public function list_table_columns( $columns ){

		$columns['size']= __( 'Size', 'adguru' );
		//$columns['width']= __( 'Width', 'adguru' );
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
		if( isset( $item->sizing ) && is_array( $item->sizing ) )
		{
			$sizing = $item->sizing;
			$width = '';
			$height = '';
			if( isset($sizing['mode']) && $sizing['mode'] == 'custom' )
			{
				$width = ( isset( $sizing['custom_width'] ) ) ? $sizing['custom_width'] : '500';
				$height = ( isset( $sizing['custom_height'] ) ) ? $sizing['custom_height'] : '500';
			}

			$html = $width. ' x '.$height;

		}
		return $html;
	}

	/** 
	 *	Print common CSS and JavaScript for all modal popup
	 *  Print only once
	 */ 
	private function print_common_assets(){

		if( $this->common_assets_printed )
		{
			return ;
		}
		?>
		<script src="<?php echo ADGURU_PLUGIN_URL ?>modules/window_popup/assets/js/window-popup.js?var=<?php echo ADGURU_VERSION ?>"></script>
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

		if( !isset( $ad->content_type ) || $ad->content_type == "")
		{
			return false;
		}
		
		$sizing = ( isset( $ad->sizing ) && is_array( $ad->sizing ) ) ? $ad->sizing : array();
		$popup_options = ( isset( $ad->popup_options ) && is_array( $ad->popup_options ) ) ? $ad->popup_options : array();
		$triggering = ( isset( $ad->triggering ) && is_array( $ad->triggering ) ) ? $ad->triggering : array();
		if( $ad->content_type == "url" )
		{
			$popup_url = isset( $ad->content_url['url'] ) ? $ad->content_url['url'] : '';
		}
		else
		{
			$popup_url = admin_url( 'admin-ajax.php' )."?action=adguru_window_popup_content&adid=".$ad->ID;
		}
		
		ob_start();
		?>
		<div id="adguru_window_popup_<?php echo $ad->ID ?>" class="adguru-window-popup hidden" popup-id="<?php echo $ad->ID ?>" popup-url="<?php echo esc_attr($popup_url) ?>" data-sizing="<?php echo esc_attr(json_encode($sizing))?>" data-popup-options="<?php echo esc_attr(json_encode($popup_options))?>" data-triggering="<?php echo esc_attr(json_encode($triggering))?>"></div>
		<?php 
		$output = ob_get_clean();
		echo $output;			
		
	}//end func display
	
	/**
	 * Serve window content via admin ajax
	 * 
	 **/
	 
	 public function popup_content(){

	 	$ad_id = isset( $_GET['adid'] ) ? intval( $_GET['adid'] ) : 0 ;
	 	
	 	if( ! $ad_id )
	 	{ 
	 		echo "No popup id found"; 
	 		exit; 
	 	}

		$ad = adguru()->manager->get_ad( $ad_id );
		
		if( ! $ad )
		{ 
			echo "Not found"; exit; 
		}
		if( !isset( $ad->content_type ) || $ad->content_type == "" || $ad->content_type == "url")
		{
			echo "Content not found"; exit;
		}
		$popup_options = ( isset( $ad->popup_options ) && is_array( $ad->popup_options ) ) ? $ad->popup_options : array();
		
		?><!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<title><?php if( isset( $popup_options['window_title'] ) ){ echo esc_html( $popup_options['window_title'] ); } ?></title>
				<style type="text/css">
					body{margin:0; padding:0; height: 100%; min-height: 100%;overflow: hidden;}
					.popup_image_link{ text-decoration:none; border:none;}
					.popup_image{ margin:0; padding:0; border:none;}
				</style>
			</head>
			<body style="margin:0px; padding:0px;">
			<?php $ad->print_content(); ?>
			</body>
		</html>
		<?php 
		exit;	 
	 }//end func
	 
	 public function get_preview_url( $adid ){
		return add_query_arg( array(
			    'adguru_preview' => 1,
			    'adtype' => 'window_popup',
			    'adid' => $adid
			), home_url() );
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
					To show any window popup ad in your site, go to <a href="<?php echo add_query_arg( array( "manager_tab" => "links" ), $page_args['base_url']) ?>">Set Window Popups to pages</a> and set window popup to appropiate page. 
					<br /><strong> OR</strong><br />
					Add following php code anywhere in your site front-end pages. Replace the word <strong>'ad_id'</strong> with the <strong>id</strong> of the Window Popup you want to show<br />
					<code>
						&lt;?php if(function_exists('adguru_ad')){adguru_ad(ad_id);} ?&gt;
					</code><br />
					<strong>Example:</strong><br />
					<code>
						&lt;?php if(function_exists('adguru_ad')){adguru_ad(1);} ?&gt;
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
$adguru_window_popup = new ADGURU_Window_Popup();
