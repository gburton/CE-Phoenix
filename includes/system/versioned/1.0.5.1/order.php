<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class order {

    public $info, $totals, $products, $customer, $delivery, $content_type;

    public function __construct($order_id = '') {
      $this->info = [];
      $this->totals = [];
      $this->products = [];
      $this->customer = [];
      $this->delivery = [];

      if (tep_not_null($order_id)) {
        $this->query($order_id);
      } else {
        $this->cart();
      }
    }

    public function query($order_id) {
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

      $order_status_query = tep_db_query("SELECT orders_status_name FROM orders_status WHERE orders_status_id = " . $order['orders_status'] . " AND language_id = " . (int)$_SESSION['languages_id']);
      $order_status = tep_db_fetch_array($order_status_query);

      $this->info = [
        'currency' => $order['currency'],
        'currency_value' => $order['currency_value'],
        'payment_method' => $order['payment_method'],
        'date_purchased' => $order['date_purchased'],
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
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            $current['attributes'][] = [
              'option' => $attributes['products_options'],
              'value' => $attributes['products_options_values'],
              'prefix' => $attributes['price_prefix'],
              'price' => $attributes['options_values_price'],
            ];
          }
        }

        $this->info['tax_groups']["{$current['tax']}"] = '1';

        $this->products[] = $current;
      }
    }

    public function cart() {
      global $currencies, $customer;

      $this->info = [
        'order_status' => DEFAULT_ORDERS_STATUS_ID,
        'currency' => $_SESSION['currency'],
        'currency_value' => $currencies->currencies[$_SESSION['currency']]['value'],
        'payment_method' => $_SESSION['payment'],
        'shipping_method' => $_SESSION['shipping']['title'],
        'shipping_cost' => $_SESSION['shipping']['cost'],
        'subtotal' => 0,
        'tax' => 0,
        'tax_groups' => [],
        'comments' => ($_SESSION['comments'] ?? ''),
      ];

      if (is_string($_SESSION['payment']) && (($GLOBALS[$_SESSION['payment']] ?? null) instanceof $_SESSION['payment'])) {
        $this->info['payment_method'] = $GLOBALS[$_SESSION['payment']]->public_title ?? $GLOBALS[$_SESSION['payment']]->title;

        if ( is_numeric($GLOBALS[$_SESSION['payment']]->order_status ?? null) && ($GLOBALS[$_SESSION['payment']]->order_status > 0) ) {
          $this->info['order_status'] = $GLOBALS[$_SESSION['payment']]->order_status;
        }
      }

      $this->customer = $customer->fetch_to_address(0);
      $this->billing = $customer->fetch_to_address($_SESSION['billto']);

      $this->content_type = $_SESSION['cart']->get_content_type();
      if ( !$_SESSION['sendto'] && ('virtual' !== $this->content_type) ) {
        $_SESSION['sendto'] = $customer->get('default_sendto');
      }

      $this->delivery = $customer->fetch_to_address($_SESSION['sendto']);

      if ('virtual' === $this->content_type) {
        $tax_address = [
          'entry_country_id' => $this->billing['country']['id'],
          'entry_zone_id' => $this->billing['zone_id'],
        ];
      } else {
        $tax_address = [
          'entry_country_id' => $this->delivery['country']['id'],
          'entry_zone_id' => $this->delivery['zone_id'],
        ];
      }

      foreach ($_SESSION['cart']->get_products() as $product) {
        $current = [
          'qty' => $product['quantity'],
          'name' => $product['name'],
          'model' => $product['model'],
          'tax' => tep_get_tax_rate($product['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
          'tax_description' => tep_get_tax_description($product['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
          'price' => $product['price'],
          'final_price' => $product['price'] + $_SESSION['cart']->attributes_price($product['id']),
          'weight' => $product['weight'],
          'id' => $product['id'],
        ];

        if ($product['attributes']) {
          foreach ($product['attributes'] as $option => $value) {
            $attributes_query = tep_db_query("SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix FROM products_options  popt, products_options_values poval, products_attributes pa WHERE pa.products_id = " . (int)$product['id'] . " AND pa.options_id = " . (int)$option . " AND pa.options_id = popt.products_options_id AND pa.options_values_id = " . (int)$value . " AND pa.options_values_id = poval.products_options_values_id AND popt.language_id = " . (int)$_SESSION['languages_id'] . " AND poval.language_id = " . (int)$_SESSION['languages_id']);
            $attributes = tep_db_fetch_array($attributes_query);

            $current['attributes'][] = [
              'option' => $attributes['products_options_name'],
              'value' => $attributes['products_options_values_name'],
              'option_id' => $option,
              'value_id' => $value,
              'prefix' => $attributes['price_prefix'],
              'price' => $attributes['options_values_price'],
            ];
          }
        }

        $shown_price = $currencies->calculate_price($current['final_price'], $current['tax'], $current['qty']);
        $this->info['subtotal'] += $shown_price;

        $products_tax = $current['tax'];
        $products_tax_description = $current['tax_description'];
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $tax = $shown_price - ($shown_price / ((($products_tax < 10) ? "1.0" : "1.") . str_replace('.', '', $products_tax)));
        } else {
          $tax = ($products_tax / 100) * $shown_price;
        }

        $this->info['tax'] += $tax;
        if (!isset($this->info['tax_groups']["$products_tax_description"])) {
          $this->info['tax_groups']["$products_tax_description"] = 0;
        }
        $this->info['tax_groups']["$products_tax_description"] += $tax;

        $this->products[] = $current;
      }

      $this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
      if (DISPLAY_PRICE_WITH_TAX != 'true') {
        $this->info['total'] += $this->info['tax'];
      }
    }

  }
