<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_REPORTS ) {
      $group['apps'][] = array('code' => 'stats_low_stock_attrib.php',
                               'title' => MODULES_ADMIN_MENU_REPORTS_STATS_LOW_STOCK_ATTRIB,
                               'link' => tep_href_link('stats_low_stock_attrib.php'));
      break;
    }
  }
  