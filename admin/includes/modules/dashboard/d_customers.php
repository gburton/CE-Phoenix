<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com


  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/


  class d_customers extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_CUSTOMERS_';

    const REQUIRES = [
      'id',
      'sortable_name',
      'date_account_created',
    ];

    public $content_width = 6;

    public function __construct() {
      parent::__construct();

      if ($this->enabled) {
        $this->content_width = (int)($this->base_constant('CONTENT_WIDTH') ?? 6);
      }
    }

    function getOutput() {
      global $customer_data;

      $output = sprintf(<<<'EOTEXT'
<table class="table table-striped table-hover mb-2">
 <thead>
    <tr class="thead-dark">
      <th>%s</th>
      <th class="text-right">%s</th>
    </tr>
  </thead>
  <tbody>
EOTEXT
, MODULE_ADMIN_DASHBOARD_CUSTOMERS_TITLE, MODULE_ADMIN_DASHBOARD_CUSTOMERS_DATE);

      $customer_limit = $this->base_constant('DISPLAY') ?? 6;
      $customers_query = tep_db_query(
        $customer_data->add_order_by(
          $customer_data->build_read(['id', 'sortable_name', 'date_account_created'], 'customers'), ['date_account_created' => 'DESC'])
        . ' LIMIT ' . (int)$customer_limit);
      while ($customers = tep_db_fetch_array($customers_query)) {
        $output .= sprintf(<<<'EOTEXT'
    <tr>
      <td><a href="%s">%s</a></td>
      <td class="text-right">%s</td>
    </tr>
EOTEXT
, tep_href_link('customers.php', 'cID=' . (int)$customer_data->get('id', $customers) . '&action=edit'),
  htmlspecialchars($customer_data->get('sortable_name', $customers)),
  tep_date_short($customer_data->get('date_account_created', $customers)));
      }

      $output .= "  </tbody>\n</table>";

      return $output;
    }

    public function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_CUSTOMERS_STATUS' => [
          'title' => 'Enable Customers Module',
          'value' => 'True',
          'desc' => 'Do you want to show the newest customers on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_CUSTOMERS_DISPLAY' => [
          'title' => 'Customers to display',
          'value' => '5',
          'desc' => 'This number of Customers will display, ordered by most recent sign up.',
        ],
        'MODULE_ADMIN_DASHBOARD_CUSTOMERS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_CUSTOMERS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
