<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/contact_us.php";

  if (tep_validate_form_action_is('send')) {
    $error = false;

    $name = tep_db_prepare_input($_POST['name']);
    $email_address = tep_db_prepare_input($_POST['email']);
    $enquiry = tep_db_prepare_input($_POST['enquiry']);

    if (!tep_validate_email($email_address)) {
      tep_block_form_processing();

      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }
    
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    $actionRecorder = new actionRecorder('ar_contact_us', ($_SESSION['customer_id'] ?? null), $name);
    if (!$actionRecorder->canPerform()) {
      tep_block_form_processing();

      $actionRecorder->record(false);

      $messageStack->add('contact', sprintf(ERROR_ACTION_RECORDER, (defined('MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES : 15)));
    }

    if (tep_form_processing_is_valid()) {
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sprintf(EMAIL_SUBJECT, STORE_NAME), $enquiry, $name, $email_address);

      $actionRecorder->record();

      tep_redirect(tep_href_link('contact_us.php', 'action=success'));
    }
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
