<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/create_account.php";

  $message_stack_area = 'create_account';

  $page_fields = $customer_data->get_fields_for_page('create_account');
  $customer_details = null;
  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);

    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (tep_form_processing_is_valid()) {
      $customer_data->create($customer_details);

      $OSCOM_Hooks->call('siteWide', 'postAccountCreation');
      $OSCOM_Hooks->call('siteWide', 'postLogin');

      if (SESSION_RECREATE == 'True') {
        tep_session_recreate();
      }

      $customer = new customer($customer_data->get('id', $customer_details));
      $_SESSION['customer_id'] = $customer->get_id();
      $customer_id =& $_SESSION['customers_id'];

      tep_reset_session_token();
      $_SESSION['cart']->restore_contents();

      tep_notify('create_account', $customer);

      tep_redirect(tep_href_link('create_account_success.php', '', 'SSL'));
    }
  }

  $grouped_modules = $customer_data->get_grouped_modules();
  $customer_data_group_query = tep_db_query(<<<'EOSQL'
SELECT customer_data_groups_id, customer_data_groups_name
 FROM customer_data_groups
 WHERE language_id = 
EOSQL
    . (int)$languages_id . ' ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order');

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
