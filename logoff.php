<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/logoff.php";

  unset($_SESSION['customer_id']);

  unset($_SESSION['sendto']);
  unset($_SESSION['billto']);
  unset($_SESSION['shipping']);
  unset($_SESSION['payment']);
  unset($_SESSION['comments']);

  $_SESSION['cart']->reset();

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
