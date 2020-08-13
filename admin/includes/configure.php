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

// Set the level of error reporting
  error_reporting(E_ALL);

// define our webserver variables
// FS = Filesystem (physical)
// WS = Webserver (virtual)
  const HTTP_SERVER = ''; // eg, http://localhost or - https://localhost should not be NULL for production servers
  const COOKIE_OPTIONS = [
    'lifetime' => 0,
    'domain' => '',
    'path' => '',
    'samesite' => 'Lax',
  ];

  define('DIR_FS_DOCUMENT_ROOT', $DOCUMENT_ROOT); // where your pages are located on the server. if $DOCUMENT_ROOT doesn't suit you, replace with your local path. (eg, /usr/local/apache/htdocs)
  const DIR_WS_ADMIN = '/admin/';
  const DIR_FS_ADMIN = DIR_FS_DOCUMENT_ROOT . DIR_WS_ADMIN;
  const DIR_FS_BACKUP = DIR_FS_ADMIN . 'backups/';

  // leave blank or omit to use MySQL sessions
  const DIR_FS_SESSION = '';

  const HTTP_CATALOG_SERVER = '';
  const DIR_WS_CATALOG = '/catalog/';
  const DIR_FS_CATALOG = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG;

// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  date_default_timezone_set(date_default_timezone_get());

// define our database connection
  const DB_SERVER = '';
  const DB_SERVER_USERNAME = 'mysql';
  const DB_SERVER_PASSWORD = '';
  const DB_DATABASE = 'osCommerce';
