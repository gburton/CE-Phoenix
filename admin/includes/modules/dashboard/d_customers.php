<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class d_customers {
    var $code = 'd_customers';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_CUSTOMERS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_CUSTOMERS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_CUSTOMERS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_CUSTOMERS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      $output = null; $count = 0;
      
      $output .= '<table class="table table-striped table-hover mb-2">';
        $output .= '<thead class="thead-dark">';
          $output .= '<tr>';
            $output .= '<th>' . MODULE_ADMIN_DASHBOARD_CUSTOMERS_TITLE . '</th>';
            $output .= '<th class="text-right">'. MODULE_ADMIN_DASHBOARD_CUSTOMERS_DATE . '</th>';
          $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        $customers_query = tep_db_query("select c.customers_id, c.customers_lastname, c.customers_firstname, ci.customers_info_date_account_created from customers c, customers_info ci where c.customers_id = ci.customers_info_id order by ci.customers_info_date_account_created desc limit " . (int)MODULE_ADMIN_DASHBOARD_CUSTOMERS_DISPLAY);
        while ($customers = tep_db_fetch_array($customers_query)) {
          $output .= '<tr>';
            $output .= '<td><a href="' . tep_href_link('customers.php', 'cID=' . (int)$customers['customers_id'] . '&action=edit') . '">' . tep_output_string_protected($customers['customers_firstname'] . ' ' . $customers['customers_lastname']) . '</a></td>';
            $output .= '<td class="text-right">' . tep_date_short($customers['customers_info_date_account_created']) . '</td>';
          $output .= '</tr>';
        }

        $output .= '</tbody>';        
      $output .= '</table>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Customers Module', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS', 'True', 'Do you want to show the newest customers on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Customers to display', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_DISPLAY', '5', 'This number of Customers will display, ordered by most recent sign up.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_SORT_ORDER', '400', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_DISPLAY', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_SORT_ORDER');
    }
  }
  