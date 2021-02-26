<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class n_create_account extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_NOTIFICATIONS_CREATE_ACCOUNT_';

    const TRIGGERS = [ 'create_account' ];
    const REQUIRES = [ 'greeting', 'name', 'email_address' ];

    public function notify($customer) {
      ob_start();
      include $GLOBALS['oscTemplate']->map_to_template(__FILE__);
      echo $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountCreationNotification');
      $email_text = ob_get_clean();

      return tep_mail($customer->get('name'), $customer->get('email_address'), MODULE_NOTIFICATIONS_CREATE_ACCOUNT_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    }

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Account Creation Notification module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

  }

