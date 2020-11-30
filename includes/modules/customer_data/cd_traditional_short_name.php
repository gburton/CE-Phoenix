<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_traditional_short_name extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_TRADITIONAL_SHORT_NAME_';

    const PROVIDES = [ 'short_name', 'greeting' ];
    const REQUIRES = [ 'firstname', 'lastname' ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Traditional Short Name module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'short_name':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $GLOBALS['customer_data']->get('firstname', $customer_details);
          }

          return $customer_details[$field];
        case 'greeting':
          if (!isset($customer_details[$field])) {
            $gender = $GLOBALS['customer_data']->get('gender', $customer_details);
            if (!is_null($gender) && !is_null($GLOBALS['customer_data']->get('lastname', $customer_details))) {
              if ('m' === $gender) {
                $customer_details[$field] = sprintf(MODULE_CUSTOMER_DATA_TRADITIONAL_SHORT_NAME_GREET_MR, $GLOBALS['customer_data']->get('lastname', $customer_details));
              } elseif ('f' === $gender) {
                $customer_details[$field] = sprintf(MODULE_CUSTOMER_DATA_TRADITIONAL_SHORT_NAME_GREET_MS, $GLOBALS['customer_data']->get('lastname', $customer_details));
              }
            }

            if (!isset($customer_details[$field])) {
              $customer_details[$field] = sprintf(MODULE_CUSTOMER_DATA_TRADITIONAL_SHORT_NAME_GREET_NONE, $GLOBALS['customer_data']->get('short_name', $customer_details));
            }
          }

          return $customer_details[$field];
      }
    }

  }
