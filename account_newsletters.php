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

  if (!$customer_data->has(['newsletter'])) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_newsletters.php";

  $customer_data->build_read(['newsletter'], 'customers', ['id' => (int)$_SESSION['customer_id']]);
  $newsletter_query = tep_db_query($customer_data->build_read(['newsletter'], 'customers', ['id' => (int)$_SESSION['customer_id']]));
  $newsletter = tep_db_fetch_array($newsletter_query);

  if (tep_validate_form_action_is('process')) {
    if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
      $newsletter_general = tep_db_prepare_input($_POST['newsletter_general']);
    } else {
      $newsletter_general = 0;
    }

    $saved_newsletter = $customer_data->get('newsletter', $newsletter);
    if ($newsletter_general != $saved_newsletter) {
      $customer_data->update(['newsletter' => (int)(('1' == $saved_newsletter) ? 0 : 1)], ['id' => (int)$_SESSION['customer_id']]);
    }

    $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
