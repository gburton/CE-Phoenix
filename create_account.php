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
  require language::map_to_translation('create_account.php');

  $message_stack_area = 'create_account';

  $page_fields = $customer_data->get_fields_for_page('create_account');
  $customer_details = null;
  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);

    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (tep_form_processing_is_valid()) {
      $customer_data->create($customer_details);

      $OSCOM_Hooks->call('siteWide', 'postRegistration');
    }
  }

  $grouped_modules = $customer_data->get_grouped_modules();
  $customer_data_group_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT customer_data_groups_id, customer_data_groups_name
 FROM customer_data_groups
 WHERE language_id = %d
 ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order
EOSQL
    , (int)$_SESSION['languages_id']));

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
