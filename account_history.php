<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  require "includes/languages/$language/account_history.php";

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_history.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
  if (tep_count_customer_orders() > 0) {
    $history_query_raw = sprintf(<<<'EOSQL'
SELECT o.*, ot.text as order_total, s.orders_status_name
 FROM orders o INNER JOIN orders_total ot ON o.orders_id = ot.orders_id INNER JOIN orders_status s ON o.orders_status = s.orders_status_id
 WHERE ot.class = 'ot_total' AND s.public_flag = 1 AND s.language_id = %d AND o.customers_id = %d
 ORDER BY orders_id DESC
EOSQL
      , (int)$languages_id, (int)$customer_id);
    $history_split = new splitPageResults($history_query_raw, MAX_DISPLAY_ORDER_HISTORY);
    $history_query = tep_db_query($history_split->sql_query);
?>
    <div class="table-responsive">
      <table class="table table-hover table-striped">
        <caption class="sr-only"><?php echo $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></caption>
        <thead class="thead-dark">
          <tr>
            <th scope="col"><?php echo TEXT_ORDER_NUMBER; ?></th>
            <th scope="col" class="d-none d-md-table-cell"><?php echo TEXT_ORDER_STATUS; ?></th>
            <th scope="col"><?php echo TEXT_ORDER_DATE; ?></th>
            <th scope="col" class="d-none d-md-table-cell"><?php echo TEXT_ORDER_PRODUCTS; ?></th>
            <th scope="col"><?php echo TEXT_ORDER_COST; ?></th>
            <th class="text-right" scope="col"><?php echo TEXT_VIEW_ORDER; ?></th>
          </tr>
        </thead>
        <tbody>
<?php
    while ($history = tep_db_fetch_array($history_query)) {
      $products_query = tep_db_query("select sum(products_quantity) as count from orders_products where orders_id = '" . (int)$history['orders_id'] . "'");
      $products = tep_db_fetch_array($products_query);
?>
          <tr>
            <th scope="row"><?php echo $history['orders_id']; ?></th>
            <td class="d-none d-md-table-cell"><?php echo $history['orders_status_name']; ?></td>
            <td><?php echo tep_date_short($history['date_purchased']); ?></td>
            <td class="d-none d-md-table-cell"><?php echo $products['count']; ?></td>
            <td><?php echo strip_tags($history['order_total']); ?></td>
            <td class="text-right"><?php echo tep_draw_button(BUTTON_VIEW_ORDER, null, tep_href_link('account_history_info.php', tep_get_all_get_params(['order_id']) . 'order_id=' . (int)$history['orders_id'], 'SSL'), 'primary', NULL, 'btn-primary btn-sm'); ?></td>
          </tr>
<?php
    }
?>
        </tbody>
      </table>
    </div>

    <div class="row align-items-center">
      <div class="col-sm-6 d-none d-sm-block">
        <?php echo $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?>
      </div>
      <div class="col-sm-6">
        <?php echo $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info', 'x', 'y'])); ?>
      </div>
    </div>

<?php
  } else {
?>

  <div class="alert alert-info" role="alert">
    <p><?php echo TEXT_NO_PURCHASES; ?></p>
  </div>

<?php
  }
?>

  <div class="buttonSet my-2">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL'), null, null, 'btn-light'); ?>
  </div>
</div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
