<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

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

  if ( isset($_GET['payment_error']) && !Text::is_empty($_GET['payment_error']) ) {
    $redirect_url = tep_href_link('checkout_payment.php', 'payment_error=' . $_GET['payment_error'] . (isset($_GET['error']) && !Text::is_empty($_GET['error']) ? '&error=' . $_GET['error'] : ''));
  } else {
    $hidden_params = '';

    if ('sage_pay_direct' === $_SESSION['payment']) {
      $redirect_url = tep_href_link('checkout_process.php', 'check=3D');
      $hidden_params = tep_draw_hidden_field('MD', $_POST['MD']) . tep_draw_hidden_field('PaRes', $_POST['PaRes']);
    } else {
      $redirect_url = tep_href_link('checkout_process.php');
    }
  }

  require language::map_to_translation('checkout_confirmation.php');
  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
