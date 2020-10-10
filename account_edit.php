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

  $message_stack_area = 'account_edit';
// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_edit.php";

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('account_edit'));
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (tep_form_processing_is_valid()) {
      $customer_data->update($customer_details, ['id' => $customer->get_id()], 'customers');
      tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$customer->get_id());

      $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
