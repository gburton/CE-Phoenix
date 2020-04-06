<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  include __DIR__ . '/update_stock.php';
  include __DIR__ . '/update_products_ordered.php';

  tep_notify('checkout', $GLOBALS['order']);
