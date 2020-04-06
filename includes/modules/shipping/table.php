<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class table extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_SHIPPING_TABLE_';

    public $icon;

// class constructor
    function __construct() {
      global $order;

      parent::__construct();

      if ( $this->enabled ) {
        $this->icon = '';
        $this->tax_class = MODULE_SHIPPING_TABLE_TAX_CLASS;

        if ( ((int)MODULE_SHIPPING_TABLE_ZONE > 0) ) {
          $delivery_country_id = $order->delivery['country']['id'] ?? STORE_COUNTRY ?? 0;
          $delivery_zone_id = $order->delivery['zone_id'] ?? STORE_ZONE ?? 0;

          $check_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT zone_id
 FROM zones_to_geo_zones
 WHERE geo_zone_id = %d AND zone_country_id = %d
 ORDER BY zone_id
EOSQL
            , (int)MODULE_SHIPPING_TABLE_ZONE, (int)$delivery_country_id));
          while ($check = tep_db_fetch_array($check_query)) {
            if ( ($check['zone_id'] < 1) || ($check['zone_id'] == $delivery_zone_id) ) {
              $this->enabled = false;
              break;
            }
          }
        }
      }
    }

// class methods
    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes;

      if (MODULE_SHIPPING_TABLE_MODE == 'price') {
        $order_total = $this->getShippableTotal();
      } else {
        $order_total = $shipping_weight;
      }

      $table_cost = preg_split("/[:,]/" , MODULE_SHIPPING_TABLE_COST);
      for ($i = 0, $n = count($table_cost); $i < $n; $i += 2) {
        if ($order_total <= $table_cost[$i]) {
          $shipping = $table_cost[$i+1];
          break;
        }
      }

      if (MODULE_SHIPPING_TABLE_MODE == 'weight') {
        $shipping = $shipping * $shipping_num_boxes;
      }

      $this->quotes = [
        'id' => $this->code,
        'module' => MODULE_SHIPPING_TABLE_TEXT_TITLE,
        'methods' => [[
          'id' => $this->code,
          'title' => MODULE_SHIPPING_TABLE_TEXT_WAY,
          'cost' => $shipping + MODULE_SHIPPING_TABLE_HANDLING,
        ]],
      ];

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, htmlspecialchars($this->title));

      return $this->quotes;
    }

    protected function get_parameters() {
      return [
        'MODULE_SHIPPING_TABLE_STATUS' => [
          'title' => 'Enable Table Method',
          'value' => 'True',
          'desc' => 'Do you want to offer table rate shipping?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_SHIPPING_TABLE_COST' => [
          'title' => 'Shipping Table',
          'value' => '25:8.50,50:5.50,10000:0.00',
          'desc' => 'The shipping cost is based on the total cost or weight of items. Example: 25:8.50,50:5.50,etc.. Up to 25 charge 8.50, from there to 50 charge 5.50, etc',
        ],
        'MODULE_SHIPPING_TABLE_MODE' => [
          'title' => 'Table Method',
          'value' => 'weight',
          'desc' => 'The shipping cost is based on the order total or the total weight of the items ordered.',
          'set_func' => "tep_cfg_select_option(['weight', 'price'], ",
        ],
        'MODULE_SHIPPING_TABLE_HANDLING' => [
          'title' => 'Handling Fee',
          'value' => '0',
          'desc' => 'Handling fee for this shipping method.',
        ],
        'MODULE_SHIPPING_TABLE_TAX_CLASS' => [
          'title' => 'Tax Class',
          'value' => '0',
          'desc' => 'Use the following tax class on the shipping fee.',
          'use_func' => 'tep_get_tax_class_title',
          'set_func' => 'tep_cfg_pull_down_tax_classes(',
        ],
        'MODULE_SHIPPING_TABLE_ZONE' => [
          'title' => 'Shipping Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this shipping method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_SHIPPING_TABLE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display.',
        ],
      ];
    }

    function getShippableTotal() {
      global $order, $currencies;

      $order_total = $_SESSION['cart']->show_total();

      if ('mixed' === $order->content_type) {
        $order_total = 0;

        foreach ($order->products as $product) {
          $order_total += $currencies->calculate_price($product['final_price'], $product['tax'], $product['qty']);

          foreach (($product['attributes'] ?? []) as $option => $value) {
            $virtual_check_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT COUNT(*) AS total
  FROM products_attributes pa
    INNER JOIN products_attributes_download pad
      ON pa.products_attributes_id = pad.products_attributes_id
  WHERE pa.products_id = %d AND pa.options_values_id = %d
EOSQL
              , (int)$product['id'], (int)$value['value_id']));
            $virtual_check = tep_db_fetch_array($virtual_check_query);

            if ($virtual_check['total'] > 0) {
              $order_total -= $currencies->calculate_price($product['final_price'], $product['tax'], $product['qty']);
            }
          }
        }
      }

      return $order_total;
    }

  }
