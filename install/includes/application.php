<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

// Set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);

  if (defined('E_DEPRECATED')) {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
  }

// set default timezone if none exists (PHP throws an E_WARNING)
  if (strlen(ini_get('date.timezone')) < 1) {
    date_default_timezone_set(@date_default_timezone_get());
  }

  require('includes/functions/general.php');
  require('includes/functions/database.php');
  require('includes/functions/html_output.php');
?>
