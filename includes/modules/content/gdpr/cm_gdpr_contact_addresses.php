<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_gdpr_contact_addresses extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $port_my_data, $customer;

      $content_width = (int)MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_CONTENT_WIDTH;

      $addresses_query = tep_db_query("SELECT address_book_id from address_book where customers_id = '" . (int)$_SESSION['customer_id'] . "' and address_book_id != '" . (int)$customer->get_default_address_id() . "'");

      $num_addresses = tep_db_num_rows($addresses_query);

      if ($num_addresses > 0) {
        $port_my_data['YOU']['CONTACT']['ADDRESS']['OTHER']['COUNT'] = $num_addresses;

        $a = 1;
        while ($addresses = tep_db_fetch_array($addresses_query)) {
          $port_my_data['YOU']['CONTACT']['ADDRESS']['OTHER']['LIST'][$a]['ID'] = (int)$addresses['address_book_id'];
          $port_my_data['YOU']['CONTACT']['ADDRESS']['OTHER']['LIST'][$a]['ADDRESS'] = $customer->make_address_label($addresses['address_book_id'], true, ' ', ', ');

          $a++;
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS' => [
          'title' => 'Enable Addresses Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the GDPR page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '150',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
