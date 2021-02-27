<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  include(DIR_FS_CATALOG . 'includes/apps/paypal/admin/functions/boxes.php');

  $cl_box_groups[] = array('heading' => MODULES_ADMIN_MENU_PAYPAL_HEADING,
                           'apps' => app_paypal_get_admin_box_links());
                           