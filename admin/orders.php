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

  $orders_statuses = [];
  $orders_status_array = [];
  $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name FROM orders_status WHERE language_id = " . (int)$languages_id);
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = [
      'id' => $orders_status['orders_status_id'],
      'text' => $orders_status['orders_status_name'],
    ];
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $action = ($_GET['action'] ?? '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'update_order':
        $oID = tep_db_prepare_input($_GET['oID']);
        $status = tep_db_prepare_input($_POST['status']);
        $comments = tep_db_prepare_input($_POST['comments']);

        $order_updated = false;
        $check_status_query = tep_db_query("SELECT * FROM orders WHERE orders_id = " . (int)$oID);
        $check_status = tep_db_fetch_array($check_status_query);

        if ( ($check_status['orders_status'] != $status) || tep_not_null($comments)) {
          tep_db_query("UPDATE orders SET orders_status = '" . tep_db_input($status) . "', last_modified = NOW() WHERE orders_id = " . (int)$oID);

          $customer_notified = '0';
          if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
            $notify_comments = '';
            if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
            }

            $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link('account_history_info.php', 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);

            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

            $customer_notified = '1';
          }

          tep_db_query("INSERT INTO orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES ('" . (int)$oID . "', '" . tep_db_input($status) . "', NOW(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");

          $order_updated = true;
        }

        if ($order_updated == true) {
         $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
        } else {
          $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
        }

        tep_redirect(tep_href_link('orders.php', tep_get_all_get_params(['action']) . 'action=edit'));
        break;
      case 'deleteconfirm':
        $oID = tep_db_prepare_input($_GET['oID']);

        tep_remove_order($oID, $_POST['restock']);

        tep_redirect(tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action'])));
        break;
    }
  }

  if (($action == 'edit') && isset($_GET['oID'])) {
    $oID = tep_db_prepare_input($_GET['oID']);

    $orders_query = tep_db_query("SELECT orders_id FROM orders WHERE orders_id = " . (int)$oID);
    $order_exists = tep_db_num_rows($orders_query);
    if (!$order_exists) {
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
  }

  $OSCOM_Hooks->call('orders', 'orderAction');

  require 'includes/template_top.php';

  $base_url = ($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_ADMIN : HTTP_SERVER . DIR_WS_ADMIN;
?>

<script>
if ( typeof jQuery.ui == 'undefined' ) {
  document.write('<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/redmond/jquery-ui.css" />');
  document.write('<scr' + 'ipt src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></scr' + 'ipt>');

/* Custom jQuery UI */
  document.write('<style>.ui-widget { font-family: Lucida Grande, Lucida Sans, Verdana, Arial, sans-serif; font-size: 11px; } .ui-dialog { min-width: 500px; }</style>');
}
</script>

<?php
  if (($action == 'edit') && $order_exists) {
    $order = new order($oID);
    $address = $customer_data->get_module('address');
?>

<h1 class="pageHeading"><?php echo HEADING_TITLE . ': #' . (int)$oID . ' (' . $order->info['total'] . ')'; ?></h1>

<div style="text-align: right; padding-bottom: 15px;"><?php echo tep_draw_button(IMAGE_ORDERS_INVOICE, 'document', tep_href_link('invoice.php', 'oID=' . $_GET['oID']), null, ['newwindow' => true]) . tep_draw_button(IMAGE_ORDERS_PACKINGSLIP, 'document', tep_href_link('packingslip.php', 'oID=' . $_GET['oID']), null, ['newwindow' => true]) . tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link('orders.php', tep_get_all_get_params(['action']))); ?></div>

<div id="orderTabs" style="overflow: auto;">
  <ul>
    <li><?php echo '<a href="' . substr(tep_href_link('orders.php', tep_get_all_get_params()), strlen($base_url)) . '#section_summary_content">' . TAB_TITLE_SUMMARY . '</a>'; ?></li>
    <li><?php echo '<a href="' . substr(tep_href_link('orders.php', tep_get_all_get_params()), strlen($base_url)) . '#section_products_content">' . TAB_TITLE_PRODUCTS . '</a>'; ?></li>
    <li><?php echo '<a href="' . substr(tep_href_link('orders.php', tep_get_all_get_params()), strlen($base_url)) . '#section_status_history_content">' . TAB_TITLE_STATUS_HISTORY . '</a>'; ?></li>
  </ul>

  <div id="section_summary_content" style="padding: 10px;">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_CUSTOMER; ?></legend>

            <p><?php echo $address->format($order->customer, 1, '', '<br />'); ?></p>
            <p><?php echo $order->customer['telephone'] . '<br />' . '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_SHIPPING_ADDRESS; ?></legend>

            <p><?php echo $address->format($order->delivery, 1, '', '<br />'); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_BILLING_ADDRESS; ?></legend>

            <p><?php echo $address->format($order->billing, 1, '', '<br />'); ?></p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_PAYMENT_METHOD; ?></legend>

            <p><?php echo $order->info['payment_method']; ?></p>

<?php
    if (tep_not_null($order->info['cc_type']) || tep_not_null($order->info['cc_owner']) || tep_not_null($order->info['cc_number'])) {
?>

            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
                <td><?php echo $order->info['cc_type']; ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
                <td><?php echo $order->info['cc_owner']; ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
                <td><?php echo $order->info['cc_number']; ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
                <td><?php echo $order->info['cc_expires']; ?></td>
              </tr>
            </table>

<?php
    }
?>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_STATUS; ?></legend>

            <p><?php echo $order->info['status'] . '<br />' . (empty($order->info['last_modified']) ? tep_datetime_short($order->info['date_purchased']) : tep_datetime_short($order->info['last_modified'])); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend><?php echo ENTRY_TOTAL; ?></legend>

            <p><?php echo $order->info['total']; ?></p>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div id="section_products_content" style="padding: 10px;">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
<?php
    foreach ($order->products as $product) {
      echo '      <tr class="dataTableRow">' . "\n"
         . '        <td class="dataTableContent" valign="top" align="right">' . $product['qty'] . '&nbsp;x</td>' . "\n"
         . '        <td class="dataTableContent" valign="top">' . $product['name'];

      if (!empty($product['attributes'])) {
        foreach ($product['attributes'] as $attribute) {
          echo '<br /><nobr><small>&nbsp;<i> - ' . $attribute['option'] . ': ' . $attribute['value'];
          if ($attribute['price'] != '0') echo ' (' . $attribute['prefix'] . $currencies->format($attribute['price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '        </td>' . "\n"
         . '        <td class="dataTableContent" valign="top">' . $product['model'] . '</td>' . "\n"
         . '        <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($product['tax']) . '%</td>' . "\n"
         . '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format($product['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n"
         . '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true), true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n"
         . '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format($product['final_price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n"
         . '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true) * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n";
      echo '      </tr>' . "\n";
    }
?>
      <tr>
        <td align="right" colspan="8"><table border="0" cellspacing="0" cellpadding="2">
<?php
    foreach ( $order->totals as $ot ) {
      echo '          <tr>' . "\n"
         . '            <td align="right" class="smallText">' . $ot['title'] . '</td>' . "\n"
         . '            <td align="right" class="smallText">' . $ot['text'] . '</td>' . "\n"
         . '          </tr>' . "\n";
    }
?>
        </table></td>
      </tr>
    </table>
  </div>

  <div id="section_status_history_content">
    <?php echo tep_draw_form('status', 'orders.php', tep_get_all_get_params(['action']) . 'action=update_order'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo ENTRY_STATUS; ?></td>
        <td><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php echo ENTRY_ADD_COMMENT; ?></td>
        <td><?php echo tep_draw_textarea_field('comments', 'soft', '60', '6', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_NOTIFY_CUSTOMER; ?></td>
        <td><?php echo tep_draw_checkbox_field('notify', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_NOTIFY_COMMENTS; ?></td>
        <td><?php echo tep_draw_checkbox_field('notify_comments', '', true); ?></td>
      </tr>
      <tr>
        <td colspan="2" align="right"><?php echo tep_draw_button(IMAGE_UPDATE, 'disk', null, 'primary'); ?></td>
      </tr>
    </table>

    </form>

    <br />

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" align="center"><strong><?php echo TABLE_HEADING_DATE_ADDED; ?></strong></td>
        <td class="dataTableHeadingContent" align="center"><strong><?php echo TABLE_HEADING_STATUS; ?></strong></td>
        <td class="dataTableHeadingContent" align="center"><strong><?php echo TABLE_HEADING_COMMENTS; ?></strong></td>
        <td class="dataTableHeadingContent" align="right"><strong><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></strong></td>
      </tr>

<?php
    $orders_history_query = tep_db_query("SELECT orders_status_id, date_added, customer_notified, comments FROM orders_status_history WHERE orders_id = '" . tep_db_input($oID) . "' ORDER BY date_added DESC");
    if (tep_db_num_rows($orders_history_query)) {
      while ($orders_history = tep_db_fetch_array($orders_history_query)) {
        echo '      <tr class="dataTableRow">' . "\n"
           . '        <td class="dataTableContent" valign="top">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n"
           . '        <td class="dataTableContent" valign="top">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n"
           . '        <td class="dataTableContent" valign="top">' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n"
           . '        <td class="dataTableContent" valign="top" align="right">';

        if ($orders_history['customer_notified'] == '1') {
          echo tep_image('images/icons/tick.gif', ICON_TICK);
        } else {
          echo tep_image('images/icons/cross.gif', ICON_CROSS);
        }

        echo '        </td>' . "\n" .
             '      </tr>' . "\n";
      }
    } else {
        echo '      <tr class="dataTableRow">' . "\n" .
             '        <td class="dataTableContent" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
             '      </tr>' . "\n";
    }
?>

    </table>
  </div>

<?php
    echo $OSCOM_Hooks->call('orders', 'orderTab');
?>

</div>

<script>
$(function() {
  $('#orderTabs').tabs();
});
</script>

<?php
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr><?php echo tep_draw_form('orders', 'orders.php', '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit'); ?></td>
              <?php echo tep_hide_session_id(); ?></form></tr>
              <tr><?php echo tep_draw_form('status', 'orders.php', '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge([['id' => '', 'text' => TEXT_ALL_ORDERS]], $orders_statuses), '', 'onchange="this.form.submit();"'); ?></td>
              <?php echo tep_hide_session_id(); ?></form></tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    if (isset($_GET['cID'])) {
      $cID = tep_db_prepare_input($_GET['cID']);
      $orders_query_raw = "SELECT o.*, s.orders_status_name, ot.text AS order_total FROM orders o LEFT JOIN orders_total ot ON (o.orders_id = ot.orders_id), orders_status s WHERE o.customers_id = " . (int)$cID . " AND o.orders_status = s.orders_status_id AND s.language_id = " . (int)$languages_id . " AND ot.class = 'ot_total' ORDER BY orders_id DESC";
    } elseif (!empty($_GET['status']) && is_numeric($_GET['status'])) {
      $status = tep_db_prepare_input($_GET['status']);
      $orders_query_raw = "SELECT o.*, s.orders_status_name, ot.text AS order_total FROM orders o LEFT JOIN orders_total ot ON (o.orders_id = ot.orders_id), orders_status s WHERE o.orders_status = s.orders_status_id AND s.language_id = " . (int)$languages_id . " AND s.orders_status_id = " . (int)$status . " AND ot.class = 'ot_total' ORDER BY o.orders_id DESC";
    } else {
      $orders_query_raw = "SELECT o.*, s.orders_status_name, ot.text AS order_total FROM orders o LEFT JOIN orders_total ot ON (o.orders_id = ot.orders_id), orders_status s WHERE o.orders_status = s.orders_status_id AND s.language_id = " . (int)$languages_id . " AND ot.class = 'ot_total' ORDER BY o.orders_id DESC";
    }

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_query_raw, $orders_query_numrows);
    $orders_query = tep_db_query($orders_query_raw);
    while ($orders = tep_db_fetch_array($orders_query)) {
      if ((!isset($oInfo) || !($oInfo instanceof objectInfo)) && (!isset($_GET['oID']) || ($_GET['oID'] == $orders['orders_id']))) {
        $oInfo = new objectInfo($orders);
      }

      if (isset($oInfo) && $oInfo instanceof objectInfo && ($orders['orders_id'] == $oInfo->orders_id)) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'">' . "\n";
        $icon = tep_image('images/icon_arrow_right.gif', '');
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('orders.php', tep_get_all_get_params(['oID']) . 'oID=' . $orders['orders_id']) . '\'">' . "\n";
        $icon = '<a href="' . tep_href_link('orders.php', tep_get_all_get_params(['oID']) . 'oID=' . $orders['orders_id']) . '">' . tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '</a>';
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . tep_image('images/icons/preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $orders['customers_name']; ?></td>
                <td class="dataTableContent" align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                <td class="dataTableContent" align="center"><?php echo tep_datetime_short($orders['date_purchased']); ?></td>
                <td class="dataTableContent" align="right"><?php echo $orders['orders_status_name']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $icon; ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(['page', 'oID', 'action'])); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'delete':
      $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_DELETE_ORDER . '</strong>'];

      $contents = ['form' => tep_draw_form('orders', 'orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO . '<br /><br /><strong>' . $oInfo->customers_name . '</strong>'];
      $contents[] = ['text' => '<br />' . tep_draw_checkbox_field('restock') . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY];
      $contents[] = ['align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id))];
      break;
    default:
      if ($oInfo instanceof objectInfo) {
        $heading[] = ['text' => '<strong>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . tep_datetime_short($oInfo->date_purchased) . '</strong>'];

        $contents[] = ['align' => 'center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=edit')) . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=delete'))];
        $contents[] = ['align' => 'center', 'text' => tep_draw_button(IMAGE_ORDERS_INVOICE, 'document', tep_href_link('invoice.php', 'oID=' . $oInfo->orders_id), null, ['newwindow' => true]) . tep_draw_button(IMAGE_ORDERS_PACKINGSLIP, 'document', tep_href_link('packingslip.php', 'oID=' . $oInfo->orders_id), null, ['newwindow' => true])];
        $contents[] = ['text' => '<br />' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased)];
        if (tep_not_null($oInfo->last_modified)) $contents[] = ['text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified)];
        $contents[] = ['text' => '<br />' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
