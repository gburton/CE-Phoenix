<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_LAYOUT ) {
      $group['apps'][] = ['code' => 'modules_pi.php',
                          'title' => MODULES_ADMIN_MENU_LAYOUT_PI,
                          'link' => tep_href_link('modules_pi.php')];

      break;
    }
  }
  