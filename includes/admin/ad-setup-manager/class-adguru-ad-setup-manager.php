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
	public $test = "Hello";
	public $page_type_list_html;
	public $taxonomy_list;
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
	public function get_page_type_list_html(){
		
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

				<ul>
					<li class="usable">Any Taxonomy page</li>
					<li>
						<span class="group-name">Category</span>
						<ul>
							<li class="usable">Any Cagegory</li>
							<li class="usable">Uncategorized</li>
							<li class="usable">Tutorial</li>
						</ul>
					</li>
					<li>
						<span class="group-name">Tag</span>
						<ul>
							<li class="usable">Any Tag</li>
							<li class="usable">Specific Tag</li>
						</ul>
					</li>
					
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
	 * Retrieve all registred post types.
	 * @since 2.1.0
	 */

	public function get_post_type_list(){
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
	public function get_taxonomy_list(){

		if( isset( $this->taxonomy_list ) )
		{
			return $this->taxonomy_list;
		}

		$taxonomies = get_taxonomies(array(), 'objects');
		
		$remTax = array( "nav_menu", "link_category", "post_format", "single", "Single" ); #we remove "single" because it a reserve word for this plugin. This word "Single" we are using to store as a taxonomy for when  post types are stored as terms.	
		
		foreach( $taxonomies as $key => $taxobj )
		{
			if( in_array($key, $remTax ) )#remove taxonomies those are being used only for interlan usages. Those object/post_types does not have show UI.
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

}//end class
endif;