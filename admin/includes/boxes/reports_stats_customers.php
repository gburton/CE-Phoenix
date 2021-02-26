<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_REPORTS ) {
      $group['apps'][] = array('code' => 'stats_customers.php',
                               'title' => MODULES_ADMIN_MENU_REPORTS_ORDERS_TOTAL,
                               'link' => tep_href_link('stats_customers.php'));

      break;
    }
  }
