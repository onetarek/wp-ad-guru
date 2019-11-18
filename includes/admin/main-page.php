<?php
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"></div><h2>WP AD GURU</h2>
	<style type="text/css" >.button-primary.adguru-button{ margin-bottom:10px;}</style>
	<br />
	<div class="adg_section">

		<div class="adg_feature_card item-1">
			<div class="feature_name">
				<a href="admin.php?page=adguru_zone">Zone</a>
			</div>
			<div class="link-area">
				<div class="card-icon-col col_2"><a class="card-link" href="admin.php?page=adguru_zone&manager_tab=all" title="All Zones"><span class="card-icon card-icon-all"></span><div class="card-link-text">All</div></a></div>
				<div class="card-icon-col col_2 last-col"><a class="card-link" href="admin.php?page=adguru_zone&manager_tab=edit" title="Add new Zone"><span class="card-icon card-icon-new"><div class="card-link-text">New</div></a></div>
				<div style="clear:left"></div>
			</div>
		</div>
	<?php 
	//ad types menus
	$ad_types = adguru()->ad_types->types;
	$total = count($ad_types);
	$i = 1;
	foreach( $ad_types as $type =>$args)
	{ 
		$i++;
		$class_name = ($i % 4 == 0 )? 'adg_feature_card item-'.($i).' last-col' : 'adg_feature_card item-'.($i);
		$ptype = ADGURU_ADMANAGER_PAGE_SLUG_PREFIX.$type;
		$links_tab_link = "admin.php?page=".$ptype."&manager_tab=links";
		$setup_page_link = "admin.php?page=adguru_setup_ads&ad_type=".$type;
		$setup_page_link_text = ( isset($args['use_zone']) && $args['use_zone'] == true ) ? __("Set to zone", "adguru" ) : __("Set to pages", "adguru");
	?>
		
		<div class="<?php echo $class_name;?>">
			<div class="feature_name">
				<a href="admin.php?page=<?php echo $ptype ?>"><?php echo $args['plural_name'] ?></a>
			</div>
			<div class="link-area">
				<div class="card-icon-col col_3"><a class="card-link" href="admin.php?page=<?php echo $ptype ?>&manager_tab=all"><span class="card-icon card-icon-all"></span><div class="card-link-text"><?php _e("All", "adguru" )?></div></a></div>
				<div class="card-icon-col col_3"><a class="card-link" href="admin.php?page=<?php echo $ptype ?>&manager_tab=edit"><span class="card-icon card-icon-new"></span><div class="card-link-text"><?php _e("New", "adguru" )?></div></a></div>
				<div class="card-icon-col col_3 last-col"><a class="card-link" href="<?php echo $setup_page_link ?>"><span class="card-icon card-icon-link"></span><div class="card-link-text"><?php echo $setup_page_link_text ?></div></a></div>
				<div style="clear:left"></div>
			</div>
		</div>
	
	<?php 
	}

	?>
		<div style="clear:both"></div>
	</div>

	

	<style type="text/css">
		
		.adg_section{
			padding: 10px 0px;
			margin: 0;


		}
		.adg_feature_card{
			width: 23%;
			box-sizing: border-box;
			border: 0px solid #eeeeee;
			border-radius: 4px;
			border-bottom: 5px solid #eeeeee;
			margin-right:2.6666667%;
			margin-bottom: 15px;
			background: #95AFC0;
			color: #ffffff;
			-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
			-moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
			box-shadow: 0 1px 2px rgba(0,0,0,.2);
			float: left;
			text-align: center;
			text-transform: uppercase;
		}
		.adg_feature_card.last-col{
			margin-right: 0%;
		}
		
		.adg_feature_card.item-1{ background: #6AB04C; border-bottom-color:#6AB04C; }
		.adg_feature_card.item-2{ background: #EB4C4B; border-bottom-color:#EB4C4B; }
		.adg_feature_card.item-3{ background: #F0932B; border-bottom-color:#F0932B; }
		.adg_feature_card.item-4{ background: #F9CA23; border-bottom-color:#F9CA23; /*#E3B71C;*/ }
		
		.adg_feature_card.item-1:hover{ background: #7AB439; }
		.adg_feature_card.item-2:hover{ background: #FF7979; }
		.adg_feature_card.item-3:hover{ background: #FEBE76; }
		.adg_feature_card.item-4:hover{ background: #F6E58D; /*#F9CA23;*/ }
		
		.adg_feature_card .feature_name{
			font-size: 25px;
			line-height: 30px;
			text-align: center;
			color: #ffffff;
			margin-top: 40px;
			margin-bottom: 40px;
		}
		.adg_feature_card .feature_name a{
			color: #ffffff;
			text-decoration: none;
		}
		.adg_feature_card .link-area{
			background: #BADC58;
			margin-top: 10px;
			padding: 0px;
			width: 100%;
		}
		.card-icon-col{
			box-sizing: border-box;
			border-right: 1px solid #d2ed82;
			float: left;
		}
		.card-icon-col.col_2{
			width: 50%;
		}
		.card-icon-col.col_3{
			width: 33.3333%;

		}
		.card-icon-col.last-col{
			border-right: 0px;
		}
		.card-link{
			width: 100%;
			padding: 18px 0px 3px 0px;
			text-decoration: none;
			display: block;
			color: #ffffff;
			text-align: center;
			font-size: 10px;
		}
		.card-link-text{
			font-size: 10px;
			line-height: 10px;
			display: block;
			visibility: hidden;
			margin-top: -5px;
		}
		.card-link:hover .card-link-text{
			visibility: visible;
		}
		.adg_feature_card .card-icon:before{
			font-family: "dashicons";
		 	content: "\f103";
		 	color: #ffffff;
			display: inline-block;
			-webkit-font-smoothing: antialiased;
			font-weight: normal;
			vertical-align: top;
			font-size: 30px;
			height: 30px;
			
		}
		

		.adg_feature_card .card-icon.card-icon-all:before{
			content: "\f163";
		}
		.adg_feature_card .card-icon.card-icon-new:before{
			content: "\f502";
		}
		.adg_feature_card .card-icon.card-icon-link:before{
			content: "\f103";
		}
		


		.item-1 .card-link:hover, .item-1 .card-link:hover .card-icon:before{
			color: #6AB04C;
		}

		.item-2 .card-link:hover, .item-2 .card-link:hover .card-icon:before{
			color: #EB4C4B;
		}
		.item-3 .card-link:hover, .item-3 .card-link:hover .card-icon:before{
			color: #F0932B;
		}
		.item-4 .card-link:hover, .item-4 .card-link:hover .card-icon:before{
			color: #E3B71C;
		}

		






		#user_guide_box .ui-accordion-header.ui-state-default{
			background: #95AFC0;
		}
		#user_guide_box .ui-accordion-header.ui-state-default.ui-accordion-header-active.ui-state-active{
			background: #bdc3c7;
		}
		
		#user_guide_box .ui-accordion-header.ui-state-default.ui-state-hover{
			background: #bdc3c7;
		}
		
		#advance_guide_btn{
			background: #95AFC0;
			padding: 10px 20px;
			text-decoration: none;
			color: #fff;
			font-size:30px;
			border:1px solid #eeeeee;
		}
		#advance_guide_btn:hover{
			background: #bdc3c7;
		}
		
	</style>		

	<script type="text/javascript">
		jQuery(document).ready(function($){
			$( ".accordian_box" ).accordion({
			heightStyle: "content", 
			collapsible:true
			});
		
		
		});
	</script>

	<h2 style="border:0; text-align:center; font-size:30px; font-weight:normal; color:#95AFC0">BASIC USER GUIDE</h2>
	<div id="user_guide_box" class="accordian_box">
		<h3><strong>How to steup and show a banner ad?</strong></h3>
		<div>
			<strong>Step 1: Setup a zone</strong><br />
			A zone is a place where you want to show your banners. A zone is the container of banners. A zone can contain multiple banners. 
			If you did not add any zone yet, <a href="admin.php?page=adguru_zone&manager_tab=edit"><strong>add a new zone</strong></a> first. When you create new zone, must check the <strong>Active</strong> checkbox to make the zone active.  
			<br /><br />
			<strong>Step 2: Create a banner</strong><br />
			A banner is the block of ad codes. Go to <a href="admin.php?page=adguru_ad_banner&manager_tab=edit"><strong>add new banner</strong></a> page and create a new banner. 
			<br /><br />
			<strong>Step 3: Set a banner to a zone</strong><br />
			To show your banner you have to specify the banner for a specific zone. 
			<ul style="list-style-type:disc; padding-left:30px;">
				<li>Go to <a href="admin.php?page=adguru_setup_ads&ad_type=banner"><strong>set banner to zone</strong></a> page.</li>
				<li>There is a list of zone names. Select a zone where you want to show your banner.</li>
				
				<li>You will see a box where you can set banners to show in all types of pages and for vistors of any country</li>
				<li>You can set different ads for different type of pages. Click on <strong>Add new ad set and condition</strong> button, you will get new setup area. Select type of page and country</li>

				<li>Click on "Add banner" link</li>
				<li>A popup will come with a list of banners you created. Select a banner and click insert</li>
				<li>Your selected banner name will be shown with a text input field and a delete button. <strong>Put 100 in the % field</strong>.</li>
				<li>Click on <strong>Save</strong> button</li>
			</ul><br />

			<strong>Step 4: Show zone in website</strong><br />
			You have done zone and banner setting. Now you have to set the zone to a place of your website. There are three method to set a zone in a place of website.
			<ul style="list-style-type:disc; padding-left:30px;">
				<li><strong>Method-1 Automatic Insersion :</strong> Go to a zone edit page and see <strong>Automatic Insersion</strong> section. You can choose place and multiple page types to show a zone automatically</li>
				<li><strong>Method-2 Using Widget :</strong> Go to <a href="widgets.php" target="_blank"><strong>widget settings</strong></a> page. Add <strong>"adGuru Zone"</strong> widget in your sidebar and choose a zone from the zone list.</li>
				<li>
				<strong>Method-3 Using PHP function :</strong>
						Add following php code anywhere in your site front-end pages. Replace the word <strong>'zone_id'</strong> with the <strong>id</strong> of the zone you want to show<br />
						<code>
							&lt;?php if(function_exists('adguru_zone')){adguru_zone(zone_id);} ?&gt;
						</code><br />
						<strong><em>Example:</em></strong><br />
						<code>
							&lt;?php if(function_exists('adguru_zone')){adguru_zone(1);} ?&gt;
						</code>			
				
				</li>
				<li><strong>Method-4 Using Shortcode :</strong>
						Use following shortcode in your post content<br />
						<code>
							[adguru zoneid="zone_id"]
						</code><br />
						<strong><em>Example:</em></strong><br />	
						<code>
							[adguru zoneid="1"]
						</code>				
				</li>
				
				
			</ul>
			<strong>Step 5: You are done</strong><br />
		
		</div>
		
		
		<h3>How to setup and show modal popups?</h3>
		<div>
			<strong>Step 1: <a href="admin.php?page=adguru_ad_modal_popup&manager_tab=edit">Add new modal popups</a></strong><br />
			<strong>Step 2: <a href="admin.php?page=adguru_setup_ads&ad_type=modal_popup">Set modal popups to pages</a></strong><br />
		</div>
		<h3>How to setup and show window popups?</h3>
		<div>
			<strong>Step 1: <a href="admin.php?page=adguru_ad_window_popup&manager_tab=edit">Add new window popups</a></strong><br />
			<strong>Step 2: <a href="admin.php?page=adguru_setup_ads&ad_type=window_popup">Set window popups to pages</a></strong><br />
		</div>
		<!-- 
		<h3>Ads and pages link editor guide</h3>
		<div>
			<div style="background:#F0F0F0; width:700px; padding:0px 5px 15px 5px; border:1px solid #DFDFDF;">
				<img src="<?php echo ADGURU_PLUGIN_URL;?>assets/images/ad_zone_links_editor_guide.jpg" /><br />
		
				<strong>1. Country List:</strong> You can set ads for individual country where your visitor come from. Country name <strong>Default</strong> means all country. <br /><br />
				<strong>2. Slide:</strong> You are able to show multiple ads in same place like a courosel/slider. If you have only one slide then the ad will shown normally. If you have one more slide then the ads will be shown in a carousel. Ads will be changed in each 5 seconds.<br /><br />
				<strong>3. New Slide Button:</strong> After clicking on this buton a new slide will be added.  <br /><br />
				<strong>4. An Ad:</strong> This is an ad item you added for this slide. A slide may contains one more ads. But on the front-end only one ad is shown for a slide. If you have one more ads (eg. 4 ads) then ads is roteated based on its percentage you set.<br /><br />
				<strong>5. New Ad Button:</strong> By clicking on this button a popup box is shown with a list of all ads you created. This list shows all ads those are match in size with the ad zone you selected. Select an ad and click <strong>Insert</strong> button.<br /><br />
				<strong>6. Rotator Percentage:</strong> Number of percentage, how many times this ad will be shown in 100 call. A slide can contain one more ads. Total of percentage value of all ads in same slide must be 100. If you have only one ad then keep it 100%.<br /><br />
				<strong>7. Equal Button:</strong> To auto fill rotator percentage value click on this button.<br /><br />
				<strong>8. Remove ad button:</strong> To remove an ad item click on this button.<br />
			</div>	
		</div>
		-->
	</div>

	<div style=" text-align:center; margin-top:30px;"><a id="advance_guide_btn" href="http://wpadguru.com?utm_source=plugin-dashboard-main-page&utm_medium=advance-user-guide-button" target="_blank" title="Detail guide">ADVANCE USER GUIDE</a></div>
</div><!-- #end wrap -->
