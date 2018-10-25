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

  require('includes/languages/' . $language . '/account_history.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_history.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
  $orders_total = tep_count_customer_orders();

  if ($orders_total > 0) {
    ?>
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th scope="col"><?php echo TEXT_ORDER_NUMBER; ?></th>
          <th scope="col"><?php echo TEXT_ORDER_STATUS; ?></th>
          <th scope="col"><?php echo TEXT_ORDER_DATE; ?></th>
          <th scope="col"><?php echo TEXT_ORDER_PRODUCTS; ?></th>
          <th scope="col"><?php echo TEXT_ORDER_COST; ?></th>
          <th scope="col"><?php echo TEXT_VIEW_ORDER; ?></th>
        </tr>
      </thead>
      <tbody>          
    
    <?php
    $history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$customer_id . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.public_flag = '1' order by orders_id DESC";
    $history_split = new splitPageResults($history_query_raw, MAX_DISPLAY_ORDER_HISTORY);
    $history_query = tep_db_query($history_split->sql_query);

    while ($history = tep_db_fetch_array($history_query)) {
      $products_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$history['orders_id'] . "'");
      $products = tep_db_fetch_array($products_query);

      if (tep_not_null($history['delivery_name'])) {
        $order_type = TEXT_ORDER_SHIPPED_TO;
        $order_name = $history['delivery_name'];
      } else {
        $order_type = TEXT_ORDER_BILLED_TO;
        $order_name = $history['billing_name'];
      }
?>
        <tr>
          <th scope="row"><?php echo $history['orders_id']; ?></td>
          <td><?php echo $history['orders_status_name']; ?></td>
          <td><?php echo tep_date_long($history['date_purchased']); ?></td>
          <td><?php echo $products['count']; ?></td>
          <td><?php echo strip_tags($history['order_total']); ?></td>
          <td><?php echo tep_draw_button(SMALL_IMAGE_BUTTON_VIEW, 'fa fa-file', tep_href_link('account_history_info.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'order_id=' . (int)$history['orders_id'], 'SSL'), 'primary', NULL, 'btn-primary btn-sm btn-block'); ?></td>
        </tr>

<?php
    }
?>
      </tbody>
    </table>

<div class="row">
  <div class="col-sm-6 pagenumber d-none d-sm-block">
    <span class="align-middle"><?php echo $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></span>
  </div>
  <div class="col-sm-6">
    <?php echo $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>
  </div>
</div>

<?php
  } else {
?>

  <div class="alert alert-info">
    <p><?php echo TEXT_NO_PURCHASES; ?></p>
  </div>

<?php
  }
?>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?>
  </div>
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
