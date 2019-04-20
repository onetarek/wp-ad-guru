;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		selected_ads_box_to_insert_ad : null,
		
		init : function(){
			this.add_events();
			this.create_condition_sets();
			this.make_slides_sortable();

		},

		add_events : function(){
			
			
			$("#add_condition_set_btn").click(function(){
				ADGURU_ASM.create_blank_condition_set();
			});

			$('#condition_sets_box').on('click', '.ac-btn', function(){
				var condition_set = $(this).closest('.condition-set');
				condition_set.toggleClass('collapsed');
			});

			$('#condition_sets_box').on('click', '.open-close-arrow-box', function(){
				var target = $(this).closest('.page-type-list-box');
				target.toggleClass('collapsed');
			});

			$('#condition_sets_box').on('click', '.ec-btn', function(){
				var target = $(this).closest('.condition-set').find('.page-type-list-box').first();
				target.toggleClass('collapsed');
			});

			$('#condition_sets_box').on('click', '.ad-remove-btn', function(){
				$(this).closest('.ad').remove();
			});

			$('#condition_sets_box').on('click', '.add-slide-btn', function(){
				var condition_set = $(this).closest('.condition-set');
				var slides_box = $(condition_set).find('.slides-box').first();
				slides_box.append( ADGURU_ASM.get_slide_html( {'links':[]} ) );
				ADGURU_ASM.refresh_slides(condition_set);
			});

			$('#condition_sets_box').on('click', '.slide-delete-btn', function(){
				var condition_set = $(this).closest('.condition-set');
				var slides_box = $(this).closest('.slides-box');
				if( slides_box.find('.slide').length > 1 )
				{
					$(this).closest('.slide').remove();
					ADGURU_ASM.refresh_slides(condition_set);
				}
			});

			$('#condition_sets_box').on('click', '.equal-btn', function(){
				var per_list = [];									 
				var slide = $(this).closest('.slide');
				var ad_items = $(slide).find(".ad");
				var n = $(ad_items).size();
				
				if( n!=0 )
				{
					$.each(ad_items, function(){
						var per = $(this).find('.percentage').first();
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

			$('#condition_sets_box').on('click', '.add-ad-btn', function(){
				var slide = $(this).closest('.slide');
				ADGURU_ASM.selected_ads_box_to_insert_ad = slide.find('.ads-box').first();
		 		$( "#ad_list_modal" ).dialog( "open" );

			});

			$('.ads_list_item').click(function(){
				$('.ads_list_item').removeClass('selected');
				$(this).addClass('selected');
			});
			
			//-----------------------------DIALOG---------------------------------------
			$("#ad_list_modal").dialog({
				height: 355,
				width: 700,
				modal: true,
				autoOpen: false,
				buttons: {
					"Insert": function() {
					
						if( $('.ads_list_item.selected').size() == 0 )
						{ 
							alert("Please select an item"); 
							return false;
						}
						var selected = $('.ads_list_item.selected').first();
						var ad_type = $(selected).attr('ad_type');
						var ad_type_name = $(selected).attr('ad_type_name');
						var ad_name = $(selected).attr('ad_name');
						var ad_id = $(selected).attr('ad_id');
						var data = {};
						data['ad_data'] = $(selected).data('ad_data');
						data['percentage'] = 0;
						var html = ADGURU_ASM.get_ad_html( data );
						var already_added = $( ADGURU_ASM.selected_ads_box_to_insert_ad ).find('[adid='+ad_id+']').size();
						if( already_added == 0 ){ $( ADGURU_ASM.selected_ads_box_to_insert_ad ).append( html ); }
						$( this ).dialog( "close" );
					
					},
					Cancel: function(){ $( this ).dialog( "close" ); }
				}		
			
			});//END DIALOG
			//--------------------------- end dialog --------------------------
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

		},

		get_ad_html : function( data ){
			var ad_data = data['ad_data'];
			var ad_id = ad_data['ID'];
			var ad_type = ad_data['type'];
			var title = ad_data['name'];
			var percentage = data['percentage'];

			var tmpl = ADGURU_ASM_DATA.ad_html_template;
			var html = tmpl.replace(/{{AD_ID}}/g, ad_id );
			var html = html.replace(/{{AD_TITLE}}/g, title );
			var html = html.replace(/{{AD_TYPE}}/g, ad_type );
			var html = html.replace(/{{AD_TYPE_NAME}}/g, ad_type );
			var html = html.replace(/{{PERCENTAGE}}/g, percentage );
			var html = html.replace(/{{MORE_HTML}}/g, "" );
			return html;
		},

		get_slide_html : function( slide_data ){
			
			var tmpl = ADGURU_ASM_DATA.slide_html_template;
			var html = tmpl.replace('{{SLIDE_NUMBER}}', 0 );
			var ads_html = "";
			var links = slide_data['links'];
			if( links.length )
			{
				var i;
				for( i in links )
				{
					var link = links[i];
					var ad_id = link['ad_id'];
					if( typeof ADGURU_ASM_DATA.ads_data[ad_id] != 'undefined' )
					{
						var data = {};
						data['ad_data'] = ADGURU_ASM_DATA.ads_data[ad_id];
						data['percentage'] = link['percentage'];
						ads_html = ads_html + this.get_ad_html( data );
					}
					
				}
			}
			
			var html = html.replace('{{ADS_HTML}}', ads_html );
			return html;
		},

		refresh_slides : function( set_obj ){
			var num = 0;
			$(set_obj).find('.slide').each(function(){
				num++;
				$(this).find('.slide_number').first().html(num);
			});
			
			
		},

		create_condition_set : function( data ){
			this.last_set_number++;
			var html_id = 'condition_set_'+this.last_set_number;
			var tmpl = ADGURU_ASM_DATA.condition_set_html_template;
			var html = tmpl.replace('{{SET_HTML_ID}}', html_id );

			var html = html.replace('{{PAGE_TYPE_DISPLAY_HTML}}', data['page_type_display_html'] );
			var html = html.replace('{{CONDITION_DETAIL}}', data['condition_detail'] );
			var slides_html = "";
			var slide_data = {
				'links' : []
			};
			if( data['ad_zone_link_set'].length )
			{
				var slides = [];
				var i;
				for( i in data['ad_zone_link_set'] )
				{
					var link = data['ad_zone_link_set'][i];
					var slide_number = link.slide;
					if( typeof slides[slide_number] == 'undefined' )
					{
						slides[slide_number] = {
							'links' : []
						};
					}
					slides[slide_number]['links'].push( link );
				}
				
				for( i in slides )
				{
					slides_html = slides_html + this.get_slide_html( slides[i] );
				}


			}
			else
			{
				slides_html = slides_html + this.get_slide_html( slide_data );
			}
			var html = html.replace('{{SLIDES_HTML}}', slides_html );

			$("#condition_sets_box").append( html );
			$("#"+html_id).find('.country-select').val( data['country_code'] );
			this.refresh_slides( "#"+html_id );
			this.make_slides_sortable();
		},

		create_blank_condition_set : function(){
			var data = {
				'page_type_display_html' : '<span style="color:red">Select page type</span>',
				'condition_detail' : '',
				'ad_zone_link_set' : [],
			};

			this.create_condition_set(data);
		},

		create_condition_sets: function(){ //console.log(ADGURU_ASM_DATA.ad_zone_link_sets);
			if( typeof ADGURU_ASM_DATA.ad_zone_link_sets != 'undefined' && ADGURU_ASM_DATA.ad_zone_link_sets.length != 0 )
			{
				var i;
				for( i in ADGURU_ASM_DATA.ad_zone_link_sets )
				{
					var country_code = "--";
					var links = ADGURU_ASM_DATA.ad_zone_link_sets[i];
					if( links.length )
					{
						var link = links[0];
						country_code = link.country_code;
					}
					var set_data = {
						'page_type_display_html' : '<span style="color:red">Select page type</span>',
						'condition_detail' : '',
						'country_code' : country_code,
						'ad_zone_link_set' : ADGURU_ASM_DATA.ad_zone_link_sets[i]
					}
					this.create_condition_set( set_data );
				}
			}
		},

		make_slides_sortable : function(){
			$(".slides-box").sortable({
      			placeholder: "slide-drop-placeholder",
      			stop: function( event, ui ) {
      				var condition_set = $(ui.item).closest('.condition-set');
      				ADGURU_ASM.refresh_slides(condition_set);


      			}
    		});
		}

	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");