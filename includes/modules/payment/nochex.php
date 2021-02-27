<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class nochex extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_NOCHEX_';

    public $form_action_url = 'https://www.nochex.com/nochex.dll/checkout';

// class methods
    function process_button() {
      global $order, $currencies;

      $process_button_string = tep_draw_hidden_field('cmd', '_xclick')
                             . tep_draw_hidden_field('email', MODULE_PAYMENT_NOCHEX_ID)
                             . tep_draw_hidden_field('amount', number_format($order->info['total'] * $currencies->currencies['GBP']['value'], $currencies->currencies['GBP']['decimal_places']))
                             . tep_draw_hidden_field('ordernumber', $_SESSION['customer_id'] . '-' . date('Ymdhis'))
                             . tep_draw_hidden_field('returnurl', tep_href_link('checkout_process.php', '', 'SSL'))
                             . tep_draw_hidden_field('cancel_return', tep_href_link('checkout_payment.php', '', 'SSL'));

      return $process_button_string;
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_NOCHEX_STATUS' => [
          'title' => 'Enable NOCHEX Module',
          'value' => 'True',
          'desc' => 'Do you want to accept NOCHEX payments?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_NOCHEX_ID' => [
          'title' => 'E-Mail Address',
          'value' => 'you@yourbusiness.com',
          'desc' => 'The e-mail address to use for the NOCHEX service',
        ],
        'MODULE_PAYMENT_NOCHEX_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        'MODULE_PAYMENT_NOCHEX_ZONE' => [
          'title' => 'Payment Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'value' => '0',
          'desc' => 'Set the status of orders made with this payment module to this value (if non-zero)',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
      ];
    }

  }
