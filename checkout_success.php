<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce
  
  Edited by 2014 Newburns Design and Technology
  *************************************************
  ************ New addon definitions **************
  ************        Below          **************
  *************************************************
  Credit Class, Gift Vouchers & Discount Coupons osC2.3.3.4 (CCGV) added -- http://addons.oscommerce.com/info/9020  

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the shopping cart page
  if (!tep_session_is_registered('customer_id')) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by date_purchased desc limit 1");

// redirect to shopping cart page if no orders exist
  if ( !tep_db_num_rows($orders_query) ) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

  $orders = tep_db_fetch_array($orders_query);

  $order_id = $orders['orders_id'];

  $page_content = $oscTemplate->getContent('checkout_success');

  if ( isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'update') ) {
    tep_redirect(tep_href_link(FILENAME_DEFAULT));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL', 'class="form-horizontal"')); ?>

<div class="contentContainer">
  <?php echo $page_content; ?>
<?php /* ** Altered for CCGV ** */ ?>
<?php
  $gv_query=tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='" . (int)$customer_id . "'");
  if ($gv_result=tep_db_fetch_array($gv_query)) {
    if ($gv_result['amount'] > 0) {
?>
<?php echo GV_HAS_VOUCHERA; echo tep_href_link(FILENAME_GV_SEND); echo GV_HAS_VOUCHERB; ?>

<?php
}
}
?>
<?php /* ** EOF alteration for CCGV ** */ ?>
</div>

<div class="contentContainer">
  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'glyphicon glyphicon-chevron-right', null, 'primary', null, 'btn-success'); ?></div>
  </div>
</div>

</form>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
