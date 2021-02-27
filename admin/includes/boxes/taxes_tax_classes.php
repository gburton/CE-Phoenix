<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_LOCATION_AND_TAXES ) {
      $group['apps'][] = array('code' => 'tax_classes.php',
                               'title' => MODULES_ADMIN_MENU_TAXES_TAX_CLASSES,
                               'link' => tep_href_link('tax_classes.php'));

      break;
    }
  }
