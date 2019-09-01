<?php
/*
  $Id: qtprodoctor.php
  $Loc: catalog/admin/includes/languages/english/
      
  2017 QTPro 5.0 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'QTPro Doctor');

define('PAGE_HEADING', 'QTPro Doctor - Overview');

define('TEXT_EXAMINE_HEALTHY', '<span style="color: green;"><b>Product is healthy</b><br> The database entries for this products stock as they should.</span>');
define('TEXT_EXAMINE_MESSED', '<span style="color: red;"><b>Product is sick</b><br> The database entries for this products stock is messed up. This is why the table above looks messed up.</span>');
define('TEXT_AMPUTATE', '%s database entries where amputated.');
define('TEXT_CHUCK_TRASH', '%s database entries where identified as trash and deleted.');
define('TEXT_UPDATE_SUMMARY', 'The summary stock for the product was updated.');

define('QTPRO_OPTIONS_WARNING', '<strong>QT Pro Product Info Content Module</strong> is not installed. It is required.');
define('QTPRO_OPTIONS_INSTALL_NOW', '<u>Install Now QT Pro Product Info Module</u>');
define('QTPRO_HT_WARNING', '<strong>QT Pro Header Tag Module</strong> is not installed or not enabled. It is required.');
define('QTPRO_HT_INSTALL_NOW', '<u>Install Now Header Tag Module</u>');

define('TEXT_PRODUCT_COUNT', 'You currently have <b>%s</b> products in your store.<br>');
define('TEXT_PRODUCT_TRACKED_STOCK', '<b>%s</b> of them have options with tracked stock.<br>');
define('TEXT_PRODUCT_TRASH_ROWS', 'In the database we currently have <b>%s</b> trash rows.<br>');
define('TEXT_PRODUCT_SICK', '<b>%s</b> of the producks with tracked stock is sick.<br>');

define('WARNING_SICK_PRODUCTS', 'Sick products in the database:');
define('WARNING_PRODUCT_ID', 'Product with ID ');
define('WARNING_PRODUCT_DATABASE_ENTRY_SUMMARY', 'The database entries for this products stock is messy and the summary stock calculation is wrong. Please take a look at this ');
define('WARNING_PRODUCTS_STOCK', 'products stock');
define('WARNING_PRODUCT_SUMMARY_STOCK', 'The summary stock calculation is wrong. Please take a look at this ');
define('WARNING_PRODUCT_DATABASE_ENTRY', 'The database entries for this products stock is messy. Please take a look at this ');
define('WARNING_PRODUCT_OK', 'This product is all ok.');
