<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  $customer_info_query = tep_db_query("select o.customers_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " s where o.orders_id = '". (int)$_GET['order_id'] . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.public_flag = '1'");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  require('includes/languages/' . $language . '/account_history_info.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_history.php', '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $_GET['order_id']), tep_href_link('account_history_info.php', 'order_id=' . $_GET['order_id'], 'SSL'));

  require('includes/classes/order.php');
  $order = new order($_GET['order_id']);

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

  <h4><?php echo sprintf(HEADING_ORDER_NUMBER, $_GET['order_id']) . ' <span class="badge badge-secondary">' . $order->info['orders_status'] . '</span></h4>'; ?>
  <p><?php echo '<strong>' . HEADING_ORDER_DATE . '</strong> ' . tep_date_long($order->info['date_purchased']); ?></p>

  <table class="table table-hover table-bordered">
    <thead>
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
    <tr>
      <th colspan="2"><?php echo HEADING_PRODUCTS; ?></th>
      <th class="text-right"><?php echo HEADING_TAX; ?></th>
      <th class="text-right"><?php echo HEADING_TOTAL; ?></th>
    </tr>
<?php
  } else {
?>
    <tr>
      <th colspan="2"><?php echo HEADING_PRODUCTS; ?></th>
      <th class="text-right"><?php echo HEADING_TOTAL; ?></th>
    </tr>
<?php
  }
  
  echo '</thead>';

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '<tr>' . "\n" .
         '<td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '</td>' . "\n" .
         '<td valign="top">' . $order->products[$i]['name'];

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '<td valign="top" class="text-right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }

    echo '<td valign="top" class="text-right">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '</tr>' . "\n";
  }
?>


<?php
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '<tr><td colspan="4" class="text-right">' . $order->totals[$i]['title'] . ' ' . $order->totals[$i]['text'] . '</td></tr>';
  }
?>
  </table>

  <table class="table">
    <thead>
      <tr>
        <?php
        if ($order->delivery != false) {
          echo '<th>' . HEADING_DELIVERY_ADDRESS . '</th>';
        }
        echo '<th>' . HEADING_BILLING_ADDRESS . '</th>';
        ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        if ($order->delivery != false) {
          echo '<td>' . tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />') . '</td>';
        }
        echo '<td>' . tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />') . '</td>';
        ?>
      </tr>
    </tbody>
  </table>
  
  <table class="table">
    <thead>
      <tr>
        <?php
        if ($order->info['shipping_method']) {
          echo '<th scope="col">' . HEADING_SHIPPING_METHOD . '</th>';
        }
        echo '<th scope="col">' . HEADING_PAYMENT_METHOD . '</th>';
        ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        if ($order->info['shipping_method']) {
          echo '<td scope="row">' . $order->info['shipping_method'] . '</td>';
        }
        echo '<td scope="row">' . $order->info['payment_method'] . '</td>';
        ?>
      </tr>
    </tbody>
  </table>

  <h4><?php echo HEADING_ORDER_HISTORY; ?></h4>
  
  <ul class="list-group">
    <?php
    $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$_GET['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' and os.public_flag = '1' order by osh.date_added");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      
      echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<h6>' . $statuses['orders_status_name'] . '</h6>';
        echo (empty($statuses['comments']) ? '' : '<p>' . nl2br(tep_output_string_protected($statuses['comments'])) . '</p>');
        echo '<span class="badge badge-primary badge-pill"><i class="far fa-clock"></i> ' . $statuses['date_added'] . '</span>';
      echo '</li>';
    }
    ?>
  </ul>

<?php
  if (DOWNLOAD_ENABLED == 'true') include('includes/modules/downloads.php');
?>

  <br>
  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account_history_info.php', tep_get_all_get_params(array('order_id')), 'SSL')); ?>
  </div>
  
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
