<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../../');
  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');

  if ( defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED) && in_array('sage_pay_direct.php', explode(';', MODULE_PAYMENT_INSTALLED)) ) {
    $sage_pay_direct = new sage_pay_direct();

    if ( !$sage_pay_direct->enabled ) {
      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  } else {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $sage_pay_cards = new cm_account_sage_pay_cards();

  if ( !$sage_pay_cards->isEnabled() ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  if ( isset($_GET['action']) ) {
    if (tep_validate_form_action_is('delete', 2) && is_numeric($_GET['id'] ?? '')) {
      $token_query = tep_db_query("SELECT id, sagepay_token FROM customers_sagepay_tokens WHERE id = " . (int)$_GET['id'] . " AND customers_id = " . (int)$_SESSION['customer_id']);

      if ($token = tep_db_fetch_array($token_query)) {
        $sage_pay_direct->deleteCard($token['sagepay_token'], $token['id']);

        $messageStack->add_session('cards', MODULE_CONTENT_ACCOUNT_SAGE_PAY_CARDS_SUCCESS_DELETED, 'success');
      }
    }

    tep_redirect(tep_href_link('ext/modules/content/account/sage_pay/cards.php', '', 'SSL'));
  }

  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
