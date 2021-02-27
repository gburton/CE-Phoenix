<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  if (file_exists('includes/local/configure.php')) { // for developers
    include 'includes/local/configure.php';
    return;
  }

// set the level of error reporting
  error_reporting(E_ALL);

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
  define('HTTP_SERVER', ''); // eg, http://localhost - should not be empty for productive servers
  define('COOKIE_OPTIONS', [
    'lifetime' => 0,
    'domain' => '',
    'path' => '',
    'samesite' => 'Lax',
  ]);
  define('DIR_WS_CATALOG', '');

  define('DIR_FS_CATALOG', dirname($_SERVER['SCRIPT_FILENAME']) . '/');

  // leave blank or omit to use MySQL sessions
  define('DIR_FS_SESSION', '');

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  date_default_timezone_set(date_default_timezone_get());

// If you are asked to provide configure.php details
// please remove the data below before sharing

// define our database connection
  define('DB_SERVER', ''); // eg, localhost - should not be empty for production servers
  define('DB_SERVER_USERNAME', '');
  define('DB_SERVER_PASSWORD', '');
  define('DB_DATABASE', 'Phoenix');

  if (DB_SERVER == '' && is_dir('install')) {
    header('Location: install/index.php');
    exit();
  }
