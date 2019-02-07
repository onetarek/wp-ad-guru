<?php
/**
 * Modal Popup Theme List Table Class
 *
 * @package     WP AD GURU
 * @since       2.0.0
 */

//HELP : http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/

// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if( ! class_exists( 'WP_List_Table' ) )
{
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * ADGURU_Modal_Popup_Theme_List_Table Class
 *
 * Renders the modal popup theme list table
 *
 * @since 2.0.0
 */
class ADGURU_Modal_Popup_Theme_List_Table extends WP_List_Table {
	/**
	 * Number of results to show per page
	 * @var int
	 * @since 2.0.0
	 */
	public $per_page = 30;

	/**
	 *
	 * Total number of themes
	 * @var int
	 * @since 2.0.0
	 */
	public $total_count;

	/**
	 * Base url of current list page
	 * @var string
	 * @since 2.0.0
	 **/
	public $base_url;

	/**
	 * WP Post Type for themes
	 * @var string
	 * @since 2.0.0
	 **/
	 public $post_type;	
	 
	/**
	 * Constructor
	 *
	 * @param array() $args  set theme singular name , theme plural name etc...
	 * @since 2.0.0
	 * @call WP_List_Table::__construct()
	 */
	public function __construct( $args = array()){

		global $status, $page, $adguru_mp_theme_manager;
		$defaults = array(
			'singular' => 'Modal Popup Theme',
			'plural'   => 'Modal Popup Themes',
			'ajax'     => false,
			'base_url' => remove_query_arg("nothing")
			);
		$args = wp_parse_args( $args, $defaults );
		$this->base_url = $args['base_url'];
		$this->post_type = $adguru_mp_theme_manager->post_type;
		parent::__construct( $args );

		$this->get_total_theme_count();
	}

	/**
	 * Render the search input field and required hidden fields
	 *
	 * @access public
	 * @since 2.0.0
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id HTML ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ){

		if( empty( $_REQUEST['s'] ) && !$this->has_items() )
		{
			return;
		}
			
		if( ! empty( $_REQUEST['orderby'] ) )
		{
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
			
		if( ! empty( $_REQUEST['order'] ) )
		{
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
			
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php echo _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?>
		</p>
	<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 2.0.0
	 * @return array $views All the views available
	 */
	public function get_views(){
		
		$current = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count_text = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';

		$views = array(
			'all' => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'status', $this->base_url ), ( $current === 'all' || $current == '' ) ? ' class="current"' : '', __('All', 'adguru') . $total_count_text ),
			#add more view item here
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 * Used filter to get columns from module and addons
	 *
	 * @access public
	 * @since 2.0.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns(){

		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'ID'		=> 'ID',
			'name'       => __( 'Name', 'adguru' ),
			'description'       => __( 'Description', 'adguru' ),
			'type'       => __( 'Type', 'adguru' ),
		);
		
		//get colum names from module and addons
		$columns = apply_filters( "adguru_modal_popup_theme_list_columns",  $columns );
		$columns['usage_detail'] =  __( 'Usage', 'adguru' );
		return $columns;
	}

	/**
	 * Retrieve the sortable columns
	 * Used filter to get shortable columns from module and addons
	 * @access public
	 * @since 2.0.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns(){

		$shortable_columns = array(
			'ID'  	=> array( 'ID', false ),
			'name'  => array( 'name', false ),
		);
		return apply_filters( "adguru_modal_popup_theme_list_shortable_columns",  $shortable_columns );
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name(){

		return 'name';
	}

	/**
	 * Render the column value output if there no outher column specific function found
	 *
	 * @access public
	 * @since 2.0.0
	 *
	 * @param array $item Contains all the data of an item
	 * @param string $column_name The name of the column
	 *
	 * @return string Column value output
	 */
	function column_default( $item, $column_name ){

		//return isset( $item[ $column_name ] )? $item[ $column_name ] : $item['meta'][ $column_name ] ;
		return apply_filters( "adguru_modal_popup_theme_list_column_output", "", $item, $column_name  );
	}

	/**
	 * Render the ID Column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item the data of current row item
	 * @return string output of ID coulumn value
	 */
	function column_ID( $item ){

		return $item->ID;
	}

	/**
	 * Render the name Column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item the data of current row item
	 * @return string output of name coulumn value
	 */
	function column_name( $item ){

		$row_actions  = array();

		$delete_url = add_query_arg( array('action'=>'adguru_mp_delete_theme','delete_id' => $item->ID ) , admin_url( 'admin.php' ) );
		$delete_url = wp_nonce_url( $delete_url, "adguru_mp_delete_theme", "adguru_mp_delete_theme_nonce" );

		$row_actions['edit'] = '<a href="' . add_query_arg( array( 'manager_tab' => 'edit_theme', 'theme_id' => $item->ID ) , $this->base_url ) . '">' . __( 'Edit', 'adguru' ) . '</a>';
		$row_actions['copy'] = '<a href="' . add_query_arg( array( 'manager_tab' => 'edit_theme', 'cp_from_id' => $item->ID ) , $this->base_url ) . '">' . __( 'Copy', 'adguru' ) . '</a>';
		$row_actions['export'] = '<a href="#" onclick="return show_theme_export_modal('.$item->ID.', \''.$item->name.'\')">' . __( 'Export', 'adguru' ) . '</a>';
		
		if( adguru()->user->is_permitted_to('delete_mp_theme') && !( isset( $item->builtin ) && $item->builtin == 1 ) )
		{
			$row_actions['delete'] = '<a onclick="javascript: return confirm(\'Are you sure you want to delete this theme?\')" href="'.esc_url($delete_url).'">' . __( 'Delete', 'adguru' ) . '</a>';
		}

		$row_actions = apply_filters( "adguru_modal_popup_theme_list_row_actions", $row_actions, $item );

		return stripslashes( $item->name ) . $this->row_actions( $row_actions );
	}

	/**
	 * Render the Size Column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item the data of current row item
	 * @return string output of name coulumn value
	 */
	function column_size( $item ){

		return $item->width." x ".$item->height;
	}

	/**
	 * Render the Active Column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item the data of current row item
	 * @return string output of name coulumn value
	 */
	function column_active( $item ){

		return ( isset( $item->active ) && $item->active == 1 ) ? "Yes": "No" ;
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item contains all the data of current row
	 * @return string output html of a checkbox
	 */
	function column_cb( $item ){

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ 'themes',
			/*$2%s*/ $item->ID
		);
	}

	/**
	 * Render the description column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item contains all the data of current row item
	 * @return string Output HTML description column
	 */
	function column_description( $item ){

		return stripslashes( $item->description );
	}

	/**
	 * Render the type column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item contains all the data of current row item
	 * @return string Output HTML description column
	 */
	function column_type( $item ){

		return ( isset( $item->builtin ) && $item->builtin == 1 ) ? 'Builtin' : 'Custom';
	}

	/**
	 * Render the usages column
	 *
	 * @access public
	 * @since 2.0.0
	 * @param array $item contains all the data of current row item
	 * @return string Output HTML links_count column
	 * @todo show a dialog box with the links of links editor pages where this ad is being used.
	 */
	function column_usage_detail( $item ){
		global $wpdb;
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->prefix."postmeta WHERE meta_key=%s AND meta_value=%d", '_theme_id', $item->ID);
		$res = $wpdb->get_var( $sql );
		return $res;
	}

	/**
	 * Render the message when there are no items found
	 *
	 * @since 2.0.0
	 * @access public
	 */
	function no_items(){

		echo sprintf(__( 'No %s found.', 'adguru' ) , $this->_args['singular'] );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @access public
	 * @since 2.0.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions(){

		$actions = array(
			//'delete'     => __( 'Delete', 'adguru' ),
		);

		return apply_filters( "adguru_modal_popup_theme_list_bulk_actions",  $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function process_bulk_action(){

		if( empty( $_REQUEST['_wpnonce'] ) )
		{
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'adguru_bulk_mp_themes' ) )
		{
			return;
		}

		$ids = isset( $_GET['themes'] ) ? $_GET['themes'] : false;

		if( ! is_array( $ids ) ) { $ids = array( $ids ); }

		if( 'delete' === $this->current_action() )
		{
			foreach ( $ids as $id )
			{
					//do delete process here
				
			}
		}
		else
		{
			do_action( "adguru_modal_popup_theme_list_process_bulk_action",  $this->current_action() );
		}

	}

	/**
	 * Retrieve the total theme count
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function get_total_theme_count(){
		
		$wp_post_count = wp_count_posts( $this->post_type );
		$this->total_count  = $wp_post_count->publish;
	}
	
	

	/**
	 * Retrieve all the data for all the zones
	 *
	 * @access public
	 * @since 2.0.0
	 * @return array $theme_data Array of all themes
	 */
	public function themes_data(){

		global $adguru_mp_theme_manager;
		$themes_data = array();

		$per_page = $this->per_page;

		$orderby  = isset( $_GET['orderby'] )  ? $_GET['orderby']                  : 'ID';
		$order    = isset( $_GET['order'] )    ? $_GET['order']                    : 'DESC';
		//$status   = isset( $_GET['status'] )   ? $_GET['status']                   : array( 'publish');
		$status = 'publish'; 
		$meta_key = isset( $_GET['meta_key'] ) ? $_GET['meta_key']                 : null;
		$search   = isset( $_GET['s'] )        ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'post_type'		 => $this->post_type,
			'posts_per_page' => $per_page,
			'paged'          => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'orderby'        => $orderby,
			'order'          => $order,
			'post_status'    => $status,
			'meta_key'       => $meta_key,
			's'              => $search
		);

		if( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby && 'ID' != $orderby )
		{

			$args['orderby']  = 'meta_value';
			$args['meta_key'] = $orderby;
		}

		$themes = $adguru_mp_theme_manager->get_themes( $args );
		if( $themes )
		{
			$themes_data = $themes;
		}
		return $themes_data;
	}
	

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 2.0.0
	 * @uses ADGURU_Modal_Popup_Theme_List_Table::get_columns()
	 * @uses ADGURU_Modal_Popup_Theme_List_Table::get_sortable_columns()
	 * @uses ADGURU_Modal_Popup_Theme_List_Table::process_bulk_action()
	 * @uses ADGURU_Modal_Popup_Theme_List_Table::discount_codes_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items(){

		$per_page = $this->per_page;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$this->items = $this->themes_data();
		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch( $status ) {
			case 'any':
				$total_items = $this->total_count;
				break;
				//set more case here
			default:
				$total_items = $this->total_count;
				break;
		}


		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}
}
