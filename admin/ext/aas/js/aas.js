/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/

//Description editors (ckeditor and tinymce) dont like # at the end of a url. So if found only # at the end then reload page without it.
if(window.location.href.slice(-1)=='#' && window.location.hash=='') window.location.href=window.location.href.slice(0,-1);

$(function(){

	$(window).on('hashchange',function(){

		if(window.location.href.slice(-1)=='#' && window.location.hash=='') window.location.href=window.location.href.slice(0,-1);
	
	});
	
	//prevent links adding # at the end of url
	$('body').on('click', 'a', function(e){
	  if( $(this).attr('href') === '#' ) e.preventDefault(); 
	});
	
	 //enable columns drag when there are 5 or more
 if($("table#tbl thead").find("tr:first th").length>=5){
  
   $('table#tbl').dragtable({
     dragHandle:'.draghandle',
     dragaccept:'.draggable',
     persistState: function(table) {
      
      if(table.endIndex===table.startIndex) return false;
      
      var arr=[];
      table.el.find('th').each(function(i) {
        
        if(i!=0 && i!=1 && i!=2 ){
        arr.push($(this).data('clmn'));     
        }
        
      });
      
      aas.ajax.do({data:{reOrderColumns:arr,item:'reOrderColumns'},url: config.url.plugins+'core/actions/aas.php'});
      
      tbl_fix_tableHeadCells();
    
    }
   });
 
 }else $('.draghandle').remove();
	
	//reorder products-categories only if sort_order field is visible and not search
	if(fields.sort_order && config.disabled_column_actions.sort_order==0 && !config.is_searching){

		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		var fixHelperModified = function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index)
			{
			  $(this).width($originals.eq(index).width())
			});
			return $helper;
		};

		$("#tbl tbody.categories_tbody").sortable({
			containment: "#tbl",
			items:'.folder',
			helper: fixHelperModified,
			//create: function( event, ui ) {console.log('John Barounis is the Best Programmer');},
		  update: function(event,ui){
				var idsArray=$("#tbl tbody.categories_tbody").sortable( "toArray");

				if(idsArray.length>0){
				
					aas.dialog.open('dialog-processing');
					aas.ajax.do({data:{item:'reorderCategories',cats:idsArray.join(',')},url:config.url.actions},function(msg){
					
						aas.dialog.close('dialog-processing');
						
						if(msg=='1'){
						
							//fix sort order tds
								for(var i=0,n=idsArray.length;i<n;i++){
	
									$('#tbl tbody.categories_tbody tr#'+idsArray[i]).find('td.categories_sort_order').text(i+1);
	
								}
						
						}else{
							
							aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
							//revert back
							$("#tbl tbody.categories_tbody").sortable( "cancel" );
						
						}
					
					});
								
				}
							
				//window.location.href=config.url.reorder+'?'+config.cPathString+'&page='+config.page+'&entriesPerPage='+config.entriesPerPage+'&idsArray='+idsArray;		

			}
		
		}).disableSelection();

		if($('#tbl tbody.products_tbody tr.product:first').find('td.products_sort_order').text()!='---'){

			$("#tbl tbody.products_tbody").sortable({
				containment: "#tbl",
				items:'.product',
				helper: fixHelperModified,
				//create: function( event, ui ) {console.log('John Barounis is the Best Programmer');},
				update: function(event,ui){
					var idsArray=$("#tbl tbody.products_tbody").sortable( "toArray");

					if(idsArray.length>0){

						var sortOrderProducts={},tr_product=$('#tbl tbody.products_tbody tr.product'),entriesPerPage= config.entriesPerPage=='All' ? 1 : parseInt(config.entriesPerPage);
						
						if(config.ascDesc=='DESC'){
							
							var mxso=config.entriesPerPage=='All' ? config.totalProducts : (config.totalProducts)-(entriesPerPage * (parseInt(config.page)-1)) ;
							
							tr_product.each(function(index){
								sortOrderProducts[mxso-index-1]=$(this).attr('id');
							});

						}else{

							var mxso=	entriesPerPage*(parseInt(config.page)-1);
							tr_product.each(function(index){								
								sortOrderProducts[mxso+index]=$(this).attr('id');
							});
						
						}

						aas.dialog.open('dialog-processing');
						aas.ajax.do({data:{item:'reorderProducts',idsArrayObj:sortOrderProducts},url:config.url.actions},function(msg){
								
								aas.dialog.close('dialog-processing');
								
								if(msg){
								
										if(config.ascDesc=='DESC'){
										
											tr_product.each(function(index){
													$(this).find('td.products_sort_order').text(mxso-index-1);											
											});
										
										}else{
										
											tr_product.each(function(index){
													$(this).find('td.products_sort_order').text(mxso+index);
											});
										
										}
										
									//fix table tr background colors
									if(config.colorEachTableRowDifferently){
									
										tr_product.each(function(index){
										
												if(index & 1) $(this).removeClass('odd').addClass('even'); else $(this).removeClass('even').addClass('odd');
									
										});
									
									}
								
								}else{
								
										aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
										//revert back
										$("#tbl tbody.products_tbody").sortable( "cancel" );
								
								}
								
							
					});
					
					}
					
					//window.location.href=config.url.reorder+'?'+config.cPathString+'&page='+config.page+'&entriesPerPage='+config.entriesPerPage+'&idsArray='+idsArray;		

				}

			}).disableSelection();

	}//IF != ---


	}//END	if(fields.sort_order){
	
	var selectedFields=$('section#selectedFields'), selectedFieldsWrapper=$('#selectedFields-inwrapper'),selectedFields_cwidth= parseInt(aas.localStorage.get('AAS:selectedFields-width')) || 190;
	selectedFields.css({height:$(window).height() });
	selectedFieldsWrapper.css({height:$(window).height() });

	var leftSidePanel=$('section#leftSidePanel'), leftSidePanelWrapper=$('#leftSidePanel-inwrapper'),leftSidePanel_cwidth= parseInt(aas.localStorage.get('AAS:leftSidePanel-width')) || 230;
	leftSidePanel.css({height:$(window).height() });
	leftSidePanelWrapper.css({height:$(window).height() });

	var toolBox=$('section#toolBox'), toolBoxWrapper=$('#toolBox-inwrapper'), toolBoxInitHeight=parseInt(aas.localStorage.get('AAS:toolBox-height'));
	var tooltip=$('#tooltip'),tooltip_span=$('#tooltip span'),tooltip_div=$('#tooltip div');
	var overlayWindow=$('#overlayWindow');

	$(window).resize(function(){
		var this_height=$(this).height();
		selectedFields.css({height:this_height});
		selectedFieldsWrapper.css({height:this_height});
		leftSidePanel.css({height:this_height});
		leftSidePanelWrapper.css({height:this_height});
		// setter
		toolBox.resizable( "option", "maxHeight", this_height-$('#floatBottomBar').outerHeight() );
		
		tbl_fix_tableHeadCells();
	
	});
	
	if(config.defaults.enableTableStickyHeaders){

    tbl_fix_tableHeadCells();
    
	  //WINDOW SCROLL
    $(window).scroll(function(e){
	
	      if($(this).scrollTop()>=tbl_top) $('#tbl_thead_helper').show(); else $('#tbl_thead_helper').hide();
	      
    });

  }
	
	$('#search').keyup(function(){searchTable($(this).val());});

	$('.buttonakia').hover(function(){

		 $_this=$(this);
		 tooltip_span.attr('class', 'arrow up');
		 tooltip_div.text($_this.data('title'));
		 tooltip.css({top:$_this.position().top+$_this.outerHeight()+15,left:$_this.position().left}).stop().fadeIn('fast');
	 
	 },function(){
	 
			tooltip.hide();
	 
	 });
	 
	//=>TOOLBOX PANEL
	var toolBoxState= parseInt(aas.localStorage.get('AAS:toolBox-state')) || 0;

	var ctoolBoxHeight=toolBoxState ? toolBoxInitHeight : 0;
	
	//hack display toolBox only when an always div is shown because we want not to be displayed when print page
	if($('#topPanelToggle').is(':visible'))	toolBox.css({height:ctoolBoxHeight}).show();
	/*
	toolBox.animate({height:ctoolBoxHeight},1000,function(){
		toolBoxWrapper.css({height:ctoolBoxHeight});
		toolBox.show();
	});
	*/
	
	toolBox.resizable({

		handles:"n",
		maxHeight: $(window).height()-$('#floatBottomBar').outerHeight(),
		stop:function(event,ui){
	
			aas.localStorage.save('AAS:toolBox-height',ui.size.height);
	
		},
		resize:function(event,ui){
	
			toolBoxWrapper.css({height:ui.size.height});
	
		},

	});

	$('.toolBox-help-icon').on('click touchend',function(){
		aas.dialog.open('dialog-information',$(this).next('.info-explain').html());
	});

	$('body').on('click touchend','.explain-icon',function(){
		aas.dialog.open('dialog-information',$(this).next('.info-explain').html());
	});

	$('#toolBox-toggle').hover(function(){

		 $_this=$(this);
		 tooltip_span.attr('class', 'arrow bottom-right');
		 tooltip_div.text($_this.data('title'));
		 
		 tooltip.css({top:$_this.offset().top-$_this.outerHeight()-15,left:$_this.offset().left-tooltip.outerWidth()+40}).stop().fadeIn('fast');
	 
	 },function(){
	 
			tooltip.hide();
	 
	 });
	
	$('.toolbox-descriptionbutton').hover(function(){

		 $_this=$(this);
		 tooltip_span.attr('class', 'arrow up');
		 var title=$_this.data('title');
		 if(title && title!=''){
			 tooltip_div.html($_this.data('title'));
			 tooltip.css({top:$_this.position().top+$(window).scrollTop()+$_this.outerHeight()+15,left:$_this.position().left}).stop().fadeIn('fast');
		 }
	 
	},function(){
	 
		tooltip.hide();
	 
	});
	
	
	$('#toolBox-toggle').on('click touchend',function(){

		tooltip.hide();
		
		if(toolBox.height()<=0){
		
			var cheight= parseInt(aas.localStorage.get('AAS:toolBox-height')) || 135;
			aas.localStorage.save('AAS:toolBox-height',cheight);
			aas.localStorage.save('AAS:toolBox-state',1);
			toolBox.animate({height:cheight});
		
		}else{
		
			aas.localStorage.save('AAS:toolBox-state',0);
		
			toolBox.animate({height:0});
		}

	});	
	 
	//=>

	//=>RIGHTSIDE PANEL
	
	$( "#format" ).sortable({
	
		containment:'#selectedFields-inwrapper',
		items:'.wr',
		placeholder: "ui-state-highlight",
		update: function(event, ui) { 
			aas.ajax.do({data: 'value='+$("#format").sortable( "toArray")+'&column=set_session&action=sorted_fields_array', url:config.url.ajax});
		}
	 
	}).disableSelection();
	
	$('#format li').css({width:selectedFields_cwidth});
	selectedFields.resizable({

		handles:"w",
		maxWidth: $(window).width()-$('#panelToggle').width(),
		stop:function(event,ui){
	
			aas.localStorage.save('AAS:selectedFields-width',ui.size.width);
	
		},
		resize:function(event,ui){

			$('#format li').css({width:ui.size.width});
		
		},

	});
	
	$('#panelToggle').on('click touchend',function(){

		tooltip.hide();

		if(selectedFields.width()<=0){
		
			var cwidth= parseInt(aas.localStorage.get('AAS:selectedFields-width')) || 190;
			selectedFields.animate({width:cwidth});
		
		}
		else selectedFields.animate({width:0});

	});
	
	//=>
	
	//=>TOP PANEL
	
	$('#panelToggle').hover(function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow right');
		tooltip_div.text($_this.data('title'));
		tooltip.css({top:$_this.position().top+$(window).scrollTop(),left:$_this.offset().left-tooltip.outerWidth()-16}).stop().fadeIn('fast');
	 
	},function(){
	 
		tooltip.hide();
	 
	});
	
	var topPanelToggleState=config.is_searching;
	if(topPanelToggleState) $('#leftSidePanel-toggle').animate({top:100},400);
	$("#topPanelToggle").on('click touchend',function(){
	
		tooltip.hide();
		$('#panel').slideToggle({
		
			duration:400,
			start:function(a){
		
				var gotop=topPanelToggleState==1?50:100;
				$('#leftSidePanel-toggle').animate({top:gotop},400);
		
			},
			done:function(a,j){
		
				topPanelToggleState = (topPanelToggleState==0) ? 1 : 0;
		
			}
		
		});

	});
	
	//=>

	//=>LEFTSIDE PANEL
	
	$('#leftSidePanel .savedProducts_data').css({width:leftSidePanel_cwidth});
	leftSidePanel.resizable({

		handles:"e",
		maxWidth: $(window).width()-$('#leftSidePanel-toggle').width(),
		stop:function(event,ui){
	
			aas.localStorage.save('AAS:leftSidePanel-width',ui.size.width);
	
		},
		resize:function(event,ui){

			$('#leftSidePanel .savedProducts_data').css({width:ui.size.width});
	
		},

	});
	
	var temp_list_locations_cache={};
	leftSidePanel.on('mouseover','.temp-list-location-link',function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow left');
		var title=$_this.data('title');
		if(title && title!=''){
			tooltip_div.html($_this.data('title'));
			tooltip.css({top:$_this.position().top-16+$(window).scrollTop(),left:$_this.position().left+$_this.outerWidth()+26}).stop().fadeIn('fast');
			var pid=$_this.attr('id').substr(19);
			if(!temp_list_locations_cache[pid]){
				aas.ajax.do({data:'item=locate_product&product_id='+pid,url:config.url.actions},function(msg){

					$_this.data('title',msg);
					tooltip_div.html(msg);
					temp_list_locations_cache[pid]=msg;
			
				});
			}
			
		}

	});
	
	leftSidePanel.on('mouseout','.temp-list-location-link',function(){
		   tooltip.hide(); 
	});
	
	$('#leftSidePanel-toggle').hover(function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow left');
		tooltip_div.text($_this.data('title'));
		tooltip.css({top:$_this.position().top+$(window).scrollTop(),left:$_this.offset().left+$_this.outerWidth()+16}).stop().fadeIn('fast');
	 
	 },function(){
	 
		tooltip.hide();
	 
	 });
	 
	$('#leftSidePanel-toggle').on('click touchend',function(){

		tooltip.hide();
		
		if(leftSidePanel.width()<=0){
		
			var cwidth= parseInt(aas.localStorage.get('AAS:leftSidePanel-width')) || 230;
			leftSidePanel.animate({width:cwidth});
		
		}else leftSidePanel.animate({width:0});

	});	

	leftSidePanel.on('click touchend','.toggle-savedProducts-info',function(){
	
		var $_this=$(this);

		$_this.parent().next().stop().slideToggle(function(){
		
			$_this.toggleClass('toggle-savedProducts-info-minus');
		
		});
		
		$_this.closest('.savedProducts_data').siblings().children('.savedProducts-ul').hide(function(){
		
			$(this).prev().children('.toggle-savedProducts-info').removeClass('toggle-savedProducts-info-minus');
		
		});
		
	});

  	leftSidePanel.on('click touchend','.remove-savedProduct',function(){
	
		removeProductFromTempList(this);
		
	});
	
	leftSidePanel.on('click touchend','.attributesbutton',function(){
		
		aas.dialog.open('dialog-attributes','',{pid:$(this).attr('id').substr(19)},translate.AAS_DIALOG_TITLE_PRODUCT_ATTRIBUTES+': '+$(this).data('productname'));
		
	});
	
	//=>

	//=>CELLS EDITABLE
	
	$('table#tbl tbody td').not('.nojedit').editable( config.url.ajax,{
	"select" : true,
	"width":'auto',
	"callback": function( sValue, y ) {

		if(sValue=='aasSessionTimeout'){

			aas.dialog.open('dialog-sessiontimeout');
			return false;
	
		}
		
		var clmn=$(this).data('column');
		if(clmn=='products_price' && fields.products_price_gross){
			
			var rid=this.parentNode.getAttribute('id').substr(4);
			$ppg=$('#products_price_gross_'+rid);
			$ppg.text(updateGross(sValue,$ppg.data('tax-rate')));
			$ppg.data('price-net',sValue);
		
		}
		
		if(clmn=='products_price_gross' && fields.products_price){
		
			var rid=this.parentNode.getAttribute('id').substr(4);
			$pp=$('#products_price_'+rid);
			$pp.text(updateNet(sValue,$(this).data('tax-rate')));
			$(this).data('price-net',sValue);
			
		}
		
		if(clmn=='products_price' || clmn=='products_price_gross'){
		
			if(fields.special) updateSpecialsCell(this.parentNode.getAttribute('id').substr(4));
		
		}
		
		if(fields.last_modified){
		  var rid=this.parentNode.getAttribute('id').substr(4);
		  updateLastModifiedCell(rid);
		}
		
	},

	"submitdata": function ( value, settings ){
			
			return {
				"id": this.parentNode.getAttribute('id').substr(4),
				"column": $(this).data('column'),
			};


	},
	
	"ajaxoptions": {
			
			beforeSend:function(jqXHR, settings){
			  jqXHR.setRequestHeader('X-AAS', config.ajaxToken);			
		  }

	},
	
	"height": "25px"
		
	});
	
	//=>

	$('#massCheckbox').click(function(){

		var thisen=$(this);

		if(thisen.is(":checked")){
	
			$('#tbl tbody.products_tbody tr').addClass('tr_selected').find('.checkboxMassActions').prop('checked', true); 
	
			if($('.checkboxMassActions').filter(':checked').length > 0){
				
				$('#deletebutton,#savedbutton').show();

			}else{
		
				thisen.prop('checked',false);
		
			}
	
		}else{ 
		
			$('#tbl tbody.products_tbody tr').removeClass('tr_selected').find('.checkboxMassActions').prop('checked', false);
	
			$('#deletebutton,#savedbutton').hide();
	
		}

	});

	$('.checkboxMassActions').click(function(){

		var thisen=$(this);
		if(thisen.is(":checked")){

			thisen.closest('tr').addClass('tr_selected');
	
			if( $('.checkboxMassActions').filter(':not(:checked)').length === 0) $('#massCheckbox').prop('checked', true);
			
		}else{

			thisen.closest('tr').removeClass('tr_selected');	
			$('#massCheckbox').prop('checked', false);

		}

		if($('.checkboxMassActions').is(':checked')) $('#deletebutton').show(); else $('#deletebutton').hide();
		if($('.checkboxMassActions').is(':checked')) $('#savedbutton').show(); else $('#savedbutton').hide();
		//if($('.checkboxMassActions').is(':checked')) $('#massColumnsEditbutton').show(); else $('#massColumnsEditbutton').hide();

	});
	
	$('#exportbutton').on('click touchend',function(){ aas.dialog.open('dialog-export'); });
	$('#importbutton').on('click touchend',function(){ $('#overlayWindow-import').show(); });
	$('#donationbutton').on('click touchend',function(){ $('#donations').fadeIn('fast',function(){ toggleBodyVerticalScrollbar(0); }); });

	//hide tooltip when click on a button
	$('.koumpakia').on('click touchend',function(){
	
		tooltip.hide();

	});

	$('#deletebutton').on('click touchend',function(){
				
		var arr={};
		$('#tbl tr.tr_selected').each(function (a) {
		 
		 	 arr[this.id.substr(4)]=$(this).data('category');
		 
		}).get();//.join();

		aas.dialog.open('dialog-massProductsDelete','',{productsIdsArray:arr});
		
	});
	
	$('#savedbutton').on('click touchend',function(){
		
		updateTempProductsList();
					
	});
			
	//$('#printbutton').on('click touchend',function(){
	
		//window.print();
		
	//});

	$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd',

		onSelect: function(dateText, inst) {
			var $_this=$(this);
			aas.dialog.open('dialog-processing');
			aas.ajax.do({data: 'value='+dateText+'&id='+$(this).attr('id').substr(11)+'&column=products_date_available', url:config.url.ajax},function(msg){
			  aas.dialog.close('dialog-processing');
				aas.dialog.open('dialog-success',translate.successfully_changed_available_date);
				$_this.next().css({visibility:'visible'});
			});
		}

	});

	$('.product-available-to-null').on('click touchend',function(){
		var $_this=$(this);
		
		aas.dialog.open('dialog-processing');
		aas.ajax.do({data: 'item=setAvailableDateNull&product_id='+$_this.attr('id').substr(26), url:config.url.actions},function(msg){
		  aas.dialog.close('dialog-processing');
				if(msg=='1'){
					aas.dialog.open('dialog-success',translate.AAS_DIALOGS_TEXT_SUCCESSFULLY_SET_PRODUCTS_AVAILABLE_DATE_TO_NULL);
					$_this.css({visibility:'hidden'});
					$_this.prev().val('');
				}else{
				
					aas.dialog.open('dialog-error',translate.AAS_DIALOGS_TEXT_COULD_NOT_SET_PRODUCTS_AVAILBALE_TO_NULL);
				
				}
		});
	
		return false;
	});
		
	$("#lfor").focus(function () {
		var k=$(this).val();
		if(k==tbl_searching)  $(this).val('');
	});
	
	 $("#lfor").focusout(function () {
		var j=$(this).val();
		if(j=='') $(this).val(tbl_searching);
	});	
	
	$('#overlayWindow-import').on('mouseover','.file-import-button',function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow bottom');
		var title=$_this.data('title');
		if(title && title!=''){
			tooltip_div.html($_this.data('title'));
			tooltip.css({top:$_this.offset().top-$_this.outerHeight()-16,left:$_this.offset().left}).stop().fadeIn('fast');
		}

	});

	$('#overlayWindow-import').on('mouseout','.file-import-button',function(){
		   tooltip.hide(); 
	});
	 
	overlayWindow.on('click touchend','.view_products_page',function(){
	
		window.open($(this).attr('href'),'_blank');
		tooltip.hide();
		return false;
	
	});
	
	overlayWindow.on('mouseover','.product_description_action',function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow right');
		var title=$_this.data('title');
		if(title && title!=''){
			tooltip_div.html($_this.data('title'));
			tooltip.css({top:$_this.offset().top,left:$_this.offset().left-tooltip.outerWidth()-16}).stop().fadeIn('fast');
		}

	});

	overlayWindow.on('mouseout','.product_description_action',function(){
		   tooltip.hide(); 
	});
	
	overlayWindow.on('click touchend','.edit_products_attributes',function(){
	
		tooltip.hide();
		aas.dialog.open('dialog-attributes','',{pid:$(this).data('pid'),lid:$(this).data('lid')},translate.AAS_DIALOG_TITLE_PRODUCT_ATTRIBUTES+': '+$(this).data('productname'));	
		
		return false;
	
	});
		
	overlayWindow.on('click touchend','.overlay_language_img',function(){

		var languages_id=$(this).attr('id').substr(4);
		var languages_alias=$(this).data('alias');

		var of=$('#overlayWindow .container').scrollTop()-50;		
		if(config.language_id==languages_id){

			$('#overlayWindow .container').animate({scrollTop:$('#overlayWindow #overlay-fieldset').position().top+of},500);

		}else{
		
			if($('#overlayWindow #overlay-other-languages #overlay-fieldset-'+languages_id).length<=0) editInOtherLanguage(languages_id,languages_alias);
			else $('#overlayWindow .container').animate({scrollTop:$('#overlayWindow #overlay-other-languages #overlay-fieldset-'+languages_id).position().top+of},500);
		
		}

	});
	
	var $desc_next=$('#desc_next'), $desc_previous=$('#desc_previous'),$desc_close=$('#desc_close');
	
	$desc_close.on('click touchend',function(){
		tooltip.hide();
		$('#overlayWindow').hide();
		kill_editor('description_editor');
		
		var loaded_languages_ids=aas.localStorage.get('AAS:loaded_languages_ids');
		if(loaded_languages_ids){
		
			var lla_array=loaded_languages_ids.split(',');
			for(var i=0,n=lla_array.length;i<n;i++){

				kill_editor('description_editor_'+lla_array[i]);
		
			}
		
		}
	
		$('#overlay-other-languages').children().remove();

	});

	$desc_previous.on('click touchend',function(){
	
		if($desc_previous.data('previous')){ 
		
			$desc_close.click();
			$('#trigger_'+$desc_previous.data('previous').substr(4)).click();
		
		}else $desc_previous.hide();
	
	});

	$desc_next.on('click touchend',function(){
	
		if($desc_next.data('next')){ 
		
			$desc_close.click();
			$('#trigger_'+$desc_next.data('next').substr(4)).click();
		
		}else $desc_next.hide();
	
	});

	$('#desc_preview_changes').on('click touchend',function(){

		var previewId=$('#previewid').val();
	
		if($("#iframias").contents().find('#'+previewId).length){
				  	
	    		var description_data=getDescriptionData('description_editor');

			$("#iframias").contents().find('#'+previewId).html(description_data);
	
		}else aas.dialog.open('dialog-error-unique-id-wrapper-not-found','',{wrapperUID:$('#previewid').val()});
		
		var loaded_languages_ids=aas.localStorage.get('AAS:loaded_languages_ids');
		if(loaded_languages_ids){
		
			var lla_array=loaded_languages_ids.split(',');
			for(var i=0,n=lla_array.length;i<n;i++){

				$("#iframias_"+lla_array[i]).contents().find('#'+previewId).html(getDescriptionData('description_editor_'+lla_array[i]));
		
			}
		
		}

	});

	$('#desc_apply_changes').on('click touchend',function(){

		var description_data=encodeURIComponent(getDescriptionData('description_editor'));
		var languages_id=$('#overlay_lid').val();
		aas.ajax.do({data: 'value='+description_data+'&id='+$('#overlay_pid').val()+'&lid='+languages_id+'&column=products_description', url:config.url.ajax},function(msg){
		
			if(msg=='ok') aas.dialog.open('dialog-success',translate.AAS_DIALOGS_PHP_SUCCESS_SUBMITED_SUCCESSFULLY); else aas.dialog.open('dialog-error',msg);
			
			$('#iframias').attr("src", $('#iframias').attr("src"));
		
		});
		
		$('#overlay-other-languages fieldset').each(function(){
		
			var clid=$(this).children().find('.overlay_language_id').val();
			var pid=$(this).children().find('.overlay_pid').val();
		
			var description_data= encodeURIComponent(getDescriptionData('description_editor_'+clid));
			
			aas.ajax.do({data: 'value='+description_data+'&id='+pid+'&column=products_description&lid='+clid, url:config.url.ajax},function(msg){
			
				$('#iframias_'+clid).attr("src", $('#iframias_'+clid).attr("src"));
			
			});
		
		});

	});
	
	$('#desc_toggle_editors').on('click touchend',function(){
	
		changeEditor('description_editor');
		
		$('#overlay-other-languages fieldset').each(function(){
		
			var clid=$(this).children().find('.overlay_language_id').val();

        changeEditor('description_editor_'+clid);
      
		});
	
	});

	$('#desc_reload_preview').on('click touchend',function(){

		$('#iframias').attr("src", $('#iframias').attr("src"));
		
		var loaded_languages_ids=aas.localStorage.get('AAS:loaded_languages_ids');
		if(loaded_languages_ids){
		
			var lla_array=loaded_languages_ids.split(',');
			for(var i=0,n=lla_array.length;i<n;i++){

				$('#iframias_'+lla_array[i]).attr("src", $('#iframias_'+lla_array[i]).attr("src"));
		
			}
		
		}
		
	});

	//remove on load/ can be ommitted since it will be removed when you click on the description button
	aas.localStorage.remove('AAS:loaded_languages_ids');

	//$( ".descriptionbutton" ).click(function(){
	$( document ).on('click touchend','.descriptionbutton,.descriptionbuttonImg',function(){
		aas.localStorage.remove('AAS:loaded_languages_ids');
		tooltip.hide();
		
		var next_id=$(this).closest('tr').next().not('.folder').attr('id');
		if(next_id){ $desc_next.data('next',next_id); $desc_next.show(); }
		else $desc_next.hide();
		
		var previous_id=$(this).closest('tr').prev().not('.folder').attr('id');
		if(previous_id){ $desc_previous.data('previous',previous_id); $desc_previous.show(); }
		else $desc_previous.hide();
		
		var pid=$(this).attr('id').substr(8);

		$('#overlay_pid').val(pid);
		aas.ajax.do({data: { item: 'description', product_id:pid }, url:config.url.actions},function(msg){
		
			$('#description_editor').val(msg);
			load_editor('description_editor');
		
		});
		
		$('#view_in_new_window').attr('href',config.product_info_path+pid+'&language='+config.language_code);
		
		$('#edit_products_attributes').data('pid',pid);
		var data=$(this).data();
		$('#edit_products_attributes').data('productname',data.productname);
		
		$('#iframias').attr('src',config.product_info_path+pid+'&language='+config.language_code);
		
		$('#overlayWindow #productName').text(data.productname);
		overlayWindow.show();
		
	});
	
	//ATTRIBUTES GROUP COLORIZE ON HOVER
	$('#dialog-attributes').on('mouseenter', 'table#attributes-table tbody tr', function() {
	
    var jthis = $(this),bgc=jthis.css('background-color'),classen=jthis.attr('class');
    bg = bgc.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    $('table#attributes-table tbody tr.'+classen).css('background-color','rgb('+(bg[1]-5)+', '+(bg[2]-5)+', '+(bg[3]-5)+')');

  }).on('mouseleave', 'table#attributes-table tbody tr', function() {

    var jthis = $(this),classen=jthis.attr('class');
    $('table#attributes-table tbody tr.'+classen).css('background-color',jthis.data('initbgcolor'));

  });
	
	$('#dialog-attributes').on('click touchend','.delete_attribute_button',function(){

		var par=$(this).parent().parent();

		var hid=par.find('.hidden_products_attributes_id').val();
		var pid=par.find('.hidden_products_id').val();
		var opt_name=par.find('.attributes_selectMenus_options option:selected').text();
		var val_name=par.find('.attributes_selectMenus_values option:selected').text();
		var val_price=par.find('.value_price').val();
		var pref=par.find('.price_prefix').val();
		var dmsg='';
	  if(config.download_enabled){
		    
		  var dfilename=par.find('.downloadable_filename').val();
      var dmaxdays=par.find('.downloadable_maxdays').val();
      var dmaxcount=par.find('.downloadable_maxcount').val();
      var dmsg='--- [ '+dfilename+' | '+dmaxdays+' | '+dmaxcount+' ] ';
		    
		}
	
		var msg=translate.are_you_sure_to_delete_attribute+'<br /><br />'+opt_name+' --- '+val_name+' --- '+pref+'  '+val_price+dmsg;
	
		aas.dialog.open('dialog-confirm',msg,{extraData:pid+'_@_'+hid+'_@_delete_attribute'});

	});
	
  $('#dialog-attributes').on('click','.attributes_checkbox_selector',function(){
  
  	var thisen=$(this);
		if(thisen.is(":checked")){

			thisen.closest('tr').addClass('tr_selected');
	
			if( $('.attributes_checkbox_selector').filter(':not(:checked)').length === 0) $('.attributes_checkbox_all_selector').prop('checked', true);			
			$('#attributes-delete-selected').show();
			
		}else{

			thisen.closest('tr').removeClass('tr_selected');	
			$('.attributes_checkbox_all_selector').prop('checked', false);
			if( $('.attributes_checkbox_selector').filter(':checked').length === 0){ $('.attributes_checkbox_all_selector').prop('checked', false); $('#attributes-delete-selected').hide(); }

		}
		
		aas.localStorage.save('AAS:checkedAttributes',aas.get.checked_products('attributes_checkbox_selector'));
  
  });
  
  $('#dialog-attributes').on('click','.attributes_checkbox_all_selector',function(){
  
    if($(this).prop('checked')){ $('.attributes_checkbox_selector').prop('checked',true).closest('tr').addClass('tr_selected'); $('#attributes-delete-selected').show(); }
    else{ $('.attributes_checkbox_selector').prop('checked',false).closest('tr').removeClass('tr_selected'); $('#attributes-delete-selected').hide(); }
    
    aas.localStorage.save('AAS:checkedAttributes',aas.get.checked_products('attributes_checkbox_selector'));
  
  });
  
  $('#dialog-attributes').on('click touchend','#attributes-delete-selected',function(){
    
    var selatts=aas.get.checked_products('attributes_checkbox_selector');
    var msg=translate.AAS_TEXT_ARE_YOU_SURE_TO_DELETE_SELECTED_ATTRIBUTES+'<br /><br />';
    var pid,opt_name,val_name,val_price,pref;
    
    var dmsg='';
    if(config.download_enabled){
		    
		  var dfilename,dmaxdays,dmaxcount;
		    
		}
    
    for(var i=0,n=selatts.length;i<n;i++){
    
      var atr=$('#aid_'+selatts[i]);

      if(atr.length){
      
     		pid=atr.find('.hidden_products_id').val();
		    opt_name=atr.find('.attributes_selectMenus_options option:selected').text();
		    val_name=atr.find('.attributes_selectMenus_values option:selected').text();
		    val_price=atr.find('.value_price').val();
		    pref=atr.find('.price_prefix').val();
		    
		    if(config.download_enabled){
		    
		      dfilename=atr.find('.downloadable_filename').val();
		      dmaxdays=atr.find('.downloadable_maxdays').val();
		      dmaxcount=atr.find('.downloadable_maxcount').val();
		      dmsg='--- [ '+dfilename+' | '+dmaxdays+' | '+dmaxcount+' ] ';
		    
		    }
		    
		    msg+=opt_name+' --- '+val_name+' --- '+pref+'  '+val_price+dmsg+' <br>';

      }
    
    }
  
    aas.dialog.open('dialog-confirm',msg,{extraData:pid+'_@_'+selatts.join(',')+'_@_delete_attributes'});

	});
	
	$('#dialog-attributes').on('click touchend','.remove_attribute_button',function(){

		$(this).closest('tr').remove();
	
	});
	
	$('.radiostockajax').on('click touchend',function(){

		var statusIdArray=this.id.split('_'), $_this=$(this), srcArray=$_this.children().attr('src').split('/'),stockVal= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? '1' : '0';
    aas.dialog.open('dialog-processing');
		aas.ajax.do({data:'value='+stockVal+'&id='+statusIdArray[1]+'&column=products_status',url:config.url.ajax},function(msg){
			aas.dialog.close('dialog-processing');
			if(msg=='1' || msg=='0' ){
			
				if(msg=='1'){
				
					$_this.closest('tr').removeClass('unavailable');
					var translation_status=translate.AAS_STATUS_ICON_SET_OUT_OF_STOCK;

				}else if(msg=='0'){
				
					$_this.closest('tr').addClass('unavailable');
					var translation_status=translate.AAS_STATUS_ICON_SET_IN_STOCK;
				
				}
		
				aas.dialog.open('dialog-success',translate.status_successfully_changed);
		
				srcArray[srcArray.length-1]= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? 'icn_alert_success.png' : 'icn_alert_error.png';
			
				if(stockVal=='1') $_this.data('stock','0'); else $_this.data('stock','1');
								
				$_this.children().attr({'src':srcArray.join('/'),title:translation_status,alt:translation_status});
					
			}

		});

		return false;

	});

	$('table#tbl').on('focusin', '.specials_expires_at', function(e) {

		$(this).datepicker({ dateFormat: 'yy-mm-dd',

			onSelect: function(dateText, inst) {
				var $_this=$(this);
				var sid=$(this).attr('id').substr(20);
				aas.ajax.do({data: 'action=updateExpiresAt&value='+dateText+'&specials_id='+sid, url:config.url.plugins+'specials/aas.php'},function(msg){
					aas.dialog.open('dialog-success','Successfully set Expire Date');
					$_this.next().css({visibility:'visible'});
					
					//update also the value is specials
					$('#specials_datepicker_'+sid).val(dateText);
					$('#specials_datepicker_'+sid).next().css({visibility:'visible'});
					
				});
			}

		});

	});
	
	$('table#tbl').on('click touchend','.specials-unexpire',function(){
		var $_this=$(this);
		var sid=$_this.attr('id').substr(18);
		aas.ajax.do({data: 'action=setNeverExpire&specials_id='+sid, url:config.url.plugins+'specials/aas.php'},function(msg){
				
				if(msg=='1'){
					aas.dialog.open('dialog-success','Successfully set special never expire');
					$_this.css({visibility:'hidden'});
					$_this.prev().val('');
					updateSpecialsTable();
					//update also the value is specials
					$('#specials_datepicker_'+sid).val('');
					$('#specials_datepicker_'+sid).next().css({visibility:'hidden'});
					
				}else{
				
					aas.dialog.open('dialog-error','Could not set products available date to null!');
				
				}
		});
	
		return false;
	});

	$('table#tbl').on('click touchend','.radiostockajax-special',function(){

		var statusIdArray=this.id.split('_'), $_this=$(this), srcArray=$_this.children().attr('src').split('/'),stockVal= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? '1' : '0';
    aas.dialog.open('dialog-processing');
		aas.ajax.do({data:'value='+stockVal+'&specials_id='+statusIdArray[1]+'&action=changeStatus',url:config.url.plugins+'specials/aas.php'},function(msg){
			
			aas.dialog.close('dialog-processing');
			
			if(msg=='1' || msg=='0' ){
			
					if(msg=='1'){
						
						$_this.closest('td').removeClass('unavailable');
						$('table#tbl_specials tr#sid_'+statusIdArray[1]).removeClass('unavailable');
						
						
					}else if(msg=='0'){ 
					
						$_this.closest('td').addClass('unavailable');
						$('table#tbl_specials tr#sid_'+statusIdArray[1]).addClass('unavailable');
						
					}
				
					srcArray[srcArray.length-1]= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? 'icn_alert_success.png' : 'icn_alert_error.png';	
					$('table#tbl_specials tr#sid_'+statusIdArray[1]+' td a#special_status_'+statusIdArray[1]).children('img').attr('src',srcArray.join('/'));
	
					aas.dialog.open('dialog-success',translate.status_successfully_changed);
			
					//if(stockVal=='1') $_this.data('stock','0'); else $_this.data('stock','1');
				
					$_this.children().attr('src',srcArray.join('/'));
			}

		});

		return false;

	});

	$("#uidw").button().click(function(){ aas.dialog.open('dialog-unique-id-wrapper');});
	$("#uidwo").button().click(function(){

		aas.dialog.close('dialog-error-unique-id-wrapper-not-found');
		aas.dialog.open('dialog-unique-id-wrapper');

	});

	$( "table#tbl .attributesCellLink" ).click(function(){
		
		//aas.dialog.open('dialog-processing');
		aas.dialog.open('dialog-attributes','',{pid:$(this).closest('tr').attr('id').substr(4)},translate.AAS_DIALOG_TITLE_PRODUCT_ATTRIBUTES+': '+$(this).data('productname'));	
		
		
	});

	$( "#trigger_settings_dialog" ).on('click touchend',function(){
		aas.dialog.open('dialog-settings');
	});

	$( "#trigger_settings_sort_fields_dialog" ).button().click(function(){
		aas.dialog.open('dialog-settings-sort-fields');
	});

	$('#trigger_error_dialog').button().click(function(){
		aas.dialog.open('dialog-error');
	});

	$('.product_image_link').on('click touchend',function(){
	
		var poc=$(this).data('poc');
		
		if(poc=='category'){
		
			var cid=$(this).closest('tr').attr('id').substr(4);
			$('#categories_images-category_name').html($(this).data('categoryname'));

			$('#categories_images').data('cid',cid);

			var cImgSrc=$(this).find('img').attr('src');
			if(cImgSrc){
		
				$('#categories_images-current_image_path').html(cImgSrc);
				$('#categories_images-current-img').attr('src',cImgSrc);
				
				//auto select image location by img src
				var imgArr=cImgSrc.split(config.dir_ws_images);
				if(imgArr[1]){
				
				   if(imgArr[1]==''){
				  
				   }else{
				    
				     var xs=imgArr[1].split('/');
				     xs.pop();
				     $('#categories_images-images_folders input[name="categories_images-image_folder"][value="' + ( xs.join('/') || '' ) + '"]').prop('checked', true);
				     
				   }
				  
				}				

			}else{
		
				$('#categories_images-current_image_path').html('---');
				$('#categories_images-current-img').attr('src','');
		
			}
		  
			$('#categories_images').fadeIn(function(){
			
        toggleBodyVerticalScrollbar(0);
        
			});
		
		}else if(poc=='product'){
				
			var pid=$(this).closest('tr').attr('id').substr(4);
			$('#product_images-product_name').html($(this).data('productname'));

			$('#product_images').data('pid',pid);

			var cImgSrc=$(this).find('img').attr('src');
			if(cImgSrc){
		
				$('#product_images-current_image_path').html(cImgSrc);
				$('#product_images-current-img').attr('src',cImgSrc);
				
				//auto select image location by img src
				var imgArr=cImgSrc.split(config.dir_ws_images);
				if(imgArr[1]){
				
				   if(imgArr[1]==''){
				  
				   }else{
				    
				     var xs=imgArr[1].split('/');
				     xs.pop();
				     $('#product_images-images_folders input[name="product_images-image_folder"][value="' + ( xs.join('/') || '' ) + '"]').prop('checked', true);
				     
				   }
				  
				}

			}else{
		
				$('#product_images-current_image_path').html('---');
				$('#product_images-current-img').attr('src','');
		
			}
		
			$('#product_images').fadeIn(function(){

        toggleBodyVerticalScrollbar(0);
				product_images_getLargeImages();

			});
		
		}
	
	});

	$('#masseditbutton').on('click touchend',function(){
		tooltip.hide();
		if(!aas.dialog.isOpen('dialog-massedit'))	aas.dialog.open('dialog-massedit');
	});
	
	$('#massColumnsEditbutton').on('click touchend',function(){
		tooltip.hide();
		if(!aas.dialog.isOpen('dialog-massColumnsEdit'))	aas.dialog.open('dialog-massColumnsEdit');
	});
	
	$('#dialog-attributes').on('click touchend','#attributes_mass_edit_option_prices_legend',function(){

		$('#attributes_mass_edit_option_prices').toggleClass('dashed-borderize');
		$(this).children('.attributes_mass_edit_option_prices_toggle').toggleClass('attributes_mass_edit_option_prices_toggle_minus');
		$('#attributes_mass_edit_option_prices_form').toggle();//stop().fadeToggle();

	});

	$('#dialog-attributes').on('click touchend','.downloadableButton',function(){
	
		aas.dialog.open('dialog-downloadable-products-manager','',{t:$(this)});
		
	});
	
	$('#dialog-attributes-clever-copy').on('click touchend','.downloadableButton',function(){
	
		aas.dialog.open('dialog-downloadable-products-manager','',{t:$(this)});
		
	});
	
  $('#dialog-attributes-clever-copy').on('click touchend','.exclude_attribute_button_attributesbutton',function(){

		$(this).closest('tr').toggleClass('unavailable');
		
	});
		
	$('#search_filename').keyup(function(){searchList($(this).val());});
		
		$('#nav-tooltip-data').on('mouseleave',function(){

		$(this).parent().hide();

	});
	
	var nav_cache={};
	$('#nav span.raquo').on('click touchend',function(){

		var jthis=$(this),data=jthis.data(),nav_tooltip=$('#nav-tooltip'),nav_tooltip_data=$('#nav-tooltip-data');
		
		nav_tooltip.css({top:jthis.position().top+jthis.height()+10, left:jthis.position().left-23});
		
		if(nav_cache[data.catid]) {
		
			nav_tooltip_data.html(nav_links_construct(data.catid,nav_cache));
			nav_tooltip.show();
			return false;
			
		}
		
		nav_tooltip_data.html('<img src="ext/aas/images/loading.gif" alt="loading">');
		nav_tooltip.show();
		
		aas.ajax.do({
			data:{'catid':data.catid,'item':'loadCats'},
			dataType:'json',
			url:config.url.actions
		},function(msg){
			
			if(msg=='0'){
			
				aas.dialog.open('dialog-error',translate.AAS_TEXT_NO_FOUND);
				nav_tooltip.hide();
			
			}else{
				
				nav_cache[data.catid]=msg;
				if(msg!='')	nav_tooltip_data.html(nav_links_construct(data.catid,nav_cache));
				else nav_tooltip_data.html(translate.AAS_TEXT_NO_CATEGORIES_FOUND);
			
			}
		
		});
		
		return false;
	
	});
	
	$('.removeLinkedProduct').on('click touchend',function(){
	
		aas.dialog.open('dialog-remove-linked-product-confirm','',$(this).data());

	});

	$('.removeLinkedProductFromParent').on('click touchend',function(){
	
		aas.dialog.open('dialog-remove-linked-product-from-parent-confirm','',$(this).data());

	});
	
	$('#logoffbutton').on('click touchend',function(e){
	
		aas.dialog.open('dialog-confirm',translate.AAS_TEXT_ARE_YOU_SURE_TO_LOGOFF,{extraData:'logoff',ed:$(this).find('a').attr('href')});
		e.preventDefault();

	});

//upload_module_upload_area
	$('#upload_module_upload_area').filedrop({
	  xhrParams:{'X-AAS':config.ajaxToken},
		force_fallback_id:'upload_module_file_upload',
		paramname:'module_file',	
		maxfiles: 1,
	  maxfilesize: 6,
		url: 'ext/aas/plugins/core/actions/aas.php',
    //allowedfiletypes: ['application/zip'],   // filetypes allowed by Content-Type.  Empty array means no restrictions
    allowedfiletypes: [],
		allowedfileextensions: ['.zip','.ZIP'], // file extensions allowed. Empty array means no restrictions
  		data:{
  			item: function(){
  			
  				return 'uploadModule';
  			
  			}
  		},
  		error: function(err, file) {
    	
    		aas.dialog.close('dialog-processing');
    	
			switch(err) {
				case 'BrowserNotSupported':
					aas.dialog.open('dialog-error',translate.AAS_TEXT_IMPORT_BROWSER_NOT_SUPPORTED);
					break;
				case 'TooManyFiles':
					aas.dialog.open('dialog-error',translate.AAS_TEXT_IMPORT_TOO_MANY_FILES);
					break;
				case 'FileTooLarge':
					aas.dialog.open('dialog-error',file.name+translate.AAS_TEXT_IMPORT_FILE_TOO_LARGE);
					break;
				 case 'FileTypeNotAllowed':
					aas.dialog.open('dialog-error',translate.AAS_TEXT_IMPORT_FILETYPE_NOT_ALLOWED);
					break;
				case 'FileExtensionNotAllowed':
					aas.dialog.open('dialog-error',translate.AAS_TEXT_IMPORT_FILE_EXTENSION_NOT_ALLOWED);
					break;
				default:
					break;
			}
		},
		 uploadFinished: function(i, file, response, time) {
		 
		 	aas.dialog.close('dialog-processing');

			if(response=='aasSessionTimeout'){
		 		aas.dialog.open('dialog-sessiontimeout');
				return false;
			}else{
			
				if(response=='1'){
				
		 		 	aas.dialog.open('dialog-reload-page',translate.AAS_DIALOG_UPLOAD_MODULE_SUCCESS);
		 		 	
			 	}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_UPLOAD_MODULE_ERROR+'<br /><br />'+response);
			
			}

   		 },

		beforeEach: function(file){
				
		},
		
		uploadStarted:function(i, file, len){
			aas.dialog.open('dialog-processing');
		},
		
		progressUpdated: function(i, file, progress) {
		
		},
		globalProgressUpdated: function(progress) {
		        
    	}
    	 
	});

	$('#aas_upload_module-check_for_new_modules').on('click touchend',function(){
		var jthis=$(this),jthisTab=$('#aas_upload_module-tab2');
		jthisTab.find('.loading_wrapper').html('<img src="ext/aas/images/loading.gif" alt="loading new modules list">');
	
		aas.ajax.do({
		dataType:'jsonp',
		type:'GET',
		url:config.modules_check_link	
		},function(msg){

			//if(msg['modules']){
			if(msg){

				jthisTab.find('.loading_wrapper').html('');
			
				var tblupmodHtml='',cnt=0;
				//for(var i in msg['modules']['available']){
				for(var i in msg){
				
			
					var devs=[];
					for(var j in msg[i]['developers']){
				
						if(msg[i]['developers'][j]['website']!='') devs.push('<a target="_blank" href="'+msg[i]['developers'][j]['website']+'">'+msg[i]['developers'][j]['fullname']+'</a>');
						else devs.push(msg['modules']['available'][i]['developers'][j]['fullname']);
				
					}
							
				var statusHTML='';

				if(modules_installed[msg[i]['code']]){
			
					if(modules_installed[msg[i]['code']]==msg[i]['version']) statusHTML+='<span style="color:green">'+translate.AAS_UPLOAD_MODULE_STATUS_INSTALLED_AND_UPDATED+'</span>';
					if(modules_installed[msg[i]['code']]<msg[i]['version']) statusHTML+='<span style="color:red">'+translate.AAS_UPLOAD_MODULE_STATUS_INSTALLED_BUT_OUTDATED+'</span><br>'+translate.AAS_UPLOAD_MODULE_TEXT_CLICK+'<a target="_blank" href="'+msg[i]['url']+'">'+translate.AAS_UPLOAD_MODULE_TEXT_HERE+'</a>'+translate.AAS_UPLOAD_MODULE_TEXT_TO_GET_NEWEST_VERSION;
					if(modules_installed[msg[i]['code']]>msg[i]['version']) statusHTML+=translate.AAS_UPLOAD_MODULE_TEXT_WRONG_VERSION_INSTALLED+'<br>'+translate.AAS_UPLOAD_MODULE_TEXT_CLICK+'<a target="_blank" href="'+msg[i]['url']+'">'+translate.AAS_UPLOAD_MODULE_TEXT_HERE+'</a>'+translate.AAS_UPLOAD_MODULE_TEXT_TO_GET_NEWEST_VERSION;
						
				}else statusHTML+='<span style="color:navy">'+translate.AAS_UPLOAD_MODULE_STATUS_NOT_INSTALLED+'</span><br>'+translate.AAS_UPLOAD_MODULE_TEXT_CLICK+'<a target="_blank" href="'+msg[i]['url']+'">'+translate.AAS_UPLOAD_MODULE_TEXT_HERE+'</a>'+translate.AAS_UPLOAD_MODULE_TEXT_TO_INSTALL_IT;

				tblupmodHtml+='<tr class="'+(cnt + 1?'odd':'even' )+'"><td>'+(++cnt)+'</td><td><a target="_blank" href="'+msg[i]['url']+'">'+msg[i]['name']+'</a></td><td>'+msg[i]['version']+'</td><td>'+devs.join(', ')+'</td><td style="text-align:left">'+statusHTML+'</td></tr>';
				//tblupmodHtml+='<tr><td colspan="6" style="text-align:left;padding:10px;font-size:10px;">'+msg['modules']['available'][i]['description']+'<br><?php echo AAS_UPLOAD_MODULE_TEXT_CLICK; ?><a target="_blank" href="'+msg['modules']['available'][i]['url']+'"><?php echo AAS_UPLOAD_MODULE_TEXT_HERE; ?></a><?php echo AAS_UPLOAD_MODULE_TEXT_TO_FIND_OUT_MORE; ?></td></tr>';
						
				}

			jthisTab.find('table tbody').html(tblupmodHtml);
		
			}
	
		});
	
	});

	$('#aas_upload_module-tab1 .aas_upload_module-aac').on('click touchend',function(){

		aas.dialog.open('dialog-aac','',{tab:'modules'});

	});

	$('#aacTriggerButton').on('click touchend',function(){

		aas.dialog.open('dialog-aac','',{tab:''});

	});
	
	$(".tabContainer ul.tabContainerUl li a").on('click touchend',function(){

		var activeTab = $(this).data("rel");
		$(".tabContainer ul.tabContainerUl li a").removeClass("active");
		$(this).addClass("active");
		$(".tabContainer .tabContents").hide();
		$('#'+activeTab).fadeIn();
		
		return false;
		
	});
	
	$(".tabContainerModules ul.tabContainerUl li a").on('click touchend',function(){

		var activeTab = $(this).data("rel");
		$(".tabContainerModules ul.tabContainerUl li a").removeClass("active");
		$(this).addClass("active");
		$(".tabContainerModules .tabContents").hide();
		$('#'+activeTab).fadeIn();
		
		return false;
		
	});
	
	$("#dialog-aac .tabContainer ul.tabContainerUl li a").on('click touchend',function(){

		var activeTab = $(this).data("rel");

		//if($('#'+activeTab+' table tbody').children().length>0) return false;

		var type=$('#'+activeTab).data('type');
		
		aas.dialog.open('dialog-processing');
 		aas.ajax.do({data:{item:'getAac',type:type},url:config.url.actions},function(msg){
 			aas.dialog.close('dialog-processing');
 				if(msg=='0'){
 				
 					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
 				
 				}else{
 				
 				  if(type=='admins_columns_display')  $('#aas_dcd-tab2').html(msg);
 				  else  $('#'+activeTab+' table tbody').html(msg);
 				
 				}
 			
 			});
		return false;
	});
	
	$('.closeModule').on('click touchend',function(){

		module_loaded=0;
		hidePreviewPagePanel();
		$(this).closest('.overlay').fadeOut(function(){
		
      toggleBodyVerticalScrollbar();
		
		});
		
	});

//DROPDOWN APPLY BUTTON
	$('.applyButtonDropdownOnClick').on('click touchend',function(){

    displayDropDown($(this));

  });

	$('.applyButtonDropdownOnHover').on('mouseover',function(){

    displayDropDown($(this));

  });
  
  $('.dropdown').on('mouseleave',function(){
  
    $(this).hide();
  
  });
//DROPDOWN APPLY BUTTON
  
	$( ".datepickerMass_date_added" ).datepicker({ dateFormat: 'yy-mm-dd',changeYear: true,changeMonth: true,maxDate:0});
	$( ".datepickerMass" ).datepicker({ dateFormat: 'yy-mm-dd',changeYear: true,changeMonth: true});
 
  $('#dialog-massColumnsEdit').on('click touchend','.massColumnsEdit-applyButton',function(){
  
    var jthis=$(this),jthis_tr=jthis.closest('tr'),column=jthis_tr.data('column');
        
    //gather pids based on options
    var sel_list=$('#massColumnsEditOptions-select-list').val();

    var pids=[];
    switch(sel_list){
    
      case '1':
      
        pids=aas.get.checked_products();
      
      break;
      case '2':
      
        pids=aas.get.checked_products('checkedTempProducts');
      
      break;
      case '3':
      
        pids.push('0');
      
      break;
    
    }
    
    var sel_cat=$('#massColumnsEditOptions-select-cats').val();//category option select
    var sel_rec=$('#massColumnsEditOptions-select-rec').val();//recursive option select
    var sel_sta=$('#massColumnsEditOptions-select-sta').val();//status option select
    
    var value=jthis_tr.find('.massColumnsEditValue').val();
    var option=jthis_tr.find('.massColumnsEditSelectOption').val();
    
    if(pids.length<=0){
    
      aas.dialog.open('dialog-error',translate.no_products_selected);
      return false;
      
    }
    
    if(column!='products_date_available'){
    
      if(value==''){
      
        aas.dialog.open('dialog-error',translate.AAS_TEXT_SUBMITTED_WRONG_VALUE);
        return false;
        
      }
    
    }
    
    if(column=='products_price' && sel_list=='1'){
    
    	var obj_ds={},arr=[];
	
	    for(i=0,n=pids.length;i<n;i++){

		      arr.push(pids[i]); 
		      obj_ds[pids[i]]=parseFloat($('#products_price_'+pids[i]).html());
	
	      }
	      
	      var timi;
	      var value=parseFloat(value);
	      for(var i in obj_ds){

		      switch(option){

			      case '=': timi = value.toFixed( 4 ); break;
			      case '-%': timi = (obj_ds[i]-(obj_ds[i]*value/100)).toFixed( 4 ); break;
			      case '+%': timi = (obj_ds[i]+(obj_ds[i]*value/100)).toFixed( 4 ); break;
			      case '-': timi = (obj_ds[i]-value).toFixed( 4 ); break;
			      case '+': timi = (obj_ds[i]+value).toFixed( 4 ); break;

		      }

		      obj_ds[i]=timi;

	      }

      value=JSON.stringify(obj_ds);

    }
    
    if(column=='products_price_gross' && sel_list=='1'){
    
    	var obj_ds={},arr=[];
	
	    for(i=0,n=pids.length;i<n;i++){

		      arr.push(pids[i]); 
		      obj_ds[pids[i]]={'price':parseFloat($('#products_price_gross_'+pids[i]).html()), taxrate:parseFloat($('#products_price_gross_'+pids[i]).data('tax-rate'))}
	
	      }
	      
	      var timi;
	      var value=parseFloat(value);
	      for(var i in obj_ds){

		      switch(option){

			      case '=': timi = value.toFixed( 4 ); break;
			      case '-%': timi = (obj_ds[i]['price']-(obj_ds[i]['price']*value/100)).toFixed( 4 ); break;
			      case '+%': timi = (obj_ds[i]['price']+(obj_ds[i]['price']*value/100)).toFixed( 4 ); break;
			      case '-': timi = (obj_ds[i]['price']-value).toFixed( 4 ); break;
			      case '+': timi = (obj_ds[i]['price']+value).toFixed( 4 ); break;

		      }

		      obj_ds[i]['price']=timi;

	      }
    
      value=JSON.stringify(obj_ds);
    }
    
    var reloadTempList=false;
    
    aas.dialog.open('dialog-processing');
 		aas.ajax.do({data:{item:'massColumnEdit',pids:pids,value:value,column:column,option:option,sel_list:sel_list,sel_cat:sel_cat,sel_rec:sel_rec,sel_sta:sel_sta},url:config.url.actions},function(response){
			aas.dialog.close('dialog-processing');
      
      if(column=='products_price_gross' && sel_list!='1'){
      
        respObj=JSON.parse(response);
        msg='1';
      
      }else{
      
        if(response=='0') msg=response;
        else{
        
          var resp=response.split('-');
          var pids=resp[1].split(',');
          var msg=resp[0];
          
        }
      
      }
      if(msg=='1'){
      
          switch(column){
          
            case'products_quantity':
            case'products_weight':
            
                var tr=null,td=null,td_txt='';
                for(var i=0,n=pids.length;i<n;i++){
                
                  if(reloadTempList===false){
                  
                    if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                  
                  }
                  
                  tr=$('#pid_'+pids[i]);
                  
                  if(tr.length<=0) continue;
                  
                  td=tr.find("[data-column='" + column + "']");
                  td_txt=td.html();
                  
                  if(option=='=') td.html(value);
                  else{
                  
                    if(option=='+'){
                    
                      if(td.data('massedit')=='int') td.html(parseInt(td_txt)+parseInt(value));
                      if(td.data('massedit')=='decimal') td.html(parseFloat(td_txt)+parseFloat(value));
                    
                    }
                  
                    if(option=='-'){
                    
                      if(td.data('massedit')=='int') td.text(parseInt(td_txt)-parseInt(value));
                      if(td.data('massedit')=='decimal') td.text(parseFloat(td_txt)-parseFloat(value));
                    
                    }
                  
                  }
                  
                  if(fields.last_modified) updateLastModifiedCell(pids[i]);

                }
                
                if(reloadTempList) reloadTempProductsList();

            break;
            case'products_status':

              aas.dialog.open('dialog-success',translate.status_successfully_changed);

              var translation_status='',pselem;
              for(i=0,n=pids.length;i<n;i++){
              
                if(reloadTempList===false){
              
                  if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                
                }
                
                tr=$('#pid_'+pids[i]);
              
                if(tr.length<=0) continue;

                if(value==1){

                  tr.removeClass('unavailable');
                  translation_status=translate.AAS_STATUS_ICON_SET_OUT_OF_STOCK;

                }else{

                  tr.addClass('unavailable');
                  translation_status=translate.AAS_STATUS_ICON_SET_IN_STOCK;

                }
                
                pselem=$('#products_status_'+pids[i]).find('img');
                
                var srcArray=pselem.attr('src').split('/');

                srcArray[srcArray.length-1]= (value==1) ? 'icn_alert_success.png' : 'icn_alert_error.png';

                pselem.attr({'src':srcArray.join('/'),title:translation_status,alt:translation_status});
                
                if(fields.last_modified) updateLastModifiedCell(pids[i]);

              }
              
              if(reloadTempList) reloadTempProductsList();

            break;
            case'manufacturers_id':

              var manu;
              for(i=0,n=pids.length;i<n;i++){
              
                if(reloadTempList===false){
              
                  if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                
                }
              
                manu=$('#pid_'+pids[i]);
                if(manu.length<=0) continue;
              
                manu.val(value);
                
                if(fields.last_modified) updateLastModifiedCell(pids[i]);
              
              }
              
              if(reloadTempList) reloadTempProductsList();
            
            break;
            
            case'products_tax_class_id':

              var tval=value.split('_');
              var val=tval[0];
              var taxRate=tval[1];
              var ppg,tcts;
              
              for(i=0,n=pids.length;i<n;i++){
              
                if(reloadTempList===false){
              
                  if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                
                }
              
            		tcts=$('#tcts_'+pids[i]);
            		if(tcts.length<=0) continue;
            		
            		if(fields.products_price_gross){
                  //update gross value
                  ppg=$('#products_price_gross_'+pids[i]);
                  ppg.html(updateGross(ppg.data('price-net'),taxRate));
                  ppg.data('tax-rate',taxRate);
                }
                
                if(fields.special) updateSpecialsCell(pids[i]);
                if(fields.last_modified) updateLastModifiedCell(pids[i]);
              
                tcts.val(value);
              
              }
              
              if(reloadTempList) reloadTempProductsList();                  
            
            break;
            
            case'products_date_added':

              for(i=0,n=pids.length;i<n;i++){
              
                if(reloadTempList===false){
              
                  if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                
                }

                var jtd=$('#date_added_'+pids[i]);
                
                if(jtd.length<=0) continue;
                
                jtd.data('celldata',value);
                var timestamp=Date.parse(value.split(' ').join('T'))/1000; //firefox needs T otherwise it cannot parse the date
                jtd.data('ago',timestamp);
                
                if(jtd.data('agostatus')==0) jtd.find('span').html(value);
                else jtd.find('span').html(aas.format.seconds(((Math.round(new Date().getTime() / 1000))-timestamp)*1000)+' '+translate.AAS_TEXT_AGO);

                if(fields.last_modified) updateLastModifiedCell(pids[i]);

              }
              
              if(reloadTempList) reloadTempProductsList();
            
            break;
            
            case'products_date_available':

              var visibility='visible';            
              for(i=0,n=pids.length;i<n;i++){

                if(reloadTempList===false){
              
                  if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                
                }
                
                if($('#pid_'+pids[i]).length<=0) continue;
              
                $('#datepicker_'+pids[i]).val(value);
                if(value=='') visibility='hidden';
                $('#product-available-to-null_'+pids[i]).css('visibility',visibility);
                
                if(fields.last_modified) updateLastModifiedCell(pids[i]);
              
              }
              
              if(reloadTempList) reloadTempProductsList();

            break;          
          
            case'products_price':
            
              if(sel_list=='1'){
              
                var ppi=null;
              
                for(i=0,n=pids.length;i<n;i++){
                
                	  if(reloadTempList===false){
              
                      if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                    
                    }
                    
                    ppi=$('#products_price_'+pids[i]);
				            if(ppi.length<=0) continue;
				            
				            ppi.html(obj_ds[pids[i]]);
				            
				            if(fields.last_modified) updateLastModifiedCell(pids[i]);
                
                }
                
                if(fields.products_price_gross){
                
                    var $ppg;
					          for(var i in obj_ds){
					          
						          $ppg=$('#products_price_gross_'+i);
						          if($ppg.length<=0) continue;
						          $ppg.html(updateGross(obj_ds[i],$ppg.data('tax-rate')));
						          $ppg.data('price-net',obj_ds[i]);

					          }

				          }
              
                if(reloadTempList) reloadTempProductsList();
              
              }
              
              if(sel_list=='2' || sel_list=='3'){
              
                //update products in current page if exist
                var timi=0;
                
                var val=parseFloat(value);

                for(i=0,n=pids.length;i<n;i++){
                
                    if(reloadTempList===false){
              
                      if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                    
                    }
                	  
                    ppi=$('#products_price_'+pids[i]);
				            if(ppi.length<=0) continue;
				            
				            var cptext=parseFloat(ppi.html());
				            switch(option){

			                  case '=': timi = val.toFixed( 4 ); break;
			                  case '-%': timi = (cptext-(cptext*val/100)).toFixed( 4 ); break;
			                  case '+%': timi = (cptext+(cptext*val/100)).toFixed( 4 ); break;
			                  case '-': timi = (cptext-val).toFixed( 4 ); break;
			                  case '+': timi = (cptext+val).toFixed( 4 ); break;

		                }
				            
				            ppi.html(timi);
				            
				            var $ppg;
				            if(fields.products_price_gross){
				            
  				            $ppg=$('#products_price_gross_'+pids[i]);
						          if($ppg.length<=0) continue;//this is not need actually because it will elem will always present
						          
						          $ppg.html(updateGross(timi,$ppg.data('tax-rate')));
						          $ppg.data('price-net',timi);
				            
				            }
				            
				            if(fields.last_modified) updateLastModifiedCell(pids[i]);
                
                }
                
                if(reloadTempList) reloadTempProductsList();
                
              }
                           
            break;
            
            case'products_price_gross':
            
              if(sel_list=='1'){
              
                var ppi;
              
                for(i=0,n=pids.length;i<n;i++){
                
                	  if(reloadTempList===false){
              
                      if($('#temp_list_elem_'+pids[i]).length) reloadTempList=true;
                    
                    }

                    ppi=$('#products_price_gross_'+pids[i]);
				            if(ppi.length<=0) continue;
				            ppi.html(obj_ds[pids[i]]['price']);
				            
				            if(fields.last_modified) updateLastModifiedCell(pids[i]);
                
                }
                
                if(fields.products_price){
                
                    var ppg,netp;
					          for(var i in obj_ds){
						          
						          ppg=$('#products_price_'+i);
						          netp=updateNet(obj_ds[i]['price'],obj_ds[i]['taxrate']);
						          $('#products_price_'+i).html(netp);
						          ppg.data('price-net',netp);
						        
					          }

				          }
              
                if(reloadTempList) reloadTempProductsList();
              
              }

              if(sel_list=='2' || sel_list=='3'){
              
                var ppi;
                for(var i in respObj){
                
                  if(reloadTempList===false){
            
                    if($('#temp_list_elem_'+i).length) reloadTempList=true;
                  
                  }
                  ppi=$('#products_price_gross_'+i);
		              if(ppi.length<=0) continue;
		              ppi.html(doRound(respObj[i]['gross'],4));
		              ppi.data('price-net',doRound(respObj[i]['net'],4));
		              
		              if(fields.products_price){
		              
			              $('#products_price_'+i).html(doRound(respObj[i]['net'],4));
		              
		              }
                
                  if(fields.last_modified) updateLastModifiedCell(i);
                
                }
              
                if(reloadTempList) reloadTempProductsList();
               
              }
              
            break;          
          
          }
      
      }else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);

		});

  
  });
  
  
  $( "#previewPageVelakiWrapper" ).css({width:'80%',height:'70%'}).resizable();
  $( "#previewPageVelaki" ).css({height:'100%'});
  
  
  $('body').on('click touchend','.view_product_class,.view_category_class',function(){
  
    var jthis=$(this),href=jthis.attr('href');    
    var csrc=$('#previewPageVelakiWrapper iframe').attr('src');
    
    if(csrc==href && !$('#previewPageVelakiWrapper').is(':hidden')){ hidePreviewPagePanel(); return false; }
    
    $('#previewPageVelakiWrapper iframe').attr('src',href);
    
    //var topen=jthis.offset().top;
    //if((topen+$('#previewPageVelakiWrapper').outerHeight())>$(window).height()) topen=$(window).height()-$('#previewPageVelakiWrapper').outerHeight()+$(window).scrollTop();
    
    topen=($(window).height()-$('#previewPageVelakiWrapper').outerHeight())/2+$(window).scrollTop();
    
    $('#previewPageVelakiWrapper .previewPageVelakiWrapper-tools .openInNewWindowLink').attr('href',href);
    
    var widthen=($(window).width()-jthis.offset().left-100)*100/$(window).width();
    
    
    $('#previewPageVelakiWrapper').css({width:widthen+'%',top:topen,left:jthis.offset().left+15}).delay(5000).show();
    
    jthis.closest('table').find('.previewPage').removeClass('previewOpen');
    jthis.closest('td').addClass('previewOpen');
    
    return false;
  
  });
  
  $('#previewPageVelakiWrapper .previewPageVelakiWrapper-tools .removeIframePreviewLink').on('click touchend',function(){
  
    hidePreviewPagePanel();
    return false;
  
  });
  
  $('#previewPageVelakiWrapper .previewPageVelakiWrapper-tools .refreshIframePreviewLink').on('click touchend',function(){
  
    var iframe = document.getElementById('previewPageVelaki');
    iframe.src = iframe.src;
    return false;
  
  });

  $('.reorderTblColumns').on('click touchend',function(){
  
    var cIndex=parseInt($(this).closest('th').index());
    
    $("table#tbl tr").each(function () {
        var rows = $(this).find("td");
        rows.eq(cIndex+1).after(rows.eq((cIndex)));
        
        var rows = $(this).find("th");
        rows.eq(cIndex+1).after(rows.eq((cIndex)));

    });
  
  });
  
  $('#dialog-information').on('click touchend','#explain_add_linked_column_btn',function(){
    
    aas.dialog.open('dialog-processing');
 		aas.ajax.do({data:{item:'addLinkedColumn'},url:config.url.actions},function(msg){
 			aas.dialog.close('dialog-processing');
			if(msg=='1'){
			
				aas.dialog.open('dialog-success',translate.AAS_TEXT_ADD_LINKED_COLUMN_QUERY_SUCCESS);
				
			}else{
			
  			aas.dialog.open('dialog-error',translate.AAS_TEXT_ADD_LINKED_COLUMN_QUERY_FAIL);
  		
			}
 			
 		});
  
  });

  $('#dialog-attributes').on('click touchend','#visualizeAttributesTrigger',function(){
  
    tooltip.hide();
    var dadata=$('#dialog-attributes').data('data');
    var lid=dadata.lid || config.language_id;
    var pid=$(this).data('pid');
    aas.dialog.open('dialog-attributes-visualizer','',{pid:pid,lid:lid});
  
  });

  $('#dialog-attributes').on('click touchend','#attributesManagerTrigger',function(){
    
    tooltip.hide();
    var dadata=$('#dialog-attributes').data('data');
    var lid=dadata.lid || config.language_id;
    var pid=$(this).data('pid');
    aas.dialog.open('dialog-attributes-manager','',{pid:pid,lid:lid});
  
  });
  
  $('#dialog-attributes').on('click touchend','#reloadProductsAttributesTrigger',function(){
    
    tooltip.hide();
    var dadata=$('#dialog-attributes').data('data');
    var lid=dadata.lid || config.language_id;
    var pid=$(this).data('pid');  
   	var orderBy=$('#product_attributes_orderBy').val();
    var ascDesc=$('#product_attributes_ascDesc').val();

	    var data=$( "#dialog-attributes" ).data('data');

	    aas.dialog.open('dialog-processing');
	    aas.ajax.do({
	
		    data:{item:'attributes',ascDesc:ascDesc,orderBy:orderBy,product_id:pid,lid:lid},
		    url:config.url.actions
	
	    },function(msg){

		    $('#dialog-attributes-options').html(msg);
		    reCheckAttributes();
        colorizeAttributesRows();
		    aas.dialog.close('dialog-processing');
			
	    });
  
  });
  
  $('#dialog-attributes').on('click touchend','.attributesCleverCopyTrigger',function(){

    var dadata=$('#dialog-attributes').data('data');
    var lid=dadata.lid || config.language_id;

    var jthis=$(this),jtr=jthis.closest('tr');
    var paid=jtr.find('.hidden_products_attributes_id').val();
    var pid=jtr.find('.hidden_products_id').val();

    aas.dialog.open('dialog-attributes-clever-copy','',{paid:paid,pid:pid,lid:lid});
  
  });
  
  $('#dialog-attributes').on('mouseover','.attstoolicon',function(){

		$_this=$(this);
		tooltip_span.attr('class', 'arrow bottom');
		var title=$_this.data('title');
		if(title && title!=''){
			tooltip_div.html($_this.data('title'));
			tooltip.css({top:$_this.offset().top-$_this.outerHeight()-20,left:$_this.offset().left-15}).stop().fadeIn('fast');
		}

	});

	$('#dialog-attributes').on('mouseout','.attstoolicon',function(){
    tooltip.hide(); 
	});

  $('#tbl .edit_date_added').prev().datepicker({ dateFormat: 'yy-mm-dd',showOn: "button",
    buttonImage: "ext/aas/images/pixel_trans.gif",
    buttonImageOnly: true,
	  defaultDateType:$(this).val(),
	  changeYear: true,
	  changeMonth: true,
	  maxDate:0,
	  onSelect: function(dateText, inst) {
	  
		  var jthis=$(this),cls=jthis.closest('tr').attr('class').split(' '),trId=jthis.closest('tr').attr('id').substr(4);
		  		  
		  aas.dialog.open('dialog-processing');
		  aas.ajax.do({data: 'value='+dateText+'&id='+trId+'&table='+cls[0]+'&column=date_added', url:config.url.ajax},function(msg){
		    
        aas.dialog.close('dialog-processing');
        
        if(msg=='0'){ 
          aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
          return false;
        }
        
		    //update cell
		    var resp=msg.split(' '),jtd=jthis.closest('td');
        
		    jtd.data('celldata',resp[0]);
		    //var timestamp=Math.round(new Date().getTime() / 1000);
		    var timestamp=Date.parse(msg.split(' ').join('T'))/1000; //firefox needs T otherwise it cannmot parse the date
		    jtd.data('ago',timestamp);
		    		    
		    if(jtd.data('agostatus')==0) jtd.find('span').html(resp[0]);
		    else jtd.find('span').html(aas.format.seconds(((Math.round(new Date().getTime() / 1000))-timestamp)*1000)+' '+translate.AAS_TEXT_AGO);
		    
		    //update last modified cell if column is visible
		    if(fields.last_modified) updateLastModifiedCell(trId);
		    
		  });
	  }

  });

  $('#tbl td .edit_date_added').on('click touchend',function(){
    
    var elem=$(this).prev();
    if(elem.datepicker( "widget" ).is(":visible")) elem.datepicker( "hide" );
    else elem.datepicker( "show" );
    return false; //so not to trigger the agoCellToggle event
  
  });
  
  $('#tbl').on('click touchend','.agoCellToggle',function(){

    //get column clicked
    var column=$(this).data('column'),jthis, dt=0;
    
    $('#tbl tr td.agoCellToggle[data-column="'+column+'"]').each(function(i){
    
      jthis=$(this),dt=jthis.data(),span=jthis.find('span');
        
        if(dt.agostatus==0){
          
          span.html(aas.format.seconds(((Math.round(new Date().getTime() / 1000))-dt.ago)*1000)+' '+translate.AAS_TEXT_AGO);
          jthis.data('agostatus',1);
        
        }else{
            
          span.html(dt.celldata);
          jthis.data('agostatus',0);
        
        }
    
    });
  
  });
	
  //DIALOGS DOUBLE CLICK ON HEADER-TITLE
	$( ".ui-dialog" ).on('dblclick','.ui-dialog-titlebar',function(e){
    
    var d=$(this).next(),did=d.attr('id'),offset=d.outerWidth()-e.offsetX;
  
    d.dialog("option", "width", $(window).width()-offset);
    d.dialog("option", "position", 'center');
    
  });
 
});
