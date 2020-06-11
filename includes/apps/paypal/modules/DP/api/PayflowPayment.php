<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  function OSCOM_PayPal_DP_Api_PayflowPayment($OSCOM_PayPal, $server, $extra_params) {
    if ( $server == 'live' ) {
      $api_url = 'https://payflowpro.paypal.com';
    } else {
      $api_url = 'https://pilot-payflowpro.paypal.com';
    }

    $params = [
      'USER' => $OSCOM_PayPal->getCredentials('DP', ($OSCOM_PayPal->hasCredentials('DP', 'payflow_user') ? 'payflow_user' : 'payflow_vendor')),
      'VENDOR' => $OSCOM_PayPal->getCredentials('DP', 'payflow_vendor'),
      'PARTNER' => $OSCOM_PayPal->getCredentials('DP', 'payflow_partner'),
      'PWD' => $OSCOM_PayPal->getCredentials('DP', 'payflow_password'),
      'TENDER' => 'C',
      'TRXTYPE' => (OSCOM_APP_PAYPAL_DP_TRANSACTION_METHOD == '1') ? 'S' : 'A',
      'CUSTIP' => tep_get_ip_address(),
      'BUTTONSOURCE' => $OSCOM_PayPal->getIdentifier(),
    ];

    if ( !empty($extra_params) && is_array($extra_params) ) {
      $params = array_merge($params, $extra_params);
    }

    $headers = [];
    if ( isset($params['_headers']) ) {
      $headers = $params['_headers'];

      unset($params['_headers']);
    }

    $post_string = '';

    foreach ($params as $key => $value) {
      $post_string .= $key . '[' . strlen(trim($value)) . ']=' . trim($value) . '&';
    }

    $post_string = substr($post_string, 0, -strlen('&'));

    $response = $OSCOM_PayPal->makeApiCall($api_url, $post_string, $headers);
    parse_str($response, $response_array);

    return [
      'res' => $response_array,
      'success' => ($response_array['RESULT'] == '0'),
      'req' => $params,
    ];
  }
