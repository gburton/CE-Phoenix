<?php
/*
  $Id: /admin/includes/languages/english/database_check.php,v 1.0 2008/05/21
  Database checking tool for admin v1.0 MS 2.2

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

//  Heading Titles for all pages
define('HEADING_TITLE', 'Database Checking Tool v1.8');
define('MAIN_MENU_TITLE', 'Main Menu');
define('HEADING_ACTION_DUPLICATE_NAME', 'Products with duplicate names');
define('HEADING_ACTION_PRODUCTS_WITHOUT_NAME', 'Products without a Name');
define('HEADING_ACTION_PRODUCTS_PRICE_0', 'Products with a price of zero');
define('HEADING_ACTION_PRODUCTS_WEIGHT_0', 'Products with a weight of zero');
define('HEADING_ACTION_PRODUCTS_NO_CATEGORY', 'Products with no category');
define('HEADING_ACTION_MANUFACTURERS_NO_PRODUCTS', 'Manufacturers with no Products');
define('HEADING_ACTION_CATEGORIES_NO_PRODUCTS', 'Categories with no products');
define('HEADING_ACTION_PRODUCTS_TO_CATEGORIES', 'Duplicated PTC Records');
define('HEADING_ACTION_CATEGORIES_NO_DESCRIPTION', 'Categories without a Description Record or Blank Name');
define('HEADING_ACTION_CATEGORIES_DESCRIPTION_NO_CATEGORY', 'Descriptions for Non-Existent Category');
define('HEADING_ACTION_CATEGORIES_NO_PARENT', 'Categories in an invalid category');
define('HEADING_ACTION_CATEGORIES_NO_IMAGE', 'Categories do not have an images set!');
define('HEADING_ACTION_NON_EXISTANT_CAT_ON_PTC', 'PTC records missing valid category id!');
define('HEADING_ACTION_NON_EXISTANT_PROD_ON_PTC', 'PTC records missing valid  product id!');
define('HEADING_ACTION_CATEGORY_DESC_SIZE', 'Descriptions sizes are more than 31 chars long.');
define('HEADING_ACTION_PROD_NO_PTC', 'Products not in a category.');
define('HEADING_ACTION_PRODUCTS_NO_DESCRIPTION', 'Products without a Description Record or Blank Name');
define('HEADING_ACTION_PRODUCTS_DESCRIPTION_NO_CATEGORY', 'Descriptions for Non-Existent Product');
define('HEADING_ACTION_FINISH', 'Finished!');
define('HEADING_ACTION_DESCRIPTION', 'Using this tool you can carry out housekeeping functions on your database. You can eliminate duplicate database entries, add missing field values, delete erroneous records and more.<br><br>You have 2 choices to get started.<br><br>Either click on the start button to use the step-by-step process or choose an option from the table below.<br><br>Links on the pages displayed will enable you to correct the errors found.');
// Table headings
define('HEADING_ACTION_ID', 'Id');
define('HEADING_ACTION_MODEL', 'Model');
define('HEADING_ACTION_PRODUCT_NAME', 'Product Name');
define('HEADING_ACTION_LANGUAGE_ID', 'Language ID');
define('HEADING_ACTION_SUB_CATEGORY', 'Sub Cats in this Cat');
define('HEADING_ACTION_DATE_ADDED', 'Date Added');
define('HEADING_ACTION_STANDARD', 'Set to Std Wt');
define('HEADING_ACTION_DEFAULT_CATEGORY', 'Set to Default Cat');
define('HEADING_ACTION_MANUFACTURERS_NAME', 'Manufacturer');
define('HEADING_ACTION_DELETE', 'Delete');
define('HEADING_ACTION_EDIT', 'Edit');
define('HEADING_ACTION_PRICE', 'Price');
define('HEADING_ACTION_CATEGORY_NAME', 'Category Name');
define('HEADING_ACTION_CATEGORY_ID', 'Category Id');
define('HEADING_ACTION_PARENT_CATEGORY', 'Parent Category');
define('HEADING_ACTION_ADD_PRODUCTS', 'Add Products');

//  Nothing found responses for all pages + finish dialog
define('NO_PRODUCTS_NAMES', 'No duplicate products found in your database!');
define('NO_PRODUCTS_TITLES', 'All products have titles!');
define('PRODUCTS_PRICE_0', 'All products have prices!');
define('PRODUCTS_WEIGHT_0', 'All products have weights!');
define('PRODUCTS_NO_CATEGORY', 'All products are assigned to a category!');
define('CATEGORIES_NO_PRODUCTS', 'All categories have products assigned to them!');
define('MANUFACTURERS_NO_PRODUCTS', 'All maufacturers have products assigned to them!');
define('CATEGORIES_NO_DESCRIPTION', 'All Categories have Descriptions!');
define('CATEGORIES_DESCRIPTION_NO_CATEGORY', 'All Descriptions are for valid categories!');
define('CATEGORIES_NO_PARENT', 'The category structure is correct!');
define('CATEGORIES_NO_IMAGE', 'All Categories have images set!');
define('NON_EXISTANT_CAT_ON_PTC', 'The PTC records have valid categories!');
define('NON_EXISTANT_PROD_ON_PTC', 'All PTC records have valid products!');
define('CATEGORY_DESC_SIZE', 'All Descriptions are less than 32 chars long.');
define('PROD_NO_PTC', 'All Products are in a category.');
define('PRODUCTS_TO_CATEGORIES', 'No Duplicated PTC Records');
define('PRODUCTS_NO_DESCRIPTION', 'All Products have Descriptions!');
define('PRODUCTS_DESCRIPTION_NO_CATEGORY', 'All Descriptions are for valid Products!');

define('FINISH', 'There are no more checks available at this time.  If you have ideas or suggestions to make this tool even better, please do not hesitate to say so.  Your comments are always welcome.  Thank you!');
?>