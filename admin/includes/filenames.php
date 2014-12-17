<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce
  
  Edited by 2014 Newburns Design and Technology
  *************************************************
  ************ New addon definitions **************
  ************        Below          **************
  *************************************************
  Database Check Tool 1.4 added -- http://addons.oscommerce.com/info/9087
  Order Editor added -- http://addons.oscommerce.com/info/7844
  Manual Order Maker -- http://addons.oscommerce.com/info/8334
  Alternative Administration System added -- http://addons.oscommerce.com/info/9135 
  Credit Class, Gift Vouchers & Discount Coupons osC2.3.3.4 (CCGV) added -- http://addons.oscommerce.com/info/9020  
  Mail Manager added -- http://addons.oscommerce.com/info/9133/v,23
  
  Released under the GNU General Public License
*/

// define the filenames used in the project
  define('FILENAME_ACTION_RECORDER', 'action_recorder.php');
  define('FILENAME_ADMINISTRATORS', 'administrators.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_LOGIN', 'login.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_NEWSLETTERS', 'newsletters.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_INVOICE', 'invoice.php');
  define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
  define('FILENAME_ORDERS_STATUS', 'orders_status.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
  define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_SEC_DIR_PERMISSIONS', 'sec_dir_permissions.php');
  define('FILENAME_SERVER_INFO', 'server_info.php');
  define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
  define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
  define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
  define('FILENAME_STORE_LOGO', 'store_logo.php');
  define('FILENAME_TAX_CLASSES', 'tax_classes.php');
  define('FILENAME_TAX_RATES', 'tax_rates.php');
  define('FILENAME_VERSION_CHECK', 'version_check.php');
  define('FILENAME_WHOS_ONLINE', 'whos_online.php');
  define('FILENAME_ZONES', 'zones.php');
/*
************************************************************************
************** Custom Filenames can be defined below here **************
**************               Raymond Burns                **************
************************************************************************
*/
// Order Editor
  define('FILENAME_ORDERS_EDIT', 'edit_orders.php');
  define('FILENAME_ORDERS_EDIT_ADD_PRODUCT', 'edit_orders_add_product.php');
  define('FILENAME_ORDERS_EDIT_AJAX', 'edit_orders_ajax.php');
// ### MANUAL ORDER MAKER ###
  define('FILENAME_CREATE_ORDER_PROCESS', 'create_order_process.php');
  define('FILENAME_CREATE_ORDER', 'create_order.php');
// Definition for Alternative Administration System
  define('FILENAME_AAS','aas.php');  
// Database Check Tool
  define('FILENAME_DATABASE_CHECK', 'database_check.php');
// CCGV
  define('FILENAME_COUPON_ADMIN', 'coupon_admin.php');
  define('FILENAME_GV_SENT', 'gv_sent.php');
  define('FILENAME_GV_MAIL', 'gv_mail.php');
  define('FILENAME_GV_QUEUE', 'gv_queue.php');
// Mail Manager
  define('FILENAME_MM_MAIL_MANAGER', 'mm_mail_manager.php');
  define('FILENAME_MM_RESPONSEMAIL', 'mm_responsemail.php');
  define('FILENAME_MM_TEMPLATES', 'mm_templates.php');
  define('FILENAME_MM_BULKMAIL', 'mm_bulkmail.php');
  define('FILENAME_MM_EMAIL', 'mm_email.php');
?>
