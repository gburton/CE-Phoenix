<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_MODULES ) {
      $group['apps'][] = array('code' => 'modules_actions.php',
                               'title' => MODULES_ADMIN_MENU_MODULES_ACTIONS,
                               'link' => tep_href_link('modules_actions.php'));

      break;
    }
  }
  