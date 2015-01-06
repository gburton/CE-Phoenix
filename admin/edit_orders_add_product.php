<?php
/*
  $Id: edit_orders_add_product.php v5.0.5 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032
  
*/

  require('includes/application_top.php');

  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include(DIR_WS_LANGUAGES . $language. '/' . FILENAME_ORDERS_EDIT);

  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $oID = tep_db_prepare_input((int)$_GET['oID']);
  $order = new manualOrder($oID);

  // Setup variables
  $step = ((isset($_POST['step'])) ? (int)$_POST['step'] : 1);
  $add_product_categories_id = ((isset($_POST['add_product_categories_id'])) ? (int)$_POST['add_product_categories_id'] : '');
  $add_product_products_id = ((isset($_POST['add_product_products_id'])) ? (int)$_POST['add_product_products_id'] : 0);

  // $_GET['action'] switch
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
    
    ////
    // Add a product to the virtual cart
      case 'add_product':
        if ($step != 5) break;
        
        $AddedOptionsPrice = 0;
        
        // Get Product Attribute Info
        if (isset($_POST['add_product_options'])) {
          foreach($_POST['add_product_options'] as $option_id => $option_value_id) {
            $result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa INNER JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON (po.products_options_id = pa.options_id and po.language_id = '" . $languages_id . "') INNER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov on (pov.products_options_values_id = pa.options_values_id and pov.language_id = '" . $languages_id . "') WHERE products_id = '" . $add_product_products_id . "' and options_id = '" . $option_id . "' and options_values_id = '" . $option_value_id . "'");
            $row = tep_db_fetch_array($result);
			if (is_array($row)) extract($row, EXTR_PREFIX_ALL, "opt");
					if ($opt_price_prefix == '-')
					{$AddedOptionsPrice -= $opt_options_values_price;}
					else //default to positive
					{$AddedOptionsPrice += $opt_options_values_price;}
            $option_value_details[$option_id][$option_value_id] = array (
					"options_values_price" => $opt_options_values_price,
					"price_prefix" => $opt_price_prefix);
            $option_names[$option_id] = $opt_products_options_name;
            $option_values_names[$option_value_id] = $opt_products_options_values_name;
			
		//add on for downloads
		if (DOWNLOAD_ENABLED == 'true') {
        $download_query_raw ="SELECT products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount 
        FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " 
        WHERE products_attributes_id='" . $opt_products_attributes_id . "'";
        
		$download_query = tep_db_query($download_query_raw);
        if (tep_db_num_rows($download_query) > 0) {
          $download = tep_db_fetch_array($download_query);
          $filename[$option_id] = $download['products_attributes_filename'];
          $maxdays[$option_id]  = $download['products_attributes_maxdays'];
          $maxcount[$option_id] = $download['products_attributes_maxcount'];
        } //end if (tep_db_num_rows($download_query) > 0) {
		} //end if (DOWNLOAD_ENABLED == 'true') {
		//end downloads 
		
          } //end foreach($_POST['add_product_options'] as $option_id => $option_value_id) {
        } //end if (isset($_POST['add_product_options'])) {
		
        
        // Get Product Info
        //BOF Added languageid (otherwise products_name is empty)
        //$product_query = tep_db_query("select p.products_model, p.products_price, pd.products_name, p.products_tax_class_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id = p.products_id where p.products_id = '" . (int)$add_product_products_id . "'");
        $product_query = tep_db_query("select p.products_model, p.products_price, pd.products_name, p.products_tax_class_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id = p.products_id where p.products_id = '" . (int)$add_product_products_id . "' and pd.language_id = '" . $languages_id . "'");
        //EOF Added languageid
        $product = tep_db_fetch_array($product_query);
        $country_id = oe_get_country_id($order->delivery["country"]);
        $zone_id = oe_get_zone_id($country_id, $order->delivery['state']);
        $products_tax = tep_get_tax_rate($product['products_tax_class_id'], $country_id, $zone_id);
		
		
			// 2.1.3  Pull specials price from db if there is an active offer
			$special_price = tep_db_query("
			SELECT specials_new_products_price 
			FROM " . TABLE_SPECIALS . " 
			WHERE products_id =". $add_product_products_id . " 
			AND status");
			$new_price = tep_db_fetch_array($special_price);
			
			if ($new_price) 
			{ $product['products_price'] = $new_price['specials_new_products_price']; }
			
	        //sppc patch
	        //Set to false by default, configurable in the Order Editor section of the admin panel
	        //thanks to whistlerxj for the original version of this patch
    
	        if (ORDER_EDITOR_USE_SPPC == 'true') {
	
	        // first find out the customer associated with this order ID..
            $c_id_result = tep_db_query('SELECT customers_id 
	        FROM orders 
	        WHERE orders_id="' . (int)$oID . '"');
	
            $cid = tep_db_fetch_array($c_id_result);
            if ($cid){
            $cust_id = $cid['customers_id'];
            // now find the customer's group.
            $c_g_id_result = tep_db_query('SELECT customers_group_id 
	        FROM customers 
        	WHERE customers_id="' . $cust_id . '"');
	
            $c_g_id = tep_db_fetch_array($c_g_id_result);
            if ($c_g_id){
            $cust_group_id = $c_g_id['customers_group_id'];
            // get the price of the product from the products_groups table.
            $price_result = tep_db_query('SELECT customers_group_price 
	        FROM products_groups 
         	WHERE products_id="' . $add_product_products_id . '" 
        	AND customers_group_id="' . $cust_group_id . '"');
	
            $price_array = tep_db_fetch_array($price_result);
            if ($price_array){
            // set the price of the new product to the group specific price.
            $product['products_price'] = $price_array['customers_group_price'];
               }
              }
             }
         	}
	        //end sppc patch   

        $sql_data_array = array('orders_id' => tep_db_prepare_input($oID),
                                'products_id' => tep_db_prepare_input($add_product_products_id),
                                'products_model' => tep_db_prepare_input($product['products_model']),
                                'products_name' => tep_db_prepare_input($product['products_name']),
                                'products_price' => tep_db_prepare_input($product['products_price']),
                                'final_price' => tep_db_prepare_input(($product['products_price'] + $AddedOptionsPrice)),
                                'products_tax' => tep_db_prepare_input($products_tax),
                                'products_quantity' => tep_db_prepare_input($_POST['add_product_quantity']));
        tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
        $new_product_id = tep_db_insert_id();
        
        if (isset($_POST['add_product_options'])) {
          foreach($_POST['add_product_options'] as $option_id => $option_value_id) {
            $sql_data_array = array('orders_id' => tep_db_prepare_input($oID),
                                    'orders_products_id' => tep_db_prepare_input($new_product_id),
                                    'products_options' => tep_db_prepare_input($option_names[$option_id]),
                                    'products_options_values' => tep_db_prepare_input($option_values_names[$option_value_id]),
             'options_values_price' => tep_db_prepare_input($option_value_details[$option_id][$option_value_id]['options_values_price']),
             'price_prefix' => tep_db_prepare_input($option_value_details[$option_id][$option_value_id]['price_prefix']));
            tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

			
		//add on for downloads
		if (DOWNLOAD_ENABLED == 'true' && isset($filename[$option_id])) {
		
		$Query = "INSERT INTO " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET
				orders_id = '" . tep_db_prepare_input($oID) . "',
				orders_products_id = '" . tep_db_prepare_input($new_product_id) . "',
				orders_products_filename = '" . tep_db_prepare_input($filename[$option_id]) . "',
				download_maxdays = '" . tep_db_prepare_input($maxdays[$option_id]) . "',
	            download_count = '" . tep_db_prepare_input($maxcount[$option_id]) . "'";
						
					tep_db_query($Query);
					
       	} //end if (DOWNLOAD_ENABLED == 'true') {
		//end downloads 
          }
        }
		
		// Update inventory Quantity
			// This is only done if store is set up to use stock
			if (STOCK_LIMITED == 'true'){
				tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
				products_quantity = products_quantity - " . $_POST['add_product_quantity'] . " 
				WHERE products_id = '" . $_POST['add_product_products_id'] . "'");
// QT Pro Addon BOF		    
				if (ORDER_EDITOR_USE_QTPRO == 'true') { 
        if (isset($_POST['add_product_options'])) {
        	foreach($_POST['add_product_options'] as $option_id => $option_value_id) {
        		$products_stock_attributes[] = $option_id . '-'. $option_value_id;
        	} // for loop
        	sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
        	$products_stock_attributes = implode($products_stock_attributes, ',');
        	$stock_chk_q = tep_db_query("select * from " . TABLE_PRODUCTS_STOCK . " where products_id=" . $_POST['add_product_products_id']. " and products_stock_attributes='".$products_stock_attributes."'");
          $stock_chk_arr = tep_db_fetch_array($stock_chk_q);
          $new_quantity = $stock_chk_arr['products_stock_quantity'] - $_POST['add_product_quantity'];
          // update the stock
          tep_db_query("update ".TABLE_PRODUCTS_STOCK." set products_stock_quantity = ".$new_quantity." where products_id=" . $_POST['add_product_products_id']. " and products_stock_attributes='".$products_stock_attributes."'");
        }
				}
// QT Pro Addon EOF
			}
			// Update products_ordered info
			tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
			products_ordered = products_ordered + " . $_POST['add_product_quantity'] . "
			WHERE products_id = '" . $_POST['add_product_products_id'] . "'");
        
        // Unset selected product & category
        $add_product_categories_id = 0;
        $add_product_products_id = 0;
        
			 
		tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $oID . '&step=1&submitForm=yes'));
        
		break;
    }
  }

 
////
// Generate product list based on chosen category or search keywords
  $not_found = true;
  if (isset($_POST['search'])) {
    $search_array = explode(" ", $_POST['product_search']);
    $search_array = oe_clean_SQL_keywords($search_array);
    if (sizeof($search_array) <= 1) {
      $search_fields = array('p.products_id', 'p.products_price', 'p.products_model', 'pd.products_name');
      $product_search = oe_generate_search_SQL($search_array, $search_fields);
    } else {
      $search_fields = array('pd.products_name');
      $product_search = oe_generate_search_SQL($search_array, $search_fields, 'AND');
    }
  
    $products_query = tep_db_query("select p.products_id, p.products_price, p.products_model, pd.products_name from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on (p.products_id = pd.products_id) where pd.language_id = '" . $languages_id . "' and (" . $product_search . ") order by pd.products_name");
    $not_found = ((tep_db_num_rows($products_query)) ? false : true);
  } 
  
  if (!isset($_POST['search'])) {
    $product_search = " where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id ";
    
    $_GET['inc_subcat'] = '1';
    if ($_GET['inc_subcat'] == '1') {
      $subcategories_array = array();
      oe_get_subcategories($subcategories_array, $add_product_categories_id);
      $product_search .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and (p2c.categories_id = '" . (int)$add_product_categories_id . "'";
      for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) {
        $product_search .= " or p2c.categories_id = '" . $subcategories_array[$i] . "'";
      }
      $product_search .= ")";
    } else {
      $product_search .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p2c.categories_id = '" . (int)$add_product_categories_id . "'";
    }

    $products_query = tep_db_query("select distinct p.products_id, p.products_price, p.products_model, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c " . $product_search . " order by pd.products_name");
    $not_found = ((tep_db_num_rows($products_query)) ? false : true);
  }

  $category_array = array(array('id' => '', 'text' => TEXT_SELECT_CATEGORY),
                          array('id' => '0', 'text' => TEXT_ALL_CATEGORIES));
  
  if (($step > 1) && (!$not_found)) {
    $product_array = array(array('id' => 0, 'text' => TEXT_SELECT_PRODUCT));
    while($products = tep_db_fetch_array($products_query)) {
      $product_array[] = array('id' => $products['products_id'],
                               'text' => $products['products_name'] . ' (' . $products['products_model'] . ')' . ':&nbsp;' . $currencies->format($products['products_price'], true, $order->info['currency'], $order->info['currency_value']));
    }
  }

  $has_attributes = false;
  $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$add_product_products_id . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
  $products_attributes = tep_db_fetch_array($products_attributes_query);
  if ($products_attributes['total'] > 0) $has_attributes = true;   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php if ( (isset($_GET['submitForm'])) && ($_GET['submitForm'] == 'yes') ) {
        echo '<script language="javascript" type="text/javascript"><!--' . "\n" .
             '  window.opener.document.edit_order.subaction.value = "add_product";' . "\n" . 
             '  window.opener.document.edit_order.submit();' . "\n" .
             '//--></script>';
			 }
	?>
</head>

<body>
<!-- body //-->
	 <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;" align="center">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="3" align="center"><?php echo sprintf(ADDING_TITLE, $oID); ?></td>
          </tr>
          <tr class="dataTableRow">
           <form action="<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID']); ?>" method="POST">
            <td class="dataTableContent" align="right"><?php echo TEXT_STEP_1; ?></td>
            <td class="dataTableContent" valign="top"><?php echo tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree('0', '', '0', $category_array), $add_product_categories_id,'style="width:300px;" onchange="this.form.submit();"'); ?></td>
            <td class="dataTableContent" align="center">
			  <noscript>
			    <input type="submit" value="<?php echo TEXT_BUTTON_SELECT_CATEGORY; ?>">
			  </noscript>
			    <input type="hidden" name="step" value="2">
			 </td>
           </form>
          </tr>
          <tr class="dataTableRow">
            <td class="dataTableContent" colspan="3" align="center"><?php echo TEXT_PRODUCT_SEARCH; ?></td>
          </tr>
          <tr class="dataTableRow">
          <form action="<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID']); ?>" method="POST">
            <td>&nbsp;</td>
            <td class="dataTableContent" valign="top">&nbsp;<input type="text" name="product_search" value="<?php if(isset($_POST['product_search'])) echo $_POST['product_search']; ?>" onchange="this.form.submit();">
			</td>
            <td class="dataTableContent" align="center"><noscript><input type="submit" value="Search for This Product"></noscript><input type="hidden" name="step" value="2"><input type="hidden" name="search" value="1"></td>
          </form>
          </tr>
        <?php if ($not_found) { ?>
          <tr class="dataTableRow">
            <td class="dataTableContent" colspan="3" align="center"><?php echo TEXT_PRODUCT_NOT_FOUND; ?></td>
          </tr>
        <?php } ?>
<?php
  if (($step > 1) && (!$not_found)) {
    echo '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="border-bottom: 1px solid #C9C9C9;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
         '          </tr>' . "\n" .
         '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="background: #FFFFFF;">' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
         '          </tr>' . "\n";
?>
          <tr class="dataTableRow"> 
            <td colspan="3" style="border-top: 1px solid #C9C9C9;"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
          </tr>
          <tr class="dataTableRow">
          <form action="<?php echo tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID']); ?>" method="POST">
            <td class="dataTableContent" align="right"><?php echo TEXT_STEP_2; ?></td>
            <td class="dataTableContent" valign="top"><?php echo tep_draw_pull_down_menu('add_product_products_id', $product_array, $add_product_products_id, 'style="width:300px;" onchange="this.form.submit();"'); ?></td>
            <td class="dataTableContent" align="center"><noscript><input type="submit" value="<?php echo TEXT_BUTTON_SELECT_PRODUCT; ?>"></noscript><input type="hidden" name="step" value="3">
            <input type="hidden" name="add_product_categories_id" value="<?php echo $add_product_categories_id; ?>">
          <?php if (isset($_POST['search'])) { ?>
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="product_search" value="<?php echo $_POST['product_search']; ?>">
          <?php } ?>
            </td>
          </form>
          </tr>
<?php
  }

  if (($step > 2) && ($add_product_products_id > 0)) {
    echo '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="border-top: 1px solid #C9C9C9;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
         '          </tr>' . "\n" .
         '          <tr class="dataTableRow">' . "\n";
    
    if ($has_attributes) echo '          <form action="' . tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID']) . '" method="post">' . "\n";

    echo '            <td class="dataTableContent" align="right">' . TEXT_STEP_3 . '</td>' . "\n";

    if ($has_attributes) {
      $i=1;
      $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$add_product_products_id . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
      while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
        $selected = 0;
        $products_options_array = array();
        if ($i > 1) echo '            <td class="dataTableContent">&nbsp;</td>' . "\n";
        $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$add_product_products_id . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "'");
        while ($products_options = tep_db_fetch_array($products_options_query)) {
          $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options_name['products_options_name'] . ' - ' . $products_options['products_options_values_name']);
          if ($products_options['options_values_price'] != '0') {
            $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->format($products_options['options_values_price'], true, $order->info['currency'], $order->info['currency_value']) .')';
          }
        }
		
		if(isset($_POST['add_product_options'])) {
          $selected_attribute = $_POST['add_product_options'][$products_options_name['products_options_id']];
        } else {
          $selected_attribute = false;
        }
		
        echo   '            <td class="dataTableContent" valign="top">' . tep_draw_pull_down_menu('add_product_options[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute) . '</td>' . "\n" .
               '            <td class="dataTableContent">&nbsp;</td>' . "\n" .
               '          </tr>' . "\n" .
               '          <tr class="dataTableRow">' . "\n";  
        $i++;
      }
      echo '            <td class="dataTableContent">&nbsp;</td>' . "\n" .
           '            <td class="dataTableContent" colspan="2" align="left"><input type="submit" value="' . TEXT_BUTTON_SELECT_OPTIONS . '"><input type="hidden" name="step" value="4"><input type="hidden" name="add_product_categories_id" value="' . $add_product_categories_id . '"><input type="hidden" name="add_product_products_id" value="' . $add_product_products_id . '">' . ((isset($_POST['search'])) ? '<input type="hidden" name="search" value="1"><input type="hidden" name="product_search" value="' . $_POST['product_search'] . '">' : '') . '</td>' . "\n" .
           '          </tr>' . "\n" .
           '          </form>' . "\n";
    } else {
      $step = 4;
      echo '            <td class="dataTableContent" valign="top" colspan="2">' . TEXT_SKIP_NO_OPTIONS . '</td>' . "\n" .
           '          </tr>' . "\n";
    }
  }
  
  if ($step > 3) {
    echo '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="border-bottom: 1px solid #C9C9C9;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
         '          </tr>' . "\n" .
         '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="background: #FFFFFF;">' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
         '          </tr>' . "\n" .
         '          <tr class="dataTableRow">' . "\n" .
         '            <td colspan="3" style="border-top: 1px solid #C9C9C9;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
         '          </tr>' . "\n" .
         '          <form action="' . tep_href_link(FILENAME_ORDERS_EDIT_ADD_PRODUCT, 'oID=' . $_GET['oID'] . '&action=add_product') . '" method="post">' . "\n" .
         '          <tr class="dataTableRow">' . "\n" .
         '            <td class="dataTableContent" align="right" valign="middle">' . TEXT_STEP_4 . '</td>' . "\n" .
         '            <td class="dataTableContent" align="left" valign="middle">' . TEXT_QUANTITY . '&nbsp;<input name="add_product_quantity" size="3" value="1"></td>' . "\n" .
         '            <td class="dataTableContent" align="center" valign="middle"></td>' . "\n" .
		 '          </tr>' . "\n" . 
		 '          <tr class="dataTableRow">' . "\n" .
		 '             <td></td>' . "\n" . 
		 '             <td colspan="2"><input type="submit" value="' . TEXT_BUTTON_ADD_PRODUCT .'">' . "\n" .
		 '           ';
    if (isset($_POST['add_product_options'])) {
      foreach($_POST['add_product_options'] as $option_id => $option_value_id) {
        echo '<input type="hidden" name="add_product_options['.$option_id.']" value="' . $option_value_id . '">';
      }
    }
    echo '<input type="hidden" name="add_product_categories_id" value="' . $add_product_categories_id . '"><input type="hidden" name="add_product_products_id" value="' . $add_product_products_id . '"><input type="hidden" name="step" value="5"></td>' . "\n" .
         '          </tr>' . "\n" .
         '          </form>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table>
    <!-- body_text_eof //-->
 
           <div align="center" class="dataTableContent">
                   
				   <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<a href=\"javascript:self.close();\"><?php echo TEXT_CLOSE_POPUP; ?></a>");
	               //-->
                  </script>
				  
				  <noscript>
				   <strong>
				    <?php echo TEXT_ADD_PRODUCT_INSTRUCTIONS; ?>
                   </strong>
				  </noscript>
				  
		   </div>
      
	
<!-- body_eof //-->

</body>
</html>
<?php  //eof   ?>