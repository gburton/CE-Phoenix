<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  echo STORE_NAME, "\n", MODULE_NOTIFICATIONS_UPDATE_ORDER_SEPARATOR, "\n";
  printf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_ORDER_NUMBER .  "\n", $data['orders_id']);
  printf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_INVOICE_URL .  "\n",
    tep_catalog_href_link('account_history_info.php', 'order_id=' . $data['orders_id'], 'SSL'));
  printf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_DATE_ORDERED .  "\n\n",
    tep_date_long($data['date_purchased']));
  echo $notify_comments;
  printf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_STATUS_UPDATE, $data['status_name']);
?>
