<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_TOOLS ) {
      $group['apps'][] = array('code' => 'advert_manager.php',
                               'title' => MODULES_ADMIN_MENU_TOOLS_ADVERT_MANAGER,
                               'link' => tep_href_link('advert_manager.php'));

      break;
    }
  }
  