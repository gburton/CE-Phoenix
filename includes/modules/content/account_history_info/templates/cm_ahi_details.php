<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
<!-- Start cm_ahi_details module -->
  <div class="contentText col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_DETAILS_CONTENT_WIDTH; ?>">
    <div class="panel panel-default">
      <div class="panel-heading"><strong><?php echo sprintf(HEADING_ORDER_NUMBER, $oID) . ' <span class="badge pull-right">' . $order->info['orders_status'] . '</span>'; ?></strong></div>
      <div class="panel-body">

        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="table-hover order_confirmation">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TAX; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  } else {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '            <td valign="top">' . $order->products[$i]['name'];

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '            <td valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }

    echo '            <td align="right" valign="top">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
        <hr>
        <table width="100%" class="pull-right">
<?php
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" width="100%">' . $order->totals[$i]['title'] . '&nbsp;</td>' . "\n" .
         '            <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
      </div>


      <div class="panel-footer">
        <span class="pull-right hidden-xs"><?php echo HEADING_ORDER_TOTAL . ' ' . $order->info['total']; ?></span><?php echo HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?>
      </div>
    </div>
  </div>
<!-- End cm_ahi_details module -->
