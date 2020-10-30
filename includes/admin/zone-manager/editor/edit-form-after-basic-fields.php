<div id="zone_editor_design_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Design', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_zone_design_form( $zone ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="zone_editor_visibility_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Visibility', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_zone_visibility_form( $zone ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="zone_editor_inserter_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Automatic Insertion', 'adguru')?></span></h2>
	<div class="inside">
		<?php adguru_show_zone_inserter_form( $zone ); ?>
	</div><!-- ./inside -->
</div><!-- /.postbox -->

<div id="zone_editor_manual_inserter_box" class="postbox">
	<h2 class='hndle'><span><?php _e('Manual Insertion', 'adguru')?></span></h2>
	<div class="inside">
			<?php $zid = (isset($zone->ID) && $zone->ID != 0 ) ? $zone->ID : 'zone_id';?>
			<div style="list-style-type:disc; padding-left:30px;font-size:14px">
				<h3>Method 1 - Using Widget :</h3> 
				Go to <a href="widgets.php" target="_blank"><strong>widget settings</strong></a> page. Add <strong>"adGuru Zone"</strong> widget in your sidebar and choose a zone from the zone list.</li>
				
				<h3>Method 2 - Using PHP function :</h3>
				Add following php code anywhere in your site front-end pages. 
				<?php if($zid == 'zone_id'){?>Replace the word <strong>'zone_id'</strong> with the <strong>id</strong> of the zone you want to show<?php }?>
				<br />
				<code>
					&lt;?php if(function_exists('adguru_zone')){adguru_zone(<?php echo $zid?>);} ?&gt;
				</code>		
				
				
				<h3>Method 3 - Using Shortcode :</h3>
				Use following shortcode in your post content
				<?php if($zid == 'zone_id'){?>Replace the word <strong>'zone_id'</strong> with the <strong>id</strong> of the zone you want to show<?php }?>
				<br />
				<code>
					[adguru zoneid="<?php echo $zid?>"]
				</code>				
			</div>
	</div><!-- ./inside -->
</div><!-- /.postbox -->