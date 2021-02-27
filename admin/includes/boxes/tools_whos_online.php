<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_TOOLS ) {
      $group['apps'][] = array('code' => 'whos_online.php',
                               'title' => MODULES_ADMIN_MENU_TOOLS_WHOS_ONLINE,
                               'link' => tep_href_link('whos_online.php'));

      break;
    }
  }
