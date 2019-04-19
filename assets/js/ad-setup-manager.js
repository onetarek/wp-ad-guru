;
var ADGURU_ASM = {};
( function( $ ){

	ADGURU_ASM = {

		last_set_number : 0,
		
		init : function(){
			this.add_events();
			this.create_condition_sets();
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
			var html = tmpl.replace('{{SLIDE_NUMBER}}', slide_data['number'] );
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

		create_condition_set : function( data ){
			this.last_set_number++;
			var html_id = 'condition_set_'+this.last_set_number;
			var tmpl = ADGURU_ASM_DATA.condition_set_html_template;
			var html = tmpl.replace('{{SET_HTML_ID}}', html_id );

			var html = html.replace('{{PAGE_TYPE_DISPLAY_HTML}}', data['page_type_display_html'] );
			var html = html.replace('{{CONDITION_DETAIL}}', data['condition_detail'] );
			var slides_html = "";
			var slide_data = {
				'links' : [],
				'number' : 0
			};
			if( data['ad_zone_link_set'].length )
			{
				var slides = [];
				var i;
				for( i in data['ad_zone_link_set'] )
				{
					var link = data['ad_zone_link_set'][i];
					if( typeof slides[i] == 'undefined' )
					{
						slides[i] = {
							'number' : i,
							'links' : []
						};
					}
					slides[i]['links'].push( link );
				}

				for( i in slides )
				{
					slides_html = slides_html + this.get_slide_html( slides[i] );
				}


			}
			else
			{
				slide_data['number'] = 1;
				slides_html = slides_html + this.get_slide_html( slide_data );
			}
			var html = html.replace('{{SLIDES_HTML}}', slides_html );

			$("#condition_sets_box").append( html );
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
					var set_data = {
						'ad_zone_link_set' : ADGURU_ASM_DATA.ad_zone_link_sets[i]
					}
					this.create_condition_set( set_data );
				}
			}
		}

	};//end ADGURU_ASM

	$(document).ready(function(){
		ADGURU_ASM.init();
	});

} )(jQuery);
console.log("Ad setup manager loaded.....");