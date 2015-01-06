<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_GV_ADMIN,
    'apps' => array(
      array(
        'code' => FILENAME_COUPON_ADMIN,
        'title' => BOX_COUPON_ADMIN,
        'link' => tep_href_link(FILENAME_COUPON_ADMIN)
      ),
      array(
        'code' => FILENAME_GV_QUEUE,
        'title' => BOX_GV_ADMIN_QUEUE,
        'link' => tep_href_link(FILENAME_GV_QUEUE)
      ),
      array(
        'code' => FILENAME_GV_MAIL,
        'title' => BOX_GV_ADMIN_MAIL,
        'link' => tep_href_link(FILENAME_GV_MAIL)
      ),
      array(
        'code' => FILENAME_GV_SENT,
        'title' => BOX_GV_ADMIN_SENT,
        'link' => tep_href_link(FILENAME_GV_SENT)
      )
    )
  );
?>
