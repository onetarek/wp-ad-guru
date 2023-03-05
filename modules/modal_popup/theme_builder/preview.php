<link rel="stylesheet" type="text/css" href="<?php echo ADGURU_PLUGIN_URL ?>modules/modal_popup/assets/css/modal-popup-preview.css?var=<?php echo ADGURU_VERSION ?>" >
<div class="postbox" id="adguru_mp_preview_box">
	<h3 class="hndle"><?php _e('Theme Preview', 'adguru');?> <a onclick="javascript: return adguru_mp_show_preview_in_full_view()" style="font-size:14px; cursor:pointer;float:right;"><?php _e('See full view', 'adguru');?></a></h3>
	<div class="inside" style="padding:0px;margin:0px;">
		<div id="adguru_mp_preview_area">

			<div id="adguru_modal_popup_example" class="adguru-modal-popup sidebar_view hidden" popup-id="example">
				<div id="adguru_modal_popup_overlay_example" class="mp-overlay adguru-modal-popup-overlay" popup-id="example"></div>
				<div id="adguru_mp_preview_full_view_close_btn_wrap" class="hidden"><div id="adguru_mp_preview_full_view_close_btn" onclick="adguru_mp_show_preview_in_sidebar_view()"><?php _e('Close preview', 'adguru');?></div></div>
				<div id="adguru_modal_popup_container_wrap_example" class="mp-container-wrap middle-center">
					<div id="adguru_modal_popup_conatiner_example" class="mp-container" popup-id="example">
						<div id="adguru_modal_popup_content_wrap_example" class="mp-content-wrap mp-content-wrap-image" popup-id="example">
							<div id="adguru_modal_popup_content_example" class="adguru-content-image mp-content mp-content-image" popup-id="example">
								<a href="javascript: return fasle" style="display:block;line-height:0;"><img src="<?php echo ADGURU_PLUGIN_URL ?>modules/modal_popup/assets/images/sunset_600x400_1.jpeg" class="adguru_content_image"></a>
							</div>
						</div>
						<div id="adguru_modal_popup_close_wrap_example" class="mp-close-wrap top-right"><div id="adguru_modal_popup_close_example" class="mp-close adguru-modal-popup-close " popup-id="example"><img src="<?php echo ADGURU_PLUGIN_URL ?>assets/images/close-icons/close-default.png" alt="X"></div></div>
					</div>
				</div>
						
			</div>


		</div><!-- .main -->
	</div><!-- .inside -->
</div>

<style type="text/css">
	
	#adguru_mp_preview_box.sticky{
		position: fixed;
		top:40px;
		z-index: 9999;
		width: 278px;
	}
	#adguru_mp_preview_area{
		/*padding: 30px 20px;*/
		position: relative;
		background: url("<?php echo ADGURU_PLUGIN_URL ?>modules/modal_popup/assets/images/preview-bg.png");
		min-height: 800px;
	}
	#adguru_mp_preview_full_view_close_btn_wrap{
		position: fixed;
		bottom: 0;
		left: 50%;
		z-index: 99997;
	}
	#adguru_mp_preview_full_view_close_btn{
		position: relative;
		z-index: 99997;
		padding: 10px;
		background: rgba(100, 0, 0, 0.3);
		color: #ffffff;
		font-size: 30px;
		line-height: 30px;
		cursor: pointer;
		border-radius: 10px;
		font-weight: 100;
		left: -50%;
	}
	
	#adguru_modal_popup_example.full_view .mp-container{
		width: 600px;
		max-height: 400px;
	}
	#adguru_modal_popup_example.sidebar_view .mp-container{
		width: 100%;
		min-width: 100px;

	}
	#adguru_modal_popup_example.sidebar_view .mp-overlay{
		position: absolute;
		height: 100%;
		width: 100%;
	}
	#adguru_modal_popup_example.sidebar_view .mp-container-wrap{
		position: absolute;
	}
	#adguru_modal_popup_example.sidebar_view .mp-content-wrap{
		position: static;
	}

	#adguru_modal_popup_example.sidebar_view #adguru_modal_popup_container_wrap_example{
		margin-top: -75px;
	}

	#adguru_modal_popup_example .adguru-content-image{
		display: flex;
		justify-content: center;

	}

	#adguru_modal_popup_example.sidebar_view .adguru_content_image{
		/*margin-left: -190px;*/

	}

</style>