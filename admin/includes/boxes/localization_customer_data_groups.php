<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_LOCALIZATION ) {
      $group['apps'][] = [
        'code' => 'customer_data_groups.php',
        'title' => BOX_LOCALIZATION_CUSTOMER_DATA_GROUPS,
        'link' => tep_href_link('customer_data_groups.php'),
      ];

      break;
    }
  }
