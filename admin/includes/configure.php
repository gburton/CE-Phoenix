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

// Set the level of error reporting
  error_reporting(E_ALL);

// define our webserver variables
// FS = Filesystem (physical)
// WS = Webserver (virtual)
  define('HTTP_SERVER', ''); // eg, http://localhost or - https://localhost should not be NULL for production servers
  define('COOKIE_OPTIONS', [
    'lifetime' => 0,
    'domain' => '',
    'path' => '',
    'samesite' => 'Lax',
  ]);

  define('DIR_FS_DOCUMENT_ROOT', $DOCUMENT_ROOT); // where your pages are located on the server. if $DOCUMENT_ROOT doesn't suit you, replace with your local path. (eg, /usr/local/apache/htdocs)
  define('DIR_WS_ADMIN', '/admin/');
  define('DIR_FS_ADMIN', DIR_FS_DOCUMENT_ROOT . DIR_WS_ADMIN);
  define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');

  // leave blank or omit to use MySQL sessions
  define('DIR_FS_SESSION', '');

  define('HTTP_CATALOG_SERVER', '');
  define('DIR_WS_CATALOG', '/catalog/');
  define('DIR_FS_CATALOG', DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG);

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  date_default_timezone_set(date_default_timezone_get());

// define our database connection
  define('DB_SERVER', '');
  define('DB_SERVER_USERNAME', 'mysql');
  define('DB_SERVER_PASSWORD', '');
  define('DB_DATABASE', 'Phoenix');
