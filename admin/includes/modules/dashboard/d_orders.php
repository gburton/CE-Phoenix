<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class d_orders {
    var $code = 'd_orders';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_ORDERS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_ORDERS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_ORDERS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_ORDERS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_ORDERS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_ORDERS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $languages_id;
      
      $output = null;
      
      $output .= '<div class="table-responsive">';
        $output .= '<table class="table table-striped table-hover mb-2">';
          $output .= '<thead class="thead-dark">';
            $output .= '<tr>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_ORDERS_TITLE . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_ORDERS_TOTAL . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_ORDERS_DATE . '</th>';
              $output .= '<th class="text-right">' . MODULE_ADMIN_DASHBOARD_ORDERS_ORDER_STATUS . '</th>';
            $output .= '</tr>';
          $output .= '</thead>';
          $output .= '<tbody>';

          $orders_query = tep_db_query("select o.orders_id, o.customers_name, greatest(o.date_purchased, ifnull(o.last_modified, 0)) as date_last_modified, s.orders_status_name, ot.text as order_total from orders o, orders_total ot, orders_status s where o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' order by date_last_modified desc limit " . (int)MODULE_ADMIN_DASHBOARD_ORDERS_DISPLAY);
          while ($orders = tep_db_fetch_array($orders_query)) {
            $output .= '<tr>';
              $output .= '<td><a href="' . tep_href_link('orders.php', 'oID=' . (int)$orders['orders_id'] . '&action=edit') . '">' . tep_output_string_protected($orders['customers_name']) . '</a></td>';
              $output .= '<td>' . strip_tags($orders['order_total']) . '</td>';
              $output .= '<td>' . tep_date_short($orders['date_last_modified']) . '</td>';
              $output .= '<td class="text-right">' . $orders['orders_status_name'] . '</td>';
            $output .= '</tr>';
          }

          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_ORDERS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Orders Module', 'MODULE_ADMIN_DASHBOARD_ORDERS_STATUS', 'True', 'Do you want to show the latest orders on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Orders to display', 'MODULE_ADMIN_DASHBOARD_ORDERS_DISPLAY', '5', 'This number of Orders will display, ordered by most recent.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_ORDERS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_ORDERS_SORT_ORDER', '300', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_ORDERS_STATUS', 'MODULE_ADMIN_DASHBOARD_ORDERS_DISPLAY', 'MODULE_ADMIN_DASHBOARD_ORDERS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_ORDERS_SORT_ORDER');
    }
  }
  