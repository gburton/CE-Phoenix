<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_CATALOG ) {
      $group['apps'][] = array('code' => 'specials.php',
                               'title' => MODULES_ADMIN_MENU_CATALOG_SPECIALS,
                               'link' => tep_href_link('specials.php'));

      break;
    }
  }
