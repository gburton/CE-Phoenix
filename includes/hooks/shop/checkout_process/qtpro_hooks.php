<?php
/*
  $Id: qtpro_hooks.php
  $Loc: catalog/includes/hooks/shop/qtpro/

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  2018 QTPro 5.6.3 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  Copyright (c) 2016 Rainer Schmied

  Released under the GNU General Public License
*/

class hook_shop_checkout_process_qtpro_hooks {

  function listen_StockCheckProcess() {
    global $order;
    
    require('includes/classes/order_qtpro.php');
    $order = new order_qtpro;

    $any_out_of_stock = false;
    if (STOCK_CHECK == 'true') {
      $check_stock = array();
      for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
        if (isset($order->products[$i]['attributes']) && is_array($order->products[$i]['attributes'])) {
          $attributes = array();
          foreach ($order->products[$i]['attributes'] as $attribute) {
            $attributes[$attribute['option_id']]=$attribute['value_id'];
          }
          $check_stock[$i] = $this->check_stock_qtpro($order->products[$i]['id'], $order->products[$i]['qty'], $attributes);
        } else {
          $check_stock[$i] = $this->check_stock_qtpro($order->products[$i]['id'], $order->products[$i]['qty']);
        }
        if ($check_stock[$i]) {
          $any_out_of_stock = true;
        }
      }    // Out of Stock
      if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }
    }
  }
  
  function listen_StockUpdateProcess() {
    global $order, $i, $insert_id, $sql_data_array;
    $products_stock_attributes = null;
    if (STOCK_LIMITED == 'true') {
      $products_attributes = $order->products[$i]['attributes'];
      if (DOWNLOAD_ENABLED == 'true') {
        $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename 
                            FROM products p
                            LEFT JOIN products_attributes pa
                             ON p.products_id=pa.products_id
                            LEFT JOIN products_attributes_download pad
                             ON pa.products_attributes_id=pad.products_attributes_id
                            WHERE p.products_id = '" . tep_get_prid($order->products[$i]['id']) . "'";
// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
        if (is_array($products_attributes)) {
          $stock_query_raw .= " AND pa.options_id = '" . (int)$products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . (int)$products_attributes[0]['value_id'] . "'";
        }
        $stock_query = tep_db_query($stock_query_raw);
      } else {
        $stock_query = tep_db_query("select products_quantity from products where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
      }
      if (tep_db_num_rows($stock_query) > 0) {
        $stock_values = tep_db_fetch_array($stock_query);
        $actual_stock_bought = $order->products[$i]['qty'];
        $download_selected = false;
        if ((DOWNLOAD_ENABLED == 'true') && isset($stock_values['products_attributes_filename']) && tep_not_null($stock_values['products_attributes_filename'])) {
          $download_selected = true;
          $products_stock_attributes='$$DOWNLOAD$$';
        }
        
// If not downloadable and attributes present, adjust attribute stock
        if ( !$download_selected && is_array($products_attributes) ) {
          $all_nonstocked = true;
          $products_stock_attributes_array = array();
          foreach ($products_attributes as $attribute) {
            if ($attribute['track_stock'] == 1) {
              $products_stock_attributes_array[] = $attribute['option_id'] . "-" . $attribute['value_id'];
              $all_nonstocked = false;
            }
          } 
          if ($all_nonstocked) {
            $actual_stock_bought = $order->products[$i]['qty'];
          }  else {
            asort($products_stock_attributes_array, SORT_NUMERIC);
            $products_stock_attributes = implode(",", $products_stock_attributes_array);
            $attributes_stock_query = tep_db_query("select products_stock_quantity from products_stock where products_stock_attributes = '$products_stock_attributes' AND products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
            if (tep_db_num_rows($attributes_stock_query) > 0) {
              $attributes_stock_values = tep_db_fetch_array($attributes_stock_query);
              $attributes_stock_left = $attributes_stock_values['products_stock_quantity'] - $order->products[$i]['qty'];
              tep_db_query("update products_stock set products_stock_quantity = '" . $attributes_stock_left . "' where products_stock_attributes = '$products_stock_attributes' AND products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
              $actual_stock_bought = ($attributes_stock_left < 1) ? $attributes_stock_values['products_stock_quantity'] : $order->products[$i]['qty'];
            } else {
              $attributes_stock_left = 0 - $order->products[$i]['qty'];
              tep_db_query("insert into products_stock (products_id, products_stock_attributes, products_stock_quantity) values ('" . tep_get_prid($order->products[$i]['id']) . "', '" . $products_stock_attributes . "', '" . $attributes_stock_left . "')");
              $actual_stock_bought = 0;
            }
          }
        }
      }
    }

// Update products_ordered (for bestsellers list)
    tep_db_query("update products set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");

    if (!isset($products_stock_attributes)) $products_stock_attributes = null;
    $stock_data_array = array('products_stock_attributes' => $products_stock_attributes);
    $sql_data_array = array_merge($sql_data_array, $stock_data_array);
  
  }

////
// Check if the required stock is available
// If insufficent stock is available return $out_of_stock = true
  function check_stock_qtpro($products_id, $products_quantity, $attributes=array()) {
    $stock_left = $this->get_products_stock_qtpro($products_id, $attributes) - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
        $out_of_stock = true;
    }

    return $out_of_stock;
  }

////
// Return a product's stock
// TABLES: products. products_stock
  function get_products_stock_qtpro($products_id, $attributes=array()) {
    global $languages_id;
    $products_id = tep_get_prid($products_id);
    $all_nonstocked = true;
    if (sizeof($attributes)>0) {
      $attr_list='';
      $options_list=implode(",",array_keys($attributes));
      $track_stock_query=tep_db_query("select products_options_id, products_options_track_stock from products_options where products_options_id in ($options_list) and language_id= '" . (int)$languages_id . "order by products_options_id'");
      while($track_stock_array=tep_db_fetch_array($track_stock_query)) {
        if ($track_stock_array['products_options_track_stock']) {
          $attr_list.=$track_stock_array['products_options_id'] . '-' . $attributes[$track_stock_array['products_options_id']] . ',';
          $all_nonstocked=false;
        }
      }
      $attr_list=substr($attr_list,0,strlen($attr_list)-1);
    }
    
    if ((sizeof($attributes)==0) | ($all_nonstocked)) {
      $stock_query = tep_db_query("select products_quantity as quantity from products where products_id = '" . (int)$products_id . "'");
    } else {
      $stock_query=tep_db_query("select products_stock_quantity as quantity from products_stock where products_id='". (int)$products_id . "' and products_stock_attributes='$attr_list'");
    }
    if (tep_db_num_rows($stock_query)>0) {
      $stock=tep_db_fetch_array($stock_query);
      $quantity=$stock['quantity'];
    } else {
      $quantity = 0;
    }
    return $quantity;
  }

}
