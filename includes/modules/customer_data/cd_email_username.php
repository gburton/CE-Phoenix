<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_email_username extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_EMAIL_USERNAME_';

    const PROVIDES = [ 'username' ];
    const REQUIRES = [ 'email_address' ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Email Username module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'username':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $GLOBALS['customer_data']->get('email_address', $customer_details);
          }

          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $GLOBALS['customer_data']->get_module('email_address')->display_input($customer_details);
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      $GLOBALS['customer_data']->get_module('email_address')->build_db_values($db_tables, $customer_details, $table);
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      $GLOBALS['customer_data']->get_module('email_address')->build_db_aliases($db_tables, $table);
    }

  }
