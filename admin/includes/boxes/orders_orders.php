<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_ORDERS ) {
      $group['apps'][] = array('code' => 'orders.php',
                               'title' => MODULES_ADMIN_MENU_ORDERS_ORDERS,
                               'link' => tep_href_link('orders.php'));

      break;
    }
  }
