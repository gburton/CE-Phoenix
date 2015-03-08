<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_TOOLS ) {
      $group['apps'][] = array('code' => 'email_previews.php',
                               'title' => MODULES_ADMIN_MENU_TOOLS_EMAIL_PREVIEW,
                               'link' => tep_href_link('email_previews.php'));

      break;
    }
  }
?>
