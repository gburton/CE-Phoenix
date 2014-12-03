/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/
var product_images_isSortable=false;
$(function(){
	
	$('.close-button').on('click',function(){ 
	
		$(this).closest('.overlay').fadeOut();
		toggleBodyVerticalScrollbar(1);
	
	});
	
	var product_images_images_folders=$('#product_images-images_folders').clone();
	product_images_images_folders.find('input:radio[name=product_images-image_folder]').attr('name','product_images-image_folder_tmp');
	$('.product_images-large_images_folders').html(product_images_images_folders.html());

//GET LARGE IMAGES TRIGGER
	$('#getLargeImages-button').on('click',function(){
	
		product_images_getLargeImages();
	
	});
	
//LARGE IMAGE UPDATE HTML CONTENT
		$('#product_images').on('click','.updateHTMLContent',function(){
		
			var jthis=$(this), htmlcontent=jthis.parent().next().val(),lil=jthis.closest('.product_images-large_image-list').attr('id').split('_l_');
			
			aas.dialog.open('dialog-processing');
    	aas.ajax.do({data:{action:'updateHTMLContent',imgpids:lil[1],htmlcontent:htmlcontent},url:config.url.plugins+'product_images/aas.php'},function(msg){
				aas.dialog.close('dialog-processing');
				if(msg=='0') aas.dialog.open('dialog-error',translate.AAS_PRODUCT_IMAGES_HTML_CONTENT_NOT_UPDATED);
				else aas.dialog.open('dialog-success',translate.AAS_PRODUCT_IMAGES_HTML_CONTENT_UPDATED);
			});


		});

//LARGE IMAGE + TOOGLE
		$('#product_images').on('click','.dropbox-image-fieldset .product_images-image_change_save_path_toggle',function(){
	
		var jlist=$(this).parent().next();
		if(jlist.css('display')=='none'){
	
			$(this).attr('src','ext/aas/images/circle_minus-15x15.png');
			jlist.show();
			$(this).closest('fieldset').css('background','#F7F7F5');
					
		}else{
		
			$(this).attr('src','ext/aas/images/circle_plus-15x15.png');
			jlist.hide();
			$(this).closest('fieldset').css('background','transparent');
		
		}
			
	});	
	
//SMALL IMAGE + TOOGLE
	$('#product_images').on('click','#product_images-large_image-list .product_images-image_change_save_path_toggle',function(){
	
		var jlist=$(this).parent().next();
		
		if(jlist.css('display')=='none'){
	
			$(this).attr('src','ext/aas/images/circle_minus-15x15.png');
			jlist.show();
		
		}else{
		
			$(this).attr('src','ext/aas/images/circle_plus-15x15.png');
			jlist.hide();
		
		}
			
	});

//TOGGLE LARGE IMAGE SRC
	$('#product_images').on('click','#product_images-large_image-list .product_images-large_image_img',function(){
	
			var jthis=$(this),jlegend=jthis.parent().prev();
			
			if(jlegend.text()==translate.AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW) jlegend.html(jthis.attr('src'));
			else jlegend.html(translate.AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW);
		
	});
	
//ADD LARGE IMAGE FIELDSET
	$('#product_images').on('click','#add_large_image-button',function(){
		
		
		if($('#product_images-large_image-list .product_images-redIt').length>0) return false;
		
		product_images_toogleCloseButton(false);
	
		var product_images_large_image_list_length=$('#product_images-large_image-list').children().length;

		var product_images_cloned_elem=$('#product_images-large_image-cloner').clone();
		product_images_cloned_elem.find('.product_images-products_large_image_name').attr('id','product_images-products_large_image_name_'+(product_images_large_image_list_length+1));
		product_images_cloned_elem.find('.product_images-large_image-htmlcontent').attr('id','product_images-large_image-htmlcontent_'+(product_images_large_image_list_length+1));
		product_images_cloned_elem.find('.product_images-large_image-drag-n-drop-wrapper').attr('id','product_images-large_image-drag-n-drop-wrapper_'+(product_images_large_image_list_length+1));
		product_images_cloned_elem.find('.product_images-large_image-file_select_input').attr('id','product_images-large_image-file_select_input_'+(product_images_large_image_list_length+1));
		
		product_images_cloned_elem.find('.product_images-large_image-display').attr('id','product_images-large_image-display_'+(product_images_large_image_list_length+1));
		product_images_cloned_elem.find('.product_images-image_change_save_path').attr('id','product_images-image_change_save_path_'+(product_images_large_image_list_length+1));

		product_images_cloned_elem.find('input:radio[name=product_images-image_folder_tmp]').attr('name','product_images-image_folder_'+(product_images_large_image_list_length+1));

		//auto check radio based on last large image found
		if(product_images_large_image_list_length>0){

  		var radio_checked_value=$('#product_images-large_image-list').children().eq(product_images_large_image_list_length-1).find('.product_images-large_images_folders input[type=radio]:checked').val();
  		product_images_cloned_elem.find('input:radio[name=product_images-image_folder_'+(product_images_large_image_list_length+1)+'][value="'+radio_checked_value+'"]').attr('checked', true);
		
		}
		
		product_images_cloned_elem.find('.product_images-large_images_folders').show();


		$('#product_images-large_image-list').append(product_images_cloned_elem.html());
		
		var jj=$('#product_images-large_image-drag-n-drop-wrapper_'+(product_images_large_image_list_length+1)).closest('.product_images-large_image-list');
		jj.data('status','new');
		product_images_addFileDropEvent(product_images_large_image_list_length+1);
		//product_images_addFileDropEventNormal(product_images_large_image_list_length+1);
	//	console.log($('#product_images-large_image-list').children().not('.product_images-redIt').length);
		if($('#product_images-large_image-list').children().not('.product_images-redIt').length>1){
		
			$("#product_images-large_image-list").sortable( "disable" );
			$('.product_images_handle').hide();
		
		}
		
	
	});

//REMOVE LARGE IMAGE TRIGGER
	$('#product_images').on('click','#product_images-large_image-list .product_images-large-image-wrapper-remove-btn',function(){
		
		var jthis=$(this),data=jthis.closest('fieldset').data();

		if(data['status']=='new'){
		
			jthis.closest('fieldset').remove();
			product_images_toogleCloseButton(true);
			
			if($("#product_images-large_image-list .product_images-redIt").length<=0 && $('#product_images-large_image-list').children().length>1){
	 				$("#product_images-large_image-list").sortable( "enable" );
					$('.product_images_handle').show();
			}
				
		}else{
		
			aas.dialog.open('dialog-confirm-largeImageRemoval','',{pimgid:data.pimgid});
				
		}
	
	});
	
//SMALL IMAGE CHANGE
	$('#dropbox-image').filedrop({
  	xhrParams:{'X-AAS':config.ajaxToken},
		paramname:'products_image',
		force_fallback_id:'products_image_change_default',
		maxfiles: 1,
	  maxfilesize: 20,
		url: config.url.plugins+'product_images/aas.php',
   	allowedfiletypes: ['image/jpeg','image/png','image/gif'],
		allowedfileextensions: ['.jpg','.jpeg','.png','.gif','.JPG','.JPEG','.PNG','.GIF'],
  	data:{
  			pid: function(){
  			
  				var data=$( "#product_images" ).data();
  				return data.pid;
  			
  			},
  			images_path: function(){
  			
  				return $("#dropbox-image .dropbox-image-fieldset input[name='product_images-image_folder']:checked").val();
 			
  			},
  			on_duplicate_filename: function(){
  			
  				return $("#product_images .duplicate_image_filename_action").val();
 			
  			},
  			
  			action: function(){
  			
  				return 'uploadSmallImage';
  			
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
		 
		 if(response=='ask_what_to_do'){
		 
      var data=$( "#product_images" ).data();
		  aas.dialog.open('dialog-on_duplicate_image_what_to_do','',{pid:data.pid,newFileName:file.name,path:$("#dropbox-image .dropbox-image-fieldset input[name='product_images-image_folder']:checked").val()}); 
		  return false;
		 
		 }
		 
		 if(response=='abort'){
		 
		  aas.dialog.open('dialog-error','Change products image aborted.<br>There is already an image stored at selected location having the same filename.');		 
	    return false;
		 
		 }
		 
		 	var resp=response.split('_@_');
		 
		 	aas.dialog.open('dialog-general',resp[1]);
		 	
		 	if(resp[0]!='0'){
		 	
//		 		var data=$( "#dialog-change-image" ).data('data');
 				var data=$( "#product_images" ).data();
		 		var cimg=$('table#tbl tbody.products_tbody tr#pid_'+data.pid).children().find('a.product_image_link img');
		 		if(cimg.length>0){
		 			cimg.attr({'src':config.catalog_images+resp[0]});
		 		}else{
		 		
		 			$('table#tbl tbody.products_tbody tr#pid_'+data.pid).children().find('a.product_image_link').html('<img src="'+config.catalog_images+resp[0]+'" border="0" alt="" title="" width="'+config.small_mage_width+'" height="'+config.small_image_height+'">');
		 		
		 		}
		 		
		 		$('#product_images-current-img').attr('src',config.catalog_images+resp[0]);
				$('#product_images-current_image_path').html(config.catalog_images+resp[0]);
		 		
		 		//aas.dialog.close('dialog-change-image');
		 	
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



	$( "#dialog-on_duplicate_image_what_to_do" ).dialog({
		buttons: [
			{
			    id: "btn-dialog-on_duplicate_image_what_to_do",
			    text: translate.button.apply,
			    click: function(){				
					
        			var data=$(this).data('data');				      
				      var awtd=$("#dialog-on_duplicate_image_what_to_do input[type='radio'][name='awtdodifa']:checked").val();
					      
					      if(parseInt(awtd)==2) var newImageName=$('#awtdodifa-newimagefield').val();
					      
					      if(newImageName==''){ aas.dialog.open('dialog-error','New filename must not be emtpy.'); return false; }
					      
					      	aas.dialog.open('dialog-processing');
				        	aas.ajax.do({data:{action:'uploadSmallImageAwtd',awtd:awtd,pid:data.pid,images_path:data.path,oldImageName:data.newFileName,newImageName:newImageName},url:config.url.plugins+'product_images/aas.php'},function(msg){
                    
                    aas.dialog.close('dialog-processing');
                    
                    var cimg=$('table#tbl tbody.products_tbody tr#pid_'+data.pid).children().find('a.product_image_link img');
                    if(cimg.length>0){
                    cimg.attr({'src':config.catalog_images+msg});
                    }else{

                    $('table#tbl tbody.products_tbody tr#pid_'+data.pid).children().find('a.product_image_link').html('<img src="'+config.catalog_images+msg+'" border="0" alt="" title="" width="'+config.small_mage_width+'" height="'+config.small_image_height+'">');

                    }

                    $('#product_images-current-img').attr('src',config.catalog_images+msg);
                    $('#product_images-current_image_path').html(config.catalog_images+msg);
							      
							      
							       
							      $( '#dialog-on_duplicate_image_what_to_do' ).dialog("close");
							      
							      
						      });
					      
					
	    		}
			},

			{
			    id: "btn-dialog-on_duplicate_image_what_to_do",
			    text: translate.button.cancel,
			    click: function(){
					$( this ).dialog("close");
	    		}
			}
			],
		 open: function(){
		 
		 var data=$(this).data('data');
		 $('#awtdodifa-newimagefield').val(data.newFileName);
		 
				overlayBackgroundNormal = $('.ui-widget-overlay').css('background');
				$('.ui-widget-overlay').css('background', '#080');
			},
		beforeClose: function(){$('.ui-widget-overlay').css('background', overlayBackgroundNormal);},
		close: function(){}

	});


//aas.dialog.open('dialog-on_duplicate_image_what_to_do','',{pid:23}});


});


function product_images_toogleCloseButton(show){

	if(show) $('#product_images-large_image-list').children().not('.product_images-redIt').find('.product_images-large-image-wrapper-remove-btn').show();
	else $('#product_images-large_image-list').children().not('.product_images-redIt').find('.product_images-large-image-wrapper-remove-btn').hide();

}

//GET LARGE IMAGES
function product_images_getLargeImages(){

			product_images_isSortable=false;
			var data=	$('#product_images').data();
			pid=data.pid;
			 
    	aas.dialog.open('dialog-processing');
    	aas.ajax.do({data:{action:'getLargeImages',pid:pid},url:config.url.plugins+'product_images/aas.php'},function(msg){
    		aas.dialog.close('dialog-processing');
    	
    		$('#product_images-large_image-list').html(msg);
        
        //auto check radio based on image path
    		$('#product_images-large_image-list .product_images-large_image-display').each(function(index){
    	
    			var jthis=$(this),dirname=pathinfo(jthis.find('img').attr('src'),1),sx=dirname.split(config.catalog_images);
    			if(sx[0]==''){
    			
    			  jthis.parent().next().find('.product_images-large_images_folders input[type=radio][value="'+sx[1]+'"]').attr('checked', true);
    			
    			}
    		
    		});
    		
    		var product_images_large_image_list_length=$('#product_images-large_image-list').children().length;
    		
    		$('#product_images-large_image-list .product_images-large_image-list').each(function(index){
    	
    			product_images_addFileDropEvent(index+1);
					    		
    		});
    		
				$("#product_images-large_image-list").sortable({containment: "parent", handle: ".product_images_handle",
					
					update:function(event,ui){
						
						var ss=$("#product_images-large_image-list").sortable( "toArray" );
				
					 	aas.dialog.open('dialog-processing');
				  	aas.ajax.do({data:{action:'sortOrderLargeImages',imgpids:ss},url:config.url.plugins+'product_images/aas.php'},function(msg){
							aas.dialog.close('dialog-processing');
							if(msg=='0') $( "product_images-large_image-list" ).sortable( "cancel" );
						});
					
					}
					
				});//.disableSelection();

    		if((product_images_large_image_list_length+1)>2 && product_images_isSortable==false){
					
					$("#product_images-large_image-list").sortable("enable");
					$('.product_images_handle').show();
					product_images_isSortable=true;
							
				}
    		

   		});


}

//DELETE LARGE IMAGE
function product_images_deleteLargeImage(dt){

			aas.dialog.close('dialog-confirm-largeImageRemoval');
			aas.dialog.open('dialog-processing');
    	aas.ajax.do({data:{action:'deleteLargeImage',pimgid:dt.pimgid},url:config.url.plugins+'product_images/aas.php'},function(msg){
				aas.dialog.close('dialog-processing');
				
				if(msg=='0') aas.dialog.open('dialog-error',translate.AAS_PRODUCT_IMAGES_COULD_NOT_DELETE_LARGE_IMAGE);
				else{
					
					$('#product_images-large_image-list_l_'+dt.pimgid).remove();
					
					var sp=[];
					$('#product_images-large_image-list').children().not('.product_images-redIt').each(function(index){
					
						if($(this).attr('id').length) sp.push($(this).attr('id').substr(34));
										
					});
					
					aas.dialog.open('dialog-processing');
			    aas.ajax.do({data:{action:'sortOrderLargeImages',imgpids:sp},url:config.url.plugins+'product_images/aas.php'},function(msg){
  					aas.dialog.close('dialog-processing');
  					//aas.dialog.open('dialog-success','Successfully deleted Large Image');		
  				});
  				
  				
	 				if($('#product_images-large_image-list').children().not('.product_images-redIt').length==1){
		 				$("#product_images-large_image-list").sortable( "disable" );
						$('.product_images_handle').hide();
					}

					
				
				}
			});

}

//ADD LARGE IMAGE
function product_images_addFileDropEvent(idl){

	$('#product_images-large_image-drag-n-drop-wrapper_'+idl).filedrop({
	xhrParams:{'X-AAS':config.ajaxToken},
	force_fallback_id:'product_images-large_image-file_select_input_'+idl,
		paramname:'products_large_image',
		maxfiles: 1,
	    maxfilesize: 20,
		url: config.url.plugins+'product_images/aas.php',
    	allowedfiletypes: ['image/jpeg','image/png','image/gif'],   // filetypes allowed by Content-Type.  Empty array means no restrictions
		allowedfileextensions: ['.jpg','.jpeg','.png','.gif','.JPG','.JPEG','.PNG','.GIF'], // file extensions allowed. Empty array means no restrictions
  		data:{
  			action: function(){
  			
  				return 'uploadLargeImage';
  			
  			},
  			pid: function(){
  			
  				var data=$( "#product_images" ).data();
  				return data.pid;
  			
  			},
  			imgId: function(){
  			
  				var data=$( '#product_images-large_image-drag-n-drop-wrapper_'+idl ).parent().closest('fieldset').data();
  				if(data.status=='old') return data.pimgid;
  				else return '';
  			
  			},
  			
  			htmlcontent: function(){
  			
  				return $( '#product_images-large_image-htmlcontent_'+idl ).val();
  				  			
  			},
  			
  			sort_order: function(){
  			
  				return idl;
  				  			
  			},
  			
  			images_path: function(){
  			
  				return $("#product_images-image_change_save_path_"+idl+" input[name='product_images-image_folder_"+idl+"']:checked").val();
 			
  			},
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
		 	
		 	var resp=response.split('_@_');
		 
		 	aas.dialog.open('dialog-general',resp[2]);
		 
		 	if(resp[0]!='0'){
		 	
		 		var imgPath=$('#product_images-image_change_save_path_'+idl+' .product_images-image_folder:radio:checked').val(),respPath=(imgPath=='') ? resp[0] : imgPath+'/'+resp[0];
		 	
		 		$('#product_images-products_large_image_name_'+idl).val(respPath);
		 		var bdisp=$('#product_images-large_image-display_'+idl);
		 		bdisp.html('<img src="'+config.catalog_images+resp[0]+'" alt="product large image" class="product_images-large_image_img" />');
		 		bdisp.closest('fieldset').css('display','block');
		 		
		 		$('#product_images-image_change_save_path_'+idl+' legend .updateHTMLContent').show();
		 		
				var topFieldset=$('#product_images-image_change_save_path_'+idl).closest('.product_images-large_image-list');
		 		
		 		topFieldset.data('status','old');
		 		topFieldset.data('pimgid',resp[1]);
		 		topFieldset.attr('id','product_images-large_image-list_l_'+resp[1]);
		 		topFieldset.removeClass('product_images-redIt');
		 		
		 		$('#product_images-large_image-htmlcontent_'+idl).prev().find('.updateHTMLContent').show();
		 		
		 		product_images_toogleCloseButton(true);
 				
 				if($("#product_images-large_image-list .product_images-redIt").length<=0 && $('#product_images-large_image-list').children().length>1){
	 				$("#product_images-large_image-list").sortable( "enable" );
					$('.product_images_handle').show();
				}


		 	}else aas.dialog.open('dialog-error',translate.AAS_PRODUCT_IMAGES_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN);
		 			 	
   		 },

		uploadStarted:function(i, file, len){aas.dialog.open('dialog-processing');}
    	 
	});
	
}


function pathinfo(path, options) {
  //  discuss at: http://phpjs.org/functions/pathinfo/
  // original by: Nate
  //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Dmitry Gorelenkov
  //    input by: Timo
  //        note: Inspired by actual PHP source: php5-5.2.6/ext/standard/string.c line #1559
  //        note: The way the bitwise arguments are handled allows for greater flexibility
  //        note: & compatability. We might even standardize this code and use a similar approach for
  //        note: other bitwise PHP functions
  //        note: php.js tries very hard to stay away from a core.js file with global dependencies, because we like
  //        note: that you can just take a couple of functions and be on your way.
  //        note: But by way we implemented this function, if you want you can still declare the PATHINFO_*
  //        note: yourself, and then you can use: pathinfo('/www/index.html', PATHINFO_BASENAME | PATHINFO_EXTENSION);
  //        note: which makes it fully compliant with PHP syntax.
  //  depends on: basename
  //   example 1: pathinfo('/www/htdocs/index.html', 1);
  //   returns 1: '/www/htdocs'
  //   example 2: pathinfo('/www/htdocs/index.html', 'PATHINFO_BASENAME');
  //   returns 2: 'index.html'
  //   example 3: pathinfo('/www/htdocs/index.html', 'PATHINFO_EXTENSION');
  //   returns 3: 'html'
  //   example 4: pathinfo('/www/htdocs/index.html', 'PATHINFO_FILENAME');
  //   returns 4: 'index'
  //   example 5: pathinfo('/www/htdocs/index.html', 2 | 4);
  //   returns 5: {basename: 'index.html', extension: 'html'}
  //   example 6: pathinfo('/www/htdocs/index.html', 'PATHINFO_ALL');
  //   returns 6: {dirname: '/www/htdocs', basename: 'index.html', extension: 'html', filename: 'index'}
  //   example 7: pathinfo('/www/htdocs/index.html');
  //   returns 7: {dirname: '/www/htdocs', basename: 'index.html', extension: 'html', filename: 'index'}

  var opt = '',
    real_opt = '',
    optName = '',
    optTemp = 0,
    tmp_arr = {},
    cnt = 0,
    i = 0;
  var have_basename = false,
    have_extension = false,
    have_filename = false;

  // Input defaulting & sanitation
  if (!path) {
    return false;
  }
  if (!options) {
    options = 'PATHINFO_ALL';
  }

  // Initialize binary arguments. Both the string & integer (constant) input is
  // allowed
  var OPTS = {
    'PATHINFO_DIRNAME': 1,
    'PATHINFO_BASENAME': 2,
    'PATHINFO_EXTENSION': 4,
    'PATHINFO_FILENAME': 8,
    'PATHINFO_ALL': 0
  };
  // PATHINFO_ALL sums up all previously defined PATHINFOs (could just pre-calculate)
  for (optName in OPTS) {
    if(OPTS.hasOwnProperty(optName)){
      OPTS.PATHINFO_ALL = OPTS.PATHINFO_ALL | OPTS[optName];
    }
  }
  if (typeof options !== 'number') {
    // Allow for a single string or an array of string flags
    options = [].concat(options);
    for (i = 0; i < options.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[options[i]]) {
        optTemp = optTemp | OPTS[options[i]];
      }
    }
    options = optTemp;
  }

  // Internal Functions
  var __getExt = function (path) {
    var str = path + '';
    var dotP = str.lastIndexOf('.') + 1;
    return !dotP ? false : dotP !== str.length ? str.substr(dotP) : '';
  };

  // Gather path infos
  if (options & OPTS.PATHINFO_DIRNAME) {
    var dirName = path.replace(/\\/g, '/')
      .replace(/\/[^\/]*\/?$/, ''); // dirname
    tmp_arr.dirname = dirName === path ? '.' : dirName;
  }

  if (options & OPTS.PATHINFO_BASENAME) {
    if (false === have_basename) {
      have_basename = this.basename(path);
    }
    tmp_arr.basename = have_basename;
  }

  if (options & OPTS.PATHINFO_EXTENSION) {
    if (false === have_basename) {
      have_basename = this.basename(path);
    }
    if (false === have_extension) {
      have_extension = __getExt(have_basename);
    }
    if (false !== have_extension) {
      tmp_arr.extension = have_extension;
    }
  }

  if (options & OPTS.PATHINFO_FILENAME) {
    if (false === have_basename) {
      have_basename = this.basename(path);
    }
    if (false === have_extension) {
      have_extension = __getExt(have_basename);
    }
    if (false === have_filename) {
      have_filename = have_basename.slice(0, have_basename.length - (have_extension ? have_extension.length + 1 :
        have_extension === false ? 0 : 1));
    }

    tmp_arr.filename = have_filename;
  }

  // If array contains only 1 element: return string
  cnt = 0;
  for (opt in tmp_arr) {
    if(tmp_arr.hasOwnProperty(opt)){
      cnt++;
      real_opt = opt;
    }
  }
  if (cnt === 1) {
    return tmp_arr[real_opt];
  }

  // Return full-blown array
  return tmp_arr;
}

function basename(path, suffix) {
  //  discuss at: http://phpjs.org/functions/basename/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ash Searle (http://hexmen.com/blog/)
  // improved by: Lincoln Ramsay
  // improved by: djmix
  // improved by: Dmitry Gorelenkov
  //   example 1: basename('/www/site/home.htm', '.htm');
  //   returns 1: 'home'
  //   example 2: basename('ecra.php?p=1');
  //   returns 2: 'ecra.php?p=1'
  //   example 3: basename('/some/path/');
  //   returns 3: 'path'
  //   example 4: basename('/some/path_ext.ext/','.ext');
  //   returns 4: 'path_ext'

  var b = path;
  var lastChar = b.charAt(b.length - 1);

  if (lastChar === '/' || lastChar === '\\') {
    b = b.slice(0, -1);
  }

  b = b.replace(/^.*[\/\\]/g, '');

  if (typeof suffix === 'string' && b.substr(b.length - suffix.length) == suffix) {
    b = b.substr(0, b.length - suffix.length);
  }

  return b;
}


function dirname(path) {
  //  discuss at: http://phpjs.org/functions/dirname/
  //        http: //kevin.vanzonneveld.net
  // original by: Ozh
  // improved by: XoraX (http://www.xorax.info)
  //   example 1: dirname('/etc/passwd');
  //   returns 1: '/etc'
  //   example 2: dirname('c:/Temp/x');
  //   returns 2: 'c:/Temp'
  //   example 3: dirname('/dir/test/');
  //   returns 3: '/dir'

  return path.replace(/\\/g, '/')
    .replace(/\/[^\/]*\/?$/, '');
}
