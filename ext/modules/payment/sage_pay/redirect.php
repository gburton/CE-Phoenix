<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

// if the customer is not logged on, redirect them to the login page
  $parameters = [
    'page' => 'checkout_payment.php',
    'mode' => 'SSL',
  ];
  $OSCOM_Hooks->register_pipeline('loginRequired', $parameters);

  if ( isset($_GET['payment_error']) && tep_not_null($_GET['payment_error']) ) {
    $redirect_url = tep_href_link('checkout_payment.php', 'payment_error=' . $_GET['payment_error'] . (isset($_GET['error']) && tep_not_null($_GET['error']) ? '&error=' . $_GET['error'] : ''), 'SSL');
  } else {
    $hidden_params = '';

    if ('sage_pay_direct' === $_SESSION['payment']) {
      $redirect_url = tep_href_link('checkout_process.php', 'check=3D', 'SSL');
      $hidden_params = tep_draw_hidden_field('MD', $_POST['MD']) . tep_draw_hidden_field('PaRes', $_POST['PaRes']);
    } else {
      $redirect_url = tep_href_link('checkout_process.php', '', 'SSL');
    }
  }

  require "includes/languages/$language/checkout_confirmation.php";
  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
