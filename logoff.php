<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/logoff.php');

  $breadcrumb->add(NAVBAR_TITLE);

  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
if ( tep_session_is_registered('sendto') ) {
  tep_session_unregister('sendto');
}

if ( tep_session_is_registered('billto') ) {
  tep_session_unregister('billto');
}

if ( tep_session_is_registered('shipping') ) {
  tep_session_unregister('shipping');
}

if ( tep_session_is_registered('payment') ) {
  tep_session_unregister('payment');
}

if ( tep_session_is_registered('comments') ) {
  tep_session_unregister('comments');
}

  $cart->reset();

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-danger">
      <?php echo TEXT_MAIN; ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php'), null, null, 'btn-danger'); ?></div>
  </div>
</div>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
