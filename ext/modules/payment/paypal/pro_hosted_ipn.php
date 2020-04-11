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

  if ( !defined('OSCOM_APP_PAYPAL_HS_STATUS') || !in_array(OSCOM_APP_PAYPAL_HS_STATUS, ['1', '0']) ) {
    exit;
  }

  require 'includes/modules/payment/paypal_pro_hs.php';

  if ( !empty($_POST['txn_id']) ) {
    $paypal_pro_hs = new paypal_pro_hs();
    $result = $paypal_pro_hs->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => $_POST['txn_id']], (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox', true);

    if ( isset($result['ACK']) && (($result['ACK'] == 'Success') || ($result['ACK'] == 'SuccessWithWarning')) ) {
      $paypal_pro_hs->verifyTransaction($result, true);
    }
  }

  require 'includes/application_bottom.php';
?>
