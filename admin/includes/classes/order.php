<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class order {

    public $info, $totals, $products, $customer, $delivery, $content_type;

    public function __construct($order_id) {
      $this->info = [];
      $this->totals = [];
      $this->products = [];
      $this->customer = [];
      $this->delivery = [];

      $this->query($order_id);
    }

    public function query($order_id) {
      global $languages_id;

      $order_id = tep_db_prepare_input($order_id);

      $order_query = tep_db_query("SELECT * FROM orders WHERE orders_id = " . (int)$order_id);
      $order = tep_db_fetch_array($order_query);

      $totals_query = tep_db_query("SELECT title, text FROM orders_total WHERE orders_id = " . (int)$order_id . " ORDER BY sort_order");
      while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] =  [
          'title' => $totals['title'],
          'text' => $totals['text'],
        ];
      }

      $order_total_query = tep_db_query("SELECT text FROM orders_total WHERE orders_id = " . (int)$order_id . " AND class = 'ot_total'");
      $order_total = tep_db_fetch_array($order_total_query);

      $shipping_method_query = tep_db_query("SELECT title FROM orders_total WHERE orders_id = " . (int)$order_id . " AND class = 'ot_shipping'");
      $shipping_method = tep_db_fetch_array($shipping_method_query);

      $order_status_query = tep_db_query("SELECT orders_status_name FROM orders_status WHERE orders_status_id = " . $order['orders_status'] . " AND language_id = " . (int)$languages_id);
      $order_status = tep_db_fetch_array($order_status_query);

      $this->info = [
        'currency' => $order['currency'],
        'currency_value' => $order['currency_value'],
        'payment_method' => $order['payment_method'],
        'date_purchased' => $order['date_purchased'],
        'oid' => $order['orders_status'],
        'orders_status' => $order_status['orders_status_name'],
        'last_modified' => $order['last_modified'],
        'total' => strip_tags($order_total['text']),
        'shipping_method' => ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title'])),
      ];

      $this->customer = [
        'id' => $order['customers_id'],
        'name' => $order['customers_name'],
        'company' => $order['customers_company'],
        'street_address' => $order['customers_street_address'],
        'suburb' => $order['customers_suburb'],
        'city' => $order['customers_city'],
        'postcode' => $order['customers_postcode'],
        'state' => $order['customers_state'],
        'country' => ['title' => $order['customers_country']],
        'format_id' => $order['customers_address_format_id'],
        'telephone' => $order['customers_telephone'],
        'email_address' => $order['customers_email_address'],
      ];

      $this->delivery = [
        'name' => trim($order['delivery_name']),
        'company' => $order['delivery_company'],
        'street_address' => $order['delivery_street_address'],
        'suburb' => $order['delivery_suburb'],
        'city' => $order['delivery_city'],
        'postcode' => $order['delivery_postcode'],
        'state' => $order['delivery_state'],
        'country' => [ 'title' => $order['delivery_country']],
        'format_id' => $order['delivery_address_format_id']];

      if (empty($this->delivery['name']) && empty($this->delivery['street_address'])) {
        $this->delivery = false;
      }

      $this->billing = [
        'name' => $order['billing_name'],
        'company' => $order['billing_company'],
        'street_address' => $order['billing_street_address'],
        'suburb' => $order['billing_suburb'],
        'city' => $order['billing_city'],
        'postcode' => $order['billing_postcode'],
        'state' => $order['billing_state'],
        'country' => ['title' => $order['billing_country']],
        'format_id' => $order['billing_address_format_id'],
      ];

      $orders_products_query = tep_db_query("SELECT orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price FROM orders_products WHERE orders_id = " . (int)$order_id);
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        $current = [
          'qty' => $orders_products['products_quantity'],
          'id' => $orders_products['products_id'],
          'name' => $orders_products['products_name'],
          'model' => $orders_products['products_model'],
          'tax' => $orders_products['products_tax'],
          'price' => $orders_products['products_price'],
          'final_price' => $orders_products['final_price'],
        ];

        $attributes_query = tep_db_query("SELECT products_options, products_options_values, options_values_price, price_prefix FROM orders_products_attributes WHERE orders_id = " . (int)$order_id . " AND orders_products_id = " . (int)$orders_products['orders_products_id']);
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $current['attributes'][] = [
            'option' => $attributes['products_options'],
            'value' => $attributes['products_options_values'],
            'prefix' => $attributes['price_prefix'],
            'price' => $attributes['options_values_price'],
          ];
        }

        $this->info['tax_groups']["{$current['tax']}"] = '1';

        $this->products[] = $current;
      }
    }

  }
