<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/currencies.php');
  $currencies = new currencies();

  require('includes/template_top.php');
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
  if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  $customers_query_raw = "select c.customers_firstname, c.customers_lastname, sum(op.products_quantity * op.final_price) as ordersum from customers c, orders_products op, orders o where c.customers_id = o.customers_id and o.orders_id = op.orders_id group by c.customers_firstname, c.customers_lastname order by ordersum DESC";
  $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
// fix counted customers
  $customers_query_numrows = tep_db_query("select customers_id from orders group by customers_id");
  $customers_query_numrows = tep_db_num_rows($customers_query_numrows);

  $rows = 0;
  $customers_query = tep_db_query($customers_query_raw);
  while ($customers = tep_db_fetch_array($customers_query)) {
    $rows++;
?>
        <tr onclick="document.location.href='<?php echo tep_href_link('customers.php', 'search=' . $customers['customers_lastname']); ?>'">
          <td><?php echo str_pad($rows, 2, '0', STR_PAD_LEFT); ?>.</td>
          <td><?php echo '<a href="' . tep_href_link('customers.php', 'search=' . $customers['customers_lastname']) . '">' . $customers['customers_firstname'] . ' ' . $customers['customers_lastname'] . '</a>'; ?></td>
          <td class="text-right"><?php echo $currencies->format($customers['ordersum']); ?></td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col-sm-6"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></div>
    <div class="col-sm-6 text-sm-right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']); ?></div>
  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
