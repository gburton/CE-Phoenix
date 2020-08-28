<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  $sql_data = [
    'orders_id' => $order->get_id(),
    'orders_status_id' => $order->info['order_status'],
    'date_added' => 'NOW()',
    'customer_notified' => $GLOBALS['customer_notification'],
    'comments' => $order->info['comments'],
  ];
  tep_db_perform('orders_status_history', $sql_data);
