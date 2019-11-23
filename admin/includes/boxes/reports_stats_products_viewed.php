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
      $group['apps'][] = array('code' => 'stats_products_viewed.php',
                               'title' => MODULES_ADMIN_MENU_REPORTS_PRODUCTS_VIEWED,
                               'link' => tep_href_link('stats_products_viewed.php'));

      break;
    }
  }
