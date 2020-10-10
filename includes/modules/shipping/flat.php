<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class flat extends abstract_shipping_module {

    const CONFIG_KEY_BASE = 'MODULE_SHIPPING_FLAT_';

// class methods
    public function quote($method = '') {
      global $order;

      $this->quotes = [
        'id' => $this->code,
        'module' => MODULE_SHIPPING_FLAT_TEXT_TITLE,
        'methods' => [[
          'id' => $this->code,
          'title' => MODULE_SHIPPING_FLAT_TEXT_WAY,
          'cost' => $this->base_constant('COST') + $this->calculate_handling(),
        ]],
      ];

      $this->quote_common();

      return $this->quotes;
    }

    protected function get_parameters() {
      return [
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable Flat Shipping',
          'value' => 'True',
          'desc' => 'Do you want to offer flat rate shipping?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        $this->config_key_base . 'COST' => [
          'title' => 'Shipping Cost',
          'value' => '5.00',
          'desc' => 'The shipping cost for all orders using this shipping method.',
        ],
        $this->config_key_base . 'TAX_CLASS' => [
          'title' => 'Tax Class',
          'value' => '0',
          'desc' => 'Use the following tax class on the shipping fee.',
          'use_func' => 'tep_get_tax_class_title',
          'set_func' => 'tep_cfg_pull_down_tax_classes(',
        ],
        $this->config_key_base . 'ZONE' => [
          'title' => 'Shipping Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this shipping method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display.',
        ],
      ];
    }

  }
