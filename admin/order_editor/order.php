<?php
/*
  $Id: order.php v5.0 07/19/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class manualOrder {
    var $info, $totals, $products, $customer, $delivery;

    function manualOrder($order_id) {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }

    function query($order_id) {
      global $shipping;
    
      $order_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
      $order = tep_db_fetch_array($order_query);

      $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");

	  while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] = array(
	  'title' => $totals['title'], 
	  'text' => $totals['text'], 
	  'class' => $totals['class'], 
	  'value' => $totals['value'],
	  'sort_order' => $totals['sort_order'], 
	  'orders_total_id' => $totals['orders_total_id']);
      }

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order['orders_status'],
                          'last_modified' => $order['last_modified'],
                          'shipping_method' => $shipping['title'],
                          'shipping_cost' => $shipping['cost'],
						  'shipping_id' => $order['shipping_module'],
                          'subtotal' => 0,
                          'tax' => 0,
                          'tax_groups' => array());
                          
      $this->customer = array('id' => $order['customers_id'],
	                          'name' => $order['customers_name'],
							  'company' => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
							  'country_id' => oe_get_country_id($order['customers_country']),
							  'zone_id' => oe_get_zone_id(oe_get_country_id($order['customers_country']), $order['customers_state']),  
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address']);

      $this->delivery = array('name' => $order['delivery_name'],
                              'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
							  'country_id' => oe_get_country_id($order['delivery_country']),
							  'zone_id' => oe_get_zone_id(oe_get_country_id($order['delivery_country']), $order['delivery_state']),
                              'format_id' => $order['delivery_address_format_id']);

      $this->billing = array('name' => $order['billing_name'],
                             'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
							 'country_id' => oe_get_country_id($order['billing_country']),
							 'zone_id' => oe_get_zone_id(oe_get_country_id($order['billing_country']), $order['billing_state']),
                             'format_id' => $order['billing_address_format_id']);

	  
      $index = 0;
      $orders_products_query = tep_db_query("select orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "' order by orders_products_id");
	
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        
        $orders_products_tax_query = tep_db_query("select products_tax_class_id from " .TABLE_PRODUCTS . " where products_id = " . $orders_products['products_id'] . "");
        $orders_products_tax = tep_db_fetch_array($orders_products_tax_query);
		 
        $this->products[$index] = array(		
        'qty' => $orders_products['products_quantity'],
        'name' => $orders_products['products_name'],
        'model' => $orders_products['products_model'],
        'tax' => $orders_products['products_tax'],
        'tax_description' => tep_get_tax_description($orders_products_tax['products_tax_class_id'], $this->delivery["country_id"], $this->delivery["zone_id"]),
        'price' => $orders_products['products_price'],
        'final_price' => $orders_products['final_price'],
        'products_id' => $orders_products['products_id'],
        'orders_products_id' => $orders_products['orders_products_id']);

        $subindex = 0;
        $attributes_query = tep_db_query("select products_options, products_options_values, options_values_price, price_prefix, orders_products_attributes_id from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'");
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
                                                                     'value' => $attributes['products_options_values'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price'],
                                             'orders_products_attributes_id' => $attributes['orders_products_attributes_id']);

            $subindex++;
          }
        }

        $shown_price = tep_add_tax($this->products[$index]['final_price'], $this->products[$index]['tax']) * $this->products[$index]['qty'];
        $this->info['subtotal'] += $shown_price;

        $products_tax = $this->products[$index]['tax'];
        $products_tax_description = $this->products[$index]['tax_description'];
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          if (isset($this->info['tax_groups']["$products_tax_description"])) {
            $this->info['tax_groups']["$products_tax_description"] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          } else {
            $this->info['tax_groups']["$products_tax_description"] = $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          }
        } else {
          $this->info['tax'] += ($products_tax / 100) * $shown_price;
          if (isset($this->info['tax_groups']["$products_tax_description"])) {
            $this->info['tax_groups']["$products_tax_description"] += ($products_tax / 100) * $shown_price;
          } else {
            $this->info['tax_groups']["$products_tax_description"] = ($products_tax / 100) * $shown_price;
          }
        }

        $index++;
      }

      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
      } else {
        $this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
      }
    }
 
    function adjust_zones() {
      $customer_country_id = oe_get_country_id($this->customer['country']);
      $this->customer['country'] = array('id' => $customer_country_id,
                                         'title' => $this->customer['country'],
                                         'iso_code_2' => oe_get_country_iso_code_2($customer_country_id),
                                         'iso_code_3' => oe_get_country_iso_code_3($customer_country_id));
      $this->customer['zone_id'] = oe_get_zone_id($customer_country_id, $this->customer['state']);

      $billing_country_id = oe_get_country_id($this->billing['country']);
      $this->billing['country'] = array('id' => $billing_country_id,
                                        'title' => $this->billing['country'],
                                        'iso_code_2' => oe_get_country_iso_code_2($billing_country_id),
                                        'iso_code_3' => oe_get_country_iso_code_3($billing_country_id));
      $this->billing['zone_id'] = oe_get_zone_id($billing_country_id, $this->billing['state']);

      $delivery_country_id = oe_get_country_id($this->delivery['country']);
      $this->delivery['country'] = array('id' => $delivery_country_id,
                                         'title' => $this->delivery['country'],
                                         'iso_code_2' => oe_get_country_iso_code_2($delivery_country_id),
                                         'iso_code_3' => oe_get_country_iso_code_3($delivery_country_id));
      $this->delivery['zone_id'] = oe_get_zone_id($delivery_country_id, $this->delivery['state']);   
    }
       
	       function adjust_totals($order_id) {
      $totals_query = tep_db_query("select orders_total_id, title, text, class, value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "' order by sort_order");
      $this->totals = array();
      $i=0;
      while ($totals = tep_db_fetch_array($totals_query)) {
        if ($totals['class'] == 'ot_shipping') $shipping_index = $i;
        $this->totals[] = array('title' => $totals['title'],
                                'text' => $totals['text'],
                                'class' => $totals['class'],
                                'value' => $totals['value'],
                                'orders_total_id' => $totals['orders_total_id']);
        $this->totals[] = array('title' => '',
                                'text' => '',
                                'class' => 'ot_custom',
                                'value' => '',
                                'orders_total_id' => '0');
        $i=$i+2;
      }
      array_pop($this->totals);
      
      return $shipping_index;
    }


  }
?>