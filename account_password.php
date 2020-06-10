<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');

  // if password is not enabled, then no reason to be on this page
  if (!$customer_data->has(['password'])) {
    tep_redirect(tep_href_link('index.php'));
  }

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_password.php";

  $page_fields = [ 'password', 'password_confirmation' ];
  $message_stack_area = 'account_password';

  if (tep_validate_form_action_is('process')) {
    $password_current = tep_db_prepare_input($_POST['password_current']);

    $customer_details = $customer_data->process($page_fields);

    if (tep_form_processing_is_valid()) {
      $check_customer_query = tep_db_query($customer_data->build_read(['password'], 'customers', ['id' => (int)$_SESSION['customer_id']]));
      $check_customer = tep_db_fetch_array($check_customer_query);

      if (tep_validate_password($password_current, $customer_data->get('password', $check_customer))) {
        $customer_data->update(['password' => $customer_data->get('password', $customer_details)], ['id' => (int)$_SESSION['customer_id']]);

        tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$_SESSION['customer_id']);

        $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

        tep_redirect(tep_href_link('account.php', '', 'SSL'));
      } else {
        $messageStack->add($message_stack_area, ERROR_CURRENT_PASSWORD_NOT_MATCHING);
      }
    }
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
