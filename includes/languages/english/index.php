<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

define('TABLE_HEADING_NEW_PRODUCTS', 'New Products For %s');

define('TEXT_NO_PRODUCTS', 'There are no products available in this category.');
define('TEXT_NUMBER_OF_PRODUCTS', 'Number of Products: ');
define('TEXT_SHOW', '<strong>Show:</strong>');
define('TEXT_BUY', 'Buy 1 \'');
define('TEXT_NOW', '\' now');
define('TEXT_ALL_CATEGORIES', 'All Categories');
define('TEXT_ALL_MANUFACTURERS', 'All Manufacturers');

// seo
if ( ($category_depth == 'top') && (!isset($_GET['manufacturers_id'])) ) {
  define('META_SEO_TITLE', 'Index Page Title');
  define('META_SEO_DESCRIPTION', 'This is the description of your site to be used in the META Description Element');
}

