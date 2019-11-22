<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_LOCATION_AND_TAXES ) {
      $group['apps'][] = array('code' => 'countries.php',
                               'title' => MODULES_ADMIN_MENU_TAXES_COUNTRIES,
                               'link' => tep_href_link('countries.php'));

      break;
    }
  }
