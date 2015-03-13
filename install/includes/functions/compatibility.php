<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

  if (PHP_VERSION >= 4.1) {
    $_GET =& $_GET;
    $_POST =& $_POST;
    $_COOKIE =& $_COOKIE;
    $_SESSION =& $_SESSION;
    $_SERVER =& $_SERVER;
  } else {
    if (!is_array($_GET)) $_GET = array();
    if (!is_array($_POST)) $_POST = array();
    if (!is_array($_COOKIE)) $_COOKIE = array();
  }

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  if ((strlen(ini_get('date.timezone')) < 1) && function_exists('date_default_timezone_set')) {
    date_default_timezone_set(@date_default_timezone_get());
  }
?>
