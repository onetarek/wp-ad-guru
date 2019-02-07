/*
 * Migrator JS
 * Handle process of migration of old data.
 * @author : oneTarek
 * @since : 2.0.0
 */

 var ADGURU_MIGRATOR = null; 
 
 (function($){

 	ADGURU_MIGRATOR = {
 		
 		complete : 0,
 		running : 0,
 		init : function(){

 			this.add_events();
 		},
 		add_events : function(){

 			$("#adguru_migrate_ok_button").click(function(){
 				if( confirm("Are sure you want to migrate all old data ?") )
 				{
 					ADGURU_MIGRATOR.disable_all_buttons();
 					$("#adguru_migrate_do_not_box").hide();
 					ADGURU_MIGRATOR.start_migration();
 				}
 			});

 			$("#adguru_migrate_do_not_button").click(function(){
 				if( confirm("Are sure you don't want to migrate old data ?") )
 				{
 					ADGURU_MIGRATOR.disable_all_buttons();
 					$("#adguru_migrate_box").hide();
 					ADGURU_MIGRATOR.do_not_migrate();
 				}
 				
 			});
 		},

 		disable_all_buttons : function(){

 			$("#adguru_migrate_ok_button").attr("disabled", "disabled");
 			$("#adguru_migrate_do_not_button").attr("disabled", "disabled");
 		},

 		start_migration : function(){

 			ADGURU_MIGRATOR.run_migration_ajax();
 		},

 		do_not_migrate : function(){

 			ADGURU_MIGRATOR.run_do_not_ajax();
 		},

 		run_migration_ajax: function(){
 			
 			$("#migration_ajax_loader").show();
 			var qData={
				"action" : "adguru_do_migration"
			};

 			$.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "POST",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function( response ){				
					
					 	if( response.status == "success")
					 	{

							ADGURU_MIGRATOR.complete = response.complete;
							$("#migration_log").html( response.status_log );
							if( response.complete == 0 )
							{
								//run the process again
								ADGURU_MIGRATOR.run_migration_ajax();				
							}
							else
							{
								ADGURU_MIGRATOR.show_migration_complete_message();
							}
						}
						else
						{
							ADGURU_MIGRATOR.show_migration_fail_message( response );
						}
	
					},
				error: function(xhr,errorThrown){
					ADGURU_MIGRATOR.show_migration_ajax_fail_message( xhr,errorThrown );
				}
				   
			  });//end $.ajax
 		},

 		show_migration_complete_message : function(){

 			$("#migration_message").html("Migration Complete.");
 			$("#migration_ajax_loader").hide();
 		},

 		show_migration_ajax_fail_message : function( xhr,errorThrown ){

 			$("#adguru_migrate_ok_button").removeAttr("disabled");
 			$("#migration_message").html("Something went wrong , please try again");
 			$("#migration_ajax_loader").hide();
 		},

 		show_migration_fail_message : function( response ){

 			$("#adguru_migrate_ok_button").removeAttr("disabled");
 			$("#migration_message").html("Migration failed :"+response.msg );
 			$("#migration_ajax_loader").hide();
 		},

 		run_do_not_ajax: function(){
 			
 			$("#migration_do_not_ajax_loader").show();
 			var qData={
				"action" : "adguru_do_not_migrate"
			};

 			$.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "POST",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function( response ){				
						$("#migration_do_not_ajax_loader").hide();
					 	if( response.status == "success")
					 	{

							$("#migration_do_not_message").html("Done");
						}
						else
						{
							$("#migration_do_not_message").html("Failed : "+ response.msg + "Try again" );
							$("#adguru_migrate_do_not_button").removeAttr("disabled");
						}
	
					},
				error: function(xhr,errorThrown){
					$("#migration_do_not_ajax_loader").hide();
					$("#migration_do_not_message").html("Some thing went wrong, please try again");
					$("#adguru_migrate_do_not_button").removeAttr("disabled");
				}
				   
			  });//end $.ajax
 		},
 		


 	}//end ADGURU_MIGRATOR

 	$(document).ready(function(){
 		ADGURU_MIGRATOR.init();
 	});
 })(jQuery);