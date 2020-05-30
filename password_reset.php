<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// no reason to be on this page if the requirements not installed
  if (!$customer_data->has(['email_address', 'password', 'password_reset_key', 'password_reset_date'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require "includes/languages/$language/password_reset.php";

  $page_fields = [ 'password', 'password_confirmation' ];

  $error = false;

  if (isset($_GET['account']) && isset($_GET['key'])) {
    $email_address = tep_db_prepare_input($_GET['account']);
    $password_key = tep_db_prepare_input($_GET['key']);

    if ( (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) || !tep_validate_email($email_address) ) {
      $error = true;

      $messageStack->add_session('password_forgotten', TEXT_NO_EMAIL_ADDRESS_FOUND);
    } elseif (strlen($password_key) != 40) {
      $error = true;

      $messageStack->add_session('password_forgotten', TEXT_NO_RESET_LINK_FOUND);
    } else {
      $check_customer_query = tep_db_query($customer_data->build_read(['id', 'email_address', 'password_reset_key', 'password_reset_date'], 'customers', ['email_address' => $email_address]));
      if ($check_customer = tep_db_fetch_array($check_customer_query)) {
        if ( empty($check_customer['password_reset_key']) || ($check_customer['password_reset_key'] != $password_key) || (strtotime($check_customer['password_reset_date'] . ' +1 day') <= time()) ) {
          $error = true;

          $messageStack->add_session('password_forgotten', TEXT_NO_RESET_LINK_FOUND);
        }
      } else {
        $error = true;

        $messageStack->add_session('password_forgotten', TEXT_NO_EMAIL_ADDRESS_FOUND);
      }
    }
  } else {
    $error = true;

    $messageStack->add_session('password_forgotten', TEXT_NO_RESET_LINK_FOUND);
  }

  if ($error) {
    tep_redirect(tep_href_link('password_forgotten.php'));
  }

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (tep_form_processing_is_valid()) {
      $customer_data->update(['password' => $customer_data->get('password', $customer_details)], ['id' => (int)$customer_data->get('id', $check_customer)]);

      tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW(), password_reset_key = NULL, password_reset_date = NULL WHERE customers_info_id = " . (int)$check_customer['customers_id']);

      $messageStack->add_session('login', SUCCESS_PASSWORD_RESET, 'success');

      tep_redirect(tep_href_link('login.php', '', 'SSL'));
    }
  }
  
  require $oscTemplate->map_to_template(__FILE__, 'page');
  require 'includes/application_bottom.php';
