<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/


  $ppBalanceResult = ['rpcStatus' => -1];

  if ( isset($_GET['type']) && in_array($_GET['type'], ['live', 'sandbox']) ) {
    $ppBalanceResponse = $OSCOM_PayPal->getApiResult('APP', 'GetBalance', null, $_GET['type']);

    if ( isset($ppBalanceResponse['ACK']) && ($ppBalanceResponse['ACK'] === 'Success') ) {
      $currencies = new currencies();
      $ppBalanceResult['rpcStatus'] = 1;

      $counter = 0;

      while ( isset($ppBalanceResponse[$amount_key = "L_AMT$counter"], $ppBalanceResponse[$currency_key = "L_CURRENCYCODE$counter"]) ) {
        if (isset($currencies->currencies[$ppBalanceResponse[$currency_key]])) {
          $balance = $currencies->format($ppBalanceResponse[$amount_key], false, $ppBalanceResponse[$currency_key]);
        }

        $ppBalanceResult['balance'][$ppBalanceResponse[$currency_key]] = $ppBalanceResponse[$amount_key];

        $counter++;
      }
    }
  }

  echo json_encode($ppBalanceResult);
  exit();
?>
