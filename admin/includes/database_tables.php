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
  Credit Class, Gift Vouchers & Discount Coupons osC2.3.3.4 (CCGV) added -- http://addons.oscommerce.com/info/9020  
  Mail Manager added -- http://addons.oscommerce.com/info/9133/v,23  

  Released under the GNU General Public License
*/

// define the database table names used in the project
  define('TABLE_ACTION_RECORDER', 'action_recorder');
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_ADMINISTRATORS', 'administrators');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_NEWSLETTERS', 'newsletters');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_IMAGES', 'products_images');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SEC_DIRECTORY_WHITELIST', 'sec_directory_whitelist');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
/*
************************************************************************
************** Custom Filenames can be defined below here **************
**************               Raymond Burns                **************
************************************************************************
*/
// CCGV
  define('TABLE_COUPON_GV_QUEUE', 'coupon_gv_queue');
  define('TABLE_COUPON_GV_CUSTOMER', 'coupon_gv_customer');
  define('TABLE_COUPON_EMAIL_TRACK', 'coupon_email_track');
  define('TABLE_COUPON_REDEEM_TRACK', 'coupon_redeem_track');
  define('TABLE_COUPONS', 'coupons');
  define('TABLE_COUPONS_DESCRIPTION', 'coupons_description'); 
// Mail Manager
  define('TABLE_MM_RESPONSEMAIL', 'mm_responsemail');
  define('TABLE_MM_RESPONSEMAIL_RESTORE', 'mm_responsemail_backup');
  define('TABLE_MM_RESPONSEMAIL_RESET', 'mm_responsemail_reset');
  define('TABLE_MM_TEMPLATES', 'mm_templates');
  define('TABLE_MM_NEWSLETTERS', 'mm_newsletters');
?>
