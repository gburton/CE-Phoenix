<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_CUSTOMERS ) {
      $group['apps'][] = array('code' => 'customers.php',
                               'title' => MODULES_ADMIN_MENU_CUSTOMERS_CUSTOMERS,
                               'link' => tep_href_link('customers.php'));

      break;
    }
  }
