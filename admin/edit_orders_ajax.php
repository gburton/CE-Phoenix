<?php
  /*
  $Id: edit_orders_ajax.php v5.0.5 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
  
  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032
  
  */
  
  require('includes/application_top.php');
  
  // output a response header
  header('Content-type: text/html; charset=' . CHARSET . '');

  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include('order_editor/shipping.php');
  include('order_editor/http_client.php');
  include(DIR_WS_LANGUAGES . $language. '/' . FILENAME_ORDERS_EDIT);

   
  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  
  //$action 
  //all variables are sent by $_GET only or by $_POST only, never together
  if (sizeof($_GET) > 0) {
     $action = $_GET['action']; 
  } elseif (sizeof($_POST) > 0) {
	 $action = $_POST['action']; 
	 }
   
  //1.  Update most the orders table
  if ($action == 'update_order_field') {
	 tep_db_query("UPDATE " . TABLE_ORDERS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_id = '" . $_GET['oID'] . "'");
	 
	  
	  //generate responseText
	  echo $_GET['field'];
	 

  }
  
  //2.  Update the orders_products table for qty, tax, name, or model
  if ($action == 'update_product_field') {
			
		if ($_GET['field'] == 'products_quantity') {
			// Update Inventory Quantity
			$order_query = tep_db_query("
			SELECT products_id, products_quantity 
			FROM " . TABLE_ORDERS_PRODUCTS . " 
			WHERE orders_id = '" . $_GET['oID'] . "'
			AND orders_products_id = '" . $_GET['pid'] . "'");
			$orders_product_info = tep_db_fetch_array($order_query);
			
			// stock check 
			
			if ($_GET['new_value'] != $orders_product_info['products_quantity']){
			$quantity_difference = ($_GET['new_value'] - $orders_product_info['products_quantity']);
				if (STOCK_LIMITED == 'true'){
				    tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
					products_quantity = products_quantity - " . $quantity_difference . ",
					products_ordered = products_ordered + " . $quantity_difference . " 
					WHERE products_id = '" . $orders_product_info['products_id'] . "'");
// QT Pro Addon BOF	
					if (ORDER_EDITOR_USE_QTPRO == 'true') { 
					$attrib_q = tep_db_query("select distinct op.products_id, po.products_options_id, pov.products_options_values_id
						                        from products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
						                        where op.orders_id = '" . $_GET['oID'] . "'
															      and op.orders_products_id = '" . $_GET['pid'] . "'
															      and products_options_values_name = opa.products_options_values
						                        and pov.products_options_values_id = po2pov.products_options_values_id
						                        and po.products_options_id = po2pov.products_options_id
						                        and products_options_name = opa.products_options");
					while($attrib_set = tep_db_fetch_array($attrib_q)) {
						// corresponding to each option find the attribute ids ( opts and values id )
						$products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
					}
					sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
					$products_stock_attributes = implode($products_stock_attributes, ',');
					 // update the stock
					 tep_db_query("update ".TABLE_PRODUCTS_STOCK." set products_stock_quantity = products_stock_quantity - ".$quantity_difference . " where products_id= '" . $orders_product_info['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
					}
// QT Pro Addon EOF
				} else {
					tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
					products_ordered = products_ordered + " . $quantity_difference . "
					WHERE products_id = '" . $orders_product_info['products_id'] . "'");
				} //end if (STOCK_LIMITED == 'true'){
			} //end if ($_GET['new_value'] != $orders_product_info['products_quantity']){
		}//end if ($_GET['field'] = 'products_quantity'
		
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
		
	  //generate responseText
	  echo $_GET['field'];

  }
  
  //3.  Update the orders_products table for price and final_price (interdependent values)
  if ($action == 'update_product_value_field') {
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET products_price = '" . tep_db_input(tep_db_prepare_input($_GET['price'])) . "', final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  //generate responseText
	  echo TABLE_ORDERS_PRODUCTS;

  }
  
    //4.  Update the orders_products_attributes table 
if ($action == 'update_attributes_field') {
	  
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_attributes_id = '" . $_GET['aid'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  if (isset($_GET['final_price'])) {
	    
		tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  }
	  
	  //generate responseText
	  echo $_GET['field'];

  }
  
    //5.  Update the orders_products_download table 
if ($action == 'update_downloads') {
	  tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET " . $_GET['field'] . " = '" . tep_db_input(tep_db_prepare_input($_GET['new_value'])) . "' WHERE orders_products_download_id = '" . $_GET['did'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	 //generate responseText
	  echo $_GET['field'];

  }
  
  //6. Update the currency of the order
  if ($action == 'update_currency') {
  	  tep_db_query("UPDATE " . TABLE_ORDERS . " SET currency = '" . tep_db_input(tep_db_prepare_input($_GET['currency'])) . "', currency_value = '" . tep_db_input(tep_db_prepare_input($_GET['currency_value'])) . "' WHERE orders_id = '" . $_GET['oID'] . "'");
  
  	 //generate responseText
	  echo $_GET['currency'];
  
  }//end if ($action == 'update_currency') {
  
  
  //7.  Update most any field in the orders_products table
  if ($action == 'delete_product_field') {
  
  		  	       //  Update Inventory Quantity
			      $order_query = tep_db_query("
			      SELECT products_id, products_quantity 
			      FROM " . TABLE_ORDERS_PRODUCTS . " 
			      WHERE orders_id = '" . $_GET['oID'] . "'
			      AND orders_products_id = '" . $_GET['pid'] . "'");
			      $order = tep_db_fetch_array($order_query);

		   			 //update quantities first
			       if (STOCK_LIMITED == 'true'){
				    tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET 
					products_quantity = products_quantity + " . $order['products_quantity'] . ",
					products_ordered = products_ordered - " . $order['products_quantity'] . " 
					WHERE products_id = '" . (int)$order['products_id'] . "'");
// QT Pro Addon BOF	
					if (ORDER_EDITOR_USE_QTPRO == 'true') { 
					$attrib_q = tep_db_query("select distinct op.products_id, po.products_options_id, pov.products_options_values_id
						                        from products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
						                        where op.orders_id = '" . $_GET['oID'] . "'
															      and op.orders_products_id = '" . $_GET['pid'] . "'
															      and products_options_values_name = opa.products_options_values
						                        and pov.products_options_values_id = po2pov.products_options_values_id
						                        and po.products_options_id = po2pov.products_options_id
						                        and products_options_name = opa.products_options");
					while($attrib_set = tep_db_fetch_array($attrib_q)) {
						$products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
					}
					sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
					$products_stock_attributes = implode($products_stock_attributes, ',');
					 // update the stock
					 tep_db_query("update ".TABLE_PRODUCTS_STOCK." set products_stock_quantity = products_stock_quantity + ".$order['products_quantity'] . " where products_id= '" . (int)$order['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
					}
// QT Pro Addon EOF
				    } else {
					tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
					products_ordered = products_ordered - " . $order['products_quantity'] . "
					WHERE products_id = '" . (int)$order['products_id'] . "'");
				    }
		   
                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "  
	                              WHERE orders_id = '" . $_GET['oID'] . "'
					              AND orders_products_id = '" . $_GET['pid'] . "'");
      
	                tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
	                              WHERE orders_id = '" . $_GET['oID'] . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");
	                
					tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
	                              WHERE orders_id = '" . $_GET['oID'] . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");
								  
      //generate responseText
	  echo TABLE_ORDERS_PRODUCTS;

  }

  
  //8. Update the orders_status_history table
  if ($action == 'delete_comment') {
      
	  tep_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  $lastorderstatus_query=tep_db_query("select orders_status_id from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id='" . $_GET['oID'] . "' order by orders_status_history_id desc limit 1" );

		if (tep_db_num_rows($lastorderstatus_query) > 0) {
		$lastorderstatus_data = tep_db_fetch_array($lastorderstatus_query);
		tep_db_query("UPDATE " . TABLE_ORDERS . " set orders_status='".$lastorderstatus_data['orders_status_id']."' WHERE orders_id = '" . $_GET['oID'] . "'");

	  //generate responseText
	  echo TABLE_ORDERS_STATUS_HISTORY;
	  
	  }
	  
  }
  //9. Update the orders_status_history table
  if ($action == 'update_comment') {
      
	  tep_db_query("UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET comments = '" . oe_iconv($_GET['comment']) . "' WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $_GET['oID'] . "'");
	  
	  //generate responseText
	  echo TABLE_ORDERS_STATUS_HISTORY;
	  
	  }
	  

  //10. Reload the shipping and order totals block 
    if ($action == 'reload_totals') {
         
	   $oID = $_POST['oID'];
	   $shipping = array();
	  
       if (is_array($_POST['update_totals'])) {
	    foreach($_POST['update_totals'] as $total_index => $total_details) {
          extract($total_details, EXTR_PREFIX_ALL, "ot");
          if ($ot_class == "ot_shipping") {
            
			$shipping['cost'] = $ot_value;
			$shipping['title'] = $ot_title;
			$shipping['id'] = $ot_id;

           } // end if ($ot_class == "ot_shipping")
         } //end foreach
	   } //end if is_array
	
	  if (tep_not_null($shipping['id'])) {
    tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . $_POST['oID'] . "'");
	   }
	   
		$order = new manualOrder($oID);
		$order->adjust_zones();
				
		$cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();
		
		// Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
		
		if (DISPLAY_PRICE_WITH_TAX == 'true') {//extract the base shipping cost or the ot_shipping module will add tax to it again
		   $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
		   $tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		   $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );
           $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
		   }

 		//this is where we call the order total modules
		require( 'order_editor/order_total.php');
		$order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();  

	    $current_ot_totals_array = array();
		$current_ot_titles_array = array();
		$written_ot_totals_array = array();
		$written_ot_titles_array = array();
		//how many weird arrays can I make today?
		
        $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' order by sort_order");
        while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
          $current_ot_totals_array[] = $current_ot_totals['class'];
		  $current_ot_titles_array[] = $current_ot_totals['title'];
        }


        tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "'");
        
        $j=1; //giving something a sort order of 0 ain't my bag baby
		$new_order_totals = array();
		
	    if (is_array($_POST['update_totals'])) { //1
          foreach($_POST['update_totals'] as $total_index => $total_details) { //2
            extract($total_details, EXTR_PREFIX_ALL, "ot");
            if (!strstr($ot_class, 'ot_custom')) { //3
             for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //4
              			  
			  if ($order_totals[$i]['code'] == 'ot_tax') { //5
			  $new_ot_total = ((in_array($order_totals[$i]['title'], $current_ot_titles_array)) ? false : true);
			  } else { //within 5
			  $new_ot_total = ((in_array($order_totals[$i]['code'], $current_ot_totals_array)) ? false : true);
			  }  //end 5 if ($order_totals[$i]['code'] == 'ot_tax')
              
			  if ( ( ($order_totals[$i]['code'] == 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) && ($order_totals[$i]['title'] == $ot_title) ) || ( ($order_totals[$i]['code'] != 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) ) ) { //6
			  //only good for components that show up in the $order_totals array

				if ($ot_title != '') { //7
                  $new_order_totals[] = array('title' => $ot_title,
                                              'text' => (($ot_class != 'ot_total') ? $order_totals[$i]['text'] : '<b>' . $currencies->format($order->info['total'], true, $order->info['currency'], $order->info['currency_value']) . '</b>'),
                                              'value' => (($order_totals[$i]['code'] != 'ot_total') ? $order_totals[$i]['value'] : $order->info['total']),
                                              'code' => $order_totals[$i]['code'],
                                              'sort_order' => $j);
                $written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
				$j++;
                } else { //within 7
 
				  $order->info['total'] += ($ot_value*(-1)); 
				  $written_ot_totals_array[] = $ot_class;
				  $written_ot_titles_array[] = $ot_title; 

                } //end 7
				
			  } elseif ( ($new_ot_total) && (!in_array($order_totals[$i]['title'], $current_ot_titles_array)) ) { //within 6
			   
                $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $current_ot_totals_array[] = $order_totals[$i]['code'];
				$current_ot_titles_array[] = $order_totals[$i]['title'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
                $j++;
                //echo $order_totals[$i]['code'] . "<br>"; for debugging- use of this results in errors
				
			  } elseif ($new_ot_total) { //also within 6
                $order->info['total'] += ($order_totals[$i]['value']*(-1));
                $current_ot_totals_array[] = $order_totals[$i]['code'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
              }//end 6
           }//end 4
         } elseif ( (tep_not_null($ot_value)) && (tep_not_null($ot_title)) ) { // this modifies if (!strstr($ot_class, 'ot_custom')) //3
            $new_order_totals[] = array('title' => $ot_title,
                     'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
                                        'code' => 'ot_custom_' . $j,
                                        'sort_order' => $j);
            $order->info['total'] += $ot_value;
			$written_ot_totals_array[] = $ot_class;
		    $written_ot_titles_array[] = $ot_title;
            $j++;
          } //end 3
		  
		    //save ot_skippy from certain annihilation
			 if ( (!in_array($ot_class, $written_ot_totals_array)) && (!in_array($ot_title, $written_ot_titles_array)) && (tep_not_null($ot_value)) && (tep_not_null($ot_title)) && ($ot_class != 'ot_tax') && ($ot_class != 'ot_loworderfee') ) { //7
			//this is supposed to catch the oddball components that don't show up in $order_totals
				 
				    $new_order_totals[] = array(
					        'title' => $ot_title,
                            'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                            'value' => $ot_value,
                            'code' => $ot_class,
                            'sort_order' => $j);
               //$current_ot_totals_array[] = $order_totals[$i]['code'];
				//$current_ot_titles_array[] = $order_totals[$i]['title'];
				$written_ot_totals_array[] = $ot_class;
				$written_ot_titles_array[] = $ot_title;
                $j++;
				 
				 } //end 7
        } //end 2
	  } else {//within 1
	  // $_POST['update_totals'] is not an array => write in all order total components that have been generated by the sundry modules
	   for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //8
	                  $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $j++;
				
			} //end 8
				
		} //end if (is_array($_POST['update_totals']))  //1
	  

        for ($i=0, $n=sizeof($new_order_totals); $i<$n; $i++) {
          $sql_data_array = array('orders_id' => $oID,
                                  'title' => oe_iconv($new_order_totals[$i]['title']),
                                  'text' => $new_order_totals[$i]['text'],
                                  'value' => $new_order_totals[$i]['value'], 
                                  'class' => $new_order_totals[$i]['code'], 
                                  'sort_order' => $new_order_totals[$i]['sort_order']);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }


        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();
        
        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

		
  
  ?>
  
		<table width="100%">
		 <tr><td>
			 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
			  <tr>
                <td valign="top" width="100%">
				 <br>
				   <div>
					<a href="javascript:openWindow('<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_POST['oID'] . '&step=1'); ?>','addProducts');"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT); ?></a><input type="hidden" name="subaction" value="">
					</div>
					<br>
				</td>
               
             
			  <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                <table border="0" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="15" nowrap onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"><img src="images/icon_info.gif" border="0" width="13" height="13"></td>
                    <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                    <td class="dataTableHeadingContent" colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
                  </tr>
<?php
  for ($i=0; $i<sizeof($order->totals); $i++) {
   
    $id = $order->totals[$i]['class'];
	
	if ($order->totals[$i]['class'] == 'ot_shipping') {
	   if (tep_not_null($order->info['shipping_id'])) {
	       $shipping_module_id = $order->info['shipping_id'];
		   } else {
		   //here we could create logic to attempt to determine the shipping module used if it's not in the database
		   $shipping_module_id = '';
		   }
	  } else {
	    $shipping_module_id = '';
	  } //end if ($order->totals[$i]['class'] == 'ot_shipping') 
   
    $rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
    if ((!strstr($order->totals[$i]['class'], 'ot_custom')) && ($order->totals[$i]['class'] != 'ot_shipping')) {
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      } else {
        echo '                    <td class="dataTableContent" valign="middle">&nbsp;</td>' . "\n";
      }
      
      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" value="' . trim($order->totals[$i]['title']) . '" readonly="readonly"></td>' . "\n";
	  
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td class="dataTableContent">&nbsp;</td>' . "\n";
      echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format($order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                  <tr class="' . $rowStyle . '" id="update_totals['.$i.']" style="visibility: hidden; display: none;">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');">' . tep_image('order_editor/images/minus.gif', IMAGE_REMOVE_NEW_OT) . '</a></td>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"  onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . @number_format($order->totals[$i]['value'], 2, '.', '') . '" size="6"  onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table></td>
                <!-- order_totals_eof //-->
              </tr>              
              <tr>
                <td valign="bottom">
                
<?php 
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table border="0" width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
		if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onclick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' . "\n" .
             
    '      <td class="dataTableContent" valign="top" align="left" width="15px">' . "\n" .
	
	'      <input type="radio" name="shipping" id="shipping_radio_' . $r . '" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'">' . "\n" .
			 
	'      <input type="hidden" id="update_shipping['.$r.'][title]" name="update_shipping['.$r.'][title]" value="'.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):">' . "\n" .
			
    '      <input type="hidden" id="update_shipping['.$r.'][value]" name="update_shipping['.$r.'][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
	
	'      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .
    
	'        <td class="dataTableContent" valign="top">' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" . 
    
	'        <td class="dataTableContent" align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
             '                  </tr>';
      }
    }
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
<?php
  } else {
  echo AJAX_NO_QUOTES;
  }
?>
                </td>
              </tr> 
            </table>
			
		  
		  </td></tr>
		</table>
	   
  
<?php   }//end if ($action == 'reload_shipping') 
     
	
	//11. insert new comments
	 if ($action == 'insert_new_comment') {  
	 
	 	//orders status
         $orders_statuses = array();
         $orders_status_array = array();
         $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name 
                                              FROM " . TABLE_ORDERS_STATUS . " 
									          WHERE language_id = '" . (int)$languages_id . "'");
									   
         while ($orders_status = tep_db_fetch_array($orders_status_query)) {
                $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                            'text' => $orders_status['orders_status_name']);
    
	            $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
               }
			   
   // UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####

    $check_status_query = tep_db_query("
	                      SELECT customers_name, customers_email_address, orders_status, date_purchased 
	                      FROM " . TABLE_ORDERS . " 
						  WHERE orders_id = '" . $_GET['oID'] . "'");
						  
    $check_status = tep_db_fetch_array($check_status_query); 
	
  if (($check_status['orders_status'] != $_GET['status']) || (tep_not_null($_GET['comments']))) {

        tep_db_query("UPDATE " . TABLE_ORDERS . " SET 
					  orders_status = '" . tep_db_input($_GET['status']) . "', 
                      last_modified = now() 
                      WHERE orders_id = '" . $_GET['oID'] . "'");
		
		 // Notify Customer ?
      $customer_notified = '0';
			if (isset($_GET['notify']) && ($_GET['notify'] == 'true')) {
			  $notify_comments = '';
			  if (isset($_GET['notify_comments']) && ($_GET['notify_comments'] == 'true')) {
			   $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, oe_iconv($_GET['comments'])) . "\n\n";
			  }
// bof order editor 5_0_8			  
//			  $email = STORE_NAME . "\n" .
//			           EMAIL_SEPARATOR . "\n" . 
//					   EMAIL_TEXT_ORDER_NUMBER . ' ' . $_GET['oID'] . "\n" . 
//	                   EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $_GET['oID'], 'SSL') . "\n" . 
//					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . 
//					   sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$_GET['status']]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
//BEGIN SEND HTML MAIL//			  
//			  $email = STORE_NAME . "\n" .
//			           EMAIL_SEPARATOR . "\n" . 
//					   EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" . 
//                       EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" . 
//					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);

	     if (FILENAME_EMAIL_STATUS !== 'FILENAME_EMAIL_STATUS'     ) {
		   //Prepare variables for html email//
   		   $Varlogo = ''.VARLOGO.'' ;
		   $Vartable1 = ''.VARTABLE1.'' ;
		   $Vartable2 = ''.VARTABLE2.'' ;

		   $Vartext1 = ' <b>' . EMAIL_TEXT_DEAR . ' ' . $check_status['customers_name'] .' </b><br>' . EMAIL_MESSAGE_GREETING ;
		   $Vartext2 = '    ' . EMAIL_TEXT_ORDER_NUMBER . ' <STRONG> ' . $oID . '</STRONG><br>' . EMAIL_TEXT_DATE_ORDERED . ': <strong>' . tep_date_long($check_status['date_purchased']) . '</strong><br><a href="' . HTTP_SERVER . DIR_WS_CATALOG . 'account_history_info.php?order_id=' . $oID .'">' . EMAIL_TEXT_INVOICE_URL . '</a>' ; 

		   $Varbody = EMAIL_TEXT_COMMENTS_UPDATE_HTML . ' ' . $comments . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);

		   $Varmailfooter = ''.VARMAILFOOTER.'' ;

		   $Varhttp = ''.VARHTTP.'';
		   $Varstyle = ''.VARSTYLE.'';

		   //Check if HTML emails is set to true
		   if (EMAIL_USE_HTML == 'true') {	

		     //Prepare HTML email
			 require(DIR_WS_MODULES . 'email/html_orders.php');
			 $email = $html_email_orders;
			
		   } else {		

			  //Send text email
			  $email = STORE_NAME . "\n" .
			           EMAIL_SEPARATOR . "\n" . 
					   EMAIL_TEXT_ORDER_NUMBER . ' ' . $_GET['oID'] . "\n" . 
                       EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" . 
					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
		   }
	     } else {		// send standaard email if html email is not installed

		    //Send text email
	    	$email = STORE_NAME . "\n" .
			           EMAIL_SEPARATOR . "\n" . 
					   EMAIL_TEXT_ORDER_NUMBER . ' ' . $_GET['oID'] . "\n" . 
                       EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" . 
					   EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);
	     }		
	

//END SEND HTML MAIL//		
// eof order editor 5_0_8
			  
			  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  
			  $customer_notified = '1';
			}			  
          		
			tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . " 
			(orders_id, orders_status_id, date_added, customer_notified, comments) 
			values ('" . tep_db_input($_GET['oID']) . "', 
				'" . tep_db_input($_GET['status']) . "', 
				now(), 
				" . tep_db_input($customer_notified) . ", 
				'" . oe_iconv($_GET['comments'])  . "')");
// bof order editor 5_0_8	
if (FILENAME_GOOGLE_MAP     !== 'FILENAME_GOOGLE_MAP'     ) {
if ($status == GOOGLE_MAP_ORDER_STATUS )    // wenn "Versendet"
{
        //require(DIR_WS_LANGUAGES . $language . '/report_googlemap.php');

        $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);

        $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
        $order_exists = true;
        if (!tep_db_num_rows($orders_query))
        {
                $order_exists = false;
                $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
        }

        include(DIR_WS_CLASSES . 'order.php');
        $order = new order($oID);

        $url  = "http://maps.google.com/maps/geo?q=";
        $url .= $order->delivery['street_address'] . "," . $order->delivery['postcode'] . "," . $order->delivery['city'] . "," . $order->delivery['country'];
        $url .= "&output=csv&key=";
        $url .= GOOGLE_MAP_API_KEY;
        $url = str_replace (" ", "%20", $url);          // Leerzeichen -> %20
        $request = fopen($url,'r');
        $content = fread($request,100000);
        fclose($request);

        list($statuscode, $accuracy, $lat, $lng) = split(",", $content);


        if ($statuscode != 200)         //  errors occurred; the address was successfully parsedd.
        {
                // Versuch ohne Stra?e
                $url  = "http://maps.google.com/maps/geo?q=";
                $url .= $order->delivery['postcode'] . "," . $order->delivery['city'] . "," . $order->delivery['country'];
                $url .= "&output=csv&key=";
                $url .= GOOGLEMAP_APIKEY;
                $url = str_replace (" ", "%20", $url);          // Leerzeichen -> %20
                $request = fopen($url,'r');
                $content = fread($request,100000);
                fclose($request);

                list($statuscode, $accuracy, $lat, $lng) = split(",", $content);
        }
        if ($statuscode == 200)         // No errors occurred; the address was successfully parsed.
        {
                $latlng_query_raw = "insert into orders_to_latlng (orders_id, lat, lng) values ('$oID','$lat','$lng')";
                $latlng_query = tep_db_query($latlng_query_raw);
        }
} // endif versendet
} // endif check voor contribution google maps			
// eof order editor 5_0_8
			}

?>
  <table style="border: 1px solid #C9C9C9;" cellspacing="0" cellpadding="2" class="dataTableRow" id="commentsTable">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DELETE; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
   <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
   </tr>
<?php
$r = 0;
$orders_history_query = tep_db_query("SELECT orders_status_history_id, orders_status_id, date_added, customer_notified, comments 
                                    FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
									WHERE orders_id = '" . tep_db_prepare_input($_GET['oID']) . "' 
									ORDER BY date_added");
if (tep_db_num_rows($orders_history_query)) {
  while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          
		$r++;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        
	     echo '  <tr class="' . $rowClass . '" id="commentRow' . $orders_history['orders_status_history_id'] . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')">' . "\n" .
         '	  <td class="smallText" align="center"><div id="do_not_delete"><input name="update_comments[' . $orders_history['orders_status_history_id'] . '][delete]" type="checkbox" onClick="updateCommentsField(\'delete\', \'' . $orders_history['orders_status_history_id'] . '\', this.checked, \'\', this)"></div></td>' . "\n" . 
		 '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="center">';
    if ($orders_history['customer_notified'] == '1') {
      echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
    } else {
      echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
    }
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . 
  
  tep_draw_textarea_field("update_comments[" . $orders_history['orders_status_history_id'] . "][comments]", "soft", "40", "5", 
  "" .	tep_db_output($orders_history['comments']) . "", "onChange=\"updateCommentsField('update', '" . $orders_history['orders_status_history_id'] . "', 'false', encodeURIComponent(this.value))\"") . '' . "\n" .
		 
		 '    </td>' . "\n";
 
    echo '  </tr>' . "\n";
  
      }
    } else {
      echo '  <tr>' . "\n" .
       '    <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
       '  </tr>' . "\n";
      }
	
  ?>
  
  </table>
  
  <?php   }  // end if ($action == 'insert_new_comment')
     
	 //12. insert shipping method when one doesn't already exist
     if ($action == 'insert_shipping') {
	  
	  $order = new manualOrder($_GET['oID']);
	 
	  $Query = "INSERT INTO " . TABLE_ORDERS_TOTAL . " SET
	                orders_id = '" . $_GET['oID'] . "', 
					title = '" . $_GET['title'] . "', 
					text = '" . $currencies->format($_GET['value'], true, $order->info['currency'], $order->info['currency_value']) ."',
					value = '" . $_GET['value'] . "',
					class = 'ot_shipping',
					sort_order = '" . $_GET['sort_order'] . "'";
					tep_db_query($Query);
					
	  tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $_GET['id'] . "' WHERE orders_id = '" . $_GET['oID'] . "'");
	
	    $order = new manualOrder($_GET['oID']);
        $shippingKey = $order->adjust_totals($_GET['oID']);
        $order->adjust_zones();
        
        $cart = new manualCart();
        $cart->restore_contents($_GET['oID']);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();
		
		// Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();
  
  ?>
  
		<table width="100%">
		 <tr><td>
			 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
			  <tr>
                <td valign="top" width="100%">
				 <br>
				   <div>
					<a href="javascript:openWindow('<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID'] . '&step=1'); ?>','addProducts');"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT); ?></a><input type="hidden" name="subaction" value="">
					</div>
					<br>
				</td>
               
             
			  <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                <table border="0" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="15" nowrap onMouseover="ddrivetip('<?php echo oe_html_no_quote(HINT_TOTALS); ?>')"; onMouseout="hideddrivetip()"><img src="images/icon_info.gif" border="0" width="13" height="13" onLoad="reloadTotals()"></td>
                    <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                    <td class="dataTableHeadingContent" colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
                  </tr>
<?php
  for ($i=0; $i<sizeof($order->totals); $i++) {
   
    $id = $order->totals[$i]['class'];
	
    if ($order->totals[$i]['class'] == 'ot_shipping') {
	    $shipping_module_id = $order->info['shipping_id'];
	  } else {
	    $shipping_module_id = '';
	  } //end if ($order->totals[$i]['class'] == 'ot_shipping') {
   
    $rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
    if ( ($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      } else {
        echo '                    <td class="dataTableContent" valign="middle">&nbsp;</td>' . "\n";
      }
      
      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" value="' . trim($order->totals[$i]['title']) . '" readonly="readonly"></td>' . "\n";
	  
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td class="dataTableContent">&nbsp;</td>' . "\n";
      echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format($order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                  <tr class="' . $rowStyle . '" id="update_totals['.$i.']" style="visibility: hidden; display: none;">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');">' . tep_image('order_editor/images/minus.gif', IMAGE_REMOVE_NEW_OT) . '</a></td>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"><span id="update_totals['.$i.']"><a href="javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');">' . tep_image('order_editor/images/plus.gif', IMAGE_ADD_NEW_OT) . '</a></span></td>' . "\n";
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"  onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format($order->totals[$i]['value'], 2, '.', '') . '" size="6"  onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table></td>
                <!-- order_totals_eof //-->
              </tr>              
              <tr>
                <td valign="bottom">
                
<?php 
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table border="0" width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
		if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onclick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' . "\n" .
                 
    '   <td class="dataTableContent" valign="top" align="left" width="15px">' . "\n" .
	
	'   <input type="radio" name="shipping" id="shipping_radio_' . $r . '" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'">' . "\n" .
			 
	'   <input type="hidden" id="update_shipping['.$r.'][title]" name="update_shipping['.$r.'][title]" value="'.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):">' . "\n" .
			
    '   <input type="hidden" id="update_shipping['.$r.'][value]" name="update_shipping['.$r.'][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
	
	'      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .

			 '<td class="dataTableContent" valign="top">' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" . 

			 '<td class="dataTableContent" align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
             '                  </tr>';
      }
    }
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
  
  <?php
     } else {
     echo AJAX_NO_QUOTES;
     }
   ?>
                </td>
              </tr> 
            </table>
			
		  
		  </td></tr>
		</table>
	 
   <?php	 } //end if ($action == 'insert_shipping') {  

  //13. new order email 
   
    if ($action == 'new_order_email')  {
	
		$oID = tep_db_prepare_input($_GET['oID']);
		$order = new manualOrder($oID);
		
// bof order editor 5 0 9
		$order_totals_table_beginn = '<table border="0" cellpadding="5" cellspacing="0">';
		$order_totals_zelle_beginn = '<tr><td width="280" style="font-size: 12px">';
		$order_totals_zelle_mitte = '</td><td style="font-size: 12px" align="right">';
		$order_totals_zelle_end = '</td></tr>';
		$order_totals_table_end = '</table>';


		// initialized for the email confirmation
		if (EMAIL_USE_HTML == 'true'){
  		$products_ordered = $order_totals_table_beginn;
		} else{
  		$products_ordered = '';
		}

  	$subtotal = 0;
  	$total_tax = 0;

// eof order editor 5 0 9		
		
		for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
	  	//loop all the products in the order
		 	$products_ordered_attributes = '';
	  	if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
	    	for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
					$products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ' ' . $order->products[$i]['attributes'][$j]['value'];
      	}
    	}
	
// bof order editor 5 0 9
//	   	$products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . $products_model . ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
//		}
			$total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
    	$total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
    	$total_cost += $total_products_price;
      if (EMAIL_USE_HTML == 'true'){
          if ($order->products[$i]['model']) {
          	$products_ordered .= $order_totals_zelle_beginn . $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $order_totals_zelle_mitte . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . $order_totals_zelle_end;
	  			} else {
          	$products_ordered .= $order_totals_zelle_beginn . $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' = ' . $order_totals_zelle_mitte . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . $order_totals_zelle_end;
	  			}	
      } else {
        if ($order->products[$i]['model']) { 
					$products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";	
				} else {
					$products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";	
				}
   		}
 		} 
 		
    	$Text_Billing_Adress= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
    			             						 EMAIL_SEPARATOR . "\n" .
				     											 $order->billing['name'] . "\n";
			if ($order->billing['company']) {
		  	$Text_Billing_Adress .= $order->billing['company'] . "\n";
	    }
			$Text_Billing_Adress .= $order->billing['street_address'] . "\n";
			if ($order->billing['suburb']) {
				$Text_Billing_Adress .= $order->billing['suburb'] . "\n";
	    }
			$Text_Billing_Adress .= $order->billing['city'] . "\n";
		  if ($order->billing['state']) {
		  	$Text_Billing_Adress .= $order->billing['state'] . "\n";
			}
			$Text_Billing_Adress .= $order->billing['postcode'] . "\n" .
															$order->billing['country'] . "\n\n";
															

		 	$Text_Delivery_Address = "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" . 
        		            							EMAIL_SEPARATOR . "\n" .
																			$order->delivery['name'] . "\n";
			if ($order->delivery['company']) {
		  	$Text_Delivery_Address .= $order->delivery['company'] . "\n";
	    }
			$Text_Delivery_Address .= $order->delivery['street_address'] . "\n";
		  if ($order->delivery['suburb']) {
		  	$Text_Delivery_Address .= $order->delivery['suburb'] . "\n";
		  }
			$Text_Delivery_Address .= $order->delivery['city'] . "\n";
			if ($order->delivery['state']) {
		  	$Text_Delivery_Address .= $order->delivery['state'] . "\n";
	    }
			$Text_Delivery_Address .= $order->delivery['postcode'] . "\n" .	$order->delivery['country'] . "\n";
 		
		$standaard_email = 'false' ;
		if ( FILENAME_EMAIL_ORDER_TEXT !== FILENAME_EMAIL_ORDER_TEXT ) {	
			// only use if email order text is installed 
  		if (EMAIL_USE_HTML == 'true'){
  				$products_ordered .= $order_totals_table_end;
			}
 			if (EMAIL_USE_HTML == 'true'){
  				$text_query = tep_db_query("SELECT * FROM eorder_text where eorder_text_id = '2' and language_id = '" . $languages_id . "'");	
			} else{
  				$text_query = tep_db_query("SELECT * FROM eorder_text where eorder_text_id = '1' and language_id = '" . $languages_id . "'");
			}
      
      $werte = tep_db_fetch_array($text_query);
      $text = $werte["eorder_text_one"];
			$text = preg_replace('/<-STORE_NAME->/', STORE_NAME, $text);
			$text = preg_replace('/<-insert_id->/', $oID, $text);
			$text = preg_replace('/<-INVOICE_URL->/', tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL', false), $text);
			$text = preg_replace('/<-DATE_ORDERED->/', tep_date_long( $order->info[ 'date_purchased' ] ), $text ) ;
			if ($order->info['comments']) {
				$text = preg_replace('/<-Customer_Comments->/', tep_db_output($order->info['comments']), $text);
	  	} else{
	  		$text = preg_replace('/<-Customer_Comments->/', '', $text);
	  	}  
			$text = preg_replace('/<-Item_List->/', $products_ordered, $text);
			if (EMAIL_USE_HTML == 'true'){	
	    	$list_total = $order_totals_table_beginn;
	    	for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
					$list_total .= $order_totals_zelle_beginn . strip_tags($order->totals[$i]['title']) . $order_totals_zelle_mitte . strip_tags($order->totals[$i]['text']) . $order_totals_zelle_end;	
				}
	    	$list_total .= $order_totals_table_end;
			} else{
	    	for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
					$list_total .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
				}
			}	
			$text = preg_replace('/<-List_Total->/', $list_total, $text);
			if ($order->content_type != 'virtual') {
				$text = preg_replace('/<-DELIVERY_Adress->/', $Text_Delivery_Address , $text);				
			}
			elseif($order->content_type == 'virtual') {	
					if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
		  			$text = preg_replace('/<-DELIVERY_Adress->/', EMAIL_TEXT_DOWNLOAD_SHIPPING . "\n" . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL', false), $text);
					} else{
		  			$text = preg_replace('/<-DELIVERY_Adress->/', '', $text);
					}	
			} else{
	  		$text = preg_replace('/<-DELIVERY_Adress->/', '', $text);
			}
			$text = preg_replace('/<-BILL_Adress->/', $Text_Billing_Adress, $text); 
			$text = preg_replace('/<-Payment_Modul_Text->/', $order->info['payment_method'], $text);
	    $text = preg_replace('/<-Payment_Modul_Text_Footer->/', EMAIL_TEXT_FOOTER, $text);	  	
	  
			$text = preg_replace('/<-FIRMENANSCHRIFT->/', STORE_NAME_ADDRESS, $text);
			$text = preg_replace('/<-FINANZAMT->/', OWNER_BANK_FA, $text);
			$text = preg_replace('/<-STEUERNUMMER->/', OWNER_BANK_TAX_NUMBER, $text);
			$text = preg_replace('/<-USTID->/', OWNER_BANK_UST_NUMBER, $text);
			$text = preg_replace('/<-BANKNAME->/', OWNER_BANK_NAME, $text);
			$text = preg_replace('/<-KONTOINHABER->/', OWNER_BANK_ACCOUNT, $text);
			$text = preg_replace('/<-BLZ->/', STORE_OWNER_BLZ, $text);
			$text = preg_replace('/<-KONTONUMMER->/', OWNER_BANK, $text);
			$text = preg_replace('/<-SWIFT->/', OWNER_BANK_SWIFT, $text);
			$text = preg_replace('/<-IBAN->/', OWNER_BANK_IBAN, $text);
		  
	  	$email_order = $text;	
   	 } else {
   	 	// the contribution Email HTML is not installed so we must use the standaard text email
   	 	$standaard_email = 'true' ;
   	 }

   	 	
   	if ( $standaard_email == 'true' ) {
			//Build the standaard email
	  	$email_order = 	STORE_NAME . "\n" . 
      	             	EMAIL_SEPARATOR . "\n" . 
											EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" .
  										EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" .
            	    	  EMAIL_TEXT_DATE_MODIFIED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

	  	$email_order .= EMAIL_TEXT_PRODUCTS . "\n" . 
    		             	EMAIL_SEPARATOR . "\n" . 
        		          $products_ordered . 
          	  	      EMAIL_SEPARATOR . "\n";

	  	for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
        $email_order .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
    	}

	  	if ($order->content_type != 'virtual') {
    		$email_order .= $Text_Delivery_Address   ; 		
	  	}

    	$email_order .= $Text_Billing_Adress ;
    	
	  	$email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" . 
    		              EMAIL_SEPARATOR . "\n";
	    $email_order .= $order->info['payment_method'] . "\n\n";
		
		        
		//	if ( ($order->info['payment_method'] == ORDER_EDITOR_SEND_INFO_PAYMENT_METHOD) && (EMAIL_TEXT_PAYMENT_INFO) ) { 
		//     $email_order .= EMAIL_TEXT_PAYMENT_INFO . "\n\n";
		//   }
		//I'm not entirely sure what the purpose of this is so it is being shelved for now
	}
// eof order editor 5 0 9	

		if (EMAIL_TEXT_FOOTER) {
			$email_order .= EMAIL_TEXT_FOOTER . "\n\n";
	  }

    //code for plain text emails which changes the ? sign to EUR, otherwise the email will show ? instead of ?
    $email_order = str_replace("?","EUR",$email_order);
	  $email_order = str_replace("&nbsp;"," ",$email_order);

	  //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
	  $email_order = str_replace("<br>","\n",$email_order);
	  
	  // picture mode
	  $email_order = tep_add_base_ref($email_order);

	  //send the email to the customer
// bof order editor 5_0_8 	  
//	  tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
	  //tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
// bof added for pdfinvoice email attachment:
    if (FILENAME_PDF_INVOICE    !== 'FILENAME_PDF_INVOICE'    ) {
    	 if ( ORDER_EDITOR_ADD_PDF_INVOICE_EMAIL == 'true' ) {
        // All we do is set the order_id for pdfinvoice.php to pick up
        //$HTTP_GET_VARS['order_id'] = $insert_id;
        // set stream mode
        $stream = true;
        $oID= $_GET['oID'] ;
        $invoice_number = $_GET['oID'] ;
        $pdf_data = '' ;
        $pdf_data = include_once(FILENAME_PDF_INVOICE );       
        $file_name = $_GET['oID'] .'.pdf' ;
        // add text to email informing customer a pdf invoice copy has been attached:
        $email_order .= 'PDF attached' ."\n\n";
        $file_name = $_GET['oID'] .'.pdf' ;
        // send email with pdf invoice attached. Check to make sure pdfinvoice.php returns some data, else send standard email
        // note $order object reinstantiated by inclusion of pdfinvoice.php hence customer['name']
        if (tep_not_null($pdf_data)) {
            tep_mail_string_attachment($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $pdf_data, $file_name);
        } else {
            tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS );
        }
      } else {
        // send vanilla e-mail - if email attachment option is false
        tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS );
      }
    } else {
        // send vanilla e-mail - if email attachment option is false
        tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    }  
    
// eof added for pdfinvoice email attachment:

// eof order editor 5_0_8

   // send emails to other people as necessary
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  } 

  ?>
	
	<table>
	  <tr>
	    <td class="messageStackSuccess">
		  <?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_SUCCESS_EMAIL_SENT, $order->customer['email_address']); ?>
		</td>
	  </tr>
	</table>
	
	<?php } //end if ($action == 'new_order_email')  ?>