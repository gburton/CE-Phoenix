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
  SEO Header Tags Reloaded added -- http://addons.oscommerce.com/info/8864
  
  Released under the GNU General Public License
*/

define('TEXT_MAIN', '');
define('TABLE_HEADING_NEW_PRODUCTS', 'New Products For %s');
define('TABLE_HEADING_UPCOMING_PRODUCTS', 'Upcoming Products');
define('TABLE_HEADING_DATE_EXPECTED', 'Date Expected');
define('HEADING_TITLE', 'Welcome to ' . STORE_NAME);

define('TEXT_NO_PRODUCTS', 'There are no products available in this category.');
define('TEXT_NUMBER_OF_PRODUCTS', 'Number of Products: ');
define('TEXT_SHOW', '<strong>Show:</strong>');
define('TEXT_BUY', 'Buy 1 \'');
define('TEXT_NOW', '\' now');
define('TEXT_ALL_CATEGORIES', 'All Categories');
define('TEXT_ALL_MANUFACTURERS', 'All Manufacturers');
/* ** Altered for SEO Header Tags Reloaded ** */
if ( ($category_depth == 'top') && (!isset($HTTP_GET_VARS['manufacturers_id'])) ) {
  define('META_SEO_TITLE', 'Index Page Title');
  define('META_SEO_DESCRIPTION', 'This is the description of your site to be used in the META Description Element');
  // keywords are USELESS unless you are selling into China
  // and want to be listed in Baidu Search Engine
  // define('META_SEO_KEYWORDS', 'these, are, the, comma, separated, keywords, used in the META keywords Element');
}
