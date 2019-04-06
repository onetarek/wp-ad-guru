<?php 
$page = $_REQUEST['page'];
$all_ad_type_args = adguru()->ad_types->types;

$current_ad_type = isset( $_GET['ad_type'] ) ? $_GET['ad_type'] : 'banner';
if(! isset( $all_ad_type_args[ $current_ad_type ] ) )
{
	return ;
}
else
{
	$current_ad_type_args = $all_ad_type_args[ $current_ad_type ];
}

$use_zone = isset( $current_ad_type_args['use_zone'] ) ? $current_ad_type_args['use_zone'] : false;
$zone_id = isset( $_GET['zone_id'] ) ? intval( $_GET['zone_id'] ) : 0 ; 
if( $use_zone ){ $editor_title = sprintf( __("Setup %s to Zone", "adguru" ) , $current_ad_type_args['plural_name'] );  } else {  $editor_title =  sprintf( __("Setup %s to pages", "adguru" ) , $current_ad_type_args['plural_name'] ); }

?>
<div class="wrap" id="ad_setup_manger_wrap">
	<h2><?php _e( "Setup Ads", "adguru" ); ?></h2>

	<?php do_action( "adguru_ad_setup_manager_top" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_top_{$current_ad_type}" , $current_ad_type_args ); ?>

	<h2 class="nav-tab-wrapper">
		<?php 
		foreach( $all_ad_type_args as $key => $args )
		{ 
			$tab_class = ( $key == $current_ad_type  )? 'nav-tab nav-tab-active' : 'nav-tab';
			$tab_link = admin_url( 'admin.php?page=adguru_setup_ads&ad_type='.$key );
		?>
		<a class='<?php echo $tab_class?>' href="<?php echo $tab_link ?>"><?php echo $args['name'] ?></a>
		<?php }?>
	</h2>

		
	<?php do_action( "adguru_ad_setup_manager_after_tabs" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_after_tabs_{$current_ad_type}" , $current_ad_type_args ); ?>

	<div id="editor_container">
		<div id="editor_title"><?php echo $editor_title ?></div>
		<?php 
		#Print Zone select dropdown if current ad type uses zone
		if( $use_zone ){

			$zones = adguru()->manager->get_zones();
			?>
			<div>
				<form action="" method="get">
					<input type="hidden" name="page" value="<?php echo $page ?>" />
					<input type="hidden" name="ad_type" value="<?php echo $current_ad_type ?>" />
					<strong><?php _e( 'Zone', 'adguru' )?> : </strong> 
					<select id="zone_id_list" name="zone_id" onchange="this.form.submit()">
						<option value="0" <?php echo ( $zone_id == 0 ) ? ' selected="selected" ': ""  ?>><?php echo __( "Select A Zone", "adguru" ) ?></option>
						<?php 
						$valid_zone_id = false;
						foreach($zones as $zone)
						{
							$selected = '';
							$class = '';
							if( $zone->active !=1 ){ $class = ' class="inactive" '; }
							if( $zone_id == $zone->ID ){ $selected = ' selected="selected" '; $valid_zone_id = true; }
							echo '<option value="'.$zone->ID.'"'.$class.$selected.'>'.$zone->name.' - '.$zone->width.'x'.$zone->height.'</option>';
						}
						?>
					</select>
				</form>
			</div>
			<?php 

		}//end if( $use_zone )
		?>

		<div class="condition-set">
			<div class="set-header">
				<span class="ec-btn"></span>
				Single &gt; Post
				<div class="cs-box">

					<select class="country-select">
						<option>Select Country</option>
					</select>
					
				</div>
				<div class="ac-box">
					<span class="ac-btn"></span>
				</div>
			</div>
			<div class="set-body">
				<div class="condition-detail">Banners for a single page where post type is post</div>
				<div class="slides-box">
					<div class="slide">
						<div class="slide-header">
							Slide 1
							<span class="equal-btn"></span>
						</div>
						<div class="ads-box">
							<div class="ad">
								<div class="title">Amazon Ad 1 - 300x250</div>
								<div class="control-box">
									<span class="percentage-box"><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
									<span class="edit-btn"></span>
									<span class="close-btn"></span>
								</div>
							</div><!-- /.ad -->
							<div class="ad">
								<div class="title">Amazon Ad 2 - 300x250</div>
								<div class="control-box">
									<span class="percentage-box"><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
									<span class="edit-btn"></span>
									<span class="close-btn"></span>
								</div>
							</div><!-- /.ad -->
							<div class="ad">
								<div class="title">Amazon Ad 3 - 300x250</div>
								<div class="control-box">
									<span class="percentage-box"><input type="number" class="percentage" value="100" max="100" min="0"> %</span>
									<span class="edit-btn"></span>
									<span class="close-btn"></span>
								</div>
								<div class="more">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>
							</div><!-- /.ad -->
						</div><!-- /.ads-box -->
						<div class="add-ad-btn-box"><span class="add-ad-btn">Add new banner</span></div>
					</div><!-- /.slide -->
					
					<div class="slide">
						<div class="slide-header">
							Slide 2
							<span class="equal-btn"></span>
						</div>
						<div class="ads-box">
							<!-- add more ad here -->
						</div><!-- /.ads-box -->
						<div class="add-ad-btn-box"><span class="add-ad-btn">Add new banner</span></div>
					</div><!-- /.slide -->

				</div><!-- /.slides-box -->
				<div class="add-slide-btn-box"><span class="add-slide-btn">Add new slide</span></div>
				<div class="new-slide-btn-box"></div>
			</div><!-- /.set-body -->
			<div class="set-footer">Footer</div>
		</div>

		<div class="condition-set collapsed">
			<div class="set-header">
				<span class="ec-btn"></span>
				Single &gt; Post
				<div class="cs-box">

					<select class="country-select">
						<option>Select Country</option>
					</select>
					
				</div>
				<div class="ac-box">
					<span class="ac-btn"></span>
				</div>
			</div>
			<div class="set-body">Body</div>
			<div class="set-footer">Footer</div>
		</div>





	</div><!-- end #editor_container -->

	<?php do_action( "adguru_ad_setup_manager_bottom_{$current_ad_type}" , $current_ad_type_args ); ?>
	<?php do_action( "adguru_ad_setup_manager_bottom" , $current_ad_type_args ); ?>

</div><!-- end .wrap -->

<style type="text/css">
	#wpcontent{
		background: #ffffff;
	}
	.nav-tab-wrapper .nav-tab-active{
		background: #ffffff;
		border-bottom: 1px solid #ffffff;
	}
	#editor_container{
		width: 100%;
		max-width: 1000px;
		min-height: 800px;
		
		box-sizing: border-box;
	}
	#editor_title{
		width: 100%;
		font-size: 20px;
		color: #000000;
		padding: 10px;
		text-align: center;
		box-sizing: border-box;
	}
	#zone_id_list option.inactive{ color:#cccccc;}

	.condition-set{
		width: 100%
		box-sizing: border-box;
		border: 1px solid #cccccc;
		border-radius: 7px;
		margin-bottom: 10px;
		overflow: hidden;
	}
	.condition-set .set-header{
		height: 41px;
		padding-left: 5px;
		padding-top: 5px;
		font-size: 15px;
		line-height: 30px;
		background: #e5e5e5;
		color: #555555;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		border-bottom: 1px solid #cccccc;
		box-sizing: border-box;
		position: relative;
	}
	.condition-set .set-header .ec-btn{
		width: 30px;
		height: 30px;
		display: inline-block;
		border-right: 1px solid #cccccc;
		cursor: pointer;
	}
	.condition-set .set-header .ec-btn::before{
		font-family: "dashicons";
	 	content: "\f540";
	 	color: #49a0bc;
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 24px;
		line-height: 30px;
	}
	.condition-set .set-header .cs-box{
		width: 210px;
		position: absolute;
		top: 0;
		right: 40px;
		height: 40px;
		padding-top: 5px;
		padding-left: 5px;
		box-sizing: border-box;
	}
	.condition-set .set-header .ac-box{
		width: 40px;
		height: 40px;
		position: absolute;
		top: 0;
		right: 0px;
		border-left: 1px solid #cccccc;
		padding: 0px;
		box-sizing: border-box;
	}

	.condition-set .cs-box .country-select{
		width: 200px;
		height: 30px;
		border: 1px solid #ffffff;
		background: #fff;
		margin: 0;
		padding: 2px;
	}
	.condition-set .ac-box .ac-btn{
		width: 100%;
		display: inline-block;
		text-align: center;
		cursor: pointer;
	}

	.condition-set .ac-box .ac-btn::before{
		font-family: "dashicons";
	 	content: "\f343";
	 	color: #bbbbbb;
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 24px;
		line-height: 40px;
			
	}
	.condition-set.collapsed .ac-box .ac-btn::before{
		content: "\f347";
	}
	.condition-set .ac-box .ac-btn:hover::before{ color: #000000; }

	.condition-set .set-body{
		min-height: 300px;
		padding: 10px;
	}
	.condition-set.collapsed .set-body{
		display: none;
	}
	.condition-set .set-body .condition-detail{
		font-size: 14px;
		line-height: 14px;
		margin-bottom: 10px;
	}

	.condition-set .slides-box{}
	.condition-set .slide{
		padding: 10px;
		padding-top: 3px;
		border:1px solid #eeeeee;
		margin-bottom: 10px;
		position: relative;
	}
	.condition-set .slide .slide-header{
		position: relative;
		font-size: 13px;
		line-height: 21px;
		text-transform: uppercase;
		font-weight: bold;
		margin-bottom: 10px;
	}
	.condition-set .slide .slide-header .equal-btn{
		position: absolute;
		right: 102px;
		top: 0px;
		display: inline-block;
		height: 24px;
		width: 24px;
		text-align: center;
		color: #bbbbbb;
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(90deg);
		cursor: pointer;
	}
	.condition-set .slide .slide-header .equal-btn::before{
		font-family: "dashicons";
	 	content: "\f523";
	 	color: #000000;
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 20px;
		line-height: 24px;
	}
	.condition-set .slide .slide-header .equal-btn:hover{
		color: #000000;
	}

	.condition-set .ads-box{
		position: relative;
	}
	.condition-set .ad{
		position: relative;
		font-size: 13px;
		line-height: 13px;
		font-weight: normal;
		padding: 8px;
		border: 1px solid #dddddd;
		margin-bottom: 4px;
	}
	.condition-set .title{
		font-size: 13px;
		line-height: 14px;
		font-weight: normal;
	}
	.condition-set .ad .more{
		font-size: 13px;
		line-height: 13px;
		color: #cccccc;
		margin-top: 10px;

	}
	.condition-set .ad .control-box{
		position: absolute;
		width: 150px;
		height: 30px;
		top: 0px;
		right: 0px;
		text-align: right;
	}
	.condition-set .ad .control-box .percentage-box{
		width: 67px;
		height: 30px;
		display: inline-block;
		font-size: 13px;
		line-height: 13px;
		vertical-align: top;
		text-align: left;
	}
	.percentage-box .percentage{
		width: 45px;
		font-size: 13px;
		line-height: 13px;
		padding: 3px;
		text-align: center;
		height: 24px;
		margin: 3px;
		margin-left: 0px;

	}
	.condition-set .ad .control-box .edit-btn{
		width: 30px;
		height: 30px;
		display: inline-block;
		text-align: center;
		vertical-align: top;
		cursor: pointer;
		color: #bbbbbb;
	}
	.condition-set .ad .control-box .edit-btn::before{
		font-family: "dashicons";
	 	content: "\f540";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 16px;
		line-height: 30px;
	}
	.condition-set .ad .control-box .edit-btn:hover{
		color: #000000;
	}

	.condition-set .ad .control-box .close-btn{
		width: 30px;
		height: 30px;
		display: inline-block;
		text-align: center;
		vertical-align: top;
		cursor: pointer;
		color: #bbbbbb;
	}
	.condition-set .ad .control-box .close-btn::before{
		font-family: "dashicons";
	 	content: "\f335";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: top;
		font-size: 22px;
		line-height: 30px;
	}
	.condition-set .ad .control-box  .close-btn:hover{
		color: #000000;
	}
	.condition-set .add-ad-btn-box{
		text-align: center;
		margin-top: 10px;
	}
	.condition-set .add-ad-btn{
		font-size: 13px;
		line-height: 20px;
		color: #bbbbbb;
		cursor: pointer;
	}
	.condition-set .add-ad-btn::before{
		font-family: "dashicons";
	 	content: "\f132";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 16px;
		line-height: 20px;
	}
	.condition-set .add-ad-btn:hover{
		color: #000000;
	}


	.condition-set .add-slide-btn-box{
		text-align: center;
		margin-top: 10px;
	}
	.condition-set .add-slide-btn{
		font-size: 16px;
		line-height: 20px;
		color: #bbbbbb;
		cursor: pointer;
	}
	.condition-set .add-slide-btn::before{
		font-family: "dashicons";
	 	content: "\f132";
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		font-weight: normal;
		vertical-align: middle;
		font-size: 18px;
		line-height: 20px;
	}
	.condition-set .add-slide-btn:hover{
		color: #000000;
	}


	.condition-set .set-footer{
		height: 30px;
		padding: 5px;
		border-top: 1px solid #eeeeee;
	}
	.condition-set.collapsed .set-footer{
		display: none;
	}
	
</style>