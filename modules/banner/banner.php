<?php
/**
 * Banner Ad Class
 * Register Banner management settings and functions
 * @package     WP AD GURU
 * @since       2.0.0
 * @author oneTarek
 */
 

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'ADGURU_Banner' ) ) : 
class ADGURU_Banner{  
	/**
	 * Type of ad
	 * WP post type supports max 20 characters,Cannot contain capital letters , spaces and no special chars ( underscore allowed ).
	 * AdGuru reserves 4 characters for prefix adg_
	 * So Ad type must be less than or equal 16 characters. 
	 * Required
	 * @var string
	 * @since 2.0.0
	 **/
	 public $type = 'banner';

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
	 * Optional, default = false
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $use_zone = true;

	/**
	 * Are multiple slides ( contain ads ) be placed in same zone ?
	 * If multiple slides are set then ads will be shown as carousel/slider 
	 * Optional
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $multiple_slides = true;
	 
	/**
	 * Does this type rotate in a single slide?
	 * Ad will be changed in each page visit.
	 * Optional
	 * @var bool
	 * @since 2.0.0
	 **/
	 public $rotator = true;	 	 
	 
	 /**
	  * Construction
	  **/
	 public function __construct(){
	 
		$this->name = __( 'Banner', 'adguru' );
		$this->plural_name = __( 'Banners', 'adguru' );
		$this->description = __( 'Banner Ad Management.', 'adguru' );
		
	 	add_action( "adguru_init", array( $this, "register" ) );

	 	add_filter( "adguru_ad_prepare_to_save_{$this->type}", array( $this, "prepare_ad_to_save" ), 10, 2 );
		add_action( "adguru_ad_editor_left_before_content_{$this->type}" , array( $this, "ad_editor_before_content" ) , 10, 2 ); //adguru_ad_editor_left_before_content_{$this->type} . params  $ad, $error_msgs
		
		add_filter( "adguru_ad_list_columns_{$this->type}", array( $this, "list_table_columns" ) ); #"adguru_ad_list_columns_{$ad_type}"
		//add_filter( "adguru_ad_list_shortable_columns_{$this->type}", array( $this, "list_table_shortable_columns" ) ); #"adguru_ad_list_shortable_columns_{$ad_type}"
		add_filter( "adguru_ad_list_column_output_{$this->type}", array( $this, "list_table_column_output" ), 10, 3 ); #"adguru_ad_list_column_output_{$ad_type}"
		
		add_action( "adguru_ad_manager_all_bottom_{$this->type}" , array( $this, "short_user_guide" ) ); //bottom of the page after the list table: "adguru_ad_manager_{$current_manager_tab}_bottom_{$current_ad_type}"
	 
	 	add_action("adguru_ad_display_{$this->type}", array( $this, "display" ) , 10 , 1);

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
		$args[ 'multiple_slides' ] = isset( $this->multiple_slides ) ? $this->multiple_slides : false;
		$args[ 'rotator' ]  = isset( $this->rotator ) ? $this->rotator : true;

		adguru_register_ad_type( $this->type, $args );
	
	}

	/**
	 * Prepare Ad Data Before Save to Database
	 * @param array $ad
	 * @return array $ad
	 * @since 2.0.0	 
	 **/ 

	public function prepare_ad_to_save( $ad, $ad_from_db ){
	
		$width = intval( $_POST[ 'width' ] );
		$height = intval( $_POST[ 'height' ] );
		// check valid data and set errors
		if( $width == 0 ){ adguru_set_ad_input_error( 'width' , 'Set banner width' ); }
		if( $height == 0 ){ adguru_set_ad_input_error( 'height' , 'Set banner height' ); }
		
		$sizing_data = array();
		$sizing_data['width'] = $width;
		$sizing_data['height'] = $height;
		$ad->sizing = $sizing_data;

		return $ad;	
	}


	/**
	 * Print Ad New/Edit Form Elements To Left Column of Ad Editor
	 * @param array $ad All data of an ad
	 * @param array $error_msgs messages for error if there is any, error calculated in prepare_ad_to_save()
	 * @return void
	 * @since 2.0.0	 
	 **/
	public function ad_editor_before_content( $ad, $error_msgs ){
		
		if( ! isset( $ad->width )){ $ad->width = ""; }
		if( ! isset( $ad->height )){ $ad->height = ""; }
		$size_list = array(
			'728x90'=>'Leaderboard',
			'468x60'=>'Banner',
			'234x60'=>'Half Banner',
			'125x125'=>'Button',
			'120x600'=>'Skyscraper',
			'160x600'=>'Wide Skyscraper',
			'120x240'=>'Verticle Banner',
			'200x200'=>'Small square',
			'250x250'=>'Square',
			'120x90'=>'Button 2',
			'120x60'=>'Button 3',
			'180x150'=>'Small Rectangle',
			'300x250' => 'Medium Rectangle',
			'336x280'=>'Large rectangle',
			'300x600'=>'Half page',
			'300x1050'=>'Portrait',
			'320x50'=>'Mobile banner',
			'970x90'=>'Large leaderboard',
			'970x250'=>'Billboard'
		);	
		
	?>
		<div id="banner_ad_editor_basic_settings_box" class="postbox">
			<h2 class='hndle'><span>Basic options</span></h2>
			<div class="inside">
				<table class="form-table" style="width:100%;">
					<tr><td><label>Size</label></td>
						<td>
						<?php 
							$sizing = isset( $ad->sizing ) ? $ad->sizing : array();
							$width = isset( $sizing['width'] ) ? $sizing['width'] : '';
							$height = isset( $sizing['height'] ) ? $sizing['height'] : '';
							if( ( $width == "" || $height == "" ) && !isset( $_POST[ 'save' ] ) ){ $width = 300; $height = 250; }
							$size_txt = $width."x".$height;
							$custom_size = false;
							if( !in_array( $size_txt, array_keys( $size_list ) ) ){ $custom_size = true; } else { $custom_size = false; }
						?>
						<select id="size_list" style="width:312px;">
							<?php 
							foreach( $size_list as $size => $size_name )
							{
								$selected = ( $size_txt == $size ) ? ' selected="selected"' : '';
								
								echo '<option value="'.$size.'"'.$selected.'>'.$size_name.' ( '.$size.' )</option>';
							}
							?>
							<option value="custom" <?php echo ($custom_size)?' selected="selected"':'';?>>Custom</option>
						</select>
						<span id="custom_size_box">
						<?php $error_class = isset( $error_msgs['width'] )? " adg_error_field" : ""; ?>
						Width <input type="text" name="width"  id="width" size="4"  value="<?php echo $width;?>" class="<?php echo $error_class;?>" <?php echo (!$custom_size)?' readonly="readonly"':'';?> /> 
						<?php $error_class = isset( $error_msgs['height'] )? " adg_error_field" : ""; ?>
						Height <input type="text" name="height" id="height" size="4" value="<?php echo $height;?>" class="<?php echo $error_class;?>" <?php echo (!$custom_size)?' readonly="readonly"':'';?>/>
						</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
	
	<?php 
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

			//$shortable_columns[ 'width' ] = array( 'width', false );
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

		switch( $column_name ){
			case 'size':
				return $this->get_sizing_text( $item );
			break;		
			
		}
		return $html ;
		
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
			
			$width = ( isset( $sizing['width'] ) ) ? $sizing['width'] : '-';
			$height = ( isset( $sizing['height'] ) ) ? $sizing['height'] : '-';
			

			$html = $width. ' x '.$height;

		}
		return $html;
	}

	/**
	 * Generate HTML/Script to show this ad
	 * @ad object of an ad
	 * @return void
	 * @since 2.0.0
	 * @use with action "adguru_{ad_type}_display"
	 **/
	public function display( $ad ){
	
		$sizing = isset( $ad->sizing ) ? $ad->sizing : array();
		$width = isset( $sizing['width'] ) ? $sizing['width'] : '300';
		$height = isset( $sizing['height'] ) ? $sizing['height'] : '250';
		if( $width == "" || $height == "" ){ $width = 300; $height = 250; }
		
		$content = $ad->print_content( array( 'ret' => true ) );// true for return output
		
		#we could not use <span> tag here as the wrapper of content. 
		#if we call an ad via shortcode, wordpress putting $content html outside of the wrapper <span> tag. 
		#This is really strange.
		$style = 'display:inline-block;width:'.$width.'px;height:'.$height.'px;';
		$output = '<span class="adguru-ad-banner">';
		$output.= '<div style="'.esc_attr( $style ).'">'.$content.'</div>';
		$output.= '</span>';
		echo $output;
		
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
					To show any banner ad in your site, go to <a href="<?php echo add_query_arg( array( "manager_tab" => "links" ), $page_args[ 'base_url' ] ) ?>">Banner to Zone settings</a> page and set ad to appropiate zone. 
					<br /><strong> OR</strong><br />
					Add following php code anywhere in your site front-end pages. Replace the word <strong>'ad_id'</strong> with the <strong>id</strong> of the banner ad you want to show<br />
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
$adguru_banner = new ADGURU_Banner();
