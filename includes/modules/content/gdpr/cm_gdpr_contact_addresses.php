<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_gdpr_contact_addresses {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_TITLE;
      $this->description = MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      
      if ( defined('MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id;
      global $port_my_data, $customer;
      
      $content_width = (int)MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_CONTENT_WIDTH;
      
      $addresses_query = tep_db_query("SELECT address_book_id from address_book where customers_id = '" . (int)$customer_id . "' and address_book_id != '" . (int)$customer->get_default_address_id() . "'");
      
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

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Addresses Module', 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS', 'True', 'Should this module be shown on the GDPR page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_SORT_ORDER', '150', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_STATUS', 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_CONTENT_WIDTH', 'MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_SORT_ORDER');
    }
  }
  