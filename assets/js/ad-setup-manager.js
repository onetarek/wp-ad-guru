;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		
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