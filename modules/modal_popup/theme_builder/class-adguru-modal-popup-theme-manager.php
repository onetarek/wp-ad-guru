<?php
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'ADGURU_Modal_Popup_Theme_Manager' ) ) :

class ADGURU_Modal_Popup_Theme_Manager{
	
	public $post_type;
	public $post_type_args;
	public $default_theme_id = 0;
	public $default_theme_data = '{"ID":0,"name":"Default","description":"Semi-white thin border, grey overlay, white-black circular close button, no shadow.","builtin":0,"meta":{"design":{"container_border_enable":"1","container_border_width":5,"container_border_style":"solid","container_border_color":"#eeeeee","container_border_radius":0,"container_padding":0,"container_background_enable":"1","container_background_color":"#ffffff","container_background_opacity":100,"container_box_shadow_enable":"0","container_box_shadow_h_offset":0,"container_box_shadow_v_offset":0,"container_box_shadow_blur_radius":20,"container_box_shadow_spread":0,"container_box_shadow_color":"#000000","container_box_shadow_opacity":100,"container_box_shadow_inset":"no","container_custom_css_class":"","close_height":30,"close_width":30,"close_padding":0,"close_border_enable":"0","close_border_width":5,"close_border_style":"solid","close_border_color":"#dddddd","close_border_radius":0,"close_button_type":"image","close_text":"X","close_color":"#ffffff","close_font_size":18,"close_line_height":18,"close_font_family":"Arial","close_font_weight":"normal","close_font_style":"normal","close_text_shadow_enable":"1","close_text_shadow_h_offset":1,"close_text_shadow_v_offset":1,"close_text_shadow_blur_radius":1,"close_text_shadow_color":"#444444","close_image_source_type":"builtin","close_image_name":"core_close_default_png","close_custom_image_url":"","close_background_enable":"0","close_background_color":"#ffffff","close_background_opacity":100,"close_box_shadow_enable":"0","close_box_shadow_h_offset":1,"close_box_shadow_v_offset":1,"close_box_shadow_blur_radius":3,"close_box_shadow_spread":0,"close_box_shadow_color":"#000000","close_box_shadow_opacity":25,"close_box_shadow_inset":"no","close_location":"top_right","close_top":-20,"close_left":0,"close_right":-20,"close_bottom":0,"close_custom_css_class":"","overlay_background_color":"#808080","overlay_background_opacity":75}}}';
	
	public function __construct(){

		$this->post_type = ADGURU_POST_TYPE_PREFIX.'mp_theme';
		add_action( 'init', array( $this, 'register' ) );

		add_filter('adguru_ad_manager_tabs_modal_popup', array($this, 'add_ad_manager_tabs') );

		
	    add_filter( "adguru_modal_popuup_theme_editor_init", array( $this, "theme_editor_init" ) );

		add_filter( "adguru_modal_popup_theme_prepare_to_save", array( $this, "prepare_theme_to_save" ), 10, 2 );
		add_action( "adguru_modal_popup_theme_editor_left_after_basic" , array( $this, "theme_editor_after_basic" ) , 10, 2 ); //params  $ad, $error_msgs

		add_action( 'wp_ajax_adguru_mp_get_theme_data', array( $this, 'ajax_get_theme_data') );
		add_action( 'wp_ajax_adguru_mp_import_theme_data', array( $this, 'ajax_import_theme_data') );
		
		add_action( "adguru_activation", array( $this, "create_builtin_themes" ) );

		add_action( 'admin_action_adguru_mp_delete_theme', array($this, 'handle_delete_theme') );

		$this->default_theme_id = get_option('adguru_mp_default_theme_id', 0);
	}
		
	public function register(){
		
		$labels = array(
			'name'               => __('Modal Popup Theme', 'adguru'),
			'singular_name'      => __('Modal Popup Theme', 'adguru'),
			'menu_name'          => __('Modal Popup Themes', 'adguru'),
			'add_new'            => __( 'Add New Theme', 'adguru' ),
			'add_new_item'       => __( 'Add New Theme', 'adguru' ),
			'new_item'           => __( 'New Theme', 'adguru' ),
			'edit_item'          => __( 'Edit Theme', 'adguru' ),
			'view_item'          => __( 'View Theme', 'adguru' ),
			'all_items'          => __( 'All Themes', 'adguru' ),
			'search_items'       => __( 'Search Theme', 'adguru' ),
			'parent_item_colon'  => __( 'Parent Theme', 'adguru' ),
			'not_found'          => __( 'No theme found', 'adguru' ),
			'not_found_in_trash' => __( 'No theme found in Trash', 'adguru' ),
		);		

		$this->post_type_args = array(
			'labels'             => $labels,
			'description'        => __( 'Design template for a modal popup', 'adguru' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);
		

		adguru()->post_types->register( $this->post_type, $this->post_type_args );

	}

	public function add_ad_manager_tabs( $tabs ){
		$page = $_REQUEST['page'];
		$tabs['themes'] = array( 
			'slug'	=> 'themes', 
			'text'	=> __('Themes', 'adguru' ), 
			'link'	=> 'admin.php?page='.$page.'&manager_tab=themes',
			'file' 	=> ADGURU_PLUGIN_DIR."modules/modal_popup/theme_builder/theme-list.php",
			'callback' => '' 
		);
		$tabs['edit_theme'] = array( 
			'slug'	=> 'edit_theme', 
			'text'	=> __('Add new theme', 'adguru' ), 
			'link'	=> 'admin.php?page='.$page.'&manager_tab=edit_theme',
			'file' 	=> ADGURU_PLUGIN_DIR."modules/modal_popup/theme_builder/theme-edit.php",
			'callback' => '' 
		);
		return $tabs;
	}

	/**
	 * Save theme post data 
	 * Insert new or update existing
	 * @param array() $theme 
	 * @return int $id of theme
	 * @since 2.0.0
	 */ 
	public function save_theme( $theme ){
	
		$theme_id = isset( $theme->ID ) ? intval( $theme->ID ) : 0;
		
		$postarr = array(
			"ID" => $theme_id, 
			"post_type" => $this->post_type,
			"post_title" => $theme->name,
			"post_excerpt" => $theme->description,
			"post_status" => 'publish',
			"meta_input" => $theme->meta
		);
		$post_id = wp_insert_post( $postarr );
		return ( is_wp_error( $post_id) ) ? 0 : $post_id;		
		
	}//END FUNC

	/**
	 * Get Themes
	 *
	 * Retrieves an array of all themes.
	 *
	 * @since 2.0.0
	 * @param array $args Query arguments
	 * @return mixed array if themes exist, false otherwise
	 */
	public function get_themes( $args = array(), $id_as_key = false ){

		$defaults = array(
			'post_type'		 => $this->post_type,
			'posts_per_page' => -1,
			'paged'          => null,
			'post_status'    => array( 'publish' )
		);
	
		$args = wp_parse_args( $args, $defaults );
	//$args['post_type'] = 'post';
		$posts = get_posts( $args );
		if ( $posts )
		{
			$themes = array();
			
			foreach( $posts as $p )
			{
			
				$theme = new ADGURU_Modal_Popup_Theme();
				$theme->ID = $p->ID;
				$theme->name = $p->post_title;
				$theme->description = $p->post_excerpt;
				$metas = get_post_custom( $p->ID );
				//Take only first value . It simillar with useing single = true with get post meta
				foreach( $metas as $key => $val )
				{
					$theme->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
				}
				
				if( $id_as_key ){ $themes[ $p->ID ] = $theme; } else{ $themes[] = $theme; }			
					
			}
			
			return $themes; 		
		}//end if
	
	
		return false;
	}//END FUNC

	/**
	 * Get a single Theme
	 *
	 * Retrieves an array of a single theme.
	 *
	 * @since 2.0.0
	 * @param int $theme_id
	 * @return mixed array if theme exist, false otherwise
	 */
	public function get_theme( $theme_id ){

		if( ! $theme_id ) return false;
	
		$p = get_post( $theme_id );
		if ( $p && $p->post_type == $this->post_type )
		{		
			
			$theme = new ADGURU_Modal_Popup_Theme();
			$theme->ID = $p->ID;
			$theme->name = $p->post_title;
			$theme->description = $p->post_excerpt;
			$metas = get_post_custom( $p->ID );
			//Take only first value . It simillar with useing single = true with get post meta
			foreach( $metas as $key => $val )
			{
				$theme->meta[ $key ] = maybe_unserialize( $val[ 0 ] );		
			}			
			
			return $theme; 		
		}//end if
	
	
		return false;
	}//END FUNC

	/** 
	 * Delete a theme
	 * @param int|object theme. theme id or theme object
	 * @return none
	 **/
	public function delete_theme( $theme ){

		if( is_a( $theme, 'ADGURU_Modal_Popup_Theme') )
		{
			$theme_id = $theme->ID;
		}
		else
		{
			$theme_id = intval( $theme );
			if( !$theme_id )
			{ 
				return false; 
			}
			
			$theme = $this->get_theme( $theme_id );
			if( !$theme )
			{ 
				return false; 
			}
		}
		//Do not delete builtin themes
		if( isset( $theme->builtin ) && $theme->builtin == 1 )
		{ 
			return false; 
		}
		//$this->delete_links_for_an_theme( $theme );
		
		$p = wp_delete_post( $theme_id, true );//force delete.
		if( !$p )
		{ 
			return false;
		}
		
		do_action("adguru_mp_delete_theme", $theme );
		return $theme;
	}

	public function theme_editor_init(){

		include_once( dirname(__FILE__)."/../editor/form-design.php");
	}
	public function prepare_theme_to_save( $theme, $theme_from_db ){

		include( dirname(__FILE__)."/theme-prepare-to-save.php");
		return $theme;
	}

	public function theme_editor_after_basic( $theme, $error_msgs  ){

		include( dirname(__FILE__)."/theme-editor-after-basic.php");
	}

	public function ajax_get_theme_data(){

		$response = array(
			'status' => 'success',
			'message' => ''
		);
		if( isset( $_GET['theme_id'] ) && intval( $_GET['theme_id'] ) != 0 )
		{
			$theme_id = intval( $_GET['theme_id'] );
			$theme = $this->get_theme( $theme_id );
			if( $theme )
			{
				$data = array(
					'ID' => $theme->ID,
					'name' => $theme->name,
					'description' => $theme->description,
					'builtin' => isset( $theme->builtin ) ? $theme->builtin : 0,
					'meta' => array(),

				);
				$data['meta']['design'] = isset( $theme->design ) ? $theme->design : array();
				
				$data = apply_filters('adguru_modal_popup_theme_data', $data, $theme );
				
				//add builtin close icon name and url within close_image_name field as an array
				if( isset( $_GET['for_editor'] ) && $_GET['for_editor'] == 1 )
				{
					$close_image_source_type = isset( $data['meta']['design']['close_image_source_type']) ? $data['meta']['design']['close_image_source_type'] : 'builtin';
					$close_image_name = isset( $data['meta']['design']['close_image_name']) ? $data['meta']['design']['close_image_name'] : '';
					if( $close_image_source_type == 'builtin' && $close_image_name != '' )
					{
						$icon = ADGURU_Helper::get_close_icon( $close_image_name );
						if( is_array( $icon ) )
						{
							$data['meta']['design']['close_image_name'] = array('value' => $close_image_name, 'img_url' => $icon['url'] );
						}
					}
				}
				

				$response['status'] = 'success';
				$response['theme_data'] = json_encode($data);

			}
			else
			{
				$response['status'] = 'fail';
				$response['message'] = 'Theme not found';
			}

		}
		else
		{
			$response['status'] = 'fail';
			$response['message'] = 'Theme id not given';
		}
		wp_send_json( $response );
	}//end function

	public function ajax_import_theme_data(){

		$response = array(
			'status' => 'success',
			'message' => ''
		);
		if( isset( $_POST['theme_data'] ) )
		{
			$theme_data = stripcslashes($_POST['theme_data']);
			$theme_data_arr = $this->parse_theme_data( $theme_data );
			
			if( $theme_data_arr )
			{
				//Create A Blank theme Object
				$theme = new ADGURU_Modal_Popup_Theme();

				$theme->ID = 0;
				$theme->name = $theme_data_arr['name'];
				$theme->description = $theme_data_arr['description'];
				$theme->design = $theme_data_arr['meta']['design'];

				$theme = apply_filters('adguru_modal_popup_theme_prepare_to_import', $theme, $theme_data_arr );
				if( $theme instanceof ADGURU_Modal_Popup_Theme )
				{
					$theme_id = $this->save_theme( $theme );
					if( $theme_id )
					{
						$response['status'] = 'success';
				    	$response['message'] = 'Theme import success';
				    	$response['theme_id'] = $theme_id;
					}
					else
					{
						$response['status'] = 'fail';
				    	$response['message'] = 'Something went wrong in saving theme';
					}
				}
				else
				{
					$response['status'] = 'fail';
				    $response['message'] = 'Theme object is invalid';
				}
			}
			else
			{
				$response['status'] = 'fail';
				$response['message'] = 'Theme data is not valid';
			}

		}
		else
		{
			$response['status'] = 'fail';
			$response['message'] = 'Theme data not given';
		}
		wp_send_json( $response );
	}//end function

	/**
	 * Get theme data array from given JSON data
	 * @param string $theme_data
	 * @return array or false
	 * @since 2.0.0
	 */
	public function parse_theme_data( $theme_data ){

		$theme_data = trim( $theme_data );
		if( $theme_data == '' ){ return false; }
		$theme_data_arr = json_decode( $theme_data, true );//This function can return NULL on invalid json data.
		if( !$theme_data_arr ){ return false; }
		if( 
			isset( $theme_data_arr['meta']['design'] ) && 
			is_array( $theme_data_arr['meta']['design'] ) &&
			isset( $theme_data_arr['name'] ) && 
			trim( $theme_data_arr['name'] ) != '' &&  
			isset( $theme_data_arr['description'] )
		)
		{
			return $theme_data_arr;
		}
		return false;
	}

	/**
	 *	Get builtin theme id list from option table
	 * @return array
	 */
	public function get_builtin_theme_id_list(){

		$id_list = get_option('adguru_mp_builtin_theme_id_list', array() );
		return $id_list; 
	}

	/**
	 *	Create builtin themes on plugin activation
	 */
	public function create_builtin_themes(){

		$builtin_theme_id_list = $this->get_builtin_theme_id_list();
		//NOTE : same 'default' theme data is stored in $this->default_theme_data at the top of this page. So, in case if we need to update this default data we should update in both places.
		$builtin_themes = array(
			'default' 	=> '{"ID":0,"name":"Default","description":"Semi-white thin border, grey overlay, white-black circular close button, no shadow.","builtin":0,"meta":{"design":{"container_border_enable":"1","container_border_width":5,"container_border_style":"solid","container_border_color":"#eeeeee","container_border_radius":0,"container_padding":0,"container_background_enable":"1","container_background_color":"#ffffff","container_background_opacity":100,"container_box_shadow_enable":"0","container_box_shadow_h_offset":0,"container_box_shadow_v_offset":0,"container_box_shadow_blur_radius":20,"container_box_shadow_spread":0,"container_box_shadow_color":"#000000","container_box_shadow_opacity":100,"container_box_shadow_inset":"no","container_custom_css_class":"","close_height":30,"close_width":30,"close_padding":0,"close_border_enable":"0","close_border_width":5,"close_border_style":"solid","close_border_color":"#dddddd","close_border_radius":0,"close_button_type":"image","close_text":"X","close_color":"#ffffff","close_font_size":18,"close_line_height":18,"close_font_family":"Arial","close_font_weight":"normal","close_font_style":"normal","close_text_shadow_enable":"1","close_text_shadow_h_offset":1,"close_text_shadow_v_offset":1,"close_text_shadow_blur_radius":1,"close_text_shadow_color":"#444444","close_image_source_type":"builtin","close_image_name":"core_close_default_png","close_custom_image_url":"","close_background_enable":"0","close_background_color":"#ffffff","close_background_opacity":100,"close_box_shadow_enable":"0","close_box_shadow_h_offset":1,"close_box_shadow_v_offset":1,"close_box_shadow_blur_radius":3,"close_box_shadow_spread":0,"close_box_shadow_color":"#000000","close_box_shadow_opacity":25,"close_box_shadow_inset":"no","close_location":"top_right","close_top":-20,"close_left":0,"close_right":-20,"close_bottom":0,"close_custom_css_class":"","overlay_background_color":"#808080","overlay_background_opacity":75}}}',
			'fancy_box' => '{"ID":0,"name":"Fancy box","description":"White border, grey overlay, white-black circular close button, black shadow","builtin":0,"meta":{"design":{"container_border_enable":"1","container_border_width":10,"container_border_style":"solid","container_border_color":"#ffffff","container_border_radius":0,"container_padding":0,"container_background_enable":"1","container_background_color":"#f5f5f5","container_background_opacity":100,"container_box_shadow_enable":"1","container_box_shadow_h_offset":0,"container_box_shadow_v_offset":0,"container_box_shadow_blur_radius":20,"container_box_shadow_spread":0,"container_box_shadow_color":"#000000","container_box_shadow_opacity":70,"container_box_shadow_inset":"no","container_custom_css_class":"","close_height":30,"close_width":30,"close_padding":0,"close_border_enable":"0","close_border_width":5,"close_border_style":"solid","close_border_color":"#dddddd","close_border_radius":0,"close_button_type":"image","close_text":"X","close_color":"#ffffff","close_font_size":18,"close_line_height":18,"close_font_family":"Arial","close_font_weight":"normal","close_font_style":"normal","close_text_shadow_enable":"1","close_text_shadow_h_offset":1,"close_text_shadow_v_offset":1,"close_text_shadow_blur_radius":1,"close_text_shadow_color":"#444444","close_image_source_type":"builtin","close_image_name":"core_close_default_png","close_custom_image_url":"","close_background_enable":"0","close_background_color":"#ffffff","close_background_opacity":100,"close_box_shadow_enable":"0","close_box_shadow_h_offset":1,"close_box_shadow_v_offset":1,"close_box_shadow_blur_radius":3,"close_box_shadow_spread":0,"close_box_shadow_color":"#000000","close_box_shadow_opacity":25,"close_box_shadow_inset":"no","close_location":"top_right","close_top":-24,"close_left":0,"close_right":-24,"close_bottom":0,"close_custom_css_class":"","overlay_background_color":"#777777","overlay_background_opacity":70}}}',
			'dark_box' 	=> '{"ID":0,"name":"Dark box","description":"Black border with 5px round, dark overlay , grey and thin close button,  no shadow.","builtin":0,"meta":{"design":{"container_border_enable":"1","container_border_width":10,"container_border_style":"solid","container_border_color":"#000000","container_border_radius":5,"container_padding":0,"container_background_enable":"1","container_background_color":"#ffffff","container_background_opacity":100,"container_box_shadow_enable":"0","container_box_shadow_h_offset":0,"container_box_shadow_v_offset":0,"container_box_shadow_blur_radius":20,"container_box_shadow_spread":0,"container_box_shadow_color":"#000000","container_box_shadow_opacity":70,"container_box_shadow_inset":"no","container_custom_css_class":"","close_height":20,"close_width":20,"close_padding":0,"close_border_enable":"0","close_border_width":5,"close_border_style":"solid","close_border_color":"#dddddd","close_border_radius":0,"close_button_type":"image","close_text":"X","close_color":"#ffffff","close_font_size":18,"close_line_height":18,"close_font_family":"Arial","close_font_weight":"normal","close_font_style":"normal","close_text_shadow_enable":"1","close_text_shadow_h_offset":1,"close_text_shadow_v_offset":1,"close_text_shadow_blur_radius":1,"close_text_shadow_color":"#444444","close_image_source_type":"builtin","close_image_name":"core_close_cross_thin_grey_png","close_custom_image_url":"","close_background_enable":"0","close_background_color":"#ffffff","close_background_opacity":100,"close_box_shadow_enable":"0","close_box_shadow_h_offset":1,"close_box_shadow_v_offset":1,"close_box_shadow_blur_radius":3,"close_box_shadow_spread":0,"close_box_shadow_color":"#000000","close_box_shadow_opacity":25,"close_box_shadow_inset":"no","close_location":"top_right","close_top":-42,"close_left":0,"close_right":-42,"close_bottom":0,"close_custom_css_class":"","overlay_background_color":"#000000","overlay_background_opacity":90}}}',
			'light_box' => '{"ID":0,"name":"Light box","description":"Black border with 3px round, grey overlay, white-black circular close button, black shadow, white background, 8px padding","builtin":0,"meta":{"design":{"container_border_enable":"1","container_border_width":8,"container_border_style":"solid","container_border_color":"#000000","container_border_radius":3,"container_padding":8,"container_background_enable":"1","container_background_color":"#ffffff","container_background_opacity":100,"container_box_shadow_enable":"1","container_box_shadow_h_offset":0,"container_box_shadow_v_offset":0,"container_box_shadow_blur_radius":30,"container_box_shadow_spread":0,"container_box_shadow_color":"#020202","container_box_shadow_opacity":100,"container_box_shadow_inset":"no","container_custom_css_class":"","close_height":30,"close_width":30,"close_padding":0,"close_border_enable":"1","close_border_width":0,"close_border_style":"solid","close_border_color":"#dddddd","close_border_radius":15,"close_button_type":"image","close_text":"X","close_color":"#ffffff","close_font_size":18,"close_line_height":18,"close_font_family":"Arial","close_font_weight":"normal","close_font_style":"normal","close_text_shadow_enable":"1","close_text_shadow_h_offset":1,"close_text_shadow_v_offset":1,"close_text_shadow_blur_radius":1,"close_text_shadow_color":"#444444","close_image_source_type":"builtin","close_image_name":"core_close_default_png","close_custom_image_url":"","close_background_enable":"0","close_background_color":"#ffffff","close_background_opacity":100,"close_box_shadow_enable":"1","close_box_shadow_h_offset":0,"close_box_shadow_v_offset":0,"close_box_shadow_blur_radius":15,"close_box_shadow_spread":1,"close_box_shadow_color":"#020202","close_box_shadow_opacity":75,"close_box_shadow_inset":"no","close_location":"top_right","close_top":-25,"close_left":0,"close_right":-25,"close_bottom":0,"close_custom_css_class":"","overlay_background_color":"#000000","overlay_background_opacity":60}}}'
		);
		foreach( $builtin_themes as $theme_slug => $theme_data )
		{
			$theme_data_arr = $this->parse_theme_data( $theme_data );
			if( !$theme_data_arr ){ continue; }
			//Create A Blank theme Object
			$theme = new ADGURU_Modal_Popup_Theme();

			$theme->ID = isset( $builtin_theme_id_list[ $theme_slug ] ) ? $builtin_theme_id_list[ $theme_slug ] : 0;
			$theme->name = $theme_data_arr['name'];
			$theme->description = $theme_data_arr['description'];
			$theme->design = $theme_data_arr['meta']['design'];
			$theme->builtin = 1;
			$theme_id = $this->save_theme( $theme );
			if( $theme_id )
			{
				$builtin_theme_id_list[ $theme_slug ] = $theme_id;
				if( $theme_slug == 'default' )
				{
					update_option('adguru_mp_default_theme_id', $theme_id );
				}
			}

		}
		//update builtin_theme_id_list
		update_option('adguru_mp_builtin_theme_id_list', $builtin_theme_id_list );
	}

	public function handle_delete_theme(){

		if( wp_get_referer() == false )
		{
			 wp_safe_redirect( get_home_url() );
			 exit;
		}
		
		check_admin_referer( 'adguru_mp_delete_theme', 'adguru_mp_delete_theme_nonce' );
		$delete_id 	=  isset( $_GET['delete_id'] ) ? intval( $_GET['delete_id'] ) : 0;
		$deleted_theme = false;
		$refurl = wp_get_referer();
		$refurl = remove_query_arg( 'msg', $refurl );
		
		if( $delete_id )
		{
			if( adguru()->user->is_permitted_to('delete_mp_theme') )
			{
				$deleted_theme = $this->delete_theme( $delete_id );
				if( $deleted_theme )
			    {
			    	$refurl = add_query_arg( array('msg'=>'deleted' ) , $refurl );
			    }
		    }
		} 

		wp_safe_redirect( $refurl );
		exit;
	}

	
}//end class
global $adguru_mp_theme_manager;
$adguru_mp_theme_manager = new ADGURU_Modal_Popup_Theme_Manager();
endif;