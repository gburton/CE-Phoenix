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
  $OSCOM_Hooks = new hooks('shop');
  $OSCOM_Hooks->register('system');
  foreach ($OSCOM_Hooks->generate('system', 'startApplication') as $result) {
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

// define the project version --- obsolete, now retrieved with tep_get_version()
  const PROJECT_VERSION = 'OSCOM CE Phoenix';

// set the type of request (secure or not)
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

// set the cookie domain
  $cookie_domain = (($request_type == 'NONSSL') ? HTTP_COOKIE_DOMAIN : HTTPS_COOKIE_DOMAIN);
  $cookie_path = (($request_type == 'NONSSL') ? HTTP_COOKIE_PATH : HTTPS_COOKIE_PATH);

// set php_self
  $PHP_SELF = substr(parse_url($_SERVER['SCRIPT_NAME'])['path'], strlen(DIR_WS_CATALOG));

// set the application parameters
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from configuration');
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

// if gzip_compression is enabled, start to buffer the output
  if ( (GZIP_COMPRESSION == 'true') && extension_loaded('zlib') && !headers_sent() ) {
    if (function_exists('ini_set')) {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }

    if ((int)ini_get('zlib.output_compression') < 1) {
      ob_start('ob_gzhandler');
    }
  }

// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
  if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
    if (strlen(getenv('PATH_INFO')) > 1) {
      $GET_array = [];
      $PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
      $vars = explode('/', substr(getenv('PATH_INFO'), 1));
      do_magic_quotes_gpc($vars);

      for ($i = 0, $n = count($vars); $i < $n; $i += 2) {
        if (strpos($vars[$i], '[]')) {
          $GET_array[substr($vars[$i], 0, -2)][] = $vars[$i+1];
        } else {
          $_GET[$vars[$i]] = $vars[$i+1];
        }
      }

      foreach ($GET_array as $key => $value) {
        $_GET[$key] = $value;
      }
    }
  }

// define general functions used application-wide
  require 'includes/functions/general.php';
  require 'includes/functions/html_output.php';

// define how the session functions will be used
  require 'includes/functions/sessions.php';

// set the session name and save path
  tep_session_name('ceid');
  tep_session_save_path(SESSION_WRITE_DIRECTORY);

// set the session cookie parameters
  session_set_cookie_params(0, $cookie_path, $cookie_domain);

// set the session ID if it exists
  if ( SESSION_FORCE_COOKIE_USE == 'False' ) {
    @ini_set('session.use_only_cookies', 0);

    if ( isset($_GET[session_name()]) && (($_COOKIE[session_name()] ?? null) !== $_GET[session_name()]) ) {
      tep_session_id($_GET[session_name()]);
    } elseif ( isset($_POST[session_name()]) && (($_COOKIE[session_name()] ?? null) !== $_POST[session_name()]) ) {
      tep_session_id($_POST[session_name()]);
    }
  }

// start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
    @ini_set('session.use_only_cookies', 1);

    tep_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, $cookie_path, $cookie_domain);

    if (isset($_COOKIE['cookie_test'])) {
      tep_session_start();
      $session_started = true;
    }
  } elseif (SESSION_BLOCK_SPIDERS == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;

    if (tep_not_null($user_agent)) {
      foreach (file('includes/spiders.txt') as $spider) {
        if (tep_not_null($spider)) {
          if (is_integer(strpos($user_agent, trim($spider)))) {
            $spider_flag = true;
            break;
          }
        }
      }
    }

    if ($spider_flag == false) {
      tep_session_start();
      $session_started = true;
    }
  } else {
    tep_session_start();
    $session_started = true;
  }

  if ($session_started) {
    // register session variables globally
    extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
  }

// initialize a session token
  if (!isset($_SESSION['sessiontoken'])) {
    tep_reset_session_token();
  }

// set SID once, even if empty
  $SID = (defined('SID') ? SID : '');

// verify the ssl_session_id if the feature is enabled
  if ( ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
    $ssl_session_id = getenv('SSL_SESSION_ID');
    if (!isset($_SESSION['SSL_SESSION_ID'])) {
      $_SESSION['SSL_SESSION_ID'] = $ssl_session_id;
    }

    if ($_SESSION['SSL_SESSION_ID'] != $ssl_session_id) {
      tep_session_destroy();
      tep_redirect(tep_href_link('ssl_check.php'));
    }
  }

// verify the browser user agent if the feature is enabled
  if (SESSION_CHECK_USER_AGENT == 'True') {
    $http_user_agent = getenv('HTTP_USER_AGENT');
    if (!isset($_SESSION['SESSION_USER_AGENT'])) {
      $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
    }

    if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
      tep_session_destroy();
      tep_redirect(tep_href_link('login.php'));
    }
  }

// verify the IP address if the feature is enabled
  if (SESSION_CHECK_IP_ADDRESS == 'True') {
    $ip_address = tep_get_ip_address();
    if (!isset($_SESSION['SESSION_IP_ADDRESS'])) {
      $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
    }

    if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
      tep_session_destroy();
      tep_redirect(tep_href_link('login.php'));
    }
  }

// create the shopping cart
  if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof shoppingCart)) {
    $_SESSION['cart'] = new shoppingCart();
    $cart =& $_SESSION['cart'];
  }

// include currencies class and create an instance
  $currencies = new currencies();

// set the language
  if (!isset($_SESSION['language']) || isset($_GET['language'])) {
    $lng = new language();

    if (isset($_GET['language']) && tep_not_null($_GET['language'])) {
      $lng->set_language($_GET['language']);
    } else {
      $lng->get_browser_language();
    }

    $language = $lng->language['directory'];
    $languages_id = $lng->language['id'];

    if (!isset($_SESSION['language'])) {
      tep_session_register('language');
      tep_session_register('languages_id');
    }
  }

// include the language translations
  $_system_locale_numeric = setlocale(LC_NUMERIC, 0);
  require "includes/languages/$language.php";
  setlocale(LC_NUMERIC, $_system_locale_numeric); // Prevent LC_ALL from setting LC_NUMERIC to a locale with 1,0 float/decimal values instead of 1.0 (see bug #634)

// currency
  if (!isset($_SESSION['currency']) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $currency) ) ) {
    if (isset($_GET['currency']) && $currencies->is_set($_GET['currency'])) {
      $currency = $_GET['currency'];
    } else {
      $currency = ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && $currencies->is_set(LANGUAGE_CURRENCY)) ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
    }

    if (!isset($_SESSION['currency'])) tep_session_register('currency');
  }

// navigation history
  if (!isset($_SESSION['navigation']) || !($_SESSION['navigation'] instanceof navigationHistory)) {
    $_SESSION['navigation'] = new navigationHistory();
    $navigation = &$_SESSION['navigation'];
  }
  $_SESSION['navigation']->add_current_page();

// initialize the message stack for output messages
  $messageStack = new messageStack();

  $customer_data = new customer_data();
  if (isset($_SESSION['customer_id'])) {
    $customer = new customer($customer_id);
  }

// Shopping cart actions
  if (isset($_GET['action'])) {
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if (!$session_started) {
      tep_redirect(tep_href_link('cookie_usage.php'));
    }

    if (DISPLAY_CART == 'true') {
      $goto = 'shopping_cart.php';
      $parameters = ['action', 'cPath', 'products_id', 'pid'];
    } else {
      $goto = $PHP_SELF;
      if ($_GET['action'] == 'buy_now') {
        $parameters = ['action', 'pid', 'products_id'];
      } else {
        $parameters = ['action', 'pid'];
      }
    }

    include 'includes/classes/actions.php';
    osC_Actions::parse($_GET['action']);
  }

// include the who's online functions
  require 'includes/functions/whos_online.php';
  tep_update_whos_online();

// include the password crypto functions
  require 'includes/functions/password_funcs.php';

// include validation functions (right now only email address)
  require 'includes/functions/validations.php';

// auto expire special products
  require 'includes/functions/specials.php';
  tep_expire_specials();

  $oscTemplate = new oscTemplate();

// calculate category path
  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
    $cPath = tep_get_product_path($_GET['products_id']);
  } else {
    $cPath = '';
  }

  if (tep_not_null($cPath)) {
    $cPath_array = tep_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = end($cPath_array);

    $OSCOM_category = new category_tree();
  } else {
    $current_category_id = 0;
  }

// start the breadcrumb trail
  $breadcrumb = new breadcrumb();

  $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
  $breadcrumb->add(HEADER_TITLE_CATALOG, tep_href_link('index.php'));

// add category names or the manufacturer name to the breadcrumb trail
  if (isset($cPath_array)) {
    foreach ($cPath_array as $k => $v) {
      $breadcrumb_category = $OSCOM_category->getData($v, 'name');

      if ( defined('MODULE_HEADER_TAGS_CATEGORY_TITLE_SEO_BREADCRUMB_OVERRIDE') && (MODULE_HEADER_TAGS_CATEGORY_TITLE_SEO_BREADCRUMB_OVERRIDE == 'True') ) {
        if (tep_not_null($OSCOM_category->getData($v, 'seo_title'))) {
          $breadcrumb_category = $OSCOM_category->getData($v, 'seo_title');
        }
      }

      $breadcrumb->add($breadcrumb_category, tep_href_link('index.php', 'cPath=' . implode('_', array_slice($cPath_array, 0, ($k+1)))));
    }
  } elseif (isset($_GET['manufacturers_id'])) {
    $brand = new manufacturer((int)$_GET['manufacturers_id']);

    $breadcrumb_brand = $brand->getData('manufacturers_name');

    if ( defined('MODULE_HEADER_TAGS_MANUFACTURER_TITLE_SEO_BREADCRUMB_OVERRIDE') && (MODULE_HEADER_TAGS_MANUFACTURER_TITLE_SEO_BREADCRUMB_OVERRIDE == 'True') ) {
      if (tep_not_null($brand->getData('manufacturers_seo_title'))) {
        $breadcrumb_brand = $brand->getData('manufacturers_seo_title');
      }
    }

    $breadcrumb->add($breadcrumb_brand, tep_href_link('index.php', 'manufacturers_id=' . (int)$_GET['manufacturers_id']));
  }

// add the products model to the breadcrumb trail
  if (isset($_GET['products_id'])) {
    if ( defined('MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_BREADCRUMB_OVERRIDE') && (MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_BREADCRUMB_OVERRIDE == 'True') ) {
      $model_query = tep_db_query("SELECT COALESCE(NULLIF(pd.products_seo_title, ''), NULLIF(p.products_model, ''), pd.products_name) AS products_model FROM products p, products_description pd WHERE p.products_id = " . (int)$_GET['products_id'] . " AND p.products_id = pd.products_id AND pd.language_id = " . (int)$languages_id);
    } else {
      $model_query = tep_db_query("SELECT COALESCE(NULLIF(p.products_model, ''), pd.products_name) AS products_model FROM products p, products_description pd WHERE p.products_id = " . (int)$_GET['products_id'] . " AND p.products_id = pd.products_id AND pd.language_id = " . (int)$languages_id);
    }

    if ($model = tep_db_fetch_array($model_query)) {
      $breadcrumb->add($model['products_model'], tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
    }
  }

  $OSCOM_Hooks->register_page();
  $OSCOM_Hooks->call('siteWide', 'injectAppTop');

