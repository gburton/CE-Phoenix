<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_account_sage_pay_cards extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_ACCOUNT_SAGE_PAY_CARDS_';

    public function __construct() {
      parent::__construct(__FILE__);

      $sage_pay_enabled = false;

      if ( defined('MODULE_PAYMENT_INSTALLED')
        && !Text::is_empty(MODULE_PAYMENT_INSTALLED)
        && in_array('sage_pay_direct.php', explode(';', MODULE_PAYMENT_INSTALLED)) )
      {
        $sage_pay_direct = new sage_pay_direct();

        if ( $sage_pay_direct->enabled ) {
          $sage_pay_enabled = true;

          if ( MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_SERVER == 'Test' ) {
            $this->title .= ' [Test]';
            $this->public_title .= ' (' . $sage_pay_direct->code . '; Test)';
          }
        }
      }

      if ( $sage_pay_enabled !== true ) {
        $this->enabled = false;

        $this->description = '<div class="alert alert-warning">' . MODULE_CONTENT_ACCOUNT_SAGE_PAY_CARDS_ERROR_MAIN_MODULE . '</div>' . $this->description;
      }
    }

    public function execute() {
      $GLOBALS['oscTemplate']->_data['account']['account']['links']['sage_pay_cards'] = [
        'title' => $this->public_title,
        'link' => tep_href_link('ext/modules/content/account/sage_pay/cards.php'),
        'icon' => 'far fa-credit-card fa-5x',
      ];
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_ACCOUNT_SAGE_PAY_CARDS_STATUS' => [
          'title' => 'Enable Sage Pay Card Management',
          'value' => 'True',
          'desc' => 'Do you want to enable the Sage Pay Card Management module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_ACCOUNT_SAGE_PAY_CARDS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
