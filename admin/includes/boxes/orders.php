<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_ORDERS,
    'apps' => array(
      array(
        'code' => FILENAME_ORDERS,
        'title' => BOX_ORDERS_ORDERS,
        'link' => tep_href_link(FILENAME_ORDERS)
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
