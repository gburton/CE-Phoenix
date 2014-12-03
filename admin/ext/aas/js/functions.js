/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  File information: contains functions
  
*/

//super global object containing functions
var aas={

	ajax:{
	
		url:'',
		data:'',
		type:'POST',
		
		do:function(obj,onDone,onFail,onAlways){
		
			var obj_data={
			
				type:obj.type || this.type,
				url:obj.url || this.url,
				data:obj.data || this.data,
				dataType:obj.dataType || this.dataType,
				beforeSend:function(jqXHR, settings){
			
  			  jqXHR.setRequestHeader('X-AAS', config.ajaxToken);
			
			  }
						
			};
			
			var ajax_data = $.extend({}, obj, obj_data);

      //hackish method: if ajax is json then pass it also as data parameter so php can know it is ajax
			if(ajax_data.dataType=='json') ajax_data.data['dataType']='json';

			return $.ajax(ajax_data).done(function(msg){

			  var resp= ajax_data.dataType=='json' ? msg.response || '' : msg || '';
			  
		    if(resp=='aasSessionTimeout'){
		    
		      $("#dialog-sessiontimeout").dialog('open');
					return false;
		    
		    }else{
		    
		      if(typeof(onDone) == 'function') onDone(msg);
		    
		    }
						
			}).fail(function(jqXHR, textStatus){

				if(typeof(onFail) == 'function') onFail(textStatus);
				else{ 
				
					if($('#dialog-processing').dialog('isOpen'))  $('#dialog-processing').dialog('close');
					$("#dialog-ajaxFailed" ).dialog('open');
				
				}
				//return false;
			
			
			});//.always(function(a){
			//	onAlways(a);
			//});
		
		}
		
	},//ajax
	
	localStorage:{
	
		support: function(){
		
      try {
        return 'localStorage' in window && window['localStorage'] !== null;
      } catch(e){
        return false;
      }
		
		},
		save: function (key,value){localStorage.setItem(key, value);},
		get: function (key){return localStorage.getItem(key);},
		remove: function (key){localStorage.removeItem(key);},
		clear: function (event){localStorage.clear();}
	
	},//localStorage

	get:{
		classen:'checkboxMassActions',
		checked_products:function(classen){
			
			var classen = classen || this.classen;
		
			var arr=[];
			$('.'+classen+':checked').each(function(index){
				arr.push(this.value);
			});
			return arr;		
		
		}
	
	},
	
	dialog:{
	
		open:function(id,text,data_obj,title){
		
			var text=text || '',title=title || '';
			
			if(id){
			
				var elem=$('#'+id);
				if(elem.length && !elem.dialog('isOpen')){
				
					if(!is_empty(data_obj)) elem.data('data',data_obj);
					if(text!='') elem.find('.message').html(text);
					
					if(id=='dialog-success'){

						if(config.displaySuccessAlertMessages) elem.dialog('open'); else return false;

					}else if(id=='dialog-error'){
					
						if(config.displayErrorAlertMessages) elem.dialog('open'); else return false;
					
					}else elem.dialog('open');
					
					
					if(title!='') elem.dialog('option', 'title', title);
					
				}else alert(translate.cannot_open_dialog);
					
			}else alert(translate.cannot_open_dialog);
				
		},
		
		isOpen:function(id){
		
  			var elem=$('#'+id);
  			if(elem.length){
  			
  			  return elem.dialog('isOpen') ? true : false;
  			
  			}else return false;
				
		},
		
		close:function(id,clearText){
		
			if(id){
			
				var elem=$('#'+id);
				if(elem.length){
				
					if(elem.dialog('isOpen')){
					  elem.dialog('close');
  					if(clearText==true) elem.find('.message').html('');
					}
					
				}else alert(translate.cannot_close_dialog);
					
			}else alert(translate.cannot_close_dialog);
			
		}
	
	},
	cookie:{
		
		are_enabled:navigator.cookieEnabled,
		//alias
		set:function(name,value,days){
		
			this.create(name,value,days);
		
		},
		create:function(name,value,days){
			
			if(this.are_enabled){
				
				  if(days){
				
				    var date = new Date();
				    date.setTime(date.getTime()+(days*24*60*60*1000));
				    var expires = "; expires="+date.toGMTString();
				  }
				  else var expires = "";
				
				  document.cookie = name+"="+value+expires+"; path=/";
			  
			}
		
		},
		get:function(name){
			this.read(name);
		},
		read:function(name){
		
			if(this.are_enabled){
				 var nameEQ = name + "=";
				  var cache = document.cookie.split(';');
				  for(var i=0;i < cache.length;i++) {
				    var c = cache[i];
				    while (c.charAt(0)==' ') c = c.substring(1,c.length);
				    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
				  }
				  return null;
				  
			}else return false;
		
		},
		delete:function(name){
			this.remove(name);
		},
		remove:function(name){
			
			this.create(name, "", -1);
		
		},
		
		enabled:function(){
		
			var r = false;
			this.create("testing", "Hello", 1);
			if (this.read("testing") != null) {
			    r = true;
			    this.remove("testing");
			}
			
			this.are_enabled=r;
			
			return r;
		
		}
	
	
	},
	
	fullscreen:{
	
		RunPrefixMethod:function(obj, method){
		
			var pfx = ["webkit", "moz", "ms", "o", ""];
			var p = 0, m, t;
			while (p < pfx.length && !obj[m]) {
				m = method;
				if (pfx[p] == "") {
					m = m.substr(0,1).toLowerCase() + m.substr(1);
				}
				m = pfx[p] + m;
				t = typeof obj[m];
				if (t != "undefined") {
					pfx = [pfx[p]];
					return (t == "function" ? obj[m]() : obj[m]);
				}
				p++;
			}
		
		},
	
		toggle:function(id){
		
			var e = document.getElementById(id);
			if(this.RunPrefixMethod(document, "FullScreen") || this.RunPrefixMethod(document, "IsFullScreen")) this.RunPrefixMethod(document, "CancelFullScreen"); else this.RunPrefixMethod(e, "RequestFullScreen");
			
		},
		
		open:function(id){
		
			var e = document.getElementById(id);
			this.RunPrefixMethod(e, "RequestFullScreen");
		
		},
		
		close:function(){
		
			if(this.RunPrefixMethod(document, "FullScreen") || this.RunPrefixMethod(document, "IsFullScreen")) this.RunPrefixMethod(document, "CancelFullScreen");
		
		}
	
	},

	vars:{
	
	
	},
	
	array:{
	
	
	  isUnique:function(arr){
	  
      var map = {}, i, size;
      for (i = 0, size = arr.length; i < size; i++){
        
        if (map[arr[i]]){
           return false;
        }

        map[arr[i]] = true;
        
      }

      return true;
	  
	  }
	
	
	
	
	},
	
	format:{
		  
	  units : {
	  	year:[31536000,translate.AAS_TEXT_YEAR,translate.AAS_TEXT_YEARS],
			month:[2628000,translate.AAS_TEXT_MONTH,translate.AAS_TEXT_MONTHS],
			week:[604800,translate.AAS_TEXT_WEEK,translate.AAS_TEXT_WEEKS],
			day:[86400,translate.AAS_TEXT_DAY,translate.AAS_TEXT_DAYS],
			hour:[3600,translate.AAS_TEXT_HOUR,translate.AAS_TEXT_HOURS],
			min:[60,translate.AAS_TEXT_MINUTE,translate.AAS_TEXT_MINUTES],
			sec:[1,translate.AAS_TEXT_SECOND,translate.AAS_TEXT_SECONDS]
	
		},
	  
	  seconds:function(secs,start,end){

	    var strs=[];
	    secs=secs/1000;

	    for(var i in this.units){

		    if(secs < this.units[i][0] && i!='sec') continue;
		    num = parseInt(secs / this.units[i][0]);
		    secs = secs % (this.units[i][0]);

		    if(num == 1) strs.push(num+' '+this.units[i][1]);
		    else strs.push(num+' '+this.units[i][2]);

	    }
	    return strs.slice(start||0,end||3).join(', ');
	  
	  }
	
	}
	

};


/* TODO
var jjb=null;
function session_checker(){

	$.ajax({url: 'ext/aas/session_checker.php'}).done(function(msg){
			
		if(msg=='1'){
			
			clearTimeout(jjb);
			aas.dialog.close('dialog-sessiontimeout');
		
		}else{
	
			jjb=setTimeout(function(){session_checker()}, 1000);
		
		}
						
	})
}
*/


// Speed up calls to hasOwnProperty
var hasOwnProperty = Object.prototype.hasOwnProperty;

function is_empty(obj) {

    // null and undefined are empty
    if (obj == null) return true;
    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length && obj.length > 0) return false;
    if (obj.length === 0) return true;

    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    // Doesn't handle toString and toValue enumeration bugs in IE < 9

    return true;
}


function toolBoxAction(){


	var mcl = $('#move-copy-link-selectMenu').val();
	var cl = $('#categories-list-selectMenu').val();
	var post_action = $('#after-action-list-selectMenu').val();

	var arr=aas.get.checked_products();
	if(arr.length>0){
	
		switch(mcl){
			    	
			case'1':
			    	
				if(config.categoryId==cl){
			
					aas.dialog.open('dialog-error',translate.cannot_move_selected_products);
					return false;
	
				}
			break;
	
		}

		aas.dialog.open('dialog-processing');
	
		aas.ajax.do({data:'pids='+arr+'&item=multiple_products_manager&mcl='+mcl+'&cl='+cl+'&cid='+config.categoryId,url:config.url.actions},function(msg){
	
		 	switch(mcl){
			    	
			    		case'1':
			    		
				    		if(post_action=='2'){
				    		
				    			window.location.reload();
				    		
				    		}else if(post_action=='3'){
				    		
				    			window.location.href=config.url.aas+'?cPath='+cl;
				    		
				    		}else{
				    			
				    			var moved=msg.split(',');
				    	
					    		if(moved.length>0){
					    		
					    			for(var i=0,n=moved.length;i<n;i++) $('table#tbl tbody.products_tbody tr#pid_'+moved[i]).remove();
					    			
					    			aas.dialog.close('dialog-processing');
					    			aas.dialog.open('dialog-success',translate.selected_products_moved_to_selected_category);
					    		
					    		}else{
					    		
					    			aas.dialog.close('dialog-processing');
					    			aas.dialog.open('dialog-error',translate.selected_products_not_moved_to_selected_category);
					    		
					    		}
				    		}
				    	
				    	break;
			    		case'2':
			    		
			    			if(post_action=='2'){
				    		
				    			window.location.reload();
				    		
				    		}else if(post_action=='3'){
				    		
				    			window.location.href=config.url.aas+'?cPath='+cl;
				    		
				    		}else{
				    		
				    			var copied=msg.split(',');
					    	
					    		if(copied.length>0){
					    		
					    			for(var i=0,n=copied.length;i<n;i++) $('table#tbl tbody.products_tbody tr#pid_'+copied[i]).removeClass('tr_selected').find('.checkboxMassActions').prop('checked', false);
					    			aas.dialog.close('dialog-processing');
					    			aas.dialog.open('dialog-success',translate.selected_products_copied_to_selected_category);
					    		
					    		}else{
								aas.dialog.close('dialog-processing');
					    			aas.dialog.open('dialog-error',translate.selected_products_not_copied_to_selected_category);
					    		}
				    		
				    		}
				    	
				    	break;
				    	case'3':
				    	
				    		if(post_action=='2'){
				    		
				    			window.location.reload();
				    		
				    		}else if(post_action=='3'){
				    		
				    			window.location.href=config.url.aas+'?cPath='+cl;
				    		
				    		}else{
				    						    							    			
				    			aas.dialog.close('dialog-processing');
				    			
				    			if(msg==1 || msg=='1' ) window.location.reload(); //aas.dialog.open('dialog-success',translate.AAS_TEXT_SELECTED_PRODUCTS_LINKED_SUCCESSFULLY);
				    			else if(msg=='0' || msg=='0') aas.dialog.open('dialog-error',translate.AAS_TEXT_SELECTED_PRODUCTS_LINKED_FAILED);
				    			else if(msg=='3' || msg=='3') aas.dialog.open('dialog-error',translate.AAS_TEXT_SELECTED_PRODUCTS_LINKED_ABORTED_NO_PRODUCTS_TO_LINK);
				    			else if(msg=='4' || msg=='4') aas.dialog.open('dialog-error',translate.AAS_TEXT_SELECTED_PRODUCTS_LINKED_FAILED_NOCOLUMN_LINKED);
				    			else ;
				    		
				    		}
				    	
				    	break;	    	
			    	
			    	}
	
		});
	

	}else{

		aas.dialog.open('dialog-error',translate.no_products_selected);

	}

}

function toolBoxAttributesAction(){

	//gather values

	var tas = [$('#toolbox-attributes-select-1').val(), $('#toolbox-attributes-select-2').val(),  $('#toolbox-attributes-select-3').val(), $('#toolbox-attributes-select-4').val(), $('#toolbox-attributes-select-5').val(),$('#toolbox-attributes-select-6').val(),$('#toolbox-attributes-select-7').val()];
	var arr=[];

  var action_selected= tas[0]==1 ? tas[2] : tas[5];

  switch(action_selected){

	  case'1': arr=aas.get.checked_products(); break;
	  case'2': arr=aas.get.checked_products('checkedTempProducts'); break;
	  case'3':
	
		  arr.push(0);

	  break;

  }
	
	var action= tas[0]==1 ? 'copy-attributes' : 'delete-attributes';

	if(arr.length>0){
	
		aas.dialog.open('dialog-processing');	
		aas.ajax.do({data:'pids='+arr+'&item='+action+'&tas='+tas+'&cid='+config.categoryId,url:config.url.actions},function(msg){
	    aas.dialog.close('dialog-processing');
	    	
	    if(tas[0]==1){//copy
	    	
	    	var resp=msg.split('-');
	    	var pids=resp[1] ? resp[1].split(',') : [];
    	
	      	if(resp[0]=='1'){

	      	  if(fields.attributes && pids.length>0){
	      	    var aelem,cpid;
	        	  for(var i=0,n=pids.length;i<n;i++){
	        	  
	        	    cpid=pids[i].split('=');
    	      	
    	      	  aelem=$('#attributes_trigger_'+cpid[0]);
	        	  
	        	    if(aelem.length){
	        	    
	        	      aelem.html(cpid[1]);
	        	      aelem.closest('td').removeClass('attributesCellLink_zero').addClass('attributesCellLink_non_zero');
	        	    
	        	    }
	        	  
              }
              
            }	      	
	      	  
	      	  aas.dialog.open('dialog-success',translate.attributes_have_been_copied);
	      	
	      	}else if(resp[0]=='2') aas.dialog.open('dialog-error',translate.selected_product_has_not_attributes_to_copy_from);
      		else if(resp[0]=='3') aas.dialog.open('dialog-error',translate.not_found_products_to_copy_attributes);
	      	else aas.dialog.open('dialog-error',translate.attributes_have_not_been_copied);
	    	
	    }else{//delete
	    	
	    	var resp=msg.split('-');
	    	var pids=resp[1] ? resp[1].split(',') : [];
	    	
	    	if(resp[0]=='1'){
	    	  aas.dialog.open('dialog-success',translate.attributes_have_been_deleted);
	    	
	    	  if(fields.attributes && pids.length>0){
	    	    var aelem;
	      	  for(var i=0,n=pids.length;i<n;i++){
	      	  
  	      	  aelem=$('#attributes_trigger_'+pids[i]);
	      	  
	      	    if(aelem.length){
	      	    
	      	      aelem.html('0');
	      	      aelem.closest('td').removeClass('attributesCellLink_non_zero').addClass('attributesCellLink_zero');
	      	    
	      	    }
	      	  


            }
            
          }
	    	
	    	}else if(resp[0]=='2') aas.dialog.open('dialog-error',translate.AAS_TEXT_NO_PRODUCTS_FOUND_TO_DELETE_THEIR_ATTRIBUTES);
      	else aas.dialog.open('dialog-error',translate.attributes_have_not_been_deleted);
	    	
	    }
		
		});
		
	}else aas.dialog.open('dialog-error',translate.no_products_selected);
	
}

function clsa_display_or_not(val){

	if(val.value=='3'){
	
		$('#toolbox-attributes-select-4,#toolbox-attributes-select-7').show();
	
	}else{  
	
		$('#toolbox-attributes-select-4,#toolbox-attributes-select-7').hide();
	
	}

}

function tas(val){

	if(val.value=='2'){ 

		$('#toolbox-attributes-select-2, #toolbox-attributes-select-3, #toolbox-attributes-select-5,#toolbox-attributes-select-4,#toolbox-attributes-select-7').hide();
		if($('#toolbox-attributes-select-6').val()=='3')$('#toolbox-attributes-select-4,#toolbox-attributes-select-7').show(); else $('#toolbox-attributes-select-4,#toolbox-attributes-select-7').hide();
		$('#toolbox-attributes-select-6').show();

	}else{

		$('#toolbox-attributes-select-2,#toolbox-attributes-select-3,#toolbox-attributes-select-5').show();
		if($('#toolbox-attributes-select-3').val()=='3')$('#toolbox-attributes-select-4,#toolbox-attributes-select-7').show(); else $('#toolbox-attributes-select-4,#toolbox-attributes-select-7').hide();
		$('#toolbox-attributes-select-6').hide();

	}


}

function reloadTempProductsList(){
		
		var pids=aas.get.checked_products('checkedTempProducts');	
		aas.localStorage.save('AAS:tempListCheckedProducts',pids);
		
		aas.dialog.open('dialog-processing');
		aas.ajax.do({data:'item=reloadTempProductsList',url:config.url.actions},function(msg){
	
		  $('#leftSidePanel-inwrapper').html(msg);
		    	
		  var leftSidePanel_cwidth=parseInt(aas.localStorage.get('AAS:leftSidePanel-width')) || 230;
		  $('#leftSidePanel .savedProducts_data').css({width:leftSidePanel_cwidth});
		  
		  //auto check any previously checked
		  var pids=aas.localStorage.get('AAS:tempListCheckedProducts').split(',') || [];

		  if(pids.length>0){
	  
  		  var tempar=[];
  		  for(i=0,n=pids.length;i<n;i++) tempar.push('#temp_list_elem_'+pids[i]);
  		  $(tempar.join(',')).closest('.savedProducts_data').find('.checkedTempProducts').prop('checked', true);

		  }
		  
		  aas.dialog.close('dialog-processing');

		});
	
}
			
function updateTempProductsList(){
	
	var arr=aas.get.checked_products();
	
	if(arr.length>0){
	
		var pids=aas.get.checked_products('checkedTempProducts');	
		aas.localStorage.save('AAS:tempListCheckedProducts',pids);
	
		aas.dialog.open('dialog-processing');
	
		aas.ajax.do({data:'pids='+arr+'&item=updateTempProductsList',url:config.url.actions},function(msg){
	
			aas.dialog.close('dialog-processing');
      $('#leftSidePanel-inwrapper').html(msg);

      var leftSidePanel_cwidth=parseInt(aas.localStorage.get('AAS:leftSidePanel-width')) || 230;
      $('#leftSidePanel .savedProducts_data').css({width:leftSidePanel_cwidth});

      aas.dialog.open('dialog-success',translate.selected_products_successfully_added_to_temp_list);

      //auto check any previously checked
      var pids=aas.localStorage.get('AAS:tempListCheckedProducts').split(',') || [];

      if(pids.length>0){

        var tempar=[];
        for(i=0,n=pids.length;i<n;i++) tempar.push('#temp_list_elem_'+pids[i]);
        $(tempar.join(',')).closest('.savedProducts_data').find('.checkedTempProducts').prop('checked', true);

      }

		});
	
	}else aas.dialog.open('dialog-error',translate.no_products_selected);
	
}

function removeProductFromTempList(elem){

	$_this=$(elem);
	var pid=elem.id.substr(7);
	
	aas.dialog.open('dialog-processing');
	aas.ajax.do({data:'pid='+pid+'&item=removeFromTempProductsList',url:config.url.actions},function(msg){
	
		$_this.closest('.savedProducts_data').remove();
	  $('#num_of_temp_products_list').text(msg);
	  if(msg=='0') $('#leftSidePanel-inwrapper').find('.temp-list-select-action').remove();
	  aas.dialog.close('dialog-processing');
	
	});
	
}

function removeProductsFromTempList(pids){
	
	if(pids.length>0){
	
		aas.dialog.open('dialog-processing');
		aas.ajax.do({data:'pids='+pids+'&item=multipleRemoveFromTempProductsList',url:config.url.actions},function(msg){
  	
  	var tempar=[];
  	for(var i=0, n=pids.length;i<n;i++) tempar.push('#remove-'+pids[i]);
  	$(tempar.join(',')).closest('.savedProducts_data').remove();
  	
  	$('#num_of_temp_products_list').text(msg);
  	if(msg=='0') $('#leftSidePanel-inwrapper').find('.temp-list-select-action').remove();
  	aas.dialog.close('dialog-processing');
		});
	
	}else aas.dialog.open('dialog-error',translate.no_products_selected);
	
}

function editInOtherLanguage(val,alias){

	var languages_id=val;
	var languages_alias=alias;
	var pid=$('#overlay_pid').val();

	aas.dialog.open('dialog-processing');
	aas.ajax.do({data:'product_id='+pid+'&item=fetchOtherLanguage&lid='+languages_id,url:config.url.actions},function(msg){	
  	aas.dialog.close('dialog-processing');
		if(msg=='0'){
		
			aas.dialog.open('dialog-error',translate.AAS_TEXT_CANNOT_FETCH_PRODUCTS_DESCRIPTION);	
		
		}else{
	
	
			$('#overlay-other-languages').append(msg);
		
			$('#iframias_'+languages_id).attr('src',config.product_info_path+pid+'&language='+languages_alias);
		
			var loaded_languages_ids=aas.localStorage.get('AAS:loaded_languages_ids');
			if(loaded_languages_ids){
			
				var lla_array=loaded_languages_ids.split(',');
				lla_array.push(languages_id);
				aas.localStorage.save('AAS:loaded_languages_ids',lla_array);
			
			}else aas.localStorage.save('AAS:loaded_languages_ids',languages_id);
			
			if(config.disabled_column_actions.attributes) $('.edit_products_attributes').hide();
			
			var pdeditor=$('#description_editor').data('editor');
			$('#description_editor_'+languages_id).data('editor',pdeditor);
			
			//load editor
			load_editor('description_editor_'+languages_id);
			
			var of=$('#overlayWindow .container').scrollTop()-50;

			$('#overlayWindow .container').animate({scrollTop:$('#overlayWindow #overlay-other-languages #overlay-fieldset-'+languages_id).position().top+of},500);
			
	}
	
	});

}

function getDescriptionData(id){

  var pdeditor=$('#'+id).data('editor');

	switch(pdeditor){
	
		case'tinymce': return tinyMCE.get(id).getContent(); break;
		case'ckeditor': return CKEDITOR.instances[id].getData(); break;
		default: return $('#'+id).val();
	
	}

}

function setDescriptionData(id,val){

	var pdeditor=$('#'+id).data('editor') || config.productsDescriptionEditor;
	
	switch(pdeditor){
	
		case'tinymce':
		//tinyMCE.get(id).setContent(val);
		//if(!tinyMCE.get(id)){ tinymce.remove('#'+id); load_editor(id,val); }
	  //tinyMCE.activeEditor.setContent(val);
		break;
		case'ckeditor': CKEDITOR.instances[id].setData(val); break;
		default: $('#'+id).val(val);
	
	}

}

function changeGlobalEditor(id){
  
  kill_editor(id);
  
  if(config.productsDescriptionEditor=='ckeditor') config.productsDescriptionEditor='tinymce';
  else if(config.productsDescriptionEditor=='tinymce')  config.productsDescriptionEditor='textarea';
  else config.productsDescriptionEditor='ckeditor';
  
  $('#'+id).data('editor',config.productsDescriptionEditor);
  
  load_editor(id);

}

function changeEditor(id){
  
  var odata=getDescriptionData(id);
  editorHtmlContent=odata;
  kill_editor(id);
  
  var pdeditor=$('#'+id).data('editor') || '';
  
  if(pdeditor=='ckeditor') pdeditor='tinymce';
  else if(pdeditor=='tinymce')  pdeditor='textarea';
  else pdeditor='ckeditor';
  
  $('#'+id).data('editor',pdeditor);  
  load_editor(id);
  
  setDescriptionData(id,odata);

}

function kill_editor(id){

  var pdeditor=$('#'+id).data('editor') || '';

	switch(pdeditor){
		case'ckeditor':
			var editor = CKEDITOR.instances[id];
			if(editor){ editor.destroy(true); }
		break;
		case'tinymce':
    //tinymce.execCommand('mceRemoveControl',false,id);
			tinymce.remove('#'+id);
		break;
		default:
			
	}

}

function load_editor(id){

  var pdeditor=$('#'+id).data('editor') || config.productsDescriptionEditor;
	
	switch(pdeditor){
			
		case'ckeditor':
		
			CKEDITOR.replace( id,{
		 	
		 		language: 'en',
				toolbar : [{ name: 'document', items : [ 'Source','-','DocProps','Preview','Print','-','Templates' ] },
				{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
				{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
				//{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },'/',
				{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
				{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
				{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
				{ name: 'insert', items : [ 'Image', 'Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
				{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
				{ name: 'colors', items : [ 'TextColor','BGColor' ] },
				//{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
				],
				enterMode : CKEDITOR.ENTER_BR
			});
		  
		  if(id.substring(0,18)==='description_editor'){//bind only to core description editors
		    CKEDITOR.instances[id].on('change', function(e) { 

		      liveEditorChangesPreview(this.getData(),id)

		    });
		  }

		break;
		case'tinymce':
		
			tinymce.init({
			    mode : "textareas",
			    force_br_newlines : false,
			    force_p_newlines : false,
			    forced_root_block : '',
			    selector: "#"+id,
			    plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
			    ],
			    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
			    
             setup: function (ed) {
             
                ed.on('change', function(e) {
           
                   if(ed.id.substring(0,18)==='description_editor') liveEditorChangesPreview(ed.getContent(),ed.id);
                   
                }).on('init', function(args) {

                    if(editorHtmlContent!=''){ tinyMCE.get(id).setContent(editorHtmlContent); editorHtmlContent=''; }
                    
                });
             }
			});
			
			tinymce.execCommand('mceFocus',false,"#"+id);
			//tinymce.get("#"+id).focus();
		
		break;
		default:

			if(id.substring(0,18)==='description_editor'){//bind only to core description editors
			  $("textarea#"+id).on("change keyup paste", function() {
            liveEditorChangesPreview($(this).val(),id);
        });
      }
		
	}

}

//EXPIREMENTAL
function liveEditorChangesPreview(data,id){

  var lid=id.substr(19);
  var elem= lid=='' ? $("#iframias") : $("#iframias_"+lid);
	var previewId=$('#previewid').val();

	if(elem.contents().find('#'+previewId).length){
		elem.contents().find('#'+previewId).html(data);
	}//else aas.dialog.open('dialog-error-unique-id-wrapper-not-found','',{wrapperUID:$('#previewid').val()});

}

function reloadDescriptionPreview(iframeId){

  $('#'+iframeId).attr("src", $('#'+iframeId).attr("src"));

}

function submitMainDescriptionChanges(){

	var description_data=encodeURIComponent(getDescriptionData('description_editor'));
	var languages_id=$('#overlay_lid').val();
	aas.ajax.do({data: 'value='+description_data+'&id='+$('#overlay_pid').val()+'&lid='+languages_id+'&column=products_description', url:config.url.ajax},function(msg){
	
		if(msg=='ok') aas.dialog.open('dialog-success',translate.AAS_DIALOGS_PHP_SUCCESS_SUBMITED_SUCCESSFULLY); else aas.dialog.open('dialog-error',msg);
		$('#iframias').attr("src", $('#iframias').attr("src"));
	
	});

}

function submitDescriptionChanges(pid,clid){

	var description_data= encodeURIComponent(getDescriptionData('description_editor_'+clid));			
	aas.ajax.do({data: 'value='+description_data+'&id='+pid+'&column=products_description&lid='+clid, url:config.url.ajax},function(msg){
	
		if(msg=='ok') aas.dialog.open('dialog-success',translate.AAS_DIALOGS_PHP_SUCCESS_SUBMITED_SUCCESSFULLY); else aas.dialog.open('dialog-error',msg);
		$('#iframias_'+clid).attr("src", $('#iframias_'+clid).attr("src"));
	
	});

}

//ALL EDIT FUNCTIONS
function edify(val){

	if(val){

		$('table#tbl tbody td').not('.nojedit').editable('disable');
		var column_excludes={'products_price_gross':1};

		$('table#tbl tbody tr td').not('.nojedit').each(function(index){
	
			var el=$(this);		
			if(!column_excludes[el.data('column')])	el.html('<input class="lfor nojedit" type="text" value="'+el.text()+'">');
	
		});
	
		 $('#btn-massedit-submit').button("enable");
	
	}else{
	
		$('table#tbl tbody tr td').not('.nojedit').each(function(index){
			
			var v=$(this).children('.lfor').val();
			$(this).html(v);
				
		});
		
		$('table#tbl tbody td').not('.nojedit').editable('enable');
	
	}

}

function edify_proccess(){

	aas.dialog.open('dialog-processing',translate.wait_while_updating_values);

	var column_excludes={'products_price_gross':1};

	var cat_rows={},pid_rows={};

	$('table#tbl tbody.categories_tbody tr.folder').each(function(index){
		
		var cat_rows_data=[];
		$(this).children('td').not('.nojedit').each(function(i){
		
			cat_rows_data.push({column:$(this).data('column'),value:$(this).children('.lfor').val()});
			
		});
		cat_rows[this.id.substr(4)]=cat_rows_data;
	
	});

	$('table#tbl tbody.products_tbody tr').each(function(index){
		
		var pid_rows_data=[];
		$(this).children('td').not('.nojedit').each(function(i){
		
			var column=$(this).data('column');
		
			if(!column_excludes[column]) pid_rows_data.push({column:column,value:$(this).children('.lfor').val()});
						
		});
		pid_rows[this.id.substr(4)]=pid_rows_data;
	
	});
	
	aas.ajax.do({data:'item=massedit&pid_values='+JSON.stringify(pid_rows)+'&cat_values='+JSON.stringify(cat_rows),url:config.url.actions},function(msg){
				
		if(msg=='1'){
		
			//updates gross prices
			if(fields.products_price && fields.products_price_gross ){
			
					$('table#tbl tbody.products_tbody tr').each(function(index){
		
						var pid=this.id.substr(4);
						$(this).children('td').not('.nojedit').each(function(i){
		
							//make changes to gross price
							if($(this).data('column')=='products_price'){
																	
								var val=$(this).children('.lfor').val();
		
								var $ppg=$('#products_price_gross_'+pid);
								$ppg.text(updateGross(val,$ppg.data('tax-rate')));
								$ppg.data('price-net',val);
	
							}
			
						});
	
					});
			
			}
			
			if(fields.last_modified){
			  
			  	$('table#tbl tr').each(function(index){
		
						updateLastModifiedCell(this.id.substr(4));
	
					});
			
			}
			
  		aas.dialog.close('dialog-processing',true);
			aas.dialog.open('dialog-success',translate.successfully_updated_values);
		
		}else{
		  aas.dialog.close('dialog-processing');
		  aas.dialog.open('dialog-error',translate.values_have_not_been_updated);
		}
		
	});

}
//ALL EDIT FUNCTIONS

function searchTable(inputVal){
	var table = $('#tbl');
	table.find('tr').each(function(index, row){

		var allCells = $(row).find('td');
		if(allCells.length > 0){
			var found = false;
			allCells.each(function(index, td){
				var regExp = new RegExp(inputVal, 'i');
				if(regExp.test($(td).text())){
					found = true;
					return false;
				}
			});
			if(found == true)$(row).show();else $(row).hide();
		}
	});
}

function searchList(inputVal){
	var list = $('#listFiles-fieldset').children();
	list.find('li').each(function(index, row){

		var allCells = $(row).find('label');

		if(allCells.length > 0){
			var found = false;
			allCells.each(function(index, td){
				var regExp = new RegExp(inputVal, 'i');
				if(regExp.test($(td).text())){
					found = true;
					return false;
				}
			});
			if(found == true)$(row).show();else $(row).hide();
		}
		
	});
}

clearSearchQuery = function(queryString){window.location.href=queryString;}

jumpToPage = function(queryString,val){window.location.href=queryString+val.value;}

function tempListActionSelect(elem){

	var optvalue=elem.value;

	switch(optvalue){
	
		case'1':
		
			$('#leftSidePanel-inwrapper .leftSidePanel-list .truncate').find('.checkedTempProducts').prop('checked', true);
		
		break;
		
		case'2':
		
			$('#leftSidePanel-inwrapper .leftSidePanel-list .truncate').find('.checkedTempProducts').prop('checked', false);
		
		break;
		
		case'3':

			removeProductsFromTempList(aas.get.checked_products('checkedTempProducts'));
		
		break;
		
		case'4': 
	
			$('#hidden-export-what-to-export').val('tempList');
			aas.dialog.open('dialog-export');
	
		break;
	
	}
	
	elem.value=0;

}

function exportData(){

	$('#exportForm').submit();

}

function massEditOptionPrices(){

	var amount= parseFloat($('#discount-field-option-prices').val());

	if(amount<0){
		aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_WRONG_AMOUNT);
		return false;
	}

	var applyTo= parseInt($('#attributes_selectMenus_options-option-prices').val());
	if(applyTo < -1){
		aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_APPLY_TO_SELECTION);
		return false;
	}

	var obj_ds={};
	
	if(applyTo==0){
	  var op_elem,aid;
		$('#attributes-table tbody tr').each(function(index){
	
			op_elem=$(this).children().find('.value_price');
			aid=op_elem.attr('id').substr(12);
			obj_ds[aid]=parseFloat(op_elem.val());
		
		});

  }else if(applyTo==-1){
  
    var aids=aas.get.checked_products('attributes_checkbox_selector'),elem;
    
    if(aids.length<=0){
    
   		aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_NO_SELECTED_ATTRIBUTES_FOUND);
      return false;
    
    }
    
    for(var i=0,n=aids.length;i<n;i++){
    
      elem=$('#aid_'+aids[i]);
      if(elem.length){

        obj_ds[aids[i]]=parseFloat(elem.children().find('.value_price').val());
      
      }
    
    }
  
	}else{
	
		$('#attributes-table tbody tr').each(function(index){
	
			var opelem=$(this).find('.td_'+applyTo);
			if(opelem.length){
				var inputElem=opelem.find('.value_price');
				var aid=inputElem.attr('id').substr(12);
				obj_ds[aid]=parseFloat(inputElem.val());
			
			}
		
		});
	
	}
		
	var sd= $('#select-discount-option-prices').val();

	var timi;
	for(var i in obj_ds){

		switch(sd){

			case '=': timi =amount.toFixed( 4 ); break;
			case '-%': timi =(obj_ds[i]-(obj_ds[i]*amount/100)).toFixed( 4 ); break;
			case '+%': timi =(obj_ds[i]+(obj_ds[i]*amount/100)).toFixed( 4 ); break;
			case '-': timi =(obj_ds[i]-amount).toFixed( 4 ); break;
			case '+': timi =(obj_ds[i]+amount).toFixed( 4 ); break;

		}

		obj_ds[i]=timi;

	}
	
	aas.dialog.open('dialog-processing');
	
	aas.ajax.do({
	
		data:{value:JSON.stringify(obj_ds),item:'mass_edit_option_prices',applyTo:applyTo},
		url:config.url.actions,
		dataType:'json'
		
	},function(msg){
	
		aas.dialog.close('dialog-processing');
		
		if(msg=='1'){

			for(var i in obj_ds) $('#value_price_'+i).val(obj_ds[i]);
			aas.dialog.open('dialog-success',translate.AAS_DIALOG_TEXT_SUCCESSFULLY_ALTERED_OPTION_PRICES);
			
		}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_COULD_NOT_ALTER_OPTION_PRICES);
		
	});
	
}

function deleteProducts(data){

	productsIds=data.productsIdsArray;

	aas.dialog.open('dialog-processing',translate.dialog_delete_products_deleting);
	
	aas.ajax.do({data:  { item: 'deleteProducts', pids: productsIds  },url:config.url.actions},function(msg){
	
		aas.dialog.close('dialog-massProductsDelete');
		aas.dialog.close('dialog-processing');
		
		if(msg=='1'){

			aas.dialog.open('dialog-success',translate.dialog_delete_products_successfully_deleted);

			$('#tbl tr.tr_selected').remove();
			
			//hide delete icon
			$('#deletebutton').hide();

		}else aas.dialog.open('dialog-error',translate.dialog_delete_products_something_went_wrong);
	
	});

}

function afterConfirm(data){

	var dt=data.extraData.split('_@_');

	if(dt.length==1){
	
		if(data.ed && dt[0]=='logoff') window.location=data.ed;
		if(dt[0]=='submit-attributtes'){
		
		  $('#dialog-attributes').data({dt:{stMode:true}});
		  $('#btn-attributes-submit-changes').click();
		  aas.dialog.close('dialog-confirm');
		
		}
	
	}else{

    aas.dialog.open('dialog-processing');
    
		aas.ajax.do({data:'value='+dt[1]+'&id='+dt[0]+'&column='+dt[2],url:config.url.ajax},function(msg){
      
			aas.dialog.close('dialog-confirm');
		
			if(msg=='reload_attributes'){
		
				aas.ajax.do({data:{ item: 'attributes', product_id:dt[0]  },url:config.url.actions},function(msg){
			  
			    aas.dialog.close('dialog-processing');
					$('#dialog-attributes-options').html(msg);
					reCheckAttributes();
					aas.localStorage.save('AAS:checkedAttributes',aas.get.checked_products('attributes_checkbox_selector'));
					aas.dialog.open('dialog-success',translate.dialog_attributes_successfully_deleted_attribute);
				
				});
				
				var mnum=dt[2]=='delete_attributes' ? -dt[1].split(',').length : -1;			
				updateProductsAttributesCell(mnum,dt[0]);
			
			}else{
			
			  aas.dialog.close('dialog-processing');
				aas.dialog.open('dialog-error',translate.dialog_attributes_something_went_wrong);
			}

	
		}); 
	
	}

}

function selectMenuChange(vall,column){

	var j=$('#'+vall.id+' option:selected');
	var pid=vall.id.substr(5);
	var val=j.val();


	if(column=='products_tax_class_id'){

	var tval=val.split('_');

	val=tval[0];
	var taxRate=tval[1];

	}
  
  aas.dialog.open('dialog-processing');
	aas.ajax.do({data:'value='+val+'&id='+pid+'&column='+column,url:config.url.ajax},function(msg){
  
    aas.dialog.close('dialog-processing');
	
		if(column=='products_tax_class_id' && fields.products_price_gross && msg.substr(0,2)=='1_' ){
			//update gross value
			$ppg=$('#products_price_gross_'+pid);
			$ppg.text(updateGross($ppg.data('price-net'),taxRate));
			$ppg.data('tax-rate',taxRate);
		}
		
		if(column=='products_tax_class_id' && fields.special && msg.substr(0,2)=='1_' ){
		
			updateSpecialsCell(pid);
		
		}

		if(msg.substr(0,2)=='1_') aas.dialog.open('dialog-success',msg.substr(2)); else aas.dialog.open('dialog-error',msg);
		

	});
	
}

function settings(valaoritis,action){

	aas.ajax.do({data: 'value='+valaoritis+'&column=set_session&action='+action,url: config.url.ajax},function(msg){
	
		if(msg=='1'){
		
			switch(action){

				case'show_success_alert_messages':

					config.displaySuccessAlertMessages=parseInt(valaoritis);
					aas.dialog.open('dialog-success',translate.successfully_changed_setting);
					
				break;

				case'show_error_alert_messages':

					config.displayErrorAlertMessages=parseInt(valaoritis);
					aas.dialog.open('dialog-success',translate.successfully_changed_setting);
					
				break;
				
				case'enable_column_sorting':
					
					aas.dialog.open('dialog-warning',translate.in_order_to_change_setting_reload_page+'<div class="clear margin-20-auto"></div><input class="applyButton" type="button" value="'+translate.reload_now+'" onClick="window.location.reload()" />');
					
				break;

			}
		
		}else aas.dialog.open('dialog-error',translate.dialog_session_timeout_something_went_wrong);
		
	});

}


function ats_option_name_change(jthis){

	var elem=jQuery(jthis);
	
	var ovs=elem.parent().next().children('.attributes_selectMenus_values'),newAtt=false;
	if(!ovs.length){ ovs=elem.parent().next().children('.attributes_selectMenus_values_new'); newAtt=true; }

	var option_id=jthis.value;
  var dt=$( "#dialog-attributes" ).data('data');
  var lid=dt.lid ? dt.lid : config.language_id;
		

	if(atts_cache_specific[option_id+'_'+lid]){
	
	  ovs.html(atts_cache_specific[option_id+'_'+lid]);
	  if(!newAtt) colorizeAttributesRows();
	
	}else{
	
			aas.dialog.open('dialog-processing');
			
			//make ajax call
			aas.ajax.do({data:{item:'attributes-quick-load-assigned',option_id:option_id,lid:lid},url:config.url.actions},function(msg){
			
				aas.dialog.close('dialog-processing');
				
				if(msg!='0'){
				
					if(msg!=''){
			
						//cache existing options
						atts_cache[option_id]=ovs.html();
						ovs.html(msg);
						atts_cache_specific[option_id+'_'+lid]=msg;
						if(!newAtt) colorizeAttributesRows();
					
					}else{
					
						aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_OPTION_NAME_DONT_HAVE_OPTION_VALUES_ASSIGNED);
						ovs.html('<option value="0"></option>');
						if(!newAtt) colorizeAttributesRows();
					
					}
				
				}else{
			
					aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
			
				}
			
			});
	
	}	

}

function toolbox_reset_height(){

	$('#toolBox').css({height:125});
	aas.localStorage.save('AAS:toolBox-height',125);
	$('#toolBox').animate({height:'125px'},1000);

}

function reset_columns_order(){

  aas.dialog.open('dialog-processing');
	aas.ajax.do({data:'item=resetColumnsOrder',url:config.url.actions},function(msg){
  
    aas.dialog.close('dialog-processing');

		if(msg=='1') aas.dialog.open('dialog-success',translate.AAS_SETTINGS_RESET_COLUMNS_ORDER_SUCCESS); 
		else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN);
		
	});

}

function delete_attribute(val){$val=val;}

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function updateGross(grossValue,taxRate) {

  if (taxRate > 0) {
    grossValue = grossValue * ((taxRate / 100) + 1);
  }

 return doRound(grossValue, 4);
 
}

function updateNet(netValue,taxRate) {

  if (taxRate > 0) {
    netValue = netValue / ((taxRate / 100) + 1);
    
  }

 return doRound(netValue, 4);
 
}

function nav_links_construct(catid,nav_cache){

	var htmla='';
	if(nav_cache[catid]){
	
		htmla='<ul>';

	 	for(var i in nav_cache[catid]){

	 		if(nav_cache[catid][i]['cid']==config.categoryId) htmla+='<li><span>'+nav_cache[catid][i]['cname']+'<span></li>';
	 		else htmla+='<li><a href="'+config.url.aas_link+'?cPath='+nav_cache[catid][i]['cid']+'">'+nav_cache[catid][i]['cname']+'<a></li>';
	 	
	 	}
	 	htmla+='</ul>';
	 	
	}
	return htmla;

}

function load_module(val,ext){

	if(ext){

		$('#'+val).fadeIn();
		loadfunction(val+'_loader');
	
	}else{

		if(val.value=='upload_module'){
		
			aas.dialog.open('dialog-upload_module');
		
			val.value="";
		
		}else{
			
			$('#'+val.value).fadeIn();
			loadfunction(val.value+'_loader');
			val.value="";
		
		}
	}
	
}

function loadfunction(func){
  hidePreviewPagePanel();//hide preview panel if visible
  toggleBodyVerticalScrollbar();
  this[func].apply(this, Array.prototype.slice.call(arguments, 1));
}

function toggleBodyVerticalScrollbar(flag){

  var f=flag||2,body=$('body'),overflow = body.css('overflow-y');
  
  if(f==1) body.css('overflow-y','visible');
	else if(f==0) body.css('overflow-y','hidden');
	else{
	
	if(overflow=='visible') body.css('overflow-y','hidden'); else body.css('overflow-y','visible');
	
	}

}

function sort_product_attributes(val){

	var orderBy=$('#product_attributes_orderBy').val();
	var ascDesc=$('#product_attributes_ascDesc').val();

	var data=$( "#dialog-attributes" ).data('data');
	var lid=data.lid || config.language_id;

	aas.dialog.open('dialog-processing');
	
	aas.ajax.do({
	
		data:{item:'attributes',ascDesc:ascDesc,orderBy:orderBy,product_id:data.pid,lid:lid},
		url:config.url.actions
	
	},function(msg){

		$('#dialog-attributes-options').html(msg);
		reCheckAttributes();
    colorizeAttributesRows();
		aas.dialog.close('dialog-processing');
			
	});

}

function removeLinkedProduct(dt){

	aas.dialog.close('dialog-remove-linked-product-confirm');	
	aas.dialog.open('dialog-processing');
	aas.ajax.do({
	
		data:{item:'removeLinkedProduct',cid:dt.cid,product_id:dt.pid},
		url:config.url.actions
	
	},function(msg){

		aas.dialog.close('dialog-processing');
		
		if(msg=='1'){
		
			//remove row from table
			$('table#tbl tr#pid_'+dt.pid).remove();
		
			aas.dialog.open('dialog-success',translate.AAS_DIALOG_TEXT_LINKED_PRODUCT_SUCCESSFULLY_REMOVED);

		}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_LINKED_PRODUCT_NOT_REMOVED);
		
	});

}

function removeLinkedProductFromParent(dt){

	aas.dialog.close('dialog-remove-linked-product-from-parent-confirm');	
	aas.dialog.open('dialog-processing');
	aas.ajax.do({
	
		data:{item:'removeLinkedProduct',cid:dt.cid,product_id:dt.pid},
		url:config.url.actions
	
	},function(msg){

		aas.dialog.close('dialog-processing');
		
		if(msg=='1'){
		
			$('#products_linked_'+dt.pid).html(translate.AAS_TEXT_NOT_LINKED);
		
			aas.dialog.open('dialog-success',translate.AAS_DIALOG_TEXT_LINKED_PRODUCT_SUCCESSFULLY_REMOVED);

		}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_TEXT_LINKED_PRODUCT_NOT_REMOVED);
		
	});

}


function tbl_fix_tableHeadCells(){

    $('#tbl thead tr:eq(0)').children().each(function(){
		
		  $(this).width($(this).width()+1);
					
		});
		
    $('#tbl_thead_helper table').html($('#tbl thead').clone());
    //mass checkbox fix
    $('#tbl_thead_helper table').find('#massCheckbox').attr('id','massCheckbox_old').attr('disabled',true);
    
    $('#tbl0 thead').css({width:$('#tbl').outerWidth(),left:$('#tbl').offset().left});
    tbl_top=$('#tbl').position().top;

}

function displayDropDown(jthis){

  if(jthis.next('.dropdown')){
  
    if(jthis.next('.dropdown').is(':visible')) jthis.next('.dropdown').hide();
    else jthis.next('.dropdown').css({left:jthis.offset().left,top:jthis.offset().top+jthis.outerHeight()-$(window).scrollTop()}).show();
  
  }
     
}

function printHtmlElement(flag,val){

  if(flag==0){
  
    window.print();
    return false;
    
  }

	$('#print_frame').remove();
	var iframe = $('<iframe id="print_frame" src="about:blank" width="100px" height="100px" style="position: absolute;top: -1000px; z-index:-10;" >');  // display:block needed for IE
	$('body').append(iframe);
	var frameWindow = iframe[0].contentWindow;
	frameWindow.document.write('<html><head></head><body></body></html>');
	frameWindow.document.close();

	var cssElems = $('link,style');
	for(var i=0;i<cssElems.length;i++){
		var head = frameWindow.document.getElementsByTagName('head')[0];
		if(frameWindow.document.importNode) head.appendChild( frameWindow.document.importNode(cssElems[i],true) );
		else head.appendChild( cssElems[i].cloneNode(true) );
	}

  switch(flag){
  
    case 1:
    
      $("#print_frame").contents().find('body').html($('#tbl-wrapper').html());
  
    break;
    
    case 2:
    
    $("#print_frame").contents().find('body').html($('#tbl-wrapper').html());
    $("#print_frame").contents().find('#tbl tbody.products_tbody').remove();
    
    break;
  
    case 3:
    
    $("#print_frame").contents().find('body').html($('#tbl-wrapper').html());
    $("#print_frame").contents().find('#tbl tbody.categories_tbody').remove();
    
    break;
  
  }
  var input_next=$(val).next().find('input');
  if(input_next.length && !input_next.is(':checked')){
      
    $("#print_frame").contents().find('#pagination').remove();
      
  }
  
  if(fields.special){//remove some icons form printed doc when special column is visible
    $("#print_frame").contents().find('.add-selected-product-as-special').remove();
    $("#print_frame").contents().find('.edit-selected-product-as-special').remove();
    $("#print_frame").contents().find('.specials-unexpire').remove();
  }
  
  if(fields.last_modified || fields.date_added) $("#print_frame").contents().find('.edit_date_added').remove();
  

	setTimeout(function(){
		if (frameWindow.focus) frameWindow.focus(); // focus is needed for IE
		frameWindow.print();
	},1);

  return false;

}

function hidePreviewPagePanel(){

    $('table').find('.previewPage').removeClass('previewOpen');
    $('#previewPageVelakiWrapper').hide();

}

function updateProductsAttributesCell(value,pid){

  if(value<0){
  
    var cvalue=parseInt($('table#tbl tbody.products_tbody tr#pid_'+pid+' td.attributesCell span').text());
    value=cvalue+value;
  
  }
   
  $('table#tbl tbody.products_tbody tr#pid_'+pid+' td.attributesCell span').text(value);
  
  if(value<=0) $('table#tbl tbody.products_tbody tr#pid_'+pid+' td.attributesCell').removeClass('attributesCellLink_non_zero').addClass('attributesCellLink_zero');
  else $('table#tbl tbody.products_tbody tr#pid_'+pid+' td.attributesCell').removeClass('attributesCellLink_zero').addClass('attributesCellLink_non_zero');

}

function clearAttsCache(){

  atts_cache={};
  atts_cache_specific={};
  atts_option_values_cache={};
  atts_all_option_values='';
  atts_add_new_attribute={};

}

function attributes_visualizer_print(){

	$('#attributes_visualizer_print_frame').remove();
	var iframe = $('<iframe id="attributes_visualizer_print_frame" src="about:blank" width="100px" height="100px" style="position: absolute;top: -1000px; z-index:-10;" >');  // display:block needed for IE
	$('body').append(iframe);
	var frameWindow = iframe[0].contentWindow;
	frameWindow.document.write('<html><head></head><body></body></html>');
	frameWindow.document.close();

	var cssElems = $('link,style');
	for(var i=0;i<cssElems.length;i++){
		var head = frameWindow.document.getElementsByTagName('head')[0];
		if(frameWindow.document.importNode) head.appendChild( frameWindow.document.importNode(cssElems[i],true) );
		else head.appendChild( cssElems[i].cloneNode(true) );
	}
  
  $("#attributes_visualizer_print_frame").contents().find('body').html($('#dialog-attributes-visualizer #attributes-chart').html());
    
	setTimeout(function(){
		if (frameWindow.focus) frameWindow.focus(); // focus is needed for IE
		frameWindow.print();
	},1);

 // return false;

}

function updateLastModifiedCell(id){

//TODO make timestamp from server not browser so to prevent time differnce

  var timestamp=Math.round(new Date().getTime() / 1000);
  var date = new Date(timestamp * 1000), dtv = {
        year:date.getFullYear(),
        month:('0'+(date.getMonth()+1)).slice(-2),
        day:('0'+date.getDate()).slice(-2),
        hours:('0'+date.getHours()).slice(-2),
        minutes:('0'+date.getMinutes()).slice(-2),
        seconds:('0'+date.getSeconds()).slice(-2),
     };
  
  var cells=$('#tbl td[data-column="last_modified"]');

  if(cells.length>1) var agostatus=cells.eq(0).data('agostatus');
  else if(cells.length==0)  var agostatus=cells.eq(0).data('agostatus');
  else var agostatus=0;

  var cell=$('#tbl td#last_modified_'+id);
  if(agostatus==0) cell.find('span').html(dtv.year+'-'+dtv.month+'-'+dtv.day+' '+dtv.hours+':'+dtv.minutes+':'+dtv.seconds);
  else cell.find('span').html(translate.AAS_TEXT_A_MOMENT_AGO);
  cell.addClass('agoCellToggle');//if not present because of not ever modified
  cell.data('ago',timestamp);
  cell.data('celldata',dtv.year+'-'+dtv.month+'-'+dtv.day+' '+dtv.hours+':'+dtv.minutes+':'+dtv.seconds);
  cell.data('agostatus',agostatus);
          
}

function massColumnsEditOptions(val){

  if(val.value==3){
  
    $('#massColumnsEditOptions-select-cats, #massColumnsEditOptions-select-rec, #massColumnsEditOptions-select-rec-explain,#massColumnsEditOptions-select-sta').css({display:'inline-block'});
  
  }else{
  
    $('#massColumnsEditOptions-select-cats, #massColumnsEditOptions-select-rec, #massColumnsEditOptions-select-rec-explain,#massColumnsEditOptions-select-sta').css({display:'none'});
  
  }

}

function reCheckAttributes(){

  var ca=aas.localStorage.get('AAS:checkedAttributes') || '';
  var catts=(ca!='')?ca.split(',') : [];

  if(catts.length<=0) return false;
  
  for(var i=0,n=catts.length;i<n;i++){
  
    if($('#aid_'+catts[i]).length){
    
      $('#aid_'+catts[i]).find('.attributes_checkbox_selector').prop('checked',true).closest('tr').addClass('tr_selected');
      $('#attributes-delete-selected').show();
    
    }
  
  }

}

function rainbow(numOfSteps, step) {
    // This function generates vibrant, "evenly spaced" colours (i.e. no clustering). This is ideal for creating easily distinguishable vibrant markers in Google Maps and other apps.
    // Adam Cole, 2011-Sept-14
    // HSV to RBG adapted from: http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
    var r, g, b;
    var h = step / numOfSteps;
    var i = ~~(h * 6);
    var f = h * 6 - i;
    var q = 1 - f;
    switch(i % 6){
        case 0: r = 1, g = f, b = 0; break;
        case 1: r = q, g = 1, b = 0; break;
        case 2: r = 0, g = 1, b = f; break;
        case 3: r = 0, g = q, b = 1; break;
        case 4: r = f, g = 0, b = 1; break;
        case 5: r = 1, g = 0, b = q; break;
    }
    var c = "#" + ("00" + (~ ~(r * 255)).toString(16)).slice(-2) + ("00" + (~ ~(g * 255)).toString(16)).slice(-2) + ("00" + (~ ~(b * 255)).toString(16)).slice(-2);
    return (c);
}

/*
function randomColor(brightness){
  function randomChannel(brightness){
    var r = 255-brightness;
    var n = 0|((Math.random() * r) + brightness);
    var s = n.toString(16);
    return (s.length==1) ? '0'+s : s;
  }
  return '#' + randomChannel(brightness) + randomChannel(brightness) + randomChannel(brightness);
}
*/

function randomColor(brightness){
  function randomChannel(brightness){
    var n = 0|((Math.random() * (255-brightness)) + brightness);
    var s = n.toString(16);
    return (s.length==1) ? '0'+s : s;
  }
  return '#' + randomChannel(brightness) + randomChannel(brightness) + randomChannel(brightness);
}

function colorizeAttributesRows(){

  $('#attributes-table tbody tr').each(function(){
  
    var onv=$(this).find('.attributes_selectMenus_options').val();
    if(!attsRowsBackgroundColorVariations[onv]) attsRowsBackgroundColorVariations[onv]=randomColor(242);
    $(this).css({backgroundColor:attsRowsBackgroundColorVariations[onv]}).data('initbgcolor',attsRowsBackgroundColorVariations[onv]);
  
  });

}
