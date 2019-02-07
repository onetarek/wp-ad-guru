/*
Ad Guru Links Editor
@author: oneTarek
*/

var adgLinksEditor;

(function($){
	adgLinksEditor = {
		
		selectedAdSet: null, //stores jquery object for element
		
		init: function(){
		
		},
	
	
		setupEvents: function(){
			
			//Accordian if current ad type need multiple slider
			if( adgLinksEditorVars.multiple_slides == true )
			{
				$( ".ad_slide_set_box" ).accordion({
						heightStyle: "content", 
						collapsible:true
						}).sortable({
						axis: "y",
						handle: "h3",
						stop: function( event, ui ) {
							// IE doesn't register the blur when sorting
							// so trigger focusout handlers to remove .ui-state-focus
							ui.item.children( "h3" ).triggerHandler( "focusout" );
							}
						});
			}
			//----------------------------------DRAGGING AND SHORTING------------------------------------------------	
			$(".ad_set").sortable();
			
			//-----------------------------DIALOG---------------------------------------
			$("#ad_list_modal").dialog({
				height: 355,
				width: 500,
				modal: true,
				autoOpen: false,
				buttons: {
					"Insert": function() {
					
						if($('.ads_list_item.selected').size()==0){ alert("Please select an item"); return false;}
						var selected = $('.ads_list_item.selected').first();
						var ad_type = $(selected).attr('ad_type');
						var ad_type_name = $(selected).attr('ad_type_name');
						var ad_name = $(selected).attr('ad_name');
						var ad_id = $(selected).attr('ad_id');
						var html="";
							html+='<div class="ad_item" ad_id="'+ad_id+'">';
								html+='<div class="ad_item_left" title="'+ad_type_name+'">'+ad_name+'</div>';
								html+='<div class="ad_item_middle"><input type="text" size="3" class="percentage" /> %</div>';
								html+='<div class="ad_item_right"><span class="remove_ad_btn" title="Remove this ad"></span></div>';
								html+='<div class="clear"></div>';
							html+='</div>';
						
						var already_added = $( adgLinksEditor.selectedAdSet ).children('[ad_id='+ad_id+']').size();
						if( already_added == 0 ){ $( adgLinksEditor.selectedAdSet ).append( html ); }
						$( this ).dialog( "close" );
					
					},
					Cancel: function(){ $( this ).dialog( "close" ); }
				}		
			
			});//END DIALOG
		//--------------------------- end dialog --------------------------
			
			$(".add_slide_btn").live('click', function(){
				var ad_slide_set_box = $(this).parent().parent().children(".ad_slide_set_box").first();
				var n = $(ad_slide_set_box).children(".ad_slide").size();
				n++;
				$(ad_slide_set_box).append( adgLinksEditor.getNewSlideHtml(n) );
				$(ad_slide_set_box).accordion('refresh');
				$(".ad_set").sortable();
			
			});	
				
			$('.ads_list_item').click(function(){
				$('.ads_list_item').removeClass('selected');
				$(this).addClass('selected');
			});
		
			$(".remove_ad_btn").live("click", function(){
				$(this).parent().parent().remove();	
			
			});	
		
			//TABING.......................
			$("#ctab_set li").live("click", function(){
				$("#ctab_set li").removeClass('selected');
				$(this).addClass('selected');
				var tabid = $(this).attr("tabid");
				$('.ctab_box').hide();
				$("#ctab_box_"+tabid).show();
			});
		
			//END TABBING..................
			
			$(".save_ad_zone_links_btn").click(function(){
				adgLinksEditor.updateAdZoneLink();									
			});//$(".save_ad_zone_links").click
			
			$("#add_new_country_btn").click(function(){
				var lastTab = $("#ctab_set li").last();
				var n = parseInt($(lastTab).attr("tabid"));
				n++;
				$("#ctab_set").append('<li tabid="'+n+'" code="--">New Country</li>');
				
				var html="";
					html+='<div class="ctab_box ad_zone_link_set" tabid="'+n+'" id="ctab_box_'+n+'">';
						html+='<div class="ctab_box_head">';
							if( adGuruAdminVars.options.geoLocationEnabled == false )
							{
								html+='<div style="color:red">Geo location feature is not enabled</div>';
							}
							html+='<div class="ctab_box_title">Select A Country</div>';
							html+='<div class="ctab_control_box" tabid="'+n+'"><span class="remove_country_btn" title="Remove this country">&nbsp;</span></div>';
							html+='<div class="clear"></div>';
						html+='</div>';				
					
						html+=get_country_list_html();
						html+='<div>';
							html+='<div class="ad_slide_set_box">';
							html+=adgLinksEditor.getNewSlideHtml(1);
							html+='</div>';
							if( adgLinksEditorVars.multiple_slides == true )
							{
							html+='<div style="margin-top:10px; margin-bottom:10px;"><span class="add_slide_btn">Add New Slide</span></div>';
							}
						html+='</div>';
					html+='</div>';
				$("#ad_zone_link_set_wrap").append(html);
				if( adgLinksEditorVars.multiple_slides == true )
				{
					$(".ad_slide_set_box").accordion({heightStyle: "content", collapsible:true});// accordian refresh is not working, so declare again
				}
				$(".ad_set").sortable();
				var newTab = $("#ctab_set li").last();
				$(newTab).trigger('click');
			
			});	
			
			$(".remove_country_btn").live("click",function(){
				var tabid = parseInt($(this).parent().attr('tabid'));
				$("#ctab_box_"+tabid+"").remove();
				$.each($("#ctab_set li"), function(){
					var t = parseInt($(this).attr('tabid'));
					if( t == tabid ){ $(this).remove();}
					$("#ctab_set li").first().trigger('click');
				});
			});
			
			$(".equal_button").live("click",function(){
				var per_list = new Array();									 
				var ad_set = $(this).parent().parent().parent().children(".ad_set").first();
				var ad_items = $(ad_set).children(".ad_item");
				var n = $(ad_items).size();
				
				if( n!=0 )
				{
					$.each(ad_items, function(){
						var ad_item_middle = $(this).children(".ad_item_middle").first();
						var per = $(ad_item_middle).children('.percentage').first();
						per_list.push(per);								  
					
					});
					var total_item=per_list.length;
					var value = 0;
					var x = parseInt(100/total_item);
					var y = 100%total_item;
					for( i=0; i<total_item; i++ )
					{
						if(i==0){ value = x+y; }else{ value=x; }
						$(per_list[i]).val(value);
						$(per_list[i]).removeClass('error');
					
					}
	
	
				}//end if(n!=0)
				
				
			});
	
			//*******************************START SEARCH TECHNIQUE **************************************
			//search technique help: http://www.marceble.com/2010/02/simple-jquery-table-row-filter/
			//Declare the custom selector 'containsIgnoreCase'.
			  $.expr[':'].containsIgnoreCase = function(n,i,m){
				  return $(n).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
			  };
			  
			  $("#search_ad_list").keyup(function(){
				//hide all the rows
				  $("#ads_list").find("div").hide();
				//split the current value of searchInput
				  var data = this.value.split(" ");
				//create a jquery object of the rows
				  var jo = $("#ads_list").find(".ad_name");
				  //Recusively filter the jquery object to get results.
				  $.each(data, function(i, v){
					  jo = jo.filter("*:containsIgnoreCase('"+v+"')");
				  });
				//show the rows that match.
				  jo.parent().show();
			 //Removes the placeholder text  
		   
			  }).focus(function(){
				  this.value = "";
				  $(this).unbind('focus');
			  })
			  //*******************************END SEARCH TECHNIQUE **************************************
	
		},//end setupEvents


		getNewSlideHtml: function(n){
			var html='';
				if( adgLinksEditorVars.multiple_slides == true )
				{
				html+='<h3>Slide '+n+'</h3>';
				}
				html+='<div class="ad_slide">';
					html+='<div class="slide_header">';
						html+='<div class="sl_hd_left">Ad Name</div>';
						html+='<div class="sl_hd_middle"><span class="equal_button" title="Set all percentage fields equal"></span></div>';
						html+='<div class="sl_hd_right">&nbsp;</div>';
						html+='<div class="clear"></div>';
					html+='</div>';
					html+='<div class="ad_set">';
					html+='</div>';
					html+='<div class="slide_footer">';
						html+='<div class="sl_ft_left"><span class="add_ad_btn" onclick="adgLinksEditor.showAdListModal(this)">Add new ad</span></div>';
						html+='<div class="sl_ft_middle">&nbsp;</div>';
						html+='<div class="sl_ft_right">&nbsp;</div>';
						html+='<div class="clear"></div>';
					html+='</div>';
				html+='</div>';
		
			
			return html;
		},

		showAdListModal: function(p){
		 adgLinksEditor.selectedAdSet = $(p).parent().parent().parent().children('.ad_set').first();
		 $( "#ad_list_modal" ).dialog( "open" );
		
		},	
	
		checkDuplicateCountry: function(p){
		 var val = $(p).val();
		 var text = $(p).find("*:selected").first().html();
		 var tabbox = $(p).parent();
		 var tabid = parseInt($(tabbox).attr('tabid'));
			$(tabbox).find("div > .ctab_box_title").first().html(text);
			$("#ctab_set [tabid='"+tabid+"']").first().html(text).attr('code',val);
					
		 return true;
		},

		updateAdZoneLink: function(){
		
			var qData={
					"action"	:	"adguru_save_ad_zone_links", 
					"ad_type"	: 	adgLinksEditorVars.ad_type, 
					"zone_id"	: 	adgLinksEditorVars.zone_id, 
					"post_id"	: 	adgLinksEditorVars.post_id, 
					"page_type"	: 	adgLinksEditorVars.page_type, 
					"taxonomy"	: 	adgLinksEditorVars.taxonomy, 
					"term"		: 	adgLinksEditorVars.term, 
					"ad_zone_link_set":[]
					};
			$.each($('.ad_zone_link_set') , function(){
				var ad_zone_link_set_item={}
				
				var country = $(this).children('.country_name').first();
				var country_code = $(country).val();
				var ad_zone_link_set_item = {'country_code':country_code, "ad_slide_set":[]};
				
				var ad_slide_set_box = $(this).children('div').children('.ad_slide_set_box').first();
				
				$.each($(ad_slide_set_box).find('.ad_slide'), function(){
					var ad_slide_set_item = [];
					
					var ad_set = $(this).children('.ad_set').first();
					$.each($(ad_set).children('.ad_item'), function(){
						var ad_id = $(this).attr('ad_id');
						var percentage = parseInt($(this).find('div > .percentage').first().val());
						var ad_item = {"ad_id":ad_id, "percentage":percentage};
						ad_slide_set_item.push(ad_item);
					});
					ad_zone_link_set_item.ad_slide_set.push(ad_slide_set_item);
				});
				qData.ad_zone_link_set.push(ad_zone_link_set_item);
				
			});
			
			if(this.checkAdZoneLinkInputError(qData)){return;}
			$(this).attr('disabled', 'disabled');
			ADGURU_ADMIN_HELPER.add_loading_overlay( "#links_editor_table" );		
			
			 $.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "POST",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function(json_result){				
					
					var response=$.parseJSON( json_result )
						if(response.status == 'success')
						{
						$(".ad_zone_link_msg").html("Saved");
						$(".ad_zone_link_msg").addClass('success');					
						}
						else
						{
						alert(response.message);
						$(".ad_zone_link_msg").html(response.message);
						$(".ad_zone_link_msg").addClass('fail');
						}
	
						$(".save_ad_zone_links_btn").removeAttr("disabled");
						ADGURU_ADMIN_HELPER.remove_loading_overlay( "#links_editor_table" );
					},
				error: function(xhr,errorThrown){}
				   
			  });//end $.ajax	
			
		},//end updateAdZoneLink
	
		checkAdZoneLinkInputError: function(qData){
			
			$("#ctab_set li").removeClass('error');
			var ctabs = $("#ctab_set li");
			var tot_country = $("#ctab_set li").size();
			var code = ""; var code2 = "";
	
			for(i=1; i<tot_country; i++)//i=1 to ommit first 'default' tab
			{
				code = "";
				code = $(ctabs[i]).attr('code');	
				if(code == "--")
				{
					$(ctabs[i]).addClass('error');
					$(ctabs[i]).trigger('click');
					alert("Error: Select a Country Name");
					return true;
				}
			}
	
			for( i=1; i<tot_country; i++)//i=1 to ommit first 'default' tab
			{
				code = "";
				code = $(ctabs[i]).attr('code');
	
				for( j=i+1; j<tot_country; j++)
				{
					var code2 = $(ctabs[j]).attr('code');
					if( code == code2 )
					{
						$(ctabs[i]).addClass('error');
						$(ctabs[j]).addClass('error');
						$(ctabs[j]).trigger('click');
						alert("Error: You Selected Duplicate Country Name");
						return true;
					}
				}
			}
			
			$("#links_editor_body .percentage").removeClass('error');
			var ad_zone_link_set = qData.ad_zone_link_set;
			var tot_country = ad_zone_link_set.length;
			var i=0; var j=0; var k=0; var ad_slide_set; var ad_slide_set_item; var ad_item; var country_code="";var got_error=false;
			var per=0;
			
			for( i=0; i<tot_country; i++ )
			{
				country_code=ad_zone_link_set[i].country_code;			
				ad_slide_set=ad_zone_link_set[i].ad_slide_set;
				
				
				for( j=0; j<ad_slide_set.length; j++ )
				{
					ad_slide_set_item=ad_slide_set[j];
					per=0;
					if( ad_slide_set_item.length )
					{
						for( k=0; k<ad_slide_set_item.length; k++ )
						{
							per=per+ad_slide_set_item[k].percentage;	
						}
					if( per != 100 ){got_error=true; break; }
					}
					//alert(per);
					
					
				}
				if( got_error == true ){ break; }
			}
			
			if( got_error == true )
			{
				
				var ctab_set = $("#ctab_set");
				var tab = $(ctab_set).children('li:eq('+i+')');
				$(tab).trigger('click');
				var ctab_box = $(".ctab_box:eq("+i+")");
				var ad_slide_set_box = $(ctab_box).find('div > .ad_slide_set_box').first();
				var ad_slide = $(ad_slide_set_box).children(".ad_slide:eq("+j+")");
					if( $(ad_slide).is(":visible") == false )
					{
						$(ad_slide).prev().trigger('click');
	
					}
					$(ad_slide).find("div > div > .percentage").addClass('error');
					alert("Error: Total percentage must be 100. \n Change value of all percentage fields \n or click on the = button");				
	
			return true;	
			}
			return false;	
			
		}//end checkAdZoneLinkInputError()
	
	};//end adgLinksEditor object
	
	$(document).ready(function ($) {
		adgLinksEditor.init();
		adgLinksEditor.setupEvents();
		//alert("updated: 11");
	});
	
	
})( jQuery );	
	