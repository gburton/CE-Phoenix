<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/password_forgotten.php";

  if (!$customer_data->has(['email_address'])) {
    tep_redirect(tep_href_link('index.php', '', 'SSL'));
  }

  $password_reset_initiated = false;

  if (tep_validate_form_action_is('process')) {
    $email_address = tep_db_prepare_input($_POST['email_address']);

    $check_customer_query = tep_db_query($customer_data->build_read(['name', 'id'], 'customers', ['email_address' => $email_address]));
    if ($check_customer = tep_db_fetch_array($check_customer_query)) {
      $actionRecorder = new actionRecorder('ar_reset_password', $customer_data->get('id', $check_customer), $email_address);

      if ($actionRecorder->canPerform()) {
        $actionRecorder->record();

        $reset_key = tep_create_random_value(40);

        tep_db_query("UPDATE customers_info SET password_reset_key = '" . tep_db_input($reset_key) . "', password_reset_date = NOW() WHERE customers_info_id = " . (int)$check_customer['id']);

        $reset_key_url = tep_href_link('password_reset.php', 'account=' . urlencode($email_address) . '&key=' . $reset_key, 'SSL', false);

        if ( strpos($reset_key_url, '&amp;') !== false ) {
          $reset_key_url = str_replace('&amp;', '&', $reset_key_url);
        }

        tep_mail($customer_data->get('name', $check_customer), $email_address, EMAIL_PASSWORD_RESET_SUBJECT, sprintf(EMAIL_PASSWORD_RESET_BODY, $reset_key_url), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        $password_reset_initiated = true;
      } else {
        $actionRecorder->record(false);

        $messageStack->add('password_forgotten', sprintf(ERROR_ACTION_RECORDER, $ar_reset_password->minutes));
      }
    } else {
      $messageStack->add('password_forgotten', TEXT_NO_EMAIL_ADDRESS_FOUND);
    }
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
