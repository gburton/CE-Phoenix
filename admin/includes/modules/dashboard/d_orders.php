<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_orders extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_ORDERS_';

    public $content_width = 6;

    public function __construct() {
      parent::__construct();

      if ($this->enabled) {
        $this->content_width = (int)($this->base_constant('CONTENT_WIDTH') ?? 6);
      }
    }

    function getOutput() {
      $orders_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT o.orders_id, o.customers_name, COALESCE(o.last_modified, o.date_purchased) AS date_last_modified, s.orders_status_name, ot.text AS order_total
 FROM orders o INNER JOIN orders_total ot ON o.orders_id = ot.orders_id INNER JOIN orders_status s ON o.orders_status = s.orders_status_id AND s.language_id = %d
 WHERE ot.class = 'ot_total'
 ORDER BY date_last_modified DESC
 LIMIT %d
EOSQL
        , (int)$_SESSION['languages_id'], (int)MODULE_ADMIN_DASHBOARD_ORDERS_DISPLAY));

      $output = '<div class="table-responsive">';
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

          while ($order = tep_db_fetch_array($orders_query)) {
            $output .= '<tr>';
              $output .= '<td><a href="' . tep_href_link('orders.php', 'oID=' . (int)$order['orders_id'] . '&action=edit') . '">' . htmlspecialchars($order['customers_name']) . '</a></td>';
              $output .= '<td>' . strip_tags($order['order_total']) . '</td>';
              $output .= '<td>' . tep_date_short($order['date_last_modified']) . '</td>';
              $output .= '<td class="text-right">' . $order['orders_status_name'] . '</td>';
            $output .= '</tr>';
          }

          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';

      return $output;
    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_ORDERS_STATUS' => [
          'title' => 'Enable Orders Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest orders on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_ORDERS_DISPLAY' => [
          'title' => 'Orders to display',
          'value' => '5',
          'desc' => 'This number of Orders will display, ordered by most recent.',
        ],
        'MODULE_ADMIN_DASHBOARD_ORDERS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_ORDERS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '300',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
