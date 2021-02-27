<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  chdir('../../../../../');
  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');

  if ( defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED) && in_array('braintree_cc.php', explode(';', MODULE_PAYMENT_INSTALLED)) ) {
    $braintree_cc = new braintree_cc();

    if ( !$braintree_cc->enabled ) {
      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  } else {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $braintree_cards = new cm_account_braintree_cards();

  if ( !$braintree_cards->isEnabled() ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  if ( isset($_GET['action']) ) {
    if ( tep_validate_form_action_is('delete', 2) && is_numeric($_GET['id'] ?? '')) {
      $token_query = tep_db_query("SELECT id, braintree_token FROM customers_braintree_tokens WHERE id = " . (int)$_GET['id'] . " AND customers_id = " . (int)$_SESSION['customer_id']);

      if ($token = tep_db_fetch_array($token_query)) {
        $braintree_cc->deleteCard($token['braintree_token'], $token['id']);

        $messageStack->add_session('cards', MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_SUCCESS_DELETED, 'success');
      }
    }

    tep_redirect(tep_href_link('ext/modules/content/account/braintree/cards.php', '', 'SSL'));
  }

  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
