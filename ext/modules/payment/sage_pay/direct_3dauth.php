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

  if (!isset($_SESSION['sage_pay_direct_acsurl'])) {
    tep_redirect(tep_href_link('checkout_payment.php'));
  }

  if (!isset($_SESSION['payment']) || ($_SESSION['payment'] != 'sage_pay_direct')) {
    tep_redirect(tep_href_link('checkout_payment.php'));
  }

  require language::map_to_translation('checkout_confirmation.php');
  require language::map_to_translation('modules/payment/sage_pay_direct.php');

  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
