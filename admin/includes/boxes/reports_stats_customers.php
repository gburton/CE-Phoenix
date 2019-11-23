<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

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
