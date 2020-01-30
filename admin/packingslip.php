<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/currencies.php');
  $currencies = new currencies();

  $oID = tep_db_prepare_input($_GET['oID']);
  $orders_query = tep_db_query("select orders_id from orders where orders_id = '" . (int)$oID . "'");

  include('includes/classes/order.php');
  $order = new order($oID);
  
  require('includes/template_top.php');
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
        <li class="list-group-item border-0"><h6 class="lead m-0"><?php echo ENTRY_SOLD_TO; ?></h6></li>
        <li class="list-group-item border-0"><?php echo tep_address_format($order->customer['format_id'], $order->billing, 1, '', '<br>'); ?></li>
        <li class="list-group-item border-0"><i class="fas fa-phone fa-fw"></i> <?php echo $order->customer['telephone']; ?> <i class="fas fa-at fa-fw"></i> <?php echo $order->customer['email_address']; ?></li>
     </ul>
    </div>
    <div class="col">
      <ul class="list-group">
        <li class="list-group-item border-0"><h6 class="lead m-0"><?php echo ENTRY_SHIP_TO; ?></h6></li>
        <li class="list-group-item border-0 font-weight-bold"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>'); ?></li>
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
      for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        echo '<tr>';
          echo '<td>' . $order->products[$i]['qty'] . '</td>';
          echo '<th>' . $order->products[$i]['name'];
          if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
            for ($j=0, $k=sizeof($order->products[$i]['attributes']); $j<$k; $j++) {
              echo '<br /><small><i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
            }            
          }
          echo '</th>';
          echo '<td>' . $order->products[$i]['model'] . '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
  
  <?php
  echo $OSCOM_Hooks->call('packingslip', 'extraComments');
  ?>

</body>
</html>

<?php require('includes/application_bottom.php'); ?>
