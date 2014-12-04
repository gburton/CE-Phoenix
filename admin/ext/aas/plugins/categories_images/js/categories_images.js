/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/
$(function(){
	
	$('.close-button').on('click',function(){ 
	
		$(this).closest('.overlay').fadeOut(); 
		toggleBodyVerticalScrollbar(1);
	
	});
	
	var categories_images_images_folders=$('#categories_images-images_folders').clone();
	categories_images_images_folders.find('input:radio[name=categories_images-image_folder]').attr('name','categories_images-image_folder_tmp');
	$('.categories_images-large_images_folders').html(categories_images_images_folders.html());


//SMALL IMAGE + TOOGLE
	$('#categories_images').on('click','.categories_images-image_change_save_path_toggle',function(){
	
		var jlist=$(this).parent().next();
		
		if(jlist.css('display')=='none'){
	
			$(this).attr('src','ext/aas/images/circle_minus-15x15.png');
			jlist.show();
		
		}else{
		
			$(this).attr('src','ext/aas/images/circle_plus-15x15.png');
			jlist.hide();
		
		}
			
	});

//SMALL IMAGE CHANGE
	$('#dropbox-image-categories').filedrop({
  	xhrParams:{'X-AAS':config.ajaxToken},
		paramname:'categories_image',
		force_fallback_id:'categories_image_change_default',
		maxfiles: 1,
	  maxfilesize: 20,
		url: config.url.plugins+'categories_images/aas.php',
   	allowedfiletypes: ['image/jpeg','image/png','image/gif'],
		allowedfileextensions: ['.jpg','.jpeg','.png','.gif','.JPG','.JPEG','.PNG','.GIF'],
  	data:{
  			cid: function(){
  			
  				var data=$( "#categories_images" ).data();
  				return data.cid;
  			
  			},
  			images_path: function(){
  			
  				return $("#dropbox-image-categories .dropbox-image-fieldset input[name='categories_images-image_folder']:checked").val();
 			
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
		 
		 	var resp=response.split('_@_');
		 
		 	aas.dialog.open('dialog-general',resp[1]);
		 	
		 	if(resp[0]!='0'){
		 	
 				var data=$( "#categories_images" ).data();
		 		var cimg=$('table#tbl tbody:eq(0) tr#cat_'+data.cid).children().find('a.product_image_link img');
		 		if(cimg.length>0){
		 			cimg.attr({'src':config.catalog_images+resp[0]});
		 		}else{
		 		
		 			$('table#tbl tbody:eq(0) tr#cat_'+data.cid).children().find('a.product_image_link').html('<img src="'+config.catalog_images+resp[0]+'" border="0" alt="" title="" width="'+config.small_mage_width+'" height="'+config.small_image_height+'">');
		 		
		 		}
		 		
		 		$('#categories_images-current-img').attr('src',config.catalog_images+resp[0]);
				$('#categories_images-current_image_path').html(config.catalog_images+resp[0]);
		 		
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


});
