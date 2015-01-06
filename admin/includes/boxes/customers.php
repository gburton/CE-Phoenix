<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_CUSTOMERS,
    'apps' => array(
      array(
        'code' => FILENAME_CUSTOMERS,
        'title' => BOX_CUSTOMERS_CUSTOMERS,
        'link' => tep_href_link(FILENAME_CUSTOMERS)
      ),
/* ** Altered for Manual Order Maker ** */	  
      array(
        'code' => FILENAME_CREATE_ORDER,
        'title' => BOX_CUSTOMERS_CREATE_ORDER,
        'link' => tep_href_link(FILENAME_CREATE_ORDER)
      )
/* ** EOF for Manual Order Maker ** */	  
    )
  );
?>
