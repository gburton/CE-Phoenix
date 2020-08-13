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

// load server configuration parameters
  include 'includes/configure.php';

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
