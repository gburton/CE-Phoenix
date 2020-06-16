<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// load server configuration parameters
  if (file_exists('includes/local/configure.php')) { // for developers
    include 'includes/local/configure.php';
  } else {
    include 'includes/configure.php';
  }

  if (DB_SERVER == '' && is_dir('install')) {
    header('Location: install/index.php');
    exit;
  }

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  date_default_timezone_set(defined('CFG_TIME_ZONE') ? CFG_TIME_ZONE : date_default_timezone_get());

// autoload classes in the classes or modules directories
  require 'includes/functions/autoloader.php';
  spl_autoload_register('tep_autoload_catalog');

// include the database functions
  require 'includes/functions/database.php';

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

  // hooks
  $hooks = new hooks('shop');
  $OSCOM_Hooks =& $hooks;
  $hooks->register('system');
  foreach ($hooks->generate('system', 'startApplication') as $result) {
    if (!isset($result)) {
      continue;
    }

    if (is_string($result)) {
      $result = [ $result ];
    }

    if (is_array($result)) {
      foreach ($result as $path) {
        if (is_string($path ?? null) && file_exists($path)) {
          require $path;
        }
      }
    }
  }
