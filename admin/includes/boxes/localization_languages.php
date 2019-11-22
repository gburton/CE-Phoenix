<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_LOCALIZATION ) {
      $group['apps'][] = array('code' => 'languages.php',
                               'title' => MODULES_ADMIN_MENU_LOCALIZATION_LANGUAGES,
                               'link' => tep_href_link('languages.php'));

      break;
    }
  }
