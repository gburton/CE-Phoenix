<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());


// load server configuration parameters
  include 'includes/configure.php';

  // autoload classes in the classes or modules directories
  require DIR_FS_CATALOG . 'includes/functions/autoloader.php';
  require 'includes/functions/autoloader.php';
  spl_autoload_register('tep_autoload_admin');
  spl_autoload_register('tep_autoload_catalog');

// include the database functions
  require 'includes/functions/database.php';

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

  $OSCOM_Hooks = new hooks('admin');
  $OSCOM_Hooks->register('system');
  $OSCOM_Hooks->generate('system', 'startApplication');

  // Define the project version --- obsolete, now retrieved with tep_get_version()
  define('PROJECT_VERSION', 'CE Phoenix');

  // set the type of request (secure or not)
  $request_type = (getenv('HTTPS') === 'on') ? 'SSL' : 'NONSSL';

  // set php_self in the local scope
  $req = parse_url($_SERVER['SCRIPT_NAME']);
  $PHP_SELF = substr($req['path'], strlen(DIR_WS_ADMIN));

// set application wide parameters
  $configuration_query = tep_db_query('SELECT configuration_key, configuration_value FROM configuration');
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['configuration_key'], $configuration['configuration_value']);
  }

// define our general functions used application-wide
  require 'includes/functions/general.php';
  require 'includes/functions/html_output.php';

// define how the session functions will be used
  require 'includes/functions/sessions.php';

// set the session name
  session_name('osCAdminID');

// set the session cookie parameters
  Cookie::save_session_parameters();

  @ini_set('session.use_only_cookies', (SESSION_FORCE_COOKIE_USE == 'True') ? 1 : 0);

// let's start our session
  tep_session_start();

// set the language
  if (!isset($_SESSION['language']) || isset($_GET['language'])) {
    $lng = language::build();
  }

// register session variables globally
  extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);

// redirect to login page if administrator is not yet logged in
  if (!isset($_SESSION['admin'])) {
    $current_page = $PHP_SELF;

// if the first page request is to the login page, set the current page to the index page
// so the redirection on a successful login is not made to the login page again
    if ( ('login.php' === $current_page) && !isset($_SESSION['redirect_origin']) ) {
      $current_page = 'index.php';
      $_GET = [];
    }

    $redirect = false;
    if ('login.php' !== $current_page) {
      if (!isset($_SESSION['redirect_origin'])) {
        $_SESSION['redirect_origin'] = [
          'page' => $current_page,
          'get' => $_GET,
        ];

        $redirect_origin =& $_SESSION['redirect_origin'];
      }

// try to automatically log in with the HTTP Authentication values if it exists
      if (!isset($_SESSION['auth_ignore'])) {
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
          $redirect_origin['auth_user'] = $_SERVER['PHP_AUTH_USER'];
          $redirect_origin['auth_pw'] = $_SERVER['PHP_AUTH_PW'];
        }
      }

      $redirect = true;
    }

    if ($redirect || !isset($login_request) || isset($_GET['login_request']) || isset($_POST['login_request']) || isset($_COOKIE['login_request']) || isset($_SESSION['login_request']) || isset($_FILES['login_request']) || isset($_SERVER['login_request'])) {
      tep_redirect(tep_href_link('login.php', (isset($redirect_origin['auth_user']) ? 'action=process' : '')));
    }

    unset($redirect);
  }

// include the language translations
  $_system_locale_numeric = setlocale(LC_NUMERIC, 0);
  if (!file_exists("includes/languages/$language.php")) {
    $language = 'english';
  }
  require "includes/languages/$language.php";
  setlocale(LC_NUMERIC, $_system_locale_numeric); // Prevent LC_ALL from setting LC_NUMERIC to a locale with 1,0 float/decimal values instead of 1.0 (see bug #634)

  $current_page = basename($PHP_SELF);
  $current_page_language_file = "includes/languages/$language/$current_page";
  if (file_exists($current_page_language_file)) {
    include $current_page_language_file;
  }

// Include validation functions (right now only email address)
  require 'includes/functions/validations.php';

// initialize the message stack for output messages
  $messageStack = new messageStack();

  $customer_data = new customer_data();

// initialize configuration modules
  $cfgModules = new cfg_modules();


  $OSCOM_Hooks->register_page();
