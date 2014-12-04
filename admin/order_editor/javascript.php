<?php 
/*
  $Id: javascript.php v5.0 08/05/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/   
?>

<script language="javascript" type="text/javascript"><!--

var xmlHttp = false;

function  createRequest() {
	if(window.ActiveXObject) {
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else if(window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
	}
	
	     if (!xmlHttp) {
         alert('<?php echo AJAX_CANNOT_CREATE_XMLHTTP; ?>');
         return false;
      }
 }   

function rewriteDiv(field, stack) {
  
  if (stack == 'orders') {
  document.getElementById("ordersMessageStack").innerHTML ='<table><tr class="messageStackSuccess"><td class="messageStackSuccess"><?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_MESSAGE_STACK_SUCCESS, 'field'); ?></td></tr></table>' ;
  
  document.getElementById("productsMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;
	
  document.getElementById("historyMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;
	
    }
  
  
  if (stack == 'products') {
  document.getElementById("productsMessageStack").innerHTML ='<table><tr class="messageStackSuccess"><td class="messageStackSuccess"><?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_MESSAGE_STACK_SUCCESS, 'field'); ?></td></tr></table>' ;
  
  document.getElementById("historyMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;
  
  document.getElementById("ordersMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;
  
    }
  
  
  if (stack == 'history') {
  document.getElementById("historyMessageStack").innerHTML ='<table><tr class="messageStackSuccess"><td class="messageStackSuccess"><?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_MESSAGE_STACK_SUCCESS, 'field'); ?></td></tr></table>' ;
  
  document.getElementById("ordersMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;

  document.getElementById("productsMessageStack").innerHTML ='<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>' ;
  
	}   
		   
  } //end function rewriteDiv
		   
		   
function deleteDiv(div) {//can be used on any element with an id, not just divs, but if you want to delete a tr better to use deleteRow()
  document.getElementById(div).innerHTML = '' ;
  }
		   
		   
function reloadDiv(div, data) {
  document.getElementById(div).innerHTML = data ;
  }
  
  
function deleteRow(info, div) {
    var i = info.parentNode.parentNode.parentNode.rowIndex;
    document.getElementById(div).deleteRow(i);
   }
   
   
function updateOrdersField(field, value) {

		createRequest();
		
		var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_order_field&oID=<?php echo $_GET['oID']; ?>&field=" + field + "&new_value=" + value;
		
		xmlHttp.open("GET", url, true);
        
		xmlHttp.onreadystatechange=
                function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'orders')}};
		
		xmlHttp.send(null);


} //end function updatOrdersField

function updateProductsField(action, pid, field, value, info) {

      createRequest();

		if ( (action == 'update') || (action == 'reload1') ) {

        var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_product_field&oID=<?php echo $_GET['oID']; ?>&pid=" + pid + "&field=" + field + "&new_value=" + value;

		xmlHttp.open("GET", url, true);

		    if (action == 'reload1') {
		        xmlHttp.onreadystatechange=
		          function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');obtainTotals();}};
		    } else {//action == 'update'
		        xmlHttp.onreadystatechange=
		          function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');}};
            }
				
		}//end if ( (action == 'update') || (action == 'reload1') ) {
		
		
		if (action == 'reload2') {
		
		var price = document.getElementById("update_products[" + pid + "][price]").value;
		var final_price = document.getElementById("update_products[" + pid + "][final_price]").value;
		
		var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_product_value_field&oID=<?php echo $_GET['oID']; ?>&pid=" + pid + "&price=" + price + "&final_price=" + final_price;

		xmlHttp.open("GET", url, true);

		xmlHttp.onreadystatechange=
	            function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');obtainTotals();}};
		
		}//end if action == 'reload2'


		if ( (action == 'delete') && (field == 'delete') && (value == true) ){

		  if (confirm('<?php echo AJAX_CONFIRM_PRODUCT_DELETE; ?>')) {

           var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=delete_product_field&oID=<?php echo $_GET['oID']; ?>&pid=" + pid + "&field=" + field + "&new_value=" + value;

		   xmlHttp.open("GET", url, true);

		   xmlHttp.onreadystatechange=
		           function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');deleteRow(info, 'productsTable');obtainTotals();}};

		   }
		 
	 }//end if (action == 'delete') {
  	
	 xmlHttp.send(null);

} //end function updateProductsField(action, pid, field, value) {


  function updateAttributesField (action, field, aid, pid, value) {
    
	 createRequest();        
		
		if (action == 'simple') {
		var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_attributes_field&oID=<?php echo $_GET['oID']; ?>&aid=" + aid +"&pid=" + pid + "&field=" + field + "&new_value=" + value;
		}
	 
        if (action == 'hard') {
	    var final_price = document.getElementById("update_products[" + pid + "][final_price]").value;
		var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_attributes_field&oID=<?php echo $_GET['oID']; ?>&aid=" + aid +"&pid=" + pid + "&field=" + field + "&new_value=" + value + "&final_price=" + final_price;
		}

		xmlHttp.open("GET", url, true);

		  if (action == 'simple') {
		
		  xmlHttp.onreadystatechange=
		          function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');}};

		  }//end if (action == 'simple') {

          
		  if (action == 'hard') {
		
		  xmlHttp.onreadystatechange=
		          function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');obtainTotals();}};

		  }//end if (action == 'hard') {
	 
	   xmlHttp.send(null);
  
    }//end function updateAttributesField
	
	
	
	function updateDownloads (field, did, pid, value) {
	  
	  createRequest();
	  
	    var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_downloads&oID=<?php echo $_GET['oID']; ?>&pid=" + pid + "&field=" + field + "&new_value=" + value + "&did=" + did;

		xmlHttp.open("GET", url, true);

		xmlHttp.onreadystatechange=
		        function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'products');}};
				  
		xmlHttp.send(null);
	
	} //end function updateDownloads (field, did, pid) {


function updateCommentsField(action, id, status, value, info) {

      if ( (status) && (status == true) && (action == 'delete') ) {
	     if (confirm('<?php echo AJAX_CONFIRM_COMMENT_DELETE; ?>')) {
		    
			createRequest();
		    var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=delete_comment&cID=" + id + "&oID=<?php echo $_GET['oID']; ?>";
		    xmlHttp.open("GET", url, true);
			
			xmlHttp.onreadystatechange=
		            function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'history');deleteRow(info, 'commentsTable')}};
        
		    xmlHttp.send(null);
					
			
		 }
	  }
	  	  
	 if (action == 'update') {
	 
	  		createRequest();
		    var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_comment&cID=" + id + "&comment=" + value + "&oID=<?php echo $_GET['oID']; ?>";
		    xmlHttp.open("GET", url, true);
			
			xmlHttp.onreadystatechange=
		            function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'history');}};
        
		    xmlHttp.send(null);
			
			}
			
  }//end function updateCommentsField(action, id, status) {
  

function updateShippingZone(field, value) {

    updateOrdersField(field, value);
	
		    if (confirm('<?php echo AJAX_CONFIRM_RELOAD_TOTALS; ?>')) {
			
			  obtainTotals();
		    
			}

}//end function updateShippingZone(field, value) {


  function setShipping(method) {
    if (document.getElementById("ot_shipping[title]")) {
	document.getElementById("ot_shipping[title]").value = document.getElementById("update_shipping["+method+"][title]").value;
    document.getElementById("ot_shipping[value]").value = document.getElementById("update_shipping["+method+"][value]").value;
	document.getElementById("ot_shipping[id]").value = document.getElementById("update_shipping["+method+"][id]").value;
	<?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>obtainTotals();<?php } ?>
	} <?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?> else {
	   
		  	if (confirm('<?php echo AJAX_SELECTED_NO_SHIPPING; ?>')) {
			
			createRequest();
			var title = document.getElementById("update_shipping["+method+"][title]").value;
			var value = document.getElementById("update_shipping["+method+"][value]").value;
			var id = document.getElementById("update_shipping["+method+"][id]").value;
			var sort_order = "<?php echo MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER ?>";
		    var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=insert_shipping&title=" + title + "&id=" + id + "&value=" + value + "&sort_order=" + sort_order + "&oID=<?php echo $_GET['oID']; ?>";
		    xmlHttp.open("GET", url, true);
			
			xmlHttp.onreadystatechange=
                    function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){reloadDiv('totalsBlock', xmlHttp.responseText);}};
        
		    xmlHttp.send(null);
		  
	     }
	  }<?php } ?>
   }

  function reloadTotals () { //this is called after a shipping method is added to an order that didn't previously have one
   //there's an onload command tucked in with the info icon used in conjunction with the order totals tooltip that fires this
    
	if (confirm('<?php echo AJAX_RELOAD_TOTALS; ?>')) {
	
	    obtainTotals();
	  
	  }
	}
	
	

		function currency(value) {
		    var selObj = document.getElementById("update_info_payment_currency");
			var selIndex = selObj.selectedIndex;
			var currency;
			var currency_value;
			<?php
			 $currency_query_raw = "SELECT code, value FROM " . TABLE_CURRENCIES . "";
             $currency_query = tep_db_query($currency_query_raw);
             while ($currency = tep_db_fetch_array($currency_query)) {
  echo '                  if (selObj.options[selIndex].value == \'' . $currency['code'] . '\') {' . "\n";
  echo '                  document.getElementById("update_info_payment_currency_value").value = \'' . $currency['value'] . '\';' . "\n";
  echo '                  currency = \'' . $currency['code'] . '\'' . "\n";
  echo '                  currency_value = \'' . $currency['value'] . '\'' . "\n";
  echo '                  }' . "\n";
			echo "\n";
			}
            ?>
			
		<?php if (ORDER_EDITOR_USE_AJAX == 'true') { ?>
		
		createRequest();
	    var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=update_currency&oID=<?php echo $_GET['oID']; ?>&table=<?php echo TABLE_ORDERS; ?>&currency=" + currency + "&currency_value=" + currency_value;
		
		xmlHttp.open("GET", url, true);
        
	    xmlHttp.onreadystatechange=
		        function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){rewriteDiv(xmlHttp.responseText, 'orders');obtainTotals();}};
		 
	    xmlHttp.send(null);
			
		<?php } ?>
			
        }

   
  function clearComments() {
			document.getElementById("notify").checked = false;
			document.getElementById("notify_comments").checked = false;
			document.getElementById("comments").value = '';
   }//end function clearComments()


   function getNewComment() { //this is used for inserting new commments
      
	   createRequest();
	   var status = document.getElementById("status").value;
	   var notify = document.getElementById("notify").checked;
	   var notifyComments = document.getElementById("notify_comments").checked;
	   var comments = encodeURIComponent(document.getElementById("comments").value);
	   var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=insert_new_comment&oID=<?php echo $_GET['oID']; ?>&status=" + status +"&notify=" + notify + "&notify_comments=" + notifyComments + "&comments=" + comments;
		
		xmlHttp.open("GET", url, true);
        
	    xmlHttp.onreadystatechange=
                function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){reloadDiv('commentsBlock', xmlHttp.responseText);clearComments();}};
		 
	    xmlHttp.send(null);
		
   }//end function getNewComment()

  
  function obtainTotals() { //this is used for processing/updating order totals
  
    createRequest();
	
    // Set up data variable
    var formdata = "";
	 
    // Loop through form fields
    for (i=0; i < document.edit_order.elements.length; i++) {
       formdata += encodeURIComponent(document.edit_order.elements[i].name) + "=" + encodeURIComponent(document.edit_order.elements[i].value) + "&";
     }
		  formdata += "action=reload_totals&";
		  formdata += "oID=<?php echo $_GET['oID']; ?>"
		
	 var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>";
	 
	  //hey- we're busy here
	  document.getElementById("totalsBlock").innerHTML = '<div align="center"><img src="order_editor/images/working.gif"><br><?php echo AJAX_WORKING; ?><br></div>';  
	  //if you do this before you loop the form the data will be lost

	  xmlHttp.open("POST", url, true);//GET does not work with this data
	 
	  xmlHttp.onreadystatechange=
              function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){reloadDiv('totalsBlock', xmlHttp.responseText);}};
		 
      xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlHttp.setRequestHeader("Content-length", formdata.length);
      xmlHttp.setRequestHeader("Connection", "close");
      xmlHttp.send(formdata);
		  
	
   }//end function obtainTotals() {
   
   
   
   function newOrderEmail() { //sending out new order confirmation emails the AJAX way
    
     if (confirm('<?php echo AJAX_NEW_ORDER_EMAIL; ?>')) {
	  
	    //var comments = prompt('<?php echo AJAX_INPUT_NEW_EMAIL_COMMENTS; ?>');
		//at this point it seems unneccessary to include admin comments with new order confirmation emails
		//the comments included with an original order email are written in by the customer, not admin
		//also, the prompt function within IE7 is disabled by default and works like something from 1997
	 
	    createRequest();
	    //if (comments) { 
		//var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=new_order_email&comments=" + encodeURIComponent(comments) + "&oID=<?php echo $_GET['oID']; ?>";
		//} else {
		var url = "<?php echo FILENAME_ORDERS_EDIT_AJAX; ?>?action=new_order_email&oID=<?php echo $_GET['oID']; ?>";
		//}
		
		xmlHttp.open("GET", url, true);
        
		xmlHttp.onreadystatechange=
		       function(){if(xmlHttp.readyState!=4)return;if(xmlHttp.status==200){reloadDiv('ordersMessageStack', xmlHttp.responseText)}};
		 
	    xmlHttp.send(null);
   
     }
	 
   }//end function newOrderEmail()

///end AJAX section


///begin standard JavaScript for edit_orders.php file

   function setAddressVisibility(szDivID, checkID) {
     var obj = document.getElementById(szDivID);

      if (checkID.checked) {
        obj.style.visibility = "hidden";
        obj.style.display = "none";
      } else {
        obj.style.visibility = "visible";
        
		//fix IE, since Microsoft isn't going to
		if (window.navigator.userAgent.indexOf('MSIE') != -1) { 
		   obj.style.display = "inline"; //IE is not standards compliant, apparently not even IE7
		} else {		
		   obj.style.display = "table-row";
		}//this should all just be obj.style.display = "table-row"; but IE is not standards compliant
				
      }
    }
    
	
    function update_zone(countryID, zoneID, inputID, menuID) {
    var theForm = document.edit_order;
	var NumState = theForm[countryID].options.length;
    var SelectedCountry = '';

    while(NumState > 0) {
      NumState--;
      theForm[zoneID].options[NumState] = null;
    }

    SelectedCountry = theForm[countryID].options[theForm[countryID].selectedIndex].value;

<?php echo oe_js_zone_list('SelectedCountry', 'theForm', 'zoneID', 'inputID', 'menuID'); ?>
  }

 
     function setStateVisibility(ID, vis, ID2) {
    
      var obj = document.getElementById(ID);
      var obj2 = document.getElementById(ID2);

      if (vis == "hidden") {
        obj.style.visibility = "hidden";
        obj.style.display = "none";
        obj2.style.visibility = "visible";
        obj2.style.display = "inline";
      } else {
        obj.style.visibility = "visible";
        obj.style.display = "inline";
        obj2.style.visibility = "hidden";
        obj2.style.display = "none";
     
         } //end if vis == hidden
     } //end function
	 
	 
  function openWindow(file, windowName) {
    msgWindow = window.open(file, windowName,'top=250, left=250, width=550, height=450, location=0, status=1, toolbar=0, resize=1');
    if (!msgWindow.opener) msgWindow.opener = self;
  }


  function selectRowEffect(object, buttonSelect) {
    document.getElementById("shipping_radio_" + buttonSelect).checked=true;
  }
  

  function rowOverEffect(object) {
    object.className = 'dataTableRowSelected';
  }

  function rowOutEffect(object, oldClass) {
    object.className = oldClass;
  }

  function setVisibility(id, checkID) {
    var obj;
    
	obj = document.getElementById(id);
  
    if (obj != null) {
      if (obj.style) {
        obj = obj.style;
      }
      if (checkID.checked == true) {
        obj.visibility = 'visible';
      } else {
        obj.visibility = 'hidden';
      }
    }
  }



  function setCustomOTVisibility(ID, vis, ID2) {

      var obj = document.getElementById(ID);
      var obj2 = document.getElementById(ID2);

      if (vis == "hidden") {
        obj.style.visibility = "hidden";
        obj.style.display = "none";
        obj2.style.visibility = "visible";
        obj2.style.display = "inline";
      } else {
        obj.style.visibility = "visible";
		
		  //fix IE, since Microsoft can't
		  if (window.navigator.userAgent.indexOf('MSIE') != -1) { 
		  obj.style.display = "inline"; //IE is not standards compliant, apparently not even IE7
		  } else {		
		  obj.style.display = "table-row";
		  }//this should all just be obj.style.display = "table-row", however IE is not standards compliant
        
        obj2.style.visibility = "hidden";
        obj2.style.display = "none";
     
	  } //end if (vis == "hidden") {
   } //end  function setCustomOTVisibility(ID, vis, ID2) {		


  function addLoadListener(fn)
{
  if (typeof window.addEventListener != 'undefined')
  {
    window.addEventListener('load', fn, false);
  }
  else if (typeof document.addEventListener != 'undefined')
  {
    document.addEventListener('load', fn, false);
  }
  else if (typeof window.attachEvent != 'undefined')
  {
    window.attachEvent('onload', fn);
  }
  else
  {
    var oldfn = window.onload;
    if (typeof window.onload != 'function')
    {
      window.onload = fn;
    }
    else
    {
      window.onload = function()
      {
        oldfn();
        fn();
      };
    }
  }
} // end function addLoadListener(fn)


<?php echo "ORDER_EDITOR_PAYMENT_DROPDOWN = '" . ORDER_EDITOR_PAYMENT_DROPDOWN . "';"; ?>


addLoadListener(init);

function init()
{
  var optional = document.getElementById("optional");
  if (optional) {
  optional.className = "hidden";
  }
  //START dropdown option for payment method by quick_fixer
      if (ORDER_EDITOR_PAYMENT_DROPDOWN == 'true') {
            var selObj = document.getElementById('update_info_payment_method');
            if (selObj) { var selIndex = selObj.selectedIndex; }
        
        //text in lieu of value supported by firefox and mozilla but not others SO MAKE SURE text and optional value are the same (in the payment dropdown they are)
            if (selObj.options[selIndex].text) {
                var paymentMethod = selObj.options[selIndex].text;
            }
            else {
                var paymentMethod = selObj.options[selIndex].value;
            }
        }
        else {
            //if you only use an input field to display payment method use this
            var selObj = document.getElementById('update_info_payment_method');
            if (selObj) { var paymentMethod = selObj.value; }
        }
                          
    //END dropdown option for payment method by quick_fixer

  if (optional) {
  if (paymentMethod == "<?php echo ORDER_EDITOR_CREDIT_CARD ?>") {
  optional.className = "";
  return true;
  } else {
  optional.className = "hidden";
  return true;
  }
  }
  
} // end function init()

  function updatePrices(action, pid, taxdescription) { 
  //calculates all the different values as new entries are typed
    var qty = document.getElementById("update_products[" + pid + "][qty]").value;
	var taxRate = document.getElementById("update_products[" + pid + "][tax]").value;
	var attValue = getAttributesPrices(pid);	
	
	if ((action == 'qty') || (action == 'tax') || (action == 'att_price') || (action == 'price')) {
	
	var finalPriceValue = document.getElementById("update_products[" + pid + "][price]").value;
	var priceInclValue = document.getElementById("update_products[" + pid + "][price]").value;
	var totalInclValue = document.getElementById("update_products[" + pid + "][price]").value;
	var totalExclValue = document.getElementById("update_products[" + pid + "][price]").value;
			
	finalPriceValue = Number(attValue) + Number(finalPriceValue);
	priceInclValue = ( Number(attValue) + Number(priceInclValue) ) * ((taxRate / 100) + 1);
	totalInclValue = ( Number(attValue) + Number(totalInclValue) ) * ((taxRate / 100) + 1) * qty;
	totalExclValue = ( Number(attValue) + Number(totalExclValue) ) * qty;
	taxValue = taxRate * finalPriceValue / 100 * qty;
	
	}
	
	if (action == 'final_price') {
	
	var priceValue = document.getElementById("update_products[" + pid + "][final_price]").value;
	var priceInclValue = document.getElementById("update_products[" + pid + "][final_price]").value;
	var totalInclValue = document.getElementById("update_products[" + pid + "][final_price]").value;
	var totalExclValue = document.getElementById("update_products[" + pid + "][final_price]").value;
			
	priceValue = Number(priceValue) - Number(attValue);
	priceInclValue = priceInclValue * ((taxRate / 100) + 1);
	totalInclValue = totalInclValue * ((taxRate / 100) + 1) * qty;
	totalExclValue = totalExclValue * qty;
			
	} //end if ((action == 'qty') || (action == 'tax') || (action == 'final_price')) 
	
	if (action == 'price_incl') {
	
	var priceValue = document.getElementById("update_products[" + pid + "][price_incl]").value;
	var finalPriceValue = document.getElementById("update_products[" + pid + "][price_incl]").value;
	var totalInclValue = document.getElementById("update_products[" + pid + "][price_incl]").value;
	var totalExclValue = document.getElementById("update_products[" + pid + "][price_incl]").value;
		
	priceValue = Number(finalPriceValue / ((taxRate / 100) + 1)) - Number(attValue);
	finalPriceValue = finalPriceValue / ((taxRate / 100) + 1);
	totalInclValue = totalInclValue * qty;
	totalExclValue = totalExclValue * qty / ((taxRate / 100) + 1);
		
	} //end of if (action == 'price_incl')
	
	if (action == 'total_excl') {
	
	var priceValue = document.getElementById("update_products[" + pid + "][total_excl]").value;
	var finalPriceValue = document.getElementById("update_products[" + pid + "][total_excl]").value;
	var priceInclValue = document.getElementById("update_products[" + pid + "][total_excl]").value;
	var totalInclValue = document.getElementById("update_products[" + pid + "][total_excl]").value;
			
	priceValue = ( Number (finalPriceValue / qty) ) - Number (attValue);
	finalPriceValue = finalPriceValue / qty;
	priceInclValue = priceInclValue * ((taxRate / 100) + 1) / qty;
	totalInclValue = totalInclValue * ((taxRate / 100) + 1);
		
	} //end of if (action == 'total_excl')
	
	if (action == 'total_incl') {
	
	var priceValue = document.getElementById("update_products[" + pid + "][total_incl]").value;
	var finalPriceValue = document.getElementById("update_products[" + pid + "][total_incl]").value;
	var priceInclValue = document.getElementById("update_products[" + pid + "][total_incl]").value;
	var totalExclValue = document.getElementById("update_products[" + pid + "][total_incl]").value;
		
	priceValue = Number (finalPriceValue / ((taxRate / 100) + 1) / qty) - Number(attValue)
	finalPriceValue = finalPriceValue / ((taxRate / 100) + 1) / qty;
	priceInclValue = priceInclValue / qty;
	totalExclValue = totalExclValue / ((taxRate / 100) + 1);
		
	} //end of if (action == 'total_incl')
	

	if ((action != 'qty') && (action != 'tax') && (action != 'att_price') && (action != 'price')) {
	document.getElementById("update_products[" + pid + "][price]").value = doFormat(priceValue, 4);
	}
	
	if (action != 'final_price') {
	document.getElementById("update_products[" + pid + "][final_price]").value = doFormat(finalPriceValue, 4);
	}
	
	if ((action != 'qty') && (action != 'price_incl')) {
	document.getElementById("update_products[" + pid + "][price_incl]").value = doFormat(priceInclValue, 4);
	}
	
	if ((action != 'tax') && (action != 'total_excl')) {
	document.getElementById("update_products[" + pid + "][total_excl]").value = doFormat(totalExclValue, 4);
	}
	
	if (action != 'total_incl') {
	document.getElementById("update_products[" + pid + "][total_incl]").value = doFormat(totalInclValue, 4);
	}
	
	} //end function updatePrices(action, pid)

   function getAttributesPrices(pid){ //get any attributes prices that may exist 
    var sum =0;
    var el=document.getElementsByTagName('input');//all the input elements
      for(var i=0;i<el.length;i++){
       if(el[i].id.indexOf(pid)>-1){
        var aid=el[i].id.replace(pid,'').replace('p', '').replace('a', '');//extract the attribute id
        var p=el[i].id.replace(pid,'').replace(/\d/g,'');
          if((p=='pa') && (document.getElementById('p' + pid + '_' + aid + '_prefix')) && (document.getElementById('p' + pid + '_' + aid + '_prefix').value) == '-') {
           sum-=Number(el[i].value);
          }
          if((p=='pa') && (document.getElementById('p' + pid + '_' + aid + '_prefix')) && (document.getElementById('p' + pid + '_' + aid + '_prefix').value) == '+') {
           sum+=Number(el[i].value);
          }
         }
        }
      return sum
     } //end function getAttributePrices(pid)
	 
	function doRound(x, places) {  //we only have so much space
     return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
    }
 
    function doFormat(x, places) { //keeps all calculated values the same length
    var a = doRound(x, places);
    var s = a.toString();

    var decimalIndex = s.indexOf(".");
      
	  if (places > 0 && decimalIndex < 0) {
         decimalIndex = s.length;
         s += '.';
       }
     
	  while (decimalIndex + places + 1 > s.length) {
         s += '0';
      }
  
    return s;
  
    } // end function doFormat

//--></script>

<?php ?>