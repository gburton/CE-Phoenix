<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_account_braintree_cards extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_';

    public function __construct() {
      parent::__construct(__FILE__);

      $braintree_enabled = false;

      if ( defined('MODULE_PAYMENT_INSTALLED')
        && !Text::is_empty(MODULE_PAYMENT_INSTALLED)
        && in_array('braintree_cc.php', explode(';', MODULE_PAYMENT_INSTALLED)) )
      {
        $braintree_cc = new braintree_cc();

        if ( $braintree_cc->enabled ) {
          $braintree_enabled = true;

          if ( MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_SERVER == 'Sandbox' ) {
            $this->title .= ' [Sandbox]';
            $this->public_title .= ' (' . $braintree_cc->code . '; Sandbox)';
          }
        }
      }

      if ( $braintree_enabled !== true ) {
        $this->enabled = false;

        $this->description = '<div class="alert alert-warning">' . MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_ERROR_MAIN_MODULE . '</div>' . $this->description;
      }
    }

    public function execute() {
      $GLOBALS['oscTemplate']->_data['account']['account']['links']['braintree_cards'] = [
        'title' => $this->public_title,
        'link' => tep_href_link('ext/modules/content/account/braintree/cards.php'),
        'icon' => 'far fa-credit-card fa-5x',
      ];
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_STATUS' => [
          'title' => 'Enable Braintree Card Management',
          'value' => 'True',
          'desc' => 'Do you want to enable the Braintree Card Management module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
