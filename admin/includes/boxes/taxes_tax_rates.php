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
      $group['apps'][] = array('code' => 'tax_rates.php',
                               'title' => MOULES_ADMIN_MENU_TAXES_TAX_RATES,
                               'link' => tep_href_link('tax_rates.php'));

      break;
    }
  }
