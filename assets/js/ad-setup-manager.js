;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		selected_ads_box_to_insert_ad : null,
		condition_set_query_data_stringify : {},
		condition_set_initial_query_data_stringify : {},
		
		init : function(){
			this.add_events();
			this.create_condition_sets();
			this.make_slides_sortable();
		},

		add_events : function(){
			
			
			$("#exapnd_all_btn").click(function(){
				$('.condition-set').removeClass('collapsed');
			});

			$("#collapse_all_btn").click(function(){
				$('.condition-set').addClass('collapsed');
			});

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

			$('#condition_sets_box').on('click', '.usable', function(){
				ADGURU_ASM.clear_duplicate_indicator();
				var condition_set = $(this).closest('.condition-set');
				var data = $(this).data('page_type_info_data');
				ADGURU_ASM.set_page_type_display_html_and_query_data( condition_set, data );
				var target = $(this).closest('.page-type-list-box');
				target.toggleClass('collapsed');
			});

			$('#condition_sets_box').on('change', '.term-name', function(){
				ADGURU_ASM.clear_duplicate_indicator();
				var obj = $(this);
				ADGURU_ASM.process_term_name_change( obj );
			});

			$('#condition_sets_box').on('change', '.country-select', function(){
				ADGURU_ASM.clear_duplicate_indicator();
				var condition_set = $(this).closest('.condition-set');
				var value = $(this).val();
				ADGURU_ASM.set_condition_set_single_query_data(condition_set, 'country_code', value );
				
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

			$('#condition_sets_box').on('click', '.save-btn', function(){
				var obj = $(this);
				ADGURU_ASM.process_save_btn_click(obj);
			});

			$('#condition_sets_box').on('click', '.delete-set-btn', function(){
				var obj = $(this);
				ADGURU_ASM.process_delete_set_btn_click(obj);
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

			var html = html.replace('{{PAGE_TYPE_DISPLAY_HTML}}', '<span style="color:red">Select page type</span>' );
			var html = html.replace('{{CONDITION_DETAIL}}', '' );
			var slides_html = "";
			var slide_data = {
				'links' : []
			};
			if( data['links'].length )
			{
				var slides = [];
				var i;
				for( i in data['links'] )
				{
					var link = data['links'][i];
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
			var condition_set = $("#"+html_id);
			
			condition_set.find('.country-select').val( data['country_code'] );
			var new_entry = false;
			//make old query data
			var info = data['page_type_info_data'];
			if( typeof data.new_entry !== 'undefined' && data.new_entry == 1 )//for new blank condition set.
			{
				var initial_query_data = { 'new_entry' : 1 };
				new_entry = true;
			}
			else
			{
				var initial_query_data = {
					'ad_type' : ADGURU_ASM_DATA.current_ad_type,
					'zone_id' : ADGURU_ASM_DATA.current_zone_id,
					'post_id' : ADGURU_ASM_DATA.current_post_id,
					'country_code' : info['country_code'],
					'page_type' : info['page_type'],
					'taxonomy' : info['taxonomy'],
					'term' : info['term'],

				};
			}


			if( new_entry == 0 )//do not collapsed new blank set
			{
				condition_set.addClass('collapsed');
			}

			ADGURU_ASM.set_condition_set_initial_query_data( condition_set, initial_query_data );
			//end make old query data
		
			ADGURU_ASM.set_page_type_display_html_and_query_data( condition_set, data['page_type_info_data'] );

			this.refresh_slides( "#"+html_id );
			this.make_slides_sortable();
		},

		create_blank_condition_set : function(){
			var data = {
				'page_type_info_data' : {},
				'country_code' : '--',
				'links' : [],
				'new_entry' : 1,
			};

			this.create_condition_set(data);
		},

		create_blank_default_condition_set : function(){
			var data = {
				'page_type_info_data' : {
					ad_type : ADGURU_ASM_DATA.current_ad_type,
					zone_id : ADGURU_ASM_DATA.current_zone_id,
					post_id : ADGURU_ASM_DATA.current_post_id,
					page_type: "--",
					taxonomy: "--",
					term: "--",
					country_code: "--",
				},
				'country_code' : '--',
				'links' : [],
				'new_entry' : 1,
			};

			this.create_condition_set(data);
		},

		create_condition_sets: function(){
			if( typeof ADGURU_ASM_DATA.ad_zone_link_sets != 'undefined' && ADGURU_ASM_DATA.ad_zone_link_sets.length != 0 )
			{
				//IF NO DEFAULT SET, then create new blank default set
				var first = ADGURU_ASM_DATA.ad_zone_link_sets[0].page_type_info_data;
				if( first.page_type != '--' || first.taxonomy != '--' || first.term != '--' || first.country_code != '--' ) 
				{
					ADGURU_ASM.create_blank_default_condition_set();
				}

				var i;
				for( i in ADGURU_ASM_DATA.ad_zone_link_sets )
				{
					var set = ADGURU_ASM_DATA.ad_zone_link_sets[i];
					
					var set_data = {
						'page_type_info_data' : set.page_type_info_data,
						'country_code' : set.page_type_info_data.country_code,
						'links' : set.links
					}
					this.create_condition_set( set_data );
				}
				$("#condition_set_1").removeClass('collapsed');
			}
			else
			{
				ADGURU_ASM.create_blank_default_condition_set();
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
		},

		set_page_type_display_html_and_query_data : function( condition_set, data ){
			
			if( typeof data.page_type === 'undefined' ){ return; }
			$(condition_set).removeAttr("need_term_input");
			var country_code = $(condition_set).find('.country-select').first().val();
			var title_html = "";
			
			var query_data = {
				'ad_type' : ADGURU_ASM_DATA.current_ad_type,
				'zone_id' : ADGURU_ASM_DATA.current_zone_id,
				'post_id' : ADGURU_ASM_DATA.current_post_id,
				'country_code' : country_code,
				'page_type' : data['page_type'],
				'taxonomy' : data['taxonomy'],
				'term' : data['term'],

			};

			switch( data.page_type )
			{
				case '--': //default
				{
					title_html = "Default";
					break;
				}//end case 'default'
				case 'home': //home page
				{
					title_html = "Home Page";
					break;
				}//end case 'home'
				case 'singular': //single_post
				{
					
					if( data.taxonomy == 'single' )
					{
						if( data.term == '--')
						{
							title_html = "Any type single post";
						}
						else
						{
							title_html = "Single "+data.post_type_name;
						}
						
					}
					else //single_post_specific_term
					{
						if( data.hierarchical == 1 ) //category type taxonomy
						{
							title_html = "Single post having <b>"+data.term_name+"</b> "+data.taxonomy_name;
						}
						else //tag type taxonomy
						{
							if( typeof data.term === 'undefined' || data.term == '--' )
							{
								title_html = 'Single post having <input type="text" placeholder="Term name/slug" class="term-name" taxonomy="'+data.taxonomy+'"> '+data.taxonomy_name;
								$(condition_set).attr("need_term_input", 1 );
							}
							else
							{
								title_html = 'Single post having <input type="text" placeholder="Term name/slug" class="term-name" taxonomy="'+data.taxonomy+'" value="'+data.term+'"> '+data.taxonomy_name;
								$(condition_set).removeAttr('need_term_input');
							}

						}
					}

					
					break;
				}//end case 'single_post'
				case 'taxonomy': //taxonomy_archive
				{
					
					if( data.taxonomy == "--")
					{
						title_html = "Any Taxonomy Archive";
					}
					else
					{
						if( data.hierarchical == 1 )//category type archive page
						{
							if(data.term == "--")
							{
								title_html = "Taxonomy Archive &raquo; "+data.taxonomy_name;
							}
							else
							{
								title_html = "Taxonomy Archive &raquo; "+data.taxonomy_name+" &raquo; "+data.term_name;
							}
							
						}
						else //tag type archive page
						{
							if( typeof data.term === 'undefined'|| data.term == '' )
							{
								title_html = 'Taxonomy Archive &raquo; '+data.taxonomy_name+' &raquo; <input type="text" placeholder="Term name/slug" class="term-name" taxonomy="'+data.taxonomy+'"> ';
								$(condition_set).attr("need_term_input", 1 );
							}
							else if(data.term == "--")
							{
								title_html = "Taxonomy Archive &raquo; "+data.taxonomy_name;
							}
							else
							{
								
								title_html = 'Taxonomy Archive &raquo; '+data.taxonomy_name+' &raquo; <input type="text" placeholder="Term name/slug" class="term-name" taxonomy="'+data.taxonomy+'" value="'+data.term+'"> ';
								$(condition_set).removeAttr('need_term_input');
							}
							
							
						}
					}

					break;
				}//end case 'taxonomy'
				case 'author': //author_archive
				{
					title_html = "Author Archive Page";
					break;
				}//end case 'author'
				case 'search': //search_result
				{
					title_html = "Search Result Page";
					break;
				}//end case 'search'
				case '404_not_found': //404_page
				{
					title_html = "404 Page";
					break;
				}//end case '404_not_found'

			}//end switch( data.page_type )
			
			ADGURU_ASM.set_condition_set_query_data( condition_set, query_data );
			
			$(condition_set).find('.page-type-display-box').first().html(title_html);

		},

		get_condition_set_initial_query_data : function( condition_set ){
			var data = $(condition_set).data('initial_query_data');
			if( typeof data === 'undefined' )
			{
				data = {'new_entry' : 1 };
			}
			return data;
		},

		set_condition_set_initial_query_data : function( condition_set, data ){
			$(condition_set).data('initial_query_data', data);
			var id = $(condition_set).attr('id');
			ADGURU_ASM.condition_set_initial_query_data_stringify[id] = JSON.stringify( data );
		},

		set_condition_set_single_initial_query_data : function( condition_set, field, value ){
			var data = ADGURU_ASM.get_condition_set_initial_query_data(condition_set);
			data[field] = value;
			ADGURU_ASM.set_condition_set_initial_query_data( condition_set, data );
		},

		get_condition_set_query_data : function( condition_set ){
			var data = $(condition_set).data('query_data');
			if( typeof data === 'undefined' )
			{
				data = {};
			}
			return data;

		},
		
		set_condition_set_query_data : function( condition_set, data ){
			$(condition_set).data('query_data', data);
			var id = $(condition_set).attr('id');
			ADGURU_ASM.condition_set_query_data_stringify[id] = JSON.stringify( data );
		},

		set_condition_set_single_query_data : function( condition_set, field, value ){
			var data = ADGURU_ASM.get_condition_set_query_data(condition_set);
			data[field] = value;
			ADGURU_ASM.set_condition_set_query_data( condition_set, data );
		},

		process_term_name_change : function( obj ){
			var condition_set = $(obj).closest('.condition-set');
			var taxonomy = $(obj).attr('taxonomy');
			var term = $(obj).val();
			$(obj).addClass('checking');//loading icon
			$(obj).attr('disabled', 'disabled');
			var qData = {
				'action' : 'adguru_get_term_data',
				'taxonomy' : taxonomy,
				'term' : term
			}
			$.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "GET",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function(response){
					$(obj).removeClass('checking');
					$(obj).removeAttr('disabled', 'disabled');
					if(response.status == 'success')
					{
						if( response.exist == 1 )
						{
							ADGURU_ASM.set_condition_set_single_query_data(condition_set, 'term', response.term_data.slug );
							$(condition_set).removeAttr('need_term_input');
						}
						else
						{
							$(obj).val("");
							$(condition_set).attr('need_term_input', 1);
						}
										
					}
					else
					{
						alert(response.message);
					}
				},
				error: function(xhr,errorThrown){}
				   
			  });//end $.ajax

		},

		clear_duplicate_indicator : function(){
			$(".condition-set").removeClass('duplicate');
		},

		check_duplicate_page_type : function( condition_set ){
			var id = $( condition_set ).attr('id');
			var this_data = ADGURU_ASM.condition_set_query_data_stringify[id];
			var duplicate_id = false;
			
			for( var i in ADGURU_ASM.condition_set_initial_query_data_stringify )
			{
				if( i != id && this_data == ADGURU_ASM.condition_set_initial_query_data_stringify[i] )
				{
					duplicate_id = i;
					break;
				}
			}
			
			
			if( duplicate_id )
			{
				$("#"+duplicate_id).addClass('duplicate');
				alert("Duplicate page type found. Change any of them");
			}
			
			return duplicate_id;
		},

		check_input_error_and_make_data : function( condition_set ){
			var result = {
				'hasError' : false,
				'msg' : '',
				'slides': false
			};

			var slides = [];
			
			var adboxes = $(condition_set).find('.ads-box');
			for( var i=0; i< adboxes.length; i++ ) //jQuery to loop through elements https://stackoverflow.com/a/20464137
			{
				var slide = [];
				var adbox = adboxes.eq(i);
				var ads = $(adbox).find('.ad');
				if( ads.length == 0 ){ continue; }
				var total_per = 0;
				var per_fields = [];
				for( var j=0; j<ads.length; j++ )
				{
					var ad = ads.eq(j);
					var adid = parseInt( $(ad).attr('adid') );
					var adtype = $(ad).attr('adtype');
					var per_field = $(ad).find('.percentage').first();
					per_fields.push( per_field );
					var per = parseInt( $(per_field).val() );
					if( per == 0 )
					{
						result.hasError = true;
						result.msg = 'Error: Percentage value must be grater than 0.';
						per_field.addClass('error');
					}
					total_per = total_per + per;
					slide.push({ 'ad_id' : adid, 'percentage' : per, 'ad_type' : adtype });
				}
				
				if( total_per != 100 )
				{
					result.hasError = true;
					result.msg = 'Error: Total percentage in a slide must be 100. Change value of all percentage fields or click on the = button';
					for( var k in per_fields )
					{
						$( per_fields[k] ).addClass('error'); 
					}
				}
				slides.push(slide);
			}//end for( var i in adboxes ) 
			
			if( result.hasError )
			{
				ADGURU_ASM.add_error_msg( condition_set , result.msg );
			}
			else
			{
				result.slides = slides;
			}
			
			return result;
		},

		process_save_btn_click : function( obj ){
			var condition_set = $(obj).closest('.condition-set');
			ADGURU_ASM.remove_error_msg( condition_set );
			//check for duplicate page type
			if( ADGURU_ASM.check_duplicate_page_type( condition_set) )
			{
				return false;
			}

			var attr = $(condition_set).attr('need_term_input')
			if (typeof attr !== typeof undefined && attr !== false) 
			{
    			return false;
			}

			ADGURU_ASM.save_condition_set( condition_set );
		},

		save_condition_set : function( condition_set ){

			var initial_query_data = ADGURU_ASM.get_condition_set_initial_query_data( condition_set );
			var query_data = ADGURU_ASM.get_condition_set_query_data( condition_set );

			if( typeof query_data.page_type === 'undefined' )
			{
				return false;
			}

			var result = ADGURU_ASM.check_input_error_and_make_data( condition_set );
			
			if( result.hasError )
			{
				return false;
			}
			
			ADGURU_ASM.clear_duplicate_indicator();
			ADGURU_ASM.show_hide_save_loading( condition_set );
			ADGURU_ASM.disable_save_btn( condition_set );

			var qData = {
				'action' : 'adguru_save_ad_links',
				'zone_id' : query_data.zone_id,
				'post_id' : query_data.post_id,
				'ad_type' : query_data.ad_type,
				'country_code' : query_data.country_code,
				'page_type' : query_data.page_type,
				'taxonomy' : query_data.taxonomy,
				'term' : query_data.term,
				'slides' : result.slides, 
				'initial_query_data' : initial_query_data
			};

			$.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "POST",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function(response){
					
					ADGURU_ASM.show_hide_save_loading( condition_set );
					ADGURU_ASM.enable_save_btn( condition_set );

					if(response.status == 'success')
					{
						console.log(response.message);
						ADGURU_ASM.set_condition_set_initial_query_data( condition_set, query_data );
										
					}
					else
					{
						alert(response.message);
					}
				},
				error: function(xhr,errorThrown){}
				   
			});//end $.ajax


		},

		
		process_delete_set_btn_click : function( obj ){
			var condition_set = $(obj).closest('.condition-set');
			ADGURU_ASM.remove_error_msg( condition_set );
			ADGURU_ASM.delete_condition_set( condition_set );
		},

		delete_condition_set : function( condition_set ){

			var initial_query_data = ADGURU_ASM.get_condition_set_initial_query_data( condition_set );
			var query_data = ADGURU_ASM.get_condition_set_query_data( condition_set );

			
			
			ADGURU_ASM.clear_duplicate_indicator();


			if( typeof initial_query_data.new_entry !== 'undefined' && initial_query_data.new_entry == 1 )
			{
				ADGURU_ASM.destroy_condition_set( condition_set );
				return true;

			}

			if( typeof query_data.page_type === 'undefined' )
			{
				return false;

			}

			ADGURU_ASM.show_hide_delete_loading( condition_set );
			ADGURU_ASM.disable_save_btn( condition_set );
			ADGURU_ASM.enable_delete_btn( condition_set );


			var qData = {
				'action' : 'adguru_delete_condition_set', 
				'initial_query_data' : initial_query_data
			};

			$.ajax({
			   url: adGuruAdminVars.ajaxUrl,
			   type: "POST",
			   global: false,
			   cache: false,
			   async: true,
			   data:qData,
				success: function(response){
					
					ADGURU_ASM.show_hide_save_loading( condition_set );
					ADGURU_ASM.enable_save_btn( condition_set );
					ADGURU_ASM.enable_save_btn( condition_set );

					if(response.status == 'success')
					{
						console.log(response.message);
						ADGURU_ASM.destroy_condition_set( condition_set );
										
					}
					else
					{
						alert(response.message);
					}
				},
				error: function(xhr,errorThrown){}
				   
			});//end $.ajax

		},

		disable_save_btn : function( condition_set ){
			$(condition_set).find('.save-btn').first().css("pointer-events", "none").addClass('disable');
		},
		enable_save_btn : function( condition_set ){
			$(condition_set).find('.save-btn').first().css("pointer-events", "auto").removeClass('disable');
		},
		disable_delete_btn : function( condition_set ){
			$(condition_set).find('.delete-set-btn').first().css("pointer-events", "none").addClass('disable');
		},
		enable_delete_btn : function( condition_set ){
			$(condition_set).find('.delete-set-btn').first().css("pointer-events", "auto").removeClass('disable');
		},
		show_hide_save_loading : function( condition_set ){
			$(condition_set).find('.save-loading').first().toggleClass('hidden');
		},
		show_hide_delete_loading : function( condition_set ){
			$(condition_set).find('.delete-set-loading').first().toggleClass('hidden');
		},
		add_error_msg : function( condition_set, msg ){
			$(condition_set).find('.set-error-msg-box').first().html(msg);
		},
		remove_error_msg : function( condition_set ){
			$(condition_set).find('.set-error-msg-box').first().html('');
		},

		destroy_condition_set : function ( condition_set ){
			ADGURU_ASM.clear_duplicate_indicator();
			var id = $( condition_set ).attr('id');
			delete ADGURU_ASM.condition_set_query_data_stringify[id];
			$( condition_set ).remove();
		}


	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");