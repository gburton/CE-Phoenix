<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the shopping cart page
  if (!tep_session_is_registered('customer_id')) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'update')) {
    $notify_string = '';

    if (isset($HTTP_POST_VARS['notify']) && !empty($HTTP_POST_VARS['notify'])) {
      $notify = $HTTP_POST_VARS['notify'];

      if (!is_array($notify)) {
        $notify = array($notify);
      }

      for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
        if (is_numeric($notify[$i])) {
          $notify_string .= 'notify[]=' . $notify[$i] . '&';
        }
      }

      if (!empty($notify_string)) {
        $notify_string = 'action=notify&' . substr($notify_string, 0, -1);
      }
    }

    tep_redirect(tep_href_link(FILENAME_DEFAULT, $notify_string));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  $global_query = tep_db_query("select global_product_notifications from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customer_id . "'");
  $global = tep_db_fetch_array($global_query);

  if ($global['global_product_notifications'] != '1') {
    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by date_purchased desc limit 1");
    $orders = tep_db_fetch_array($orders_query);

    $products_array = array();
    $products_query = tep_db_query("select products_id, products_name from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$orders['orders_id'] . "' order by products_name");
    while ($products = tep_db_fetch_array($products_query)) {
      $products_array[] = array('id' => $products['products_id'],
                                'text' => $products['products_name']);
    }
  }

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL', 'class="form-horizontal"')); ?>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-success">
      <?php echo TEXT_SUCCESS; ?>
    </div>
  </div>

  <div class="contentText">

<?php
  if ($global['global_product_notifications'] != '1') {
    echo '<div class="form-group">';
    echo '  <label class="control-label col-xs-4">' . TEXT_NOTIFY_PRODUCTS. '</label>';
    echo '  <div class="col-xs-8">';
    $products_displayed = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      if (!in_array($products_array[$i]['id'], $products_displayed)) {
        echo '<label class="checkbox-inline">';
        echo tep_draw_checkbox_field('notify[]', $products_array[$i]['id']);
        echo '</label>';
        echo '<span class="help-block">' . $products_array[$i]['text']  . '</span>';
        $products_displayed[] = $products_array[$i]['id'];
      }
    }

    echo '  </div>';
    echo '</div>';
  }
?>
  </div>

  <div class="clearfix"></div>

  <div class="contentText">
    <div class="alert alert-info">
    <?php
    echo TEXT_SEE_ORDERS . '<br /><br />' . TEXT_CONTACT_STORE_OWNER;
    ?>
    </div>
  </div>

  <div class="contentText">
    <h3><?php echo TEXT_THANKS_FOR_SHOPPING; ?></h3>
  </div>

<?php
  if (DOWNLOAD_ENABLED == 'true') {
    include(DIR_WS_MODULES . 'downloads.php');
  }
?>

  <div class="buttonSet">
    <span class="buttonAction"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'glyphicon-chevron-right', null, 'primary'); ?></span>
  </div>
</div>

</form>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
