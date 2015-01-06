<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_CUSTOMERS ) {
      $group['apps'][] = array('code' => 'mailbeez.php',
                               'title' => 'MailBeez',
                               'link' => tep_href_link('mailbeez.php'));

      break;
    }
  }
?>
