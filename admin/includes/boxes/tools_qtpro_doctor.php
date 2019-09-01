<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_TOOLS ) {
      $group['apps'][] = array('code' => 'qtprodoctor.php',
                               'title' => MODULES_ADMIN_MENU_TOOLS_QTPRODOCTOR,
                               'link' => tep_href_link('qtprodoctor.php'));
      break;
    }
  }
  