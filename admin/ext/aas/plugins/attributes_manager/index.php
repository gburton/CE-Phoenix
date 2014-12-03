<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?> 
<div class="dialog" id="dialog-attributes-manager" title="<?php echo AAS_DIALOG_TITLE_ATTRIBUTES_MANAGER; ?>">
  <div id="tabContainer" class="message"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-edit-option-name" title="<?php echo AAS_DIALOG_TITLE_EDIT_OPTION_NAME; ?>">
  <div class="message"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-insert-option-name" title="<?php echo AAS_DIALOG_TITLE_INSERT_OPTION_NAME; ?>">
  <div class="message"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-delete-option-name" title="<?php echo AAS_DIALOG_TITLE_DELETE_OPTION_NAME; ?>">
  <div class="message" style="max-height:400px;"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-delete-option-value" title="<?php echo AAS_DIALOG_TITLE_DELETE_OPTION_VALUE; ?>">
  <div class="message" style="max-height:400px;"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-insert-option-value" title="<?php echo AAS_DIALOG_TITLE_INSERT_OPTION_VALUE; ?>">
  <div class="message"></div>
</div>
<div class="dialog" id="dialog-attributes-manager-edit-option-value" title="<?php echo AAS_DIALOG_TITLE_EDIT_OPTION_VALUE; ?>">
  <div class="message"></div>
</div>
<script>
$(function(){

  var dtdata;

  $("#dialog-attributes-manager").on('click','#tabContainer ul li a',function(){
    var activeTab = $(this).data("rel");
    $("#tabContainer ul li a").removeClass("active");
    $(this).addClass("active");
    $(".tabContents").hide();
    $('#'+activeTab).fadeIn();
    return false;
  });

  $( "#dialog-attributes-manager" ).dialog({
   	height: $(window).height()-100,
	  width:'90%',
		buttons: [

		{
		    id: "btn-attributes-manager-add-option-name",
		    text: 'Insert Option Name',
		    click: function(){
		      aas.dialog.open('dialog-attributes-manager-insert-option-name');		    
		    }
		},
		{
		    id: "btn-attributes-manager-add-option-value",
		    text: 'Insert Option Value',
		    click: function(){
		      aas.dialog.open('dialog-attributes-manager-insert-option-value');		    
		    }
		},
		{
		    id: "btn-attributes-manager-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	  ],
		open: function(event, ui) {
		
		  dtdata=$('#dialog-attributes-manager').data('data') || {};

			aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'loadData'},url:config.url.attributes_actions},function(msg){
		    
				$('#dialog-attributes-manager').children('.message').html(msg);
				
				$(".tabContents").hide(); // Hide all tab content divs by default
        $(".tabContents:first").show(); // Show the first div of tab content by default
        $("#tabContainer ul li a").removeClass("active");
        $("#tabContainer ul li:first a").addClass("active");
        colorizeAttributesOptionValuesRows();
        aas.dialog.close('dialog-processing');
				
			});

		},
			
		close: function(){}
	});


	$( "#dialog-attributes-manager-delete-option-name" ).dialog({

		buttons: [
		{
		    id: "btn-attributes-manager-delete-option-name-delete",
		    text: translate.button.yes_delete_it,
		    click: function(){

		     	 var data=$( this ).data('data');
      			aas.dialog.open('dialog-processing');
		        aas.ajax.do({data:{item:'delete-product-option',value:data.id},url:config.url.attributes_actions},function(msg){
		          aas.dialog.close('dialog-processing');
		          	if(msg=='1'){
		          	
		          	   	aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_DELETED_OPTION_NAME);
		          		//$('#tbl-attributes-delete').remove();
		          		
		          		$('table#tbl-attributes-options tr#optid_'+data.id).remove();
		          		
		          		aas.dialog.close('dialog-attributes-manager-delete-option-name');
		          		
		          		clearAttsCache();
		          		colorizeAttributesOptionValuesRows();
		          		reloadProductsAttributesDialogContent();
		          		
		          	}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_DELETE_OPTION_NAME);
		          
		       });
		
		    }
	
		},
		
		{
		    id: "btn-attributes-manager-delete-option-name-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
	
			var data=$( this ).data('data');
			aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'delete_product_options',value:data.id},url:config.url.attributes_actions},function(msg){
		    aas.dialog.close('dialog-processing');
				$('#dialog-attributes-manager-delete-option-name').children('.message').html(msg);
			
			});

		},
			
		close: function(){}
	});


	$( "#dialog-attributes-manager-insert-option-name" ).dialog({
		buttons: [
		{
		    id: "btn-attributes-manager-insert-option-name-submit",
		    text: translate.button.insert,
		    click: function(){
		     	
		     	 var data=$( this ).data('data'),atts=[],tid='';

		   	$('#insert-product-options input').each(function(i,e){
		   	
		   		tid= this.id.substr(4);
		   	
		   		atts.push({value:this.value,language_id:tid});
		   		
		   	});
		   	aas.dialog.open('dialog-processing');
		   	aas.ajax.do({data:{item:'insert-products-options-names',values:JSON.stringify(atts)},url:config.url.attributes_actions},function(msg){
		       aas.dialog.close('dialog-processing');
		          	if(msg=='2') aas.dialog.open('dialog-error',translate.AAS_TEXT_EMPTY_FIELDS_FOUND);
		          	
		          	else if(msg=='0') aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_INSERT_OPTION_NAMES);
		          	
		          	else{
		          	
		          	   	aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_INSERTED_OPTION_NAMES);
		          	   	
		          	   	$('#tbl-attributes-options tbody').append(msg);
		          	   	
		          	   	clearAttsCache();     
		          	   	colorizeAttributesOptionValuesRows();  
		          	   	reloadProductsAttributesDialogContent();   	
		          	
		          	}
		          
		        });
		                
		
		    }
	
		},
		
		{
		    id: "btn-attributes-manager-insert-option-name-cancel",
		    text: translate.button.cancel,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
			aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'get_product_options_name_insert'},url:config.url.attributes_actions},function(msg){
		    
				$('#dialog-attributes-manager-insert-option-name').children('.message').html(msg);
				
        aas.dialog.close('dialog-processing');
		
			});

		},
			
		close: function(){}
	});


	$( "#dialog-attributes-manager-edit-option-name" ).dialog({
		buttons: [
		{
		    id: "btn-attributes-manager-edit-option-name-edit",
		    text: translate.button.submit_changes,
		    click: function(){
		     	
		     	 var data=$( this ).data('data'),atts=[],cvalue='',tid='';
		       	$('#edit-product-options input').each(function(i,e){
		       	
		       		tid= this.id.substr(4);
		       	
		       		atts.push({value:this.value,language_id:tid});
		       		if(tid==config.language_id) cvalue=this.value;
		       			   	
		       	});
		        aas.dialog.open('dialog-processing');
	          aas.ajax.do({data:{item:'save-product-options',value:data.id,values:JSON.stringify(atts)},url:config.url.attributes_actions},function(msg){
	            
	            aas.dialog.close('dialog-processing');
	          	if(msg=='1'){
	          	
	          	  aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_CHANGED_OPTION_NAMES);
	          		$('table#tbl-attributes-options #option_name_'+data.id).text(cvalue);
	          		clearAttsCache();
	          		colorizeAttributesOptionValuesRows();
	          		reloadProductsAttributesDialogContent();
	          	
	          	}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_UPDATE_OPTION_NAMES)
	          
	          });
		                
		
		    }
	
		},
		
		{
		    id: "btn-attributes-manager-edit-option-name-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
	
			var data=$( this ).data('data');
      aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'get_product_options',value:data.id},url:config.url.attributes_actions},function(msg){
    		aas.dialog.close('dialog-processing');
		
				$('#dialog-attributes-manager-edit-option-name').children('.message').html(msg);
		
			
			});

		},
			
		close: function(){}
	});
	
	
	//###### OPTION VALUES
	
		$( "#dialog-attributes-manager-insert-option-value" ).dialog({
		buttons: [
		{
		    id: "btn-attributes-manager-insert-option-value-submit",
		    text: translate.button.insert,
		    click: function(){
		     	
		     	 var data=$( this ).data('data'),atts=[],tid='';

			   	$('#insert-product-options-values input').each(function(i,e){
			   	
			   		tid= this.id.substr(4);
			   		atts.push({value:this.value,language_id:tid});
			   		
			   	});
		   	
		   		var product_option_id=$('#product_option_select_value').val();
		   		var opt_name_selected=$('#product_option_select_value :selected').text();
		   	  aas.dialog.open('dialog-processing');
		   		aas.ajax.do({data:{item:'insert-products-options-values',values:JSON.stringify(atts),poid:product_option_id,poname:opt_name_selected},url:config.url.attributes_actions},function(msg){
		          aas.dialog.close('dialog-processing');
		          if(msg=='2') aas.dialog.open('dialog-error',translate.AAS_TEXT_EMPTY_FIELDS_FOUND);		          	
		          else if(msg=='0') aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_INSERT_OPTION_VALUES_NAMES);
		          else{
		          	
		          	   	aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_INSERTED_OPTION_VALUES);
		          	   	$('#tbl-attributes-options-values tbody').append(msg);
		          	   	
		          	   	clearAttsCache();
		          	   	colorizeAttributesOptionValuesRows();
		          	   	reloadProductsAttributesDialogContent();
		          	
		          	}
		          
		        });

		    }
	
		},
		
		{
		    id: "btn-attributes-manager-insert-option-value-cancel",
		    text: translate.button.cancel,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
			
			aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'get_product_options_value_insert'},url:config.url.attributes_actions},function(msg){
  			aas.dialog.close('dialog-processing');	
				$('#dialog-attributes-manager-insert-option-value').children('.message').html(msg);
			
			});

		},
			
		close: function(){}
	});
	
		$( "#dialog-attributes-manager-delete-option-value" ).dialog({

		buttons: [
		{
		    id: "btn-attributes-manager-delete-option-value-delete",
		    text: translate.button.yes_delete_it,
		    click: function(){

		     	 var data=$( this ).data('data');
			     aas.dialog.open('dialog-processing');
		       aas.ajax.do({data:{item:'delete-product-option-value',value:data.id},url:config.url.attributes_actions},function(msg){
		         aas.dialog.close('dialog-processing');
		          	if(msg=='1'){
		          	
		          	   	aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_DELETED_OPTION_VALUE_NAME);
		          		//$('#tbl-attributes-delete').remove();
		          		
		          		$('table#tbl-attributes-options-values tr#tr_povid_'+data.id).remove();
		          		
		          		aas.dialog.close('dialog-attributes-manager-delete-option-value');
		          		
		          		clearAttsCache();
		          		colorizeAttributesOptionValuesRows();
		          		reloadProductsAttributesDialogContent();
		          		
		          	}else aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_DELETE_OPTION_VALUE_NAME)
		          
		       });
		
		    }
	
		},
		
		{
		    id: "btn-attributes-manager-delete-option-value-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
	
			var data=$( this ).data('data');
			aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'delete_product_values',value:data.id},url:config.url.attributes_actions},function(msg){
		    aas.dialog.close('dialog-processing');
				$('#dialog-attributes-manager-delete-option-value').children('.message').html(msg);
			
			});

		},
			
		close: function(){}
	});

	$( "#dialog-attributes-manager-edit-option-value" ).dialog({
		buttons: [
		{
		    id: "btn-attributes-manager-edit-option-value-edit",
		    text: translate.button.submit_changes,
		    click: function(){
		     	
		     	var data=$( this ).data('data'),atts=[],cvalue='',tid='';
		     	
		     	$('#edit-product-options-values input').each(function(i,e){
		     	
		     		tid= this.id.substr(4);
		     	
		     		atts.push({value:this.value,language_id:tid});
		     		if(tid==config.language_id) cvalue=this.value;
		     			   	
		     	});
		   
		     	var product_option_id=$('#product_option_select').val();
		     	var opt_name_selected=$('#product_option_select :selected').text();
		   		   	
		     	aas.dialog.open('dialog-processing');
          aas.ajax.do({data:{item:'save-product-options-values',value:data.povid,values:JSON.stringify(atts),extra_value:product_option_id},url:config.url.attributes_actions},function(msg){
            
            aas.dialog.close('dialog-processing');
	          
	          if(msg=='1'){
	
	            aas.dialog.open('dialog-success',translate.AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_CHANGED_OPTION_VALUES_NAMES);
	            		
		          var poid_cell=$('table#tbl-attributes-options-values tbody #tr_povid_'+data.povid+' td.v_poid');
		          poid_cell.text(opt_name_selected);
		          //poid_cell.attr('id','v_poid_'+product_option_id);
		          poid_cell.data('id','v_poid_'+product_option_id);

		          $('table#tbl-attributes-options-values #option_value_'+data.povid).text(cvalue);
		          
		          clearAttsCache();
		          colorizeAttributesOptionValuesRows();
		          reloadProductsAttributesDialogContent();
		
	          }else aas.dialog.open('dialog-error',translate.AAS_DIALOG_ATTRIBUTES_COULD_NOT_UPDATE_OPTION_VALUES_NAMES)

          });
		                
		
		 }
	
		},
		
		{
		    id: "btn-attributes-manager-edit-option-value-close",
		    text: translate.button.close,
		    click: function(){$( this ).dialog( "close" );}
		}
		
	   	],
		open: function(event, ui) {
	
			var data=$( this ).data('data');
      aas.dialog.open('dialog-processing');
			aas.ajax.do({data:{item:'get_product_options_values',value:data.povid,extra_value:data.poid},url:config.url.attributes_actions},function(msg){
		    
		    aas.dialog.close('dialog-processing');
				$('#dialog-attributes-manager-edit-option-value').children('.message').html(msg);
		
			});

		},
			
		close: function(){}
	});
	
	
	$( "#attributesManagerbutton" ).on('click touchend',function(){

    $('#dialog-attributes-manager').removeData('data');//remove data since we load attributes manager from top menu
    aas.dialog.open('dialog-attributes-manager','',{});
  
  });
  
  $('#dialog-attributes-manager').on('click touchend','.edit-option-name',function(){
	
		aas.dialog.open('dialog-attributes-manager-edit-option-name','',{id:$(this).attr('id').substr(5)},translate.AAS_TEXT_AM_DIALOG_TITLE_EDITING_OPTION_NAME);
	
	});
	
	$('#dialog-attributes-manager').on('click touchend','.delete-option-name',function(){
	
		var optname=$(this).parent().prev().text();
		aas.dialog.open('dialog-attributes-manager-delete-option-name','',{id:$(this).attr('id').substr(5)},translate.AAS_TEXT_AM_DIALOG_TITLE_DELETE_OPTION_NAME+optname);
	
	});

	$('#dialog-attributes-manager').on('click touchend','.edit-option-value',function(){
	
		aas.dialog.open('dialog-attributes-manager-edit-option-value','',{povid:$(this).attr('id').substr(8),poid:$(this).parent().prev().prev().data('id').substr(7)});
	
	});
	
	$('#dialog-attributes-manager').on('click touchend','.delete-option-value',function(){
	
		var optvalue=$(this).parent().prev().text();
		aas.dialog.open('dialog-attributes-manager-delete-option-value','',{id:$(this).attr('id').substr(8)},translate.AAS_TEXT_AM_DIALOG_TITLE_DELETE_OPTION_VALUE+optvalue);
	
	});

  //since we made a change to attributes manager then reload products dialog content
  function reloadProductsAttributesDialogContent(){

    if(dtdata.pid){
    
    	var orderBy=$('#product_attributes_orderBy').val();
	    var ascDesc=$('#product_attributes_ascDesc').val();

	    var data=$( "#dialog-attributes" ).data('data');

	    aas.dialog.open('dialog-processing');
	
	    aas.ajax.do({
	
		    data:{item:'attributes',ascDesc:ascDesc,orderBy:orderBy,product_id:dtdata.pid,lid:dtdata.lid},
		    url:config.url.actions
	
	    },function(msg){

		    $('#dialog-attributes-options').html(msg);
		    reCheckAttributes();
        colorizeAttributesRows();
		    aas.dialog.close('dialog-processing');
			
	    });
    
    }

  }

});
var attsOptionValuesRowsBackgroundColorVariations={};
function colorizeAttributesOptionValuesRows(){

  $('#tbl-attributes-options-values tbody tr').each(function(){
  
    var onv=$(this).find('.v_poid').data('id');
    if(!attsOptionValuesRowsBackgroundColorVariations[onv]) attsOptionValuesRowsBackgroundColorVariations[onv]=randomColor(242);
    $(this).css({backgroundColor:attsOptionValuesRowsBackgroundColorVariations[onv]}).data('initbgcolor',attsOptionValuesRowsBackgroundColorVariations[onv]);
  
  });

}
</script>
