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

  if (!is_numeric($_GET['order_id'] ?? null)) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  $customer_info_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT o.customers_id
 FROM orders o INNER JOIN orders_status s ON o.orders_status = s.orders_status_id
 WHERE s.public_flag = 1 AND o.orders_id = %d AND s.language_id = %d
EOSQL
    , (int)$_GET['order_id'], (int)$_SESSION['languages_id']));
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $_SESSION['customer_id']) {
    tep_redirect(tep_href_link('account_history.php', '', 'SSL'));
  }

  require "includes/languages/$language/account_history_info.php";

  $order = new order($_GET['order_id']);

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
