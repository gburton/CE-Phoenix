<?php
/*
  $Id$

  Testimonials Manager 1.0
  by @raiwa
  Copyright (c) 2019 Rainer Schmied
  info@oscaddons.com
  www.oscaddons.com  

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_CATALOG ) {
      $group['apps'][] = array('code' => 'testimonials_manager.php',
                               'title' => MODULES_ADMIN_MENU_CATALOG_TESTIMONIALS_MANAGER,
                               'link' => tep_href_link('testimonials_manager.php'));

      break;
    }
  }
  