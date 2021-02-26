<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');

// needs to be included earlier to set the success message in the messageStack
  require language::map_to_translation('account_notifications.php');

  $global_query = tep_db_query("SELECT global_product_notifications FROM customers_info WHERE customers_info_id = " . (int)$_SESSION['customer_id']);
  $global = $global_query->fetch_assoc();

  if (tep_validate_form_action_is('process')) {
    if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
      $product_global = Text::input($_POST['product_global']);
    } else {
      $product_global = '0';
    }

    if ($product_global != $global['global_product_notifications']) {
      $product_global = (($global['global_product_notifications'] == '1') ? '0' : '1');

      tep_db_query("UPDATE customers_info SET global_product_notifications = '" . (int)$product_global . "' WHERE customers_info_id = " . (int)$_SESSION['customer_id']);
    } elseif (!empty($_POST['products'])) {
      $products_parsed = [];
      foreach ((array)$_POST['products'] as $value) {
        if (is_numeric($value)) {
          $products_parsed[] = $value;
        }
      }

      if (count($products_parsed) > 0) {
        $check_query = tep_db_query("SELECT COUNT(*) AS total FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id NOT IN (" . implode(',', $products_parsed) . ")");
        $check = $check_query->fetch_assoc();

        if ($check['total'] > 0) {
          tep_db_query("DELETE FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id NOT IN (" . implode(',', $products_parsed) . ")");
        }
      }
    } else {
      $check_query = tep_db_query("SELECT COUNT(*) AS total FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id']);
      $check = $check_query->fetch_assoc();

      if ($check['total'] > 0) {
        tep_db_query("DELETE FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id']);
      }
    }

    $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php'));
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
