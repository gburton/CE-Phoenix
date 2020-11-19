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
  $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name FROM orders_status WHERE language_id = " . (int)$_SESSION['languages_id']);
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = [
      'id' => $orders_status['orders_status_id'],
      'text' => $orders_status['orders_status_name'],
    ];
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('orders', 'preAction');

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
          $check_status['status_name'] = $orders_status_array[$status];
          $check_status['notify_comments'] = $comments;

          if (isset($_POST['notify']) && ('on' === $_POST['notify']) && tep_notify('update_order', $check_status)) {
            $customer_notified = 1;
          } else {
            $customer_notified = 0;
          }

          tep_db_query("INSERT INTO orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES ('" . (int)$oID . "', '" . tep_db_input($status) . "', NOW(), " . (int)$customer_notified . ", '" . tep_db_input($comments)  . "')");

          $order_updated = true;
        }

        $OSCOM_Hooks->call('orders', 'updateOrderAction');

        if ($order_updated) {
          $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
        } else {
          $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
        }

        tep_redirect(tep_href_link('orders.php', tep_get_all_get_params(['action']) . 'action=edit'));
        break;
      case 'deleteconfirm':
        $oID = tep_db_prepare_input($_GET['oID']);

        tep_remove_order($oID, $_POST['restock'] ?? false);

        $OSCOM_Hooks->call('orders', 'deleteConfirmAction');

        tep_redirect(tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action'])));
        break;
    }
  }

  $OSCOM_Hooks->call('orders', 'postAction');

  if (($action == 'edit') && isset($_GET['oID'])) {
    $oID = (int)$_GET['oID'];

    $orders_query = tep_db_query("SELECT orders_id FROM orders WHERE orders_id = " . (int)$oID);
    $order_exists = tep_db_num_rows($orders_query);

    if (!$order_exists) {
      $messageStack->add_session(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');

      tep_redirect(tep_href_link('orders.php'));
    }
  }

  $OSCOM_Hooks->call('orders', 'orderAction');

  require 'includes/template_top.php';

  $base_url = HTTP_SERVER . DIR_WS_ADMIN;

  if (($action == 'edit') && $order_exists) {
    $order = new order($oID);
    $address = $customer_data->get_module('address');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= sprintf(HEADING_TITLE_ORDER, (int)$oID); ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      echo tep_draw_bootstrap_button(IMAGE_ORDERS_INVOICE, 'fas fa-file-invoice-dollar', tep_href_link('invoice.php', 'oID=' . $_GET['oID']), null, ['newwindow' => true], 'btn-info mr-2');
      echo tep_draw_bootstrap_button(IMAGE_ORDERS_PACKINGSLIP, 'fas fa-file-contract', tep_href_link('packingslip.php', 'oID=' . $_GET['oID']), null, ['newwindow' => true], 'btn-info mr-2');
      echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('orders.php', tep_get_all_get_params(['action'])), null, null, 'btn-light');
      ?>
    </div>
  </div>


  <div id="orderTabs">
    <ul class="nav nav-tabs">
      <li class="nav-item"><?= '<a class="nav-link active" data-toggle="tab" href="#section_summary_content" role="tab">' . TAB_TITLE_SUMMARY . '</a>'; ?></li>
      <li class="nav-item"><?= '<a class="nav-link" data-toggle="tab" href="#section_products_content" role="tab">' . TAB_TITLE_PRODUCTS . '</a>'; ?></li>
      <li class="nav-item"><?= '<a class="nav-link" data-toggle="tab" href="#section_status_history_content" role="tab">' . TAB_TITLE_STATUS_HISTORY . '</a>'; ?></li>
    </ul>

    <div class="tab-content pt-3">
      <div class="tab-pane fade show active" id="section_summary_content" role="tabpanel">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th><?= ENTRY_CUSTOMER; ?></th>
              <th><?= ENTRY_SHIPPING_ADDRESS; ?></th>
              <th><?= ENTRY_BILLING_ADDRESS; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <p><?= $address->format($order->customer, 1, '', '<br>'); ?></p>
                <p><?= $order->customer['telephone'] . '<br>' . '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></p>
              </td>
              <td><p><?= $address->format($order->delivery, 1, '', '<br>'); ?></p></td>
              <td><p><?= $address->format($order->billing, 1, '', '<br>'); ?></p></td>
            </tr>
          </tbody>
          <thead class="thead-dark">
            <tr>
              <th><?= ENTRY_PAYMENT_METHOD; ?></th>
              <th><?= ENTRY_STATUS; ?></th>
              <th><?= ENTRY_TOTAL; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><p><?= sprintf(TEXT_ORDER_PAYMENT, $order->info['payment_method'], $order->info['date_purchased']); ?></p></td>
              <td><p><?= sprintf(TEXT_ORDER_STATUS, $order->info['orders_status'], ($order->info['last_modified'] ?? $order->info['date_purchased'])); ?></p></td>
              <td><h1 class="display-4"><?= $order->info['total']; ?></h1></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="tab-pane fade" id="section_products_content" role="tabpanel">
        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_PRODUCTS; ?></th>
              <th><?= TABLE_HEADING_PRODUCTS_MODEL; ?></th>
              <th class="text-right"><?= TABLE_HEADING_TAX; ?></th>
              <th class="text-right"><?= TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></th>
              <th class="text-right"><?= TABLE_HEADING_PRICE_INCLUDING_TAX; ?></th>
              <th class="text-right"><?= TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></th>
              <th class="text-right"><?= TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($order->products as $product) {
              echo '<tr>';
                echo '<td>' . $product['qty'] . ' x ' . $product['name'];
                if (!empty($product['attributes'])) {
                  foreach ($product['attributes'] as $attribute) {
                    echo '<br><small> - ' . $attribute['option'] . ': ' . $attribute['value'];
                    if ($attribute['price'] != '0') echo ' (' . $attribute['prefix'] . $currencies->format($attribute['price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
                    echo '</small>';
                  }
                }
                echo '</td>';
                echo '<td>' . $product['model'] . '&nbsp;</td>';
                echo '<td class="text-right">' . tep_display_tax_value($product['tax']) . '%</td>';
                echo '<td class="text-right">' . $currencies->format($product['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</td>';
                echo '<td class="text-right">' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true), true, $order->info['currency'], $order->info['currency_value']) . '</td>';
                echo '<td class="text-right">' . $currencies->format($product['final_price'] * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>';
                echo '<th class="text-right">' . $currencies->format(tep_add_tax($product['final_price'], $product['tax'], true) * $product['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</th>';
              echo '</tr>';
            }
            foreach ($order->totals as $ot) {
              echo '<tr>';
                echo '<td colspan="6" class="text-right bg-white">' . $ot['title'] . '</td>';
                echo '<th class="text-right bg-white">' . $ot['text'] . '</th>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>

      </div>
      <div class="tab-pane fade" id="section_status_history_content" role="tabpanel">
        <?= tep_draw_form('status', 'orders.php', tep_get_all_get_params(['action']) . 'action=update_order'); ?>

          <div class="form-group row" id="zStatus">
            <label for="oStatus" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_STATUS; ?></label>
            <div class="col-sm-9">
              <?= tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status_id'], 'id="oStatus" class="form-control"'); ?>
            </div>
          </div>

          <div class="form-group row" id="zComment">
            <label for="oComment" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_NOTIFY_COMMENTS; ?></label>
            <div class="col-sm-9">
              <?= tep_draw_textarea_field('comments', 'soft', '60', '5', null, 'id="oComment" class="form-control"'); ?>
            </div>
          </div>

          <div class="form-group row align-items-center" id="zNotify">
            <div class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_NOTIFY_CUSTOMER; ?></div>
            <div class="col-sm-9 pl-5 custom-control custom-switch">
              <?= tep_draw_selection_field('notify', 'checkbox', 'on', 1, 'class="custom-control-input" id="oNotify"'); ?>
              <label for="oNotify" class="custom-control-label text-muted"><small><?= ENTRY_NOTIFY_CUSTOMER_TEXT; ?></small></label>
            </div>
          </div>

          <div class="form-group row align-items-center" id="zNotifyComments">
            <div class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_NOTIFY_COMMENTS; ?></div>
            <div class="col-sm-9 pl-5 custom-control custom-switch">
              <?= tep_draw_selection_field('notify_comments', 'checkbox', 'on', 1, 'class="custom-control-input" id="oNotifyComments"'); ?>
              <label for="oNotifyComments" class="custom-control-label text-muted"><small><?= ENTRY_NOTIFY_COMMENTS_TEXT; ?></small></label>
            </div>
          </div>

          <?= $OSCOM_Hooks->call('orders', 'sectionStatusHistoryContentForm'); ?>

          <p><?= tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg'); ?></p>

        </form>

        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_DATE_ADDED; ?></th>
              <th><?= TABLE_HEADING_STATUS; ?></th>
              <th><?= TABLE_HEADING_COMMENTS; ?></th>
              <th class="text-right"><?= TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $orders_history_query = tep_db_query("SELECT * FROM orders_status_history WHERE orders_id = '" . tep_db_input($oID) . "' ORDER BY date_added DESC");
            if (tep_db_num_rows($orders_history_query)) {
              while ($orders_history = tep_db_fetch_array($orders_history_query)) {
                echo '<tr>';
                  echo '<td>' . $orders_history['date_added'] . '</td>';
                  echo '<td>' . $orders_status_array[$orders_history['orders_status_id']] . '</td>';
                  echo '<td>' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>';
                  echo '<td class="text-right">';
                    echo ($orders_history['customer_notified'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>';
                  echo '</td>';
                echo '</tr>' . "\n";
              }
            } else {
              echo '<tr>';
                echo '<td colspan="4">' . TEXT_NO_ORDER_HISTORY . '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <?= $OSCOM_Hooks->call('orders', 'orderTab'); ?>

    </div>
  </div>

<?php
  } else {
    $orders_sql = sprintf(<<<'EOSQL'
SELECT o.*, s.orders_status_name, ot.text AS order_total
 FROM orders o INNER JOIN orders_total ot ON o.orders_id = ot.orders_id
   LEFT JOIN orders_status s ON o.orders_status = s.orders_status_id AND s.language_id = %d
 WHERE ot.class = 'ot_total'
EOSQL
      , (int)$_SESSION['languages_id']);
    if (isset($_GET['cID'])) {
      $orders_sql .= ' AND o.customers_id = ' . (int)tep_db_prepare_input($_GET['cID']);
    }
    if (!empty($_GET['status']) && is_numeric($_GET['status'])) {
      $orders_sql .= ' AND o.orders_status = ' . (int)tep_db_prepare_input($_GET['status']);
    }
    $listing_order = ' ORDER BY o.orders_id DESC';

    $parameters = [
      'orders_sql' => &$orders_sql,
      'listing_order' => &$listing_order,
    ];
    $OSCOM_Hooks->call('orders', 'injectSQL', $parameters);
    $orders_sql .= $listing_order;

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_sql, $orders_query_numrows);
    $orders_query = tep_db_query($orders_sql);
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      echo tep_draw_form('orders', 'orders.php', '', 'get');
        echo '<div class="input-group mb-1">';
          echo '<div class="input-group-prepend">';
            echo '<span class="input-group-text">' . HEADING_TITLE_SEARCH . '</span>';
          echo '</div>';
          echo tep_draw_input_field('oID', null, null, 'number') . tep_draw_hidden_field('action', 'edit');
        echo '</div>';
        echo tep_hide_session_id();
      echo '</form>';
      echo tep_draw_form('status', 'orders.php', '', 'get');
        echo '<div class="input-group mb-1">';
          echo '<div class="input-group-prepend">';
            echo '<span class="input-group-text">' . HEADING_TITLE_STATUS . '</span>';
          echo '</div>';
          echo tep_draw_pull_down_menu('status', array_merge([['id' => '', 'text' => TEXT_ALL_ORDERS]], $orders_statuses), '', 'onchange="this.form.submit();"');
        echo '</div>';
        echo tep_hide_session_id();
      echo '</form>';
      echo $OSCOM_Hooks->call('orders', 'injectFilterForm');
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_OID; ?></th>
              <th><?= TABLE_HEADING_CUSTOMERS; ?></th>
              <th><?= TABLE_HEADING_ORDER_TOTAL; ?></th>
              <th class="text-right"><?= TABLE_HEADING_DATE_PURCHASED; ?></th>
              <th class="text-right"><?= TABLE_HEADING_STATUS; ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($orders = tep_db_fetch_array($orders_query)) {
              if ((!isset($oInfo) || !($oInfo instanceof objectInfo)) && (!isset($_GET['oID']) || ($_GET['oID'] == $orders['orders_id']))) {
                $oInfo = new objectInfo($orders);
              }

              if (isset($oInfo->orders_id) && ($orders['orders_id'] == $oInfo->orders_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'">';
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('orders.php', tep_get_all_get_params(['oID']) . 'oID=' . $orders['orders_id']) . '\'">';
                $icon = '<a href="' . tep_href_link('orders.php', tep_get_all_get_params(['oID']) . 'oID=' . $orders['orders_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $orders['orders_id']; ?></td>
                <td><?= $orders['customers_name']; ?></td>
                <td><?= strip_tags($orders['order_total']); ?></td>
                <td class="text-right"><?= $orders['date_purchased']; ?></td>
                <td class="text-right"><?= $orders['orders_status_name']; ?></td>
                <td class="text-right"><?= '<a href="' . tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $orders['orders_id'] . '&action=edit') . '"><i class="fas fa-cogs mr-2 text-dark"></i></a>' . $icon; ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></div>
        <div class="col text-right mr-2"><?= $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(['page', 'oID', 'action'])); ?></div>
      </div>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_ORDER];

      $contents = ['form' => tep_draw_form('orders', 'orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO . '<br><br><strong>' . $oInfo->customers_name . '</strong>'];
      $contents[] = ['text' => '<div class="custom-control custom-switch py-2">' . tep_draw_selection_field('restock', 'checkbox', 'on', null, 'class="custom-control-input" id="oRestock"') . '<label for="oRestock" class="custom-control-label text-muted">' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY . '</label></div>'];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id), null, null, 'btn-light')];
      break;
    default:
      if (($oInfo ?? null) instanceof objectInfo) {
        $heading[] = ['text' => '[' . $oInfo->orders_id . '] ' . $oInfo->date_purchased];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('orders.php', tep_get_all_get_params(['oID', 'action']) . 'oID=' . $oInfo->orders_id . '&action=delete'), null, null, 'btn-danger')];
        $contents[] = ['text' => sprintf(TEXT_DATE_ORDER_CREATED, $oInfo->date_purchased)];
        if (tep_not_null($oInfo->last_modified)) $contents[] = ['text' => sprintf(TEXT_DATE_ORDER_LAST_MODIFIED, $oInfo->last_modified)];
        $contents[] = ['text' => sprintf(TEXT_INFO_PAYMENT_METHOD, $oInfo->payment_method)];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_ORDERS_INVOICE, 'fas fa-file-invoice-dollar', tep_href_link('invoice.php', 'oID=' . $oInfo->orders_id), null, ['newwindow' => true], 'btn-info mr-2') . tep_draw_bootstrap_button(IMAGE_ORDERS_PACKINGSLIP, 'fas fa-file-contract', tep_href_link('packingslip.php', 'oID=' . $oInfo->orders_id), null, ['newwindow' => true], 'btn-info')];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
