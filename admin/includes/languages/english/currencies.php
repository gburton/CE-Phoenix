<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Currencies');

define('TABLE_HEADING_CURRENCY_NAME', 'Currency');
define('TABLE_HEADING_CURRENCY_CODES', 'Code');
define('TABLE_HEADING_CURRENCY_VALUE', 'Value');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_INFO_COMMON_CURRENCIES', '-- Common Currencies --');
define('TEXT_INFO_CURRENCY_TITLE', 'Title: %s');
define('TEXT_INFO_CURRENCY_CODE', 'Code: %s');
define('TEXT_INFO_CURRENCY_SYMBOL_LEFT', 'Symbol Left: %s');
define('TEXT_INFO_CURRENCY_SYMBOL_RIGHT', 'Symbol Right: %s');
define('TEXT_INFO_CURRENCY_DECIMAL_POINT', 'Decimal Point: %s');
define('TEXT_INFO_CURRENCY_THOUSANDS_POINT', 'Thousands Point: %s');
define('TEXT_INFO_CURRENCY_DECIMAL_PLACES', 'Decimal Places: %s');
define('TEXT_INFO_CURRENCY_LAST_UPDATED', 'Last Updated: %s');
define('TEXT_INFO_CURRENCY_VALUE', 'Value: %s');
define('TEXT_INFO_CURRENCY_EXAMPLE', 'Example Output: %s =  %s');

define('TEXT_INFO_INSERT_INTRO', 'Please enter the new currency with its related data');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this currency?');
define('TEXT_INFO_HEADING_NEW_CURRENCY', 'New Currency');
define('TEXT_INFO_HEADING_EDIT_CURRENCY', 'Edit Currency');
define('TEXT_INFO_HEADING_DELETE_CURRENCY', 'Delete Currency');
define('TEXT_INFO_SET_AS_DEFAULT', TEXT_SET_DEFAULT . ' (requires a manual update of currency values)');
define('TEXT_INFO_CURRENCY_UPDATED', 'The exchange rate for %s (%s) was updated successfully via %s.');

define('ERROR_REMOVE_DEFAULT_CURRENCY', '<strong>Error:</strong> The default currency can not be removed. Please set another currency as default, and try again.');
define('ERROR_CURRENCY_INVALID', '<strong>Error:</strong> The exchange rate for %s (%s) was not updated via %s. Is it a valid currency code?');
define('WARNING_PRIMARY_SERVER_FAILED', '<strong>Warning:</strong> The primary exchange rate server (%s) failed for %s (%s) - trying the secondary exchange rate server.');

define('ERROR_INSTALL_CURRENCY_CONVERTER', 'You do not have a Currency Conversion module installed.  <a class="alert-link font-weight-bold" href="' . tep_href_link('modules.php', 'set=currencies') . '">Install Now</a>');
