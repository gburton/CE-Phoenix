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

  if (!isset($_SESSION['customer_id'])) {
    tep_redirect(tep_href_link('login.php'));
  }

  if ( MODULE_CONTENT_ACCOUNT_SET_PASSWORD_ALLOW_PASSWORD != 'True' ) {
    tep_redirect(tep_href_link('account.php'));
  }

  if (!$customer_data->has(['password'])) {
    tep_redirect(tep_href_link('account.php'));
  }

  $check_customer_query = tep_db_query($customer_data->build_read(['password'], 'both', ['id' => (int)$_SESSION['customer_id']]));
  $check_customer = $check_customer_query->fetch_assoc();

  // only allow to set the password when it is blank
  if ( !empty($customer_data->get('password', $check_customer)) ) {
    tep_redirect(tep_href_link('account.php'));
  }

// needs to be included earlier to set the success message in the messageStack
  require language::map_to_translation('modules/content/account/cm_account_set_password.php');

  $page_fields = ['password', 'password_confirmation'];

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);

    if (tep_form_processing_is_valid()) {
      $customer_data->update(['password' => $customer_data->get('password', $customer_details)], ['id' => (int)$_SESSION['customer_id']]);

      tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$_SESSION['customer_id']);

      $messageStack->add_session('account', MODULE_CONTENT_ACCOUNT_SET_PASSWORD_SUCCESS_PASSWORD_SET, 'success');

      tep_redirect(tep_href_link('account.php'));
    }
  }

  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
