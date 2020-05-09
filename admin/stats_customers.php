<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $currencies = new currencies();

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

        $row = 0;
        $customers_query = tep_db_query($customers_query_raw);

        while ($customer = tep_db_fetch_array($customers_query)) {
          $row++;
          ?>
          <tr onclick="document.location.href='<?php echo tep_href_link('customers.php', 'cID=' . $customer['customers_id']); ?>'">
            <td><?php echo str_pad($row, 2, '0', STR_PAD_LEFT); ?>.</td>
            <td><?php echo '<a href="' . tep_href_link('customers.php', 'cID=' . $customer['customers_id']) . '">' . $customer_data->get('name', $customer) . '</a>'; ?></td>
            <td class="text-right"><?php echo $currencies->format($customer['ordersum']); ?>&nbsp;</td>
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
