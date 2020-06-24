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

  $oID = tep_db_prepare_input($_GET['oID']);

  $order = new order($oID);
  $address = $customer_data->get_module('address');
  
  require 'includes/template_top.php';
?>

  <div class="row align-items-center mx-1">
    <div class="col"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . STORE_LOGO, STORE_NAME); ?></div>
    <div class="col text-right">
      <?php
      echo '<h1 class="display-4">' . STORE_NAME . '</h1>';
      echo '<p>' . nl2br(STORE_ADDRESS) . '</p>';
      echo '<p>' . STORE_PHONE . '</p>';
      ?>
    </div>
  </div>

  <hr>
  
  <div class="row">
    <div class="col">
      <ul class="list-group">
        <li class="list-group-item border-0"><h6 class="lead m-0"><?php echo ENTRY_SHIP_TO; ?></h6></li>
        <li class="list-group-item border-0 font-weight-bold"><?php echo $address->format($order->delivery, 1, '', '<br>'); ?></li>
      </ul>
    </div>
    <div class="col">
      <ul class="list-group">
        <li class="list-group-item border-0"><h6 class="lead m-0"><?php echo ENTRY_SOLD_TO; ?></h6></li>
        <li class="list-group-item border-0"><?php echo $address->format($order->billing, 1, '', '<br>'); ?></li>
        <li class="list-group-item border-0"><i class="fas fa-phone fa-fw"></i> <?php echo ($order->customer['telephone'] ?? ''); ?> <i class="fas fa-at fa-fw"></i> <?php echo ($order->customer['email_address'] ?? ''); ?></li>
     </ul>
    </div>    
    <div class="col text-right">
      <ul class="list-group">
        <li class="list-group-item border-0"><h6 class="lead m-0"><?php echo sprintf(ENTRY_INVOICE_NUMBER, (int)$_GET['oID']); ?></h6></li>
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
      </tr>
    </thead>
    <tbody>
      <?php
        foreach ($order->products as $product) {
          echo '<tr>';
            echo '<td>' . $product['qty'] . '</td>';
            echo '<th>' . $product['name'];
            foreach (($product['attributes'] ?? []) as $attribute) {
              echo '<br><small><i> - ' . $attribute['option'] . ': ' . $attribute['value'] . '</i></small>';
            }
            echo '</th>';
            echo '<td>' . $product['model'] . '</td>';
          echo '</tr>';
        }
      ?>
    </tbody>
  </table>
  
  <?php
  echo $OSCOM_Hooks->call('packingslip', 'extraComments');
  ?>
  
<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>