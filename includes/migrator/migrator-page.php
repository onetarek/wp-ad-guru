<?php
// Don't allow direct access
if( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"></div><h2>AD GURU Migrator</h2>

	<style type="text/css" >
		.button-primary.adguru-button{ margin-bottom:10px;}
		#adguru_migrate_box{ 
			margin-top: 20px;
			margin-bottom:20px;
			padding:10px;
			border: 1px solid green;

		}

		#adguru_migrate_do_not_box{ 
			margin-top: 20px;
			margin-bottom:20px;
			padding:10px;
			border: 1px solid red;
		}
		.adg_action_button{
			border:2px solid green;
			color:#ffffff;
			font-size: 20px;
			text-align: center;
			padding: 10px;
			box-shadow: none;
			border-radius: 3px;
			background: green;
			cursor: pointer;

		}
		.adg_action_button:hover:enabled{
			opacity: 0.7;
		}
		
		#adguru_migrate_ok_button{
			
		}

		#adguru_migrate_ok_button:active:enabled{
			background: #00dd00;
		}

		#adguru_migrate_do_not_button{
			background: red;
			border-color: red;
		}
		#adguru_migrate_do_not_button:active:enabled{
			background: #dd0000;
		}

		.button_box{
			float: left;
			width: 180px;
			margin-right: 3px;

		}
		.loader_box{
			margin-top:3px;
			display:none;
			margin-bottom: 5px;
		}
		.message_box{
			float: left;
			font-size: 30px;
			padding:10px;
			padding-left:8px;
			padding-right: 0px;
		}
		#migration_log{
			font-size: 14px;

		}



	</style>
	<?php

		$migration_running = get_option("adguru_migration_running", 0 );
		$migraito_button_text = ( $migration_running ) ? "Continue Migration" : "Start Migration";
		$log_text = "";
		if( $migration_running )
		{
			$status = get_option("adguru_migration_status", array() );
			if( ! isset( $status["log"] ) ){ $status["log"] = array(); }
			$log_text = implode("<br>", $status["log"] );
		}

	?>
	<br />
	<div style="font-size:16px">
	Old data tables have been detected. You were using free lite or premium version of <b>WP Ad Guru</b>.<br>
	You can migrate old ads data to this new plugin.<br>
	</div>

	<?php if( $migration_running == 0 ){?>
	<h3>What will be done with migration ?</h3>
	<ul>
		<li>All zones will be copied</li>
		<li>All ads will be copied</li>
		<li>All ad zone links will be copied</li>
		<li>Widgets will be updated with new zone id</li>
	</ul>
	<div style="color:red">
	<h3 style="color:red">What will you loose with migration ?</h3>
	All zones and ads will be created newly and their ID will be changed, <br>
	If you alreay added some zone/ad via SHORTCODE or PHP function , you will loose the output for those items.<br>
	you have to update manually those SHORTCODE and functions with new zone/ad id.
	</div>
	<?php } else { ?>

	<h3 style="color:green">Migration process was started before and was not completed. Start the process again</h3>

	<?php }?>

	<div id="adguru_migrate_box">
		<div class="button_box">
			<input type="button" id="adguru_migrate_ok_button" class="adg_action_button" value="<?php echo $migraito_button_text ?>" />
			<div class="loader_box" id="migration_ajax_loader">
				<img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading.gif" alt="loading.." />
			</div>
		</div>
		
		<div class="message_box" id="migration_message"></div>
		<div style="clear:both;"></div>
		<div id="migration_log"><?php echo $log_text ?></div>

	</div>
<?php if( $migration_running == 0 ){?>
	<div id="adguru_migrate_do_not_box">
		<div class="button_box">
			<input type="button" id="adguru_migrate_do_not_button" class="adg_action_button" value="Do not Migrate" />
			<div class="loader_box" id="migration_do_not_ajax_loader">
				<img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/loading.gif" alt="loading.." />
			</div>
		</div>
		<div class="message_box" id="migration_do_not_message"></div>
		<div style="clear:both;"></div>
	</div>
<?php } ?>
</div><!-- end #wrap -->
<script type="text/javascript" src="<?php echo ADGURU_PLUGIN_URL ?>includes/migrator/migrator.js"></script>

