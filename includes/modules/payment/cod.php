<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cod extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_COD_';

    public function __construct() {
      parent::__construct();

      $this->sort_order = defined('MODULE_PAYMENT_COD_SORT_ORDER') ? MODULE_PAYMENT_COD_SORT_ORDER : 0;
    }

    public function update_status() {
      if (!$this->enabled || !isset($GLOBALS['order'])) {
        return;
      }

      // disable the module if the order only contains virtual products
      if ('virtual' === $GLOBALS['order']->content_type) {
        $this->enabled = false;
        return;
      }

      if (isset($GLOBALS['order']->delivery['country']['id'])) {
        $this->update_status_by($GLOBALS['order']->delivery);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_COD_STATUS' => [
          'title' => 'Enable Cash On Delivery Module',
          'value' => 'True',
          'desc' => 'Do you want to accept Cash On Delivery payments?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_COD_ZONE' => [
          'title' => 'Payment Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_COD_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        'MODULE_PAYMENT_COD_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'value' => '0',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
      ];
    }

  }
