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

  if (!is_numeric($_GET['order_id'] ?? null)) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  $customer_info_query = tep_db_query("SELECT o.customers_id FROM orders o, orders_status s WHERE o.orders_id = ". (int)$_GET['order_id'] . " AND o.orders_status = s.orders_status_id AND s.language_id = " . (int)$languages_id . " AND s.public_flag = 1");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $_SESSION['customer_id']) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  require "includes/languages/$language/account_history_info.php";

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_history.php', '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $_GET['order_id']), tep_href_link('account_history_info.php', 'order_id=' . $_GET['order_id'], 'SSL'));

  $order = new order($_GET['order_id']);

  require 'includes/template_top.php';
?>

<div class="row">
  <div class="col-7"><h1 class="display-4"><?php echo HEADING_TITLE; ?></h1></div>
  <div class="col text-right">
    <h2 class="h4"><?php echo sprintf(HEADING_ORDER_NUMBER, $_GET['order_id']) . ' <span class="badge badge-secondary">' . $order->info['orders_status'] . '</span>'; ?></h2>
    <p><?php echo '<strong>' . HEADING_ORDER_DATE . '</strong> ' . tep_date_long($order->info['date_purchased']); ?></p>
  </div>
</div>

<div class="contentContainer">
  <div class="row">
    <div class="col-sm-7">
      <table class="table table-hover table-bordered">
        <thead class="thead-dark">
          <tr>
            <th colspan="2"><?php echo HEADING_PRODUCTS; ?></th>
<?php
  if (count($order->info['tax_groups']) > 1) {
?>
            <th class="text-right"><?php echo HEADING_TAX; ?></th>
<?php
  }
?>
            <th class="text-right"><?php echo HEADING_TOTAL; ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  foreach ($order->products as $product) {
    echo '          <tr>' . PHP_EOL;
    echo '            <td align="right" width="30">' . $product['qty'] . '</td>' . PHP_EOL;
    echo '            <td>' . $product['name'];
    foreach (($product['attributes'] ?? []) as $attribute) {
      echo '<br><small><i> - ' . $attribute['option'] . ': ' . $attribute['value'] . '</i></small>';
    }
    echo '</td>' . PHP_EOL;

    if (count($order->info['tax_groups']) > 1) {
      echo '            <td valign="top" class="text-right">' . tep_display_tax_value($product['tax']) . '%</td>' . PHP_EOL;
    }

    echo '            <td class="text-right">'
      . $currencies->format(tep_add_tax($product['final_price'], $product['tax']) * $product['qty'], true, $order->info['currency'], $order->info['currency_value'])
      . '</td>' . PHP_EOL;
    echo '          </tr> . PHP_EOL';
  }

  foreach ($order->totals as $total) {
    echo '          <tr>' . PHP_EOL;
    echo '            <td colspan="4" class="text-right">' . $total['title'] . ' ' . $total['text'] . '</td>' . PHP_EOL;
    echo '          </tr>' . PHP_EOL;
  }
?>
        <tbody>
      </table>
    </div>
    <div class="col">
      <div class="border">
        <ul class="list-group list-group-flush">
<?php
  $address = $customer_data->get_module('address');
  if ($order->delivery) {
    echo '          <li class="list-group-item">';
    echo SHIPPING_FA_ICON;
    echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b><br>';
    echo $address->format($order->delivery, 1, ' ', '<br>');
    echo '</li>' . PHP_EOL;
  }
?>
          <li class="list-group-item">
<?php
  echo PAYMENT_FA_ICON;
  echo '<b>' . HEADING_BILLING_ADDRESS . '</b><br>';
  echo $address->format($order->billing, 1, ' ', '<br>');
?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <h2 class="h4"><?php echo HEADING_ORDER_HISTORY; ?></h2>

  <ul class="list-group">
<?php
    $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from orders_status os, orders_status_history osh where osh.orders_id = '" . (int)$_GET['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' and os.public_flag = '1' order by osh.date_added");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
      echo '<h3 class="h6">' . $statuses['orders_status_name'] . '</h3>';
      echo (empty($statuses['comments']) ? '' : '<p>' . nl2br(tep_output_string_protected($statuses['comments'])) . '</p>');
      echo '<span class="badge badge-secondary badge-pill"><i class="far fa-clock mr-1"></i>' . $statuses['date_added'] . '</span>';
      echo '</li>';
    }
?>
  </ul>

<?php
  if (DOWNLOAD_ENABLED == 'true') {
    include 'includes/modules/downloads.php';
  }
?>

  <div class="buttonSet my-2">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account_history.php', tep_get_all_get_params(['order_id']), 'SSL'), null, null, 'btn-light'); ?>
  </div>

</div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
