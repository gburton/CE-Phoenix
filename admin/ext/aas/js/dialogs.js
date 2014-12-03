/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  File information: constucts dialogs
  
*/

$(function() {

	$.widget( "ui.dialog", $.ui.dialog, {
		_allowInteraction: function( event ) {
			return !!$( event.target ).closest( ".other-popups" ).length || this._super( event );
		}
		
	});
	
	$.extend($.ui.dialog.prototype.options, {
		autoOpen: false,
		show: "fade",
		hide: "fade",
		height: 'auto',
		width: 'auto',
		modal: true,
		autoResize:true
	});

  $( ".dialog" ).on( "dialogdragstart", function( event, ui ) {
  
    $(event.currentTarget.offsetParent).addClass('ui-dialog-moving');
  
  }).on( "dialogdragstop", function( event, ui ) {
  
    $(event.currentTarget.offsetParent).removeClass('ui-dialog-moving');
  
  });
  
  //DIALOGS
  
	$( "#dialog-unique-id-wrapper" ).dialog({
		buttons: [{
		    id: "btn-unique-id-wrapper-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}	
		}],
		open: function(event, ui) {
			overlayBackGroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#050');	
		}, beforeClose: function(){
			$('.ui-widget-overlay').css('background', 'black');
		}
	});	

	$( "#dialog-error-unique-id-wrapper-not-found" ).dialog({
		buttons: [{
      id: "btn-error-unique-id-wrapper-not-found-close",
      text: translate.button.close,
      click: function(){$( this ).dialog( "close" );}
		}],
		open: function(event, ui) {

			var data=$(this).data('data');
			
			$('#uidwe').html(data.wrapperUID);
			overlayBackGroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#500');
	
		}, beforeClose: function(){
				$('.ui-widget-overlay').css('background', overlayBackGroundNormal);
		}
	});

	var attrs_counter=1;
	$( "#dialog-attributes" ).dialog({
		buttons: [

		{
		   id: "btn-attributes-add-new",
		    text: translate.button.add_new_attribute,
		    click: function(){
		    
		   	  var data=$( "#dialog-attributes" ).data('data');
			    var lid=data.lid ? data.lid : config.language_id;

			    //check to see if we have js cache
			    if(atts_add_new_attribute[lid] && atts_add_new_attribute[lid]!=''){
			      
            $('#attributes-table tbody').append(atts_add_new_attribute[lid]);
            attrs_counter++;
			      
			    }else{
			    
			      aas.dialog.open('dialog-processing');
			      aas.ajax.do({data:{item: 'attributes-add-new', product_id:attrs_counter,lid:lid},url:config.url.actions},function(msg){
			
				      $('#attributes-table tbody').append(msg);
				      aas.dialog.close('dialog-processing');
				      atts_add_new_attribute[lid]=msg;
				      attrs_counter++;
		
			      });
			      
			    }

			    $(this).animate({scrollTop: $('#attributes-table').prop('scrollHeight')},100);

		    }

		},
		{
		    id: "btn-attributes-submit-changes",
		    text: translate.button.submit_changes,
		    click: function(){
		    
		    //start processing-dialog
		    aas.dialog.open('dialog-processing');
		    
				var attr_obj=[],attr_obj_new=[];
				var pref='+',pref_new='+';
        var safeToProceed=true;

				$('#attributes-table tbody tr').each(function(i,e){
					
					var temp_arr=[],temp_arr_new=[], jthis=$(this);
					
					if(jthis.find('.hidden_products_attributes_id').length>0){

						temp_arr.push(jthis.find('.hidden_products_attributes_id').val());
						temp_arr.push(jthis.find('.attributes_selectMenus_options option:selected').val());
						var aso=jthis.find('.attributes_selectMenus_values option:selected').val();
						if(aso==0) safeToProceed=false;
						temp_arr.push(aso);
						temp_arr.push(jthis.find('.value_price').val());
						
						pref=jthis.find('.price_prefix').val();
						switch(pref){
						
						  case'+': temp_arr.push('plus'); break;
						  case'-': temp_arr.push('minus'); break;
						  default: temp_arr.push('none');
						
						}
					
						if(config.download_enabled){//find the downloadable products data
						
							temp_arr.push(jthis.find('.downloadable_filename').val());
							temp_arr.push(jthis.find('.downloadable_maxdays').val());
							temp_arr.push(jthis.find('.downloadable_maxcount').val());
						
						}

						attr_obj[i]=temp_arr.join(';');
					
					}else{//find the new
			   	
						temp_arr_new.push(jthis.find('.attributes_selectMenus_options_new option:selected').val());
						var aso=jthis.find('.attributes_selectMenus_values_new option:selected').val();
						if(aso==0) safeToProceed=false;
						temp_arr_new.push(aso);
						
						temp_arr_new.push(jthis.find('.value_price_new').val());
						
						pref_new=jthis.find('.price_prefix_new').val();
						switch(pref_new){
						
						  case'+': temp_arr_new.push('plus'); break;
						  case'-': temp_arr_new.push('minus'); break;
						  default: temp_arr_new.push('none');
						
						}

						if(config.download_enabled){//find the downloadable products data
						
							temp_arr_new.push(jthis.find('.new_downloadable_filename').val());
							temp_arr_new.push(jthis.find('.new_downloadable_maxdays').val());
							temp_arr_new.push(jthis.find('.new_downloadable_maxcount').val());
						
						}

						attr_obj_new.push(temp_arr_new.join(';'));

					}
				});

				if(attr_obj.length<=0 && attr_obj_new.length<=0){
				
				  aas.dialog.close('dialog-processing');
				  aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_NO_ATTRIBUTES_FOUND);
				  return false;
				  
				}
				
				if(safeToProceed==false){
				
				  aas.dialog.close('dialog-processing');
				  aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_FOUND_OPTION_NAME_WITHOUT_OPTION_VALUES_ASSIGNED_TO_IT);
				  return false;
				
				}
				
				var dt=$(this).data('dt') || false;
				var stMode= dt.stMode || false;
				if(!stMode){
				
				  //first check to see if we have duplicates in existing attributes
				  var isUnique=true,sattrobj,tempArray=[];
				  for(var i=0,n=attr_obj.length;i<n;i++){
				  
				    sattrobj=attr_obj[i].split(';');
				    tempArray.push(sattrobj[1]+'_'+sattrobj[2]);
				  
				  }
				
				  isUnique=aas.array.isUnique(tempArray);
				  if(!isUnique){
				  
				    aas.dialog.close('dialog-processing');
				    aas.dialog.open('dialog-confirm',translate.AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_ON_EXISTING_ENTRIES,{extraData:'submit-attributtes',stMode:true});
				    return false;
				  
				  }
				
					//Secondly check to see if we have duplicates in new attributes
				  var isUniqueNew=true,sattrobjNew,tempArrayNew=[];
				  for(var i=0,n=attr_obj_new.length;i<n;i++){
				  
				    sattrobjNew=attr_obj_new[i].split(';');
				    tempArrayNew.push(sattrobjNew[0]+'_'+sattrobjNew[1]);
				  
				  }
				
				  isUniqueNew=aas.array.isUnique(tempArrayNew);
				  if(!isUniqueNew){
				  
				    aas.dialog.close('dialog-processing');
				    aas.dialog.open('dialog-confirm',translate.AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_ON_ABOUT_TO_ADD_ENTRIES,{extraData:'submit-attributtes',stMode:true});
				    return false;
				  
				  }

				  //Third check if existing and new is unique
          var tempArrayAll=tempArray.concat(tempArrayNew);
          var isUniqueAll=aas.array.isUnique(tempArrayAll);
          if(!isUniqueAll){

            aas.dialog.close('dialog-processing');
            aas.dialog.open('dialog-confirm',translate.AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_BETWEEN_EXISTING_AND_ABOUT_TO_ADD_ENTRIES,{extraData:'submit-attributtes',stMode:true});
            return false;

          }          
					
				}
				
				$(this).data({dt:{stMode:false}});
				
				var dt=$( "#dialog-attributes" ).data('data');
				//aas.dialog.open('dialog-processing');
				aas.ajax.do({data:'value='+attr_obj.join('_@_')+'&value1='+attr_obj_new.join('_@_')+'&id='+dt.pid+'&column=products_attributes',url:config.url.ajax},function(msg){
          
					if(msg=='ok_reload'){

						//reload attributes table
						//var data=$( "#dialog-attributes" ).data('data');
						var lid=dt.lid ? dt.lid : config.language_id;
						aas.ajax.do({data:{item: 'attributes', product_id:dt.pid,lid:lid},url:config.url.actions},function(msg){

							$('#dialog-attributes-options').html(msg);
							reCheckAttributes();
							colorizeAttributesRows();
							aas.dialog.close('dialog-processing');
							aas.dialog.open('dialog-success',translate.dialog_attributes_successfully_submited_attributes_changes);

						});
						
						updateProductsAttributesCell(attr_obj.length+attr_obj_new.length,dt.pid);

					}else if(msg=='ok'){

						//change .td_x1 to .td_x2
						var opt_names_list={};
						
						$("#dialog-attributes tbody tr").each(function(){
	
							var aso = $(this).children().find('.attributes_selectMenus_options').val();
							var td_aso=$(this).children().find('.value_price').parent().attr('class').substr(3);
		
							if(aso!=td_aso){
		
								$(this).children().find('.value_price').parent().attr('class','td_'+aso);
		
							}
							
							opt_names_list[aso]=$(this).children().find('.attributes_selectMenus_options').find(":selected").text();
	
						});
						
						//update select menu
						var lsopt=[];
						for(var i in opt_names_list) lsopt.push('<option name="'+opt_names_list[i]+'" value="'+i+'" >'+opt_names_list[i]+'</option>');
						
						$('#attributes_selectMenus_options-option-prices > option:gt(0)').remove();
						$('#attributes_selectMenus_options-option-prices').append(lsopt);
					
						aas.dialog.close('dialog-processing');
						aas.dialog.open('dialog-success',translate.dialog_attributes_successfully_submited_attributes_changes);
						
						updateProductsAttributesCell(attr_obj.length+attr_obj_new.length,dt.pid);

					}else{
					
						aas.dialog.close('dialog-processing');
						aas.dialog.open('dialog-error',translate.dialog_attributes_there_was_an_error);
					
					}

				});		                
		
		    }
	
		},
		
		{
		    id: "btn-attributes-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" ); aas.localStorage.remove('AAS:checkedAttributes');  }
		}
		
	   	],
		open: function(event, ui) {

			aas.dialog.open('dialog-processing');
			
			var dt=$( "#dialog-attributes" ).data('data');
			var lid=dt.lid ? dt.lid : config.language_id;
		
			$('#dialog-attributes-product-name').html(dt.productName);
      
			aas.ajax.do({data:{item:'attributes',product_id:dt.pid,lid:lid},url:config.url.actions},function(msg){
				
				aas.dialog.close('dialog-processing');
				
				$('#dialog-attributes-options').html(msg);
				colorizeAttributesRows();
				$( "#dialog-attributes" ).dialog("option", "position", 'center');
		
			});

		},
			
		close: function(){$('#dialog-attributes-options').html('');}
	});




	$( "#dialog-processing" ).dialog({
  	dialogClass: 'noDialogTitle',
		open: function(){
	  //overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		//$('.ui-widget-overlay').css('background', 'lightGray');
		},
		//beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

	$( "#dialog-massColumnsEdit" ).dialog({
		modal: false,
		buttons: [
			{
			    id: "btn-dialog-massColumnsEdit-close",
			    text: translate.button.close,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
			overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});


	$( "#dialog-export" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-export-apply",
			    text: translate.button.export,
			    click: function(){exportData();}
			},
			{
			    id: "btn-dialog-export-close",
			    text: translate.button.close,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
			overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

	$( "#dialog-massProductsDelete" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-massProductsDelete-yes",
			    text: translate.button.yes,
			    click: function(){ deleteProducts($(this).data('data')); }
			},
			{
			    id: "btn-dialog-massProductsDelete-no",
			    text: translate.button.no,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
			overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

	$( "#dialog-sessiontimeout" ).dialog({
		closeOnEscape: false,
		buttons: [
			{
			    id: "btn-dialog-sessiontimeout-login",
			    text: translate.button.login,
			    click: function(){
				
					window.location='login.php';
				
	    		}
			}
			],
		open: function(event, ui){
		
				$(".ui-dialog-titlebar-close").hide();
		 
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#F8FF7D');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

	$( "#dialog-confirm" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-confirm-yes",
			    text: translate.button.yes,
			    click: function(){
				
				    afterConfirm($( "#dialog-confirm" ).data('data'));
				
	    		}
			},
			{
			    id: "btn-dialog-confirm-no",
			    text: translate.button.no,
			    click: function(){
				    $( this ).dialog( "close" );
	    		}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	$( "#dialog-remove-linked-product-confirm" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-confirm-yes",
			    text: translate.button.yes,
			    click: function(){
				
				removeLinkedProduct($( this ).data('data'));
				
	    		}
			},
			{
			    id: "btn-dialog-confirm-no",
			    text: translate.button.no,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	$( "#dialog-remove-linked-product-from-parent-confirm" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-confirm-yes",
			    text: translate.button.yes,
			    click: function(){
				
				removeLinkedProductFromParent($( this ).data('data'));
				
	    		}
			},
			{
			    id: "btn-dialog-confirm-no",
			    text: translate.button.no,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});	
	$( "#dialog-general" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-general-close",
			    text: translate.button.close,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#fff');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	$( "#dialog-success" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-success-close",
			    text: translate.button.close,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');				
				$('.ui-widget-overlay').css('background','#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);
		},
		close: function(){}

	});
		
	$( "#dialog-warning" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-warning-close",
			    text: translate.button.close,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#E09B1B');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

		
	$( "#dialog-error" ).dialog({
		buttons: [
		{
		    text: translate.button.close,
		    click: function(){
			$( this ).dialog( "close" );
    			}
		}
		],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#800');
			},
			beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);}
	});
		
		
	$( "#dialog-ajaxFailed" ).dialog({
		buttons: [
		{
		    text: translate.button.close,
		    click: function(){
			$( this ).dialog( "close" );
    			}
		}
		],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#800');
			},
			beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);}
	});		
		
	$( "#dialog-information" ).dialog({
		buttons: [
		{
		    text: translate.button.close,
		    click: function(){
			$( this ).dialog( "close" );
    			}
		}
		],
		open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#008');
			},
			beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);}
	});


	$( "#dialog-massedit" ).dialog({
		modal:false,
		buttons: [
		 {
		    id: "btn-massedit-submit",
		    text: translate.button.submit_changes,
		    click: function(){

		       edify_proccess();
		       
		    }
		},
		 {
		    id: "btn-massedit-enable",
		    text: translate.button.enable,
		    click: function(){

		       edify(true);
		       $('#btn-massedit-enable').button("disable");
		      
		       
		    }
		},
		{
		    id: "btn-massedit-close",
		    text: translate.button.close,
		    click: function(){
		   
		       $( this ).dialog( "close" );
		       
		    }
		}
		
	   	],
		open: function(){
		
			$(this).closest('.ui-dialog').addClass('shadower');
		
			$('#btn-massedit-submit').button("disable");
		
			overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
			$('.ui-widget-overlay').css('background', '#808');
			
		},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){ 
		
			edify(false);
		
			$('#btn-massedit-submit').button("disable");
			$('#btn-massedit-enable').button("enable");
		
			$(this).closest('.ui-dialog').removeClass('shadower');
			
		}
	});

	// Settings Dialog
	$( "#dialog-settings" ).dialog({
		buttons: [	 
		{
		    id: "btn-settings-close",
		    text: translate.button.close,
		    click: function(){
		       $( this ).dialog( "close" );
		    }
		}
		
	  ],

		open: function(event, ui) {


		},
		close: function(){

		}
	});	
	// Settings Dialog

	$( "#dialog-specials" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-specials-apply",
			    text: translate.button.apply,
			    click: function(){
			    
			      var data=$(this).data('data');
			    
			      if(data.action=='edit') specialsEdit(data);
   			    if(data.action=='delete') specialsDelete(data);
			    
			    }
			},
			{
			    id: "btn-dialog-specials-delete",
			    text: translate.button.delete,
			    click: function(){
			    
			    	$( this ).dialog( "close" );
			    	var data=$(this).data('data');
					$('table#tbl_specials tr#sid_'+data.sid).find('.specials-delete-button').click();
			    
			    }
			},
			{
			    id: "btn-dialog-specials-cancel",
			    text: translate.button.cancel,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
		 

	 		overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		 
		 	var data=$(this).data('data');

		 	if(data.action=='delete'){
		 	
		 		$('#specials_edit_wrapper').hide();
		 		$('#specials_label_product').show();
		 		$('#btn-dialog-specials-apply span').text('Delete');
		 		$('#btn-dialog-specials-delete').hide();

				$('.ui-widget-overlay').css('background', '#FFD447');
		 	
		 	}else{
		 	
		 		$(this).children('.message').html('');
		 		$('#specials_edit_wrapper').show();
		 		$('#specials_label_product').show();
		 		$('#btn-dialog-specials-apply span').text('Apply');
		 		$('#btn-dialog-specials-delete').show();

				$('.ui-widget-overlay').css('background', '#555');
		 	
		 	}
		 	
		 	$('#specials_product_name').text(data.product_name);
		 	$('#specials_oldPrice').text(data.oldPrice);
		 	$('#specials-special-price-field').val(data.specialPrice_raw);
		 
			
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	$( "#dialog-specials-add" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-specials-add-add",
			    text: translate.button.add,
			    click: function(){
			    
			    	specialsAdd();
   			    
			    }
			},
			{
			    id: "btn-dialog-specials-cancel",
			    text: translate.button.cancel,
			    click: function(){$( this ).dialog( "close" );}
			}
			],
		 open: function(){
		 		
		 		var data=$(this).data('data');
		 		
		 		if(data.pid) spid=data.pid; else spid=0;
		 		aas.dialog.open('dialog-processing');
	 			aas.ajax.do({data:{action:'getProductsList',pid:spid},url:config.url.plugins+'specials/aas.php'},function(msg){
	 			  aas.dialog.close('dialog-processing');
	 				if(msg=='0'){
	 				
	 					aas.dialog.close('dialog-specials-add');
	 					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
	 				
	 				}
	 				else $('#specials_add_select_products-list').html(msg);
	 			
	 			});
	 			
		 		overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		 		$('.ui-widget-overlay').css('background', '#555');
		 		
			
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});

	
	$( "#dialog-change-image" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-change-image-large-images",
			    text: 'add/edit large Images',
			    click: function(){
			    
			    $(this).dialog( "close" );
			    
			    $('#large_images-product_name').html($('#dialog-change-image-product-name').html());
			   	
			    $('#large_images').fadeIn(function(){
			    
			    	aas.dialog.open('dialog-processing');
			    	aas.ajax.do({url:config.url.plugins+'large_images/aas.php'},function(msg){
			    	
			    	});
			    
			    });
			    
			    
			    }
			},
			{
			    id: "btn-dialog-change-image-cancel",
			    text: translate.button.cancel,
			    click: function(){$(this).dialog( "close" );}
			}
			],
		 open: function(){
		 		
		 		var data=$(this).data('data');
		 		$('#dialog-change-image-product-name').html(data.productName);
		 		 		
		 		overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		 		$('.ui-widget-overlay').css('background', '#666');
		 		
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	$( "#dialog-downloadable-products-manager" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-downloadable-products-manager-select",
			    text: translate.button.select,
			    click: function(){
			    
					var checked=$('input[name="dowloadable_filename"]:checked');
					if(checked.length > 0){
					
						var data=$(this).data('data');
						if(data.t) data.t.next().val(checked.val());
						$(this).dialog( "close" );
					
					}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_YOU_DID_NOT_SELECT_A_FILE);
			    
			    }
			},
			{
			    id: "btn-dialog-downloadable-products-manager-cancel",
			    text: translate.button.cancel,
			    click: function(){$(this).dialog( "close" );}
			}
			],
		 open: function(){
		 				 		 		
		 		overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		 		$('.ui-widget-overlay').css('background', '#666');
		 		
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	$( "#dialog-upload_module" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-upload_module-close",
			    text: translate.button.close,
			    click: function(){$(this).dialog( "close" );}
			}
			],
		 open: function(){
		 		
		 		$("#dialog-upload_module .tabContainer ul.tabContainerUl li a").removeClass("active");
				$("#dialog-upload_module .tabContainer ul.tabContainerUl li:first a").addClass("active").click();
		 		
		 		overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
		 		$('.ui-widget-overlay').css('background', '#666');
		 		
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	$( "#dialog-reload-page" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-reload-page-yes",
			    text: translate.button.yes,
			    click: function(){
				
					window.location.reload();
				
	    		}
			},
			{
			    id: "btn-dialog-reload-page-no",
			    text: translate.button.no,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#9EFFB0');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	
		$( "#dialog-confirm-largeImageRemoval" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-confirm-largeImageRemoval-yes",
			    text: translate.button.yes,
			    click: function(){
				
							product_images_deleteLargeImage($(this).data('data'));
				
	    		}
			},
			{
			    id: "btn-dialog-confirm-no",
			    text: translate.button.no,
			    click: function(){
				$( this ).dialog( "close" );
	    			}
			}
			],
		 open: function(){
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});
	
	
	$( "#dialog-aac" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-aac-apply",
			    text: translate.button.save_changes_for_selected_tab,
			    click: function(){
				
						var jthis=$(this),aac={},aTabId=jthis.find('.active').data('rel'),type=$('#'+aTabId).data('type');

            if(type=='default-dcd'){
            
              aas.dialog.open('dialog-processing');
              var dcd={};

						  $('#'+aTabId+' table tbody tr').each(function(index){
						  
						    dcd[$(this).data('column')]={
						      'v':$(this).find('input[name=default_columns_display]:checked').length,
						      'l':$(this).find('input[name=default_columns_display_lock]:checked').length
						    };

						  });
						  						  
						  aas.ajax.do({data:{item:'saveDcd',dcd:dcd,type:type},dataType:'json',url:config.url.actions},function(msg){
				   			aas.dialog.close('dialog-processing');
				   				if(msg=='0'){
				   					
				   					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
				   				
				   				}else aas.dialog.open('dialog-success',translate.AAS_AAC_TEXT_ADMIN_OPTIONS_SAVED);
				   			
				   		});
            
            
            }else if(type=='admins_columns_display'){
            
            	aas.dialog.open('dialog-processing');
						
						  var dcd={},ams=$('#'+aTabId+' input[name=dcd_hidden_adminId]').map(function(){
              
                return this.value;
              
              }).get();
              
              for(var i=0,n=ams.length;i<n;i++) dcd[ams[i]]={};
              
							$('#'+aTabId+' table tbody tr').each(function(index){
						                  
                dcd[$(this).data('adminid')][$(this).data('column')]={
                 'v':$(this).find('input[name=default_columns_display]:checked').length,
						     'l':$(this).find('input[name=default_columns_display_lock]:checked').length,
						     'o':$(this).find('input[name=default_columns_display_overide]:checked').length
						    };
						  	    
						  });
						
						  aas.ajax.do({data:{item:'saveDcd',dcd:dcd,type:type},dataType:'json',url:config.url.actions},function(msg){
				   			aas.dialog.close('dialog-processing');
				   				if(msg=='0'){
				   					
				   					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
				   				
				   				}else aas.dialog.open('dialog-success',translate.AAS_AAC_TEXT_ADMIN_OPTIONS_SAVED);
				   			
				   		});
            
            }else{

						//gather aac data
						var dataKey='';
						$('#'+aTabId+' .aac-adminsList').each(function(index){
								dataKey=$(this).data('key');
								aac[dataKey]={};
								
								$(this).find('li input').each(function(index){
					
									aac[dataKey][$(this).val()]= $(this).prop('checked') ? 1 : 0;
														
								});
						
						});

						aas.dialog.open('dialog-processing');
						aas.ajax.do({data:{item:'saveAac',aac:aac,type:type},url:config.url.actions},function(msg){
				 			aas.dialog.close('dialog-processing');
				 				if(msg=='0'){
				 					
				 					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
				 				
				 				}else aas.dialog.open('dialog-success',translate.AAS_DIALOG_AAC_TEXT_UPDATED);
				 			
				 			});
				 			
				 			
				 		}//END ELSE	

	    		}
			},
			{
			    id: "btn-dialog-aac-cancel",
			    text: translate.button.cancel,
			    click: function(){
						$( this ).dialog( "close" );
	    		}
			}
			],
		 open: function(){
		 
		 		var dt=$(this).data();
		 		
		 		$("#dialog-aac .tabContainer ul.tabContainerUl li a").removeClass("active");

				if (dt['data']) {
		 		
		 			if(dt['data'].tab=='modules') $("#dialog-aac #modulesTabLink").addClass("active").click();
		 			else  $("#dialog-aac .tabContainer ul.tabContainerUl li:first a").addClass("active").click();
		 		
		 		}else{
		 		
		 		$("#dialog-aac .tabContainer ul.tabContainerUl li:first a").addClass("active").click();
		 		
		 		}
		 
				//overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				//$('.ui-widget-overlay').css('background', '#9EFFB0');
			},
		beforeClose: function(){//$('.ui-widget-overlay').css('background', overlayBackgroundNormal);
		},
		close: function(){}

	});
	
	
	
	//DIALOG ATTRIBUTES VISUALIZE
	$("#dialog-attributes-visualizer").dialog({
	  height: $(window).height()-50,
	  width:'95%',
		buttons: [
			{
				  id: "btn-dialog-attributes-visualizer-print",
				  text: translate.button.print,
				  click: function(){
						attributes_visualizer_print();
		  		}
			},
			{
				  id: "btn-dialog-attributes-visualizer-close",
				  text: translate.button.close,
				  click: function(){
						$(this).dialog("close");
		  		}
			}

			],
		 open: function(){
		 //return false;
		 
		 	var jthis=$(this),data=jthis.data('data'),pid = data['pid'],lid = data['lid'];
		 	
	 		var orderBy=$('#product_attributes_orderBy').val();
      var ascDesc=$('#product_attributes_ascDesc').val();

    	aas.dialog.open('dialog-processing');
	
      aas.ajax.do({

	      data:{item:'attributes',ascDesc:ascDesc,orderBy:orderBy,product_id:pid,lid:lid,visualizer:'true'},
	      url:config.url.actions

      },function(msg){

	      jthis.find('.message').html(msg);
	      
	      jthis.find('#org').jOrgChart({
            chartElement : '#attributes-chart'
        });
	      aas.dialog.close('dialog-processing');
		
      });
		 	
			//overlayBackgroundNormala = $('.ui-widget-overlay').css('background');
			//$('.ui-widget-overlay').css('background', '#9EFFB0');
			},
		beforeClose: function(){
		//$('.ui-widget-overlay').css('background', overlayBackgroundNormala);
		},
		close: function(){}

	});

	//DIALOG ATTRIBUTES CLEVER COPY
	$("#dialog-attributes-clever-copy").dialog({
	  height: $(window).height()-150,
	  width:'90%',
		buttons: [
			{
				  id: "btn-dialog-attributes-clever-copy-aaa",
				  text: translate.button.add_available_attributes,
				  click: function(){

            aas.dialog.open('dialog-processing');

            var attr_obj_new=[];
				    var pref_new='+';
            var safeToProceed=true;

				    $('#attributes-available-to-add-table tbody tr').not('.unavailable').each(function(i,e){
					
					    var temp_arr_new=[], jthis=$(this);

						    temp_arr_new.push(jthis.find('.products_options_id').val());
						    var aso=jthis.find('.products_options_values_id').val();
						    if(aso==0) safeToProceed=false;
						    temp_arr_new.push(aso);
						
						    temp_arr_new.push(jthis.find('.value_price').val());
						
						    pref_new=jthis.find('.price_prefix').val();
						    switch(pref_new){
						
						      case'+': temp_arr_new.push('plus'); break;
						      case'-': temp_arr_new.push('minus'); break;
						      default: temp_arr_new.push('none');
						
						    }

						    if(config.download_enabled){//find the downloadable products data
						
							    temp_arr_new.push(jthis.find('.downloadable_filename').val());
							    temp_arr_new.push(jthis.find('.downloadable_maxdays').val());
							    temp_arr_new.push(jthis.find('.downloadable_maxcount').val());
						
						    }

						    attr_obj_new.push(temp_arr_new.join(';'));
					  
					  });
					  
					  if(attr_obj_new.length<=0){
				
				      aas.dialog.close('dialog-processing');
				      aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_NO_ATTRIBUTES_FOUND_FOR_SMART_COPY);
				      return false;
				  
				    }
				    
  			    var jthis=$(this),dt=jthis.data('data'),pid = dt['pid'],paid = dt['paid'];
  			    var lid=dt.lid ? dt.lid : config.language_id;

				    aas.ajax.do({data:'value='+attr_obj_new.join('_@_')+'&id='+pid+'&column=products_attributes_smart_copy',url:config.url.ajax},function(msg){
              
					    if(msg=='reload'){
					    						
						    //reload attributes smart copy table
						    var orderBy=$('#product_attributes_orderBy').val();
                var ascDesc=$('#product_attributes_ascDesc').val();

                var ajax_reload_smart_copy_attributes_display=aas.ajax.do({

	                data:{item:'attributes-clever-copy',ascDesc:ascDesc,orderBy:orderBy,paid:paid,lid:lid},
	                url:config.url.actions

                },function(msg){

	                jthis.find('.message').html(msg);
	                
	                $( "#attributes_smart_copy_accordion" ).accordion({
                    heightStyle: "content",
                    active: 1,
                    collapsible: true
                  });
		
                });
						    
						    //reload attributes table						
						    //var data=$( "#dialog-attributes" ).data('data');
						    
						    var ajax_reload_attributes_display=aas.ajax.do({data:{item: 'attributes', product_id:pid,lid:lid},url:config.url.actions},function(msg){

							    $('#dialog-attributes-options').html(msg);

						    });
						    
                $.when( ajax_reload_smart_copy_attributes_display, ajax_reload_attributes_display ).done(function( a1, a2 ) {

                  reCheckAttributes();
                  colorizeAttributesRows();
                  aas.dialog.close('dialog-processing');
                  aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SMART_COPY_SUCCESS_ADD);

                });
						    
						    var cvalue=parseInt($('table#tbl tbody.products_tbody tr#pid_'+pid+' td.attributesCell span').text());
						    updateProductsAttributesCell(cvalue+attr_obj_new.length,pid);

					    }else{
					
						    aas.dialog.close('dialog-processing');
						    aas.dialog.open('dialog-error',translate.dialog_attributes_there_was_an_error);
					
					    }

				    });

		  		}
			},
			{
				  id: "btn-dialog-attributes-clever-copy-close",
				  text: translate.button.close,
				  click: function(){
						$(this).dialog("close");
		  		}
			}

			],
		 open: function(){
	 
		 	var jthis=$(this),data=jthis.data('data'),paid=data['paid'],lid=data['lid'];
		 	
	 		var orderBy=$('#product_attributes_orderBy').val();
      var ascDesc=$('#product_attributes_ascDesc').val();

    	aas.dialog.open('dialog-processing');
	
      aas.ajax.do({

	      data:{item:'attributes-clever-copy',ascDesc:ascDesc,orderBy:orderBy,paid:paid,lid:lid},
	      url:config.url.actions

      },function(msg){

	      jthis.find('.message').html(msg);
	      
	      $( "#attributes_smart_copy_accordion" ).accordion({
          heightStyle: "content",
          active: 1,
          collapsible: true
        });
	      
	      aas.dialog.close('dialog-processing');
		
      });
		 	
			//overlayBackgroundNormala = $('.ui-widget-overlay').css('background');
			//$('.ui-widget-overlay').css('background', '#9EFFB0');
			},
		beforeClose: function(){
		//$('.ui-widget-overlay').css('background', overlayBackgroundNormala);
		},
		close: function(){}

	});

});
