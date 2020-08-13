<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

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
  const HTTP_SERVER = ''; // eg, http://localhost - should not be empty for productive servers
  const COOKIE_OPTIONS = [
    'lifetime' => 0,
    'domain' => '',
    'path' => '',
    'samesite' => 'Lax',
  ];
  const DIR_WS_CATALOG = '';

  define('DIR_FS_CATALOG', dirname($_SERVER['SCRIPT_FILENAME']) . '/');

  // leave blank or omit to use MySQL sessions
  const DIR_FS_SESSION = '';

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  date_default_timezone_set(date_default_timezone_get());

// If you are asked to provide configure.php details
// please remove the data below before sharing

// define our database connection
  const DB_SERVER = ''; // eg, localhost - should not be empty for productive servers
  const DB_SERVER_USERNAME = '';
  const DB_SERVER_PASSWORD = '';
  const DB_DATABASE = 'osCommerce';

  if (DB_SERVER == '' && is_dir('install')) {
    header('Location: install/index.php');
    exit();
  }
