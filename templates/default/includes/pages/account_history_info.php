<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_history.php'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $_GET['order_id']), tep_href_link('account_history_info.php', 'order_id=' . $_GET['order_id']));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<div class="row">
  <div class="col-7"><h1 class="display-4"><?= HEADING_TITLE ?></h1></div>
  <div class="col text-right">
    <h4><?= sprintf(HEADING_ORDER_NUMBER, $_GET['order_id']) . ' <span class="badge badge-secondary">' . $order->info['orders_status'] . '</span>' ?></h4>
    <p><?= '<strong>' . HEADING_ORDER_DATE . '</strong> ' . tep_date_long($order->info['date_purchased']) ?></p>
  </div>
</div>

  <div class="row">
    <div class="col-sm-7">
      <table class="table table-hover table-bordered">
        <thead class="thead-dark">
          <tr>
            <th colspan="2"><?= HEADING_PRODUCTS ?></th>
            <?php
  if (count($order->info['tax_groups']) > 1) {
?>
            <th class="text-right"><?= HEADING_TAX ?></th>
              <?php
  }
?>
            <th class="text-right"><?= HEADING_TOTAL ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
  foreach ($order->products as $product) {
    echo '<tr>';
    echo '<td align="right" width="30">' . $product['qty'] . '</td>';
    echo '<td>' . $product['name'];
    foreach (($product['attributes'] ?? []) as $attribute) {
      echo '<br><small><i> - ' . $attribute['option'] . ': ' . $attribute['value'] . '</i></small>';
    }
    echo '</td>';

    if (count($order->info['tax_groups']) > 1) {
      echo '<td valign="top" class="text-right">' . tep_display_tax_value($product['tax']) . '%</td>';
    }

    echo '<td class="text-right">' . $currencies->format(tep_add_tax($product['final_price'], $product['tax']) * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>';
    echo '</tr>';
  }

  foreach ($order->totals as $total) {
    echo '<tr>';
    echo '<td colspan="4" class="text-right">' . $total['title'] . ' ' . $total['text'] . '</td>';
    echo '</tr>';
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
    echo '<li class="list-group-item">';
    echo SHIPPING_FA_ICON;
    echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b><br>';
    echo $address->format($order->delivery, 1, ' ', '<br>');
    echo '</li>';
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

  <h4><?= HEADING_ORDER_HISTORY ?></h4>

  <ul class="list-group">
<?php
  $statuses_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT os.orders_status_name, osh.date_added, osh.comments
 FROM orders_status os INNER JOIN orders_status_history osh ON osh.orders_status_id = os.orders_status_id
 WHERE os.public_flag = 1 AND osh.orders_id = %d AND os.language_id = %d
 ORDER BY osh.date_added
EOSQL
    , (int)$_GET['order_id'], (int)$_SESSION['languages_id']));
  while ($statuses = $statuses_query->fetch_assoc()) {
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
    echo '<h6>' . $statuses['orders_status_name'] . '</h6>';
    echo (empty($statuses['comments']) ? '' : '<p>' . nl2br(htmlspecialchars($statuses['comments'])) . '</p>');
    echo '<span class="badge badge-secondary badge-pill"><i class="far fa-clock mr-1"></i>' . $statuses['date_added'] . '</span>';
    echo '</li>';
  }
?>
  </ul>

<?php
  if (DOWNLOAD_ENABLED == 'true') {
    include $oscTemplate->map_to_template('downloads.php', 'component');
  }

  echo $OSCOM_Hooks->call('account_history_info', 'orderDetails');
?>

  <div class="buttonSet my-2">
    <?= tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account_history.php', tep_get_all_get_params(['order_id'])), null, null, 'btn-light') ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
