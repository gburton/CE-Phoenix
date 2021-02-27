<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class d_total_customers {
    var $code = 'd_total_customers';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      $days = array();
      
      $chart_days = (int)MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_DAYS;
      
      for($i = 0; $i < $chart_days; $i++) {
        $days[date('M-d', strtotime('-'. $i .' days'))] = 0;
      }

      $orders_query = tep_db_query("select date_format(customers_info_date_account_created, '%b-%d') as dateday, count(*) as total from customers_info where date_sub(curdate(), interval '" . $chart_days . "' day) <= customers_info_date_account_created group by dateday");
      while ($orders = tep_db_fetch_array($orders_query)) {
        $days[$orders['dateday']] = $orders['total'];
      }

      $days = array_reverse($days, true);
      
      foreach ($days as $d => $r) {
        $plot_days[] = $d;
        $plot_customers[] = $r;
      }
      
      $plot_days = json_encode($plot_days);
      $plot_customers = json_encode($plot_customers);
      
      $table_header = MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_CHART_LINK;

      $output = <<<EOD
<div class="table-responsive">
  <table class="table mb-2">
    <thead class="thead-dark">
      <tr>
        <th>{$table_header}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><canvas id="totalCustomers" width="400" height="220"></canvas></td>
      </tr>
    </tbody>
  </table>
</div>

<script>
var ctx = document.getElementById('totalCustomers').getContext('2d');

var totalCustomers = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: {$plot_days},
    datasets: [{
      data: {$plot_customers},
      backgroundColor: '#eee',
      borderColor: '#aaa',
      borderWidth: 1
    }]
  },
  options: {
    scales: {yAxes: [{ticks: {stepSize: 5}}]},
    responsive: true,
    title: {display: false},
    legend: {display: false},
    tooltips: {mode: 'index', intersect: false},
    hover: {mode: 'nearest', intersect: true}      
  }
});
</script>
EOD;

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Total Customers Module', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_STATUS', 'True', 'Do you want to show the total customers chart on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Days', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_DAYS', '7', 'Days to display.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_STATUS', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_DAYS', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_TOTAL_CUSTOMERS_SORT_ORDER');
    }
  }
  
