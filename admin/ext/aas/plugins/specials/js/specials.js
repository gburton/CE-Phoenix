/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/
$(function(){

	$('table#tbl').on('click','.edit-selected-product-as-special',function(){
				
		var sid=$(this).attr('id').substr(33);
		
		if($('table#tbl_specials tr#sid_'+sid).find('.specials-edit-button').length<=0){
		
		  //silent load special table
  		updateSpecialsTable(false,function(){
  		
  	      $('table#tbl_specials tr#sid_'+sid).find('.specials-edit-button').click();	
  		
  		});
		
		}else $('table#tbl_specials tr#sid_'+sid).find('.specials-edit-button').click();
			
		return false;
	});
	
	$('table#tbl_specials').on('click','.specials-edit-button',function(){
	
		var tr=$(this).closest('tr');
	
		aas.dialog.open('dialog-specials','',{
		
			action:'edit',
			sid:tr.attr('id').substr(4),
			pid:$(this).attr('id').substr(5),
			product_name:tr.children().eq(0).text(),
			oldPrice:tr.children().find('.oldPrice').text(),
			oldPrice_raw:tr.children().find('.oldPrice_raw').text(),
			specialPrice_raw:tr.children().find('.specialPrice_raw').text()
		
		});
	
	});
	
	$('table#tbl_specials').on('click','.specials-delete-button',function(){
	
		var tr=$(this).closest('tr');
	
		aas.dialog.open('dialog-specials',translate.AAS_SPECIALS_DIALOG_TEXT_DELETE_SPECIAL,{
		
			action:'delete',
			sid:tr.attr('id').substr(4),
			pid:$(this).attr('id').substr(5),
			product_name:tr.children().eq(0).text(),
			oldPrice:tr.children().find('.oldPrice').text()
		
		},translate.AAS_SPECIALS_DIALOG_TITLE_CONFIRM_SPECIAL_DELETION);
	
	});
	
	$('table#tbl').on('click','.add-selected-product-as-special',function(){

		aas.dialog.open('dialog-specials-add','',{
		
			action:'add',
			pid:$(this).attr('id').substr(25)
								
		});
	
	});
	
	$('#add-special-button').on('click',function(){
	
		aas.dialog.open('dialog-specials-add','',{
		
			action:'add'
								
		});
	
	});
	
	$('#reload-special-button').on('click',function(){
	
		updateSpecialsTable(false);
	
	});

	$('.close-button').on('click',function(){
	
		$(this).closest('.overlay').fadeOut();
	
	});

	$('#specials').on('focusin', '.specials_datepicker', function(e) {

		$(this).datepicker({ dateFormat: 'yy-mm-dd',

			onSelect: function(dateText, inst) {
				var $_this=$(this);
				aas.ajax.do({data: 'action=updateExpiresAt&value='+dateText+'&specials_id='+$(this).attr('id').substr(20), url:config.url.plugins+'specials/aas.php'},function(msg){
					aas.dialog.open('dialog-success',translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_SET_EXPIRE_DATE);
					$_this.next().css({visibility:'visible'});
					updateSpecialsCell($_this.closest('tr').data('pid'));
				});
			}

		});

	});
	
	$( "#dialog-specials-add #specials-add-special-expiry-date-field" ).datepicker({ dateFormat: 'yy-mm-dd' });
	
	$('#specials').on('click','.specials_status',function(){

		var statusIdArray=this.id.split('_'), $_this=$(this), srcArray=$_this.children().attr('src').split('/'),stockVal= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? '1' : '0';

		aas.ajax.do({data:'action=changeStatus&value='+stockVal+'&specials_id='+statusIdArray[2],url:config.url.plugins+'specials/aas.php'},function(msg){
		
			if(msg=='1' || msg=='0' ){
			
					if(msg=='1') $_this.closest('tr').removeClass('unavailable');
					else if(msg=='0') $_this.closest('tr').addClass('unavailable');
			
					aas.dialog.open('dialog-success',translate.AAS_SPECIALS_DIALOG_TEXT_STATUS_SUCCESSFULLY_CHANGED);
			
					srcArray[srcArray.length-1]= (srcArray[srcArray.length-1]=='icn_alert_error.png') ? 'icn_alert_success.png' : 'icn_alert_error.png';
				
					if(stockVal=='1') $_this.data('stock','0'); else $_this.data('stock','1');
				
					$_this.children().attr('src',srcArray.join('/'));
					
					var pid=$_this.closest('tr').data('pid');
					if(msg=='1') $('table#tbl tr#pid_'+pid+' td#special_'+pid).removeClass('unavailable');
					if(msg=='0') $('table#tbl tr#pid_'+pid+' td#special_'+pid).addClass('unavailable');
					
					updateSpecialsCell($_this.closest('tr').data('pid'));
					
			}else aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_STATUS);

		});

		return false;

	});
	
	
	$('#specials').on('click','.specials-never-expire',function(){
		var $_this=$(this);
		aas.ajax.do({data: 'action=setNeverExpire&specials_id='+$_this.attr('id').substr(22), url:config.url.plugins+'specials/aas.php'},function(msg){
				
				if(msg=='1'){
					aas.dialog.open('dialog-success',translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_SET_EXPIRE_DATE);
					$_this.css({visibility:'hidden'});
					$_this.prev().val('');
					updateSpecialsCell($_this.closest('tr').data('pid'));
				}else{
				
					aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_COULD_NOT_SET_AVAILABLE_DATE_TO_NULL);
				
				}
		});
	
		return false;
	});
	
	$('#specialsbutton').on('click touchend',function(){
	
		$('#specials').fadeIn('fast',function(){
  
  		toggleBodyVerticalScrollbar(0);
		  updateSpecialsTable(false);
		
		});
	
	});
	
	$('#specials .close a').click(function(){

		$('#specials').fadeOut('fast',function(){
		
		toggleBodyVerticalScrollbar(1);
		
		});
		
	
	});

});

specialsEdit=function(data){

var value=$('#specials-special-price-field').val();

	if(value=='') aas.dialog.open('dialog-error','Empty value found!');
	else{

    aas.dialog.open('dialog-processing');
		aas.ajax.do({data:{action:'update',specials_id:data.sid,products_price:data.oldPrice_raw,specials_price:value},url:config.url.plugins+'specials/aas.php'},function(msg){
      aas.dialog.close('dialog-processing');
		
			if(msg=='error'){
		
				aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_SPECIALS_PRICE);
		
			}else{
		
				var tr=$('tr#sid_'+data.sid);
			
					if(value.slice(-1) == '%') value = (data.oldPrice_raw - ((parseInt(value) / 100) * data.oldPrice_raw));
				
					tr.children().find('.specialPrice_raw').text(value);
					tr.children().find('.specialPrice').text(msg);
					
					aas.dialog.close('dialog-specials');
					aas.dialog.open('dialog-success',translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_UPDATED_VALUES);
					
					updateSpecialsCell(data.pid);
						
			}
	
		});

	}

}

specialsDelete=function(data){
    
    aas.dialog.open('dialog-processing');
		aas.ajax.do({data:{action:'delete',specials_id:data.sid},url:config.url.plugins+'specials/aas.php'},function(msg){
		  aas.dialog.close('dialog-processing');			
			if(msg=='1'){
		
				aas.dialog.close('dialog-specials');
				$('tr#sid_'+data.sid).remove();
				specialsTableRowsColorize();
				
				//add to main table
				if(fields.special){
				
					var htmla=translate.AAS_SPECIALS_TEXT_NOT_A_SPECIAL_YET;
		
					if(config.defaults.enableSpecials){
					
						htmla+='<button class="applyButton add-selected-product-as-special" id="add_specials_products_id_'+data.pid+'" >'+translate.AAS_SPECIALS_TEXT_ADD+' &nbsp;<img style="opacity:0.3;height:15px" src="ext/aas/images/glyphicons_190_circle_plus.png" alt="Add"></button>';
					
					 }
					 
					 $('table#tbl tr#pid_'+data.pid+' td#special_'+data.pid).removeClass('unavailable').html(htmla);
						 
				}
				
				aas.dialog.open('dialog-success',translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_DELETED);
			
			}else{
			
				aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_DELETE_SPECIAL);
			
			}
	
		});

}

specialsTableRowsColorize=function(){

	$('table#tbl_specials tbody tr').each(function(index){
		
		var cl=index%2 ?'even':'odd';
		if($(this).hasClass('unavailable')) $(this).attr('class',cl).addClass('unavailable');
		else $(this).attr('class',cl);
		
	});
}

specialsAdd=function(){

	var products_id=$('#specials-list-products_id').val();
	var specials_price=$('#specials-add-special-price-field').val();
	var expiry_date=$('#specials-add-special-expiry-date-field').val();
	if(products_id>=0){
	
		if(specials_price=='') aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_EMPTY_SPECIAL_PRICE_FOUND);
		else{
		
		  aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{action:'insert',products_id:products_id,specials_price:specials_price,expiry_date:expiry_date},url:config.url.plugins+'specials/aas.php'},function(msg){
		    aas.dialog.close('dialog-processing');
				if(msg=='1'){
				
					aas.dialog.close('dialog-specials-add');
					aas.dialog.open('dialog-processing',translate.AAS_SPECIALS_DIALOG_TEXT_ADDED_NEW_SPECIAL_LOADING_SPECIALS_TABLE);
					aas.ajax.do({data:{action:'getSpecialsTable'},url:config.url.plugins+'specials/aas.php'},function(msg){
					
						
						$('table#tbl_specials tbody').html(msg);
						
						//add to cell if visible
						if(fields.special){
						
							aas.ajax.do({data:{action:'getCellData',products_id:products_id},url:config.url.plugins+'specials/aas.php'},function(msg){

								if(msg=='0'){
								
									aas.dialog.close('dialog-processing');								
									aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_ERROR_FETCHING_CELL_DATA);

								}else{
								
									$('table#tbl tr#pid_'+products_id+' td#special_'+products_id).html(msg);
									aas.dialog.close('dialog-processing');
								
								}
							
							});
						
						}else{
						
							aas.dialog.close('dialog-processing');
						
						}
						
					
					});
				
				}else aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_TRY_AGAIN);
		
			});
		
		}
		
	}else aas.dialog.open('dialog-error',translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_NO_VALID_PRODUCT_SELECTION);
	
}

updateSpecialsTable=function(silent,func){

		if(!silent) aas.dialog.open('dialog-processing');
		aas.ajax.do({data:{action:'getSpecialsTable'},url:config.url.plugins+'specials/aas.php'},function(msg){
		
			$('table#tbl_specials tbody').html(msg);
			if(!silent) aas.dialog.close('dialog-processing');
			if(typeof(func) == 'function') func();
			
		});
}

updateSpecialsCell=function(pid){

		if(fields.special){
		
			aas.ajax.do({data:{action:'getCellData',products_id:pid},url:config.url.plugins+'specials/aas.php'},function(msg){

				if(msg=='0'){

				}else{
				
					$('table#tbl tr#pid_'+pid+' td#special_'+pid).html(msg);
				
				}
			
			});
		
		}
}
