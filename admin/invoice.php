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

  $order = new order(tep_db_prepare_input($_GET['oID']));
  $address = $customer_data->get_module('address');

  require 'includes/template_top.php';
?>

  <div class="row align-items-center mx-1">
    <div class="col"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . STORE_LOGO, STORE_NAME); ?></div>
    <div class="col text-right">
<?php
  echo '      <h1 class="display-4">' . STORE_NAME . '</h1>' . PHP_EOL;
  echo '      <p>' . nl2br(STORE_ADDRESS) . '</p>' . PHP_EOL;
  echo '      <p>' . STORE_PHONE . '</p>' . PHP_EOL;
?>
    </div>
  </div>

  <hr>

  <div class="row">
    <div class="col">
      <ul class="list-group">
        <li class="list-group-item border-0"><h2 class="h6 lead m-0"><?php echo ENTRY_SOLD_TO; ?></h2></li>
        <li class="list-group-item border-0"><?php echo $address->format($order->billing, 1, '', ', '); ?></li>
        <li class="list-group-item border-0"><i class="fas fa-phone fa-fw"></i> <?php echo ($order->customer['telephone'] ?? ''); ?> <i class="fas fa-at fa-fw"></i> <?php echo ($order->customer['email_address'] ?? ''); ?></li>
     </ul>
    </div>
    <div class="col">
      <ul class="list-group">
        <li class="list-group-item border-0"><h2 class="h6 lead m-0"><?php echo ENTRY_SHIP_TO; ?></h2></li>
        <li class="list-group-item border-0"><?php echo $address->format($order->delivery, 1, '', ', '); ?></li>
      </ul>
    </div>
    <div class="col text-right">
      <ul class="list-group">
        <li class="list-group-item border-0"><h2 class="h6 lead m-0"><?php echo sprintf(ENTRY_INVOICE_NUMBER, (int)$_GET['oID']); ?></h2></li>
        <li class="list-group-item border-0"><?php echo sprintf(ENTRY_INVOICE_DATE, tep_date_short($order->info['date_purchased'])); ?></li>
        <li class="list-group-item border-0"><?php echo sprintf(ENTRY_PAYMENT_METHOD, $order->info['payment_method']); ?></li>
      </ul>
    </div>
  </div>

  <table class="table table-striped mt-3">
    <thead class="thead-dark">
      <tr>
        <th><?php echo TABLE_HEADING_QTY; ?></th>
        <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
        <th><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></th>
        <th class="text-right"><?php echo TABLE_HEADING_TAX; ?></th>
        <th class="text-right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></th>
        <th class="text-right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></th>
        <th class="text-right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></th>
        <th class="text-right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ($order->products as $product) {
    echo '      <tr>' . PHP_EOL;
    echo '        <td>' . $product['qty'] . '</td>' . PHP_EOL;
    echo '        <th>' . $product['name'];
    foreach ((array)$product['attributes'] as $attribute) {
      echo '<br /><small><i> - ' . $attribute['option'] . ': ' . $attribute['value'];
      if ($attribute['price'] != '0') {
        echo ' (' . $attribute['prefix'] . $currencies->format($attribute['price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
      }
      echo '</i></small>';
    }
    echo '</th>' . PHP_EOL;
    echo '        <td>' . $product['model'] . '</td>' . PHP_EOL;
    echo '        <td class="text-right">' . tep_display_tax_value($product['tax']) . '%</td>' . PHP_EOL;
    echo '        <td class="text-right">' . $currencies->format($product['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . PHP_EOL;
    echo '        <td class="text-right">' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . PHP_EOL;
    echo '        <td class="text-right">' . $currencies->format($product['final_price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . PHP_EOL;
    echo '        <th class="text-right">' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true) * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</th>' . PHP_EOL;
    echo '      </tr>';
  }

  foreach ($order->totals as $order_total) {
    echo '      <tr>' . PHP_EOL;
    echo '        <th colspan="7" class="text-right bg-white border-0" scope="row">' . $order_total['title'] . '</th>' . PHP_EOL;
    echo '        <th class="text-right bg-white border-0">' . $order_total['text'] . '</th>' . PHP_EOL;
    echo '      </tr>' . PHP_EOL;
  }
?>
    </tbody>
  </table>

<?php
  echo $OSCOM_Hooks->call('invoice', 'extraComments');
?>

</body>
</html>

<?php
  require 'includes/application_bottom.php';
?>
