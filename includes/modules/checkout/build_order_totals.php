<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  $order_totals = [];
  if (!is_array($GLOBALS['order_total_modules']->modules)) {
    return;
  }

  foreach ($GLOBALS['order_total_modules']->modules as $value) {
    $class = pathinfo($value, PATHINFO_FILENAME);
    if (!$GLOBALS[$class]->enabled) {
      continue;
    }

    foreach ($GLOBALS[$class]->output as $order_total) {
      if (tep_not_null($order_total['title']) && tep_not_null($order_total['text'])) {
        $order_totals[] = [
          'code' => $GLOBALS[$class]->code,
          'title' => $order_total['title'],
          'text' => $order_total['text'],
          'value' => $order_total['value'],
          'sort_order' => $GLOBALS[$class]->sort_order,
        ];
      }
    }
  }
