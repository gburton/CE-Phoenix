<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $currencies = new currencies();
  
  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('stats_customers', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'docsv':
        $filename = 'stats_customers.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, CSV_HEADERS);
        
        $history_query = tep_db_query("SELECT o.orders_id, o.date_purchased, ot.value as order_total, s.orders_status_name FROM orders o INNER JOIN orders_total ot ON o.orders_id = ot.orders_id INNER JOIN orders_status s ON o.orders_status = s.orders_status_id WHERE ot.class = 'ot_total' AND s.language_id = '" . (int)$languages_id . "' AND o.customers_id = '" . (int)$_GET['cID'] . "' ORDER BY orders_id DESC");
        
        while ($history = tep_db_fetch_array($history_query)) {
          fputcsv($output, [(int)$_GET['cID'], $history['orders_id'], $history['date_purchased'], $history['order_total'], $history['orders_status_name']]);
        }
        
        $OSCOM_Hooks->call('stats_customers', 'doCsvAction');
        
        exit;
      
      break;
    }
  }
  
  $OSCOM_Hooks->call('stats_customers', 'postAction');
  

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_NUMBER; ?></th>
          <th><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_TOTAL_PURCHASED; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $db_tables = $customer_data->build_db_tables(['id', 'name'], 'customers');
        $customers_query_raw = "SELECT " . customer_query::build_columns($db_tables);
        $customers_query_raw .= "o.customers_id, sum(op.products_quantity * op.final_price) AS ordersum FROM " . customer_query::build_joins($db_tables, []);
        $customers_query_raw .= ", orders_products op, orders o WHERE " . customer_query::TABLE_ALIASES['customers'];
        $customers_query_raw .= ".customers_id = o.customers_id AND o.orders_id = op.orders_id GROUP BY o.customers_id ORDER BY ordersum DESC";
        $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);

        $row = 0; if ($_GET['page'] > 1) $row += (MAX_DISPLAY_SEARCH_RESULTS * ((int)$_GET['page'] - 1));
        $customers_query = tep_db_query($customers_query_raw);
        
        // fix counted customers
        $customers_query_numrows = tep_db_query("select customers_id from orders group by customers_id");
        $customers_query_numrows = tep_db_num_rows($customers_query_numrows);

        while ($customer = tep_db_fetch_array($customers_query)) {
          $row++;
          ?>
          <tr>
            <td><?php echo $row; ?></td>
            <td><?php echo $customer_data->get('name', $customer); ?></td>
            <td class="text-right"><?php echo $currencies->format($customer['ordersum']); ?></td>
            <td class="text-right">
              <?php 
              echo '<a class="text-dark" href="' . tep_href_link('stats_customers.php', 'action=docsv&cID=' . $customer['customers_id']) . '"><i class="fas fa-file-csv mr-2"></i></a>';
              echo '<a class="text-dark" href="' . tep_href_link('orders.php', 'cID=' . $customer['customers_id']) . '"><i class="fas fa-eye"></i></a>'              
              ?>
            </td>
          </tr>
          <?php
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col-sm-6"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></div>
    <div class="col-sm-6 text-sm-right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
