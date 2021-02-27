<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

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
