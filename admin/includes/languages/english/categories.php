<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

const HEADING_TITLE = 'Categories / Products';
const HEADING_TITLE_SEARCH = 'Search';
const HEADING_TITLE_GOTO = 'Go To';

const SECTION_HEADING_GENERAL = '<i class="fas fa-language fa-fw mr-1"></i>Language specific';
const SECTION_HEADING_DATA = '<i class="fas fa-box-open fa-fw mr-1"></i>Product specific';
const SECTION_HEADING_IMAGES = '<i class="fas fa-images fa-fw mr-1"></i>Product Images';

const TABLE_HEADING_ID = 'ID';
const TABLE_HEADING_CATEGORIES_PRODUCTS = 'Categories / Products';
const TABLE_HEADING_ACTION = 'Action';
const TABLE_HEADING_STATUS = 'Status';

const TEXT_NEW_PRODUCT = 'Adding New Product <small>in %s</small>';
const TEXT_EXISTING_PRODUCT = 'Editing %s <small>in %s</small>';

const TEXT_CATEGORIES = 'Categories';
const TEXT_PRODUCTS = 'Products';
const TEXT_PRODUCTS_PRICE_INFO = 'Price';
const TEXT_PRODUCTS_TAX_CLASS = 'Tax Class';
const TEXT_PRODUCTS_AVERAGE_RATING = 'Average Rating';
const TEXT_PRODUCTS_QUANTITY_INFO = 'Quantity';
const TEXT_DATE_ADDED = 'Date Added';
const TEXT_DATE_AVAILABLE = 'Date Available';

const TEXT_LAST_MODIFIED = 'Last Modified';
const TEXT_IMAGE_NONEXISTENT = 'IMAGE DOES NOT EXIST';
const TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS = 'Please insert a new category or product in this level.';
const TEXT_PRODUCT_DATE_ADDED = 'Date Added';
const TEXT_PRODUCT_DATE_AVAILABLE = 'Date Available';

const TEXT_EDIT_INTRO = 'Please make any necessary changes';
const TEXT_EDIT_CATEGORIES_NAME = 'Category Name';
const TEXT_EDIT_CATEGORIES_IMAGE = 'Category Image';
const TEXT_EDIT_SORT_ORDER = 'Sort Order';

const TEXT_INFO_COPY_TO_INTRO = 'Please choose a new category you wish to copy this product to';
const TEXT_INFO_CURRENT_CATEGORIES = 'Current Categories';

const TEXT_INFO_HEADING_NEW_CATEGORY = 'New Category';
const TEXT_INFO_HEADING_EDIT_CATEGORY = 'Edit Category';
const TEXT_INFO_HEADING_DELETE_CATEGORY = 'Delete Category';
const TEXT_INFO_HEADING_MOVE_CATEGORY = 'Move Category';
const TEXT_INFO_HEADING_DELETE_PRODUCT = 'Delete Product';
const TEXT_INFO_HEADING_MOVE_PRODUCT = 'Move Product';
const TEXT_INFO_HEADING_COPY_TO = 'Copy To';

const TEXT_DELETE_CATEGORY_INTRO = 'Are you sure you want to delete this category?';
const TEXT_DELETE_PRODUCT_INTRO = 'Are you sure you want to permanently delete this product?';

const TEXT_DELETE_WARNING = '<strong>WARNING:</strong> There are sub-categories and/or products still linked to this category!';

const TEXT_MOVE_PRODUCTS_INTRO = 'Please select which category you wish <strong>%s</strong> to reside in';
const TEXT_MOVE_CATEGORIES_INTRO = 'Please select which category you wish <strong>%s</strong> to reside in';
const TEXT_MOVE = 'Move <strong>%s</strong> to:';

const TEXT_NEW_CATEGORY_INTRO = 'Please fill out the following information for the new category';
const TEXT_CATEGORIES_NAME = 'Category Name';
const TEXT_CATEGORIES_IMAGE = 'Category Image';
const TEXT_SORT_ORDER = 'Sort Order';

const TEXT_PRODUCTS_STATUS = 'Products Status';
const TEXT_PRODUCTS_DATE_AVAILABLE = 'Date Available';
const TEXT_PRODUCTS_DATE_AVAILABLE_HELP = 'YYYY-MM-DD';
const TEXT_PRODUCT_AVAILABLE = 'In Stock';
const TEXT_PRODUCT_NOT_AVAILABLE = 'Out of Stock';
const TEXT_PRODUCTS_MANUFACTURER = 'Products Manufacturer';
const TEXT_PRODUCTS_NAME = 'Products Name';
const TEXT_PRODUCTS_DESCRIPTION = 'Products Description';
const TEXT_PRODUCTS_QUANTITY = 'Products Quantity';
const TEXT_PRODUCTS_MODEL = 'Products Model';
const TEXT_PRODUCTS_IMAGE = 'Products Image';
const TEXT_PRODUCTS_MAIN_IMAGE = 'Main Image';
const TEXT_PRODUCTS_LARGE_IMAGE = 'Large Image';
const TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT = 'HTML Content';
const TEXT_PRODUCTS_ADD_LARGE_IMAGE = '<i class="fas fa-plus mr-2"></i>Add New Gallery Image';
const TEXT_PRODUCTS_URL = 'Products URL';
const TEXT_PRODUCTS_URL_WITHOUT_HTTP = 'Make sure to include http:// or https://';
const TEXT_PRODUCTS_PRICE_NET = 'Products Price (Net)';
const TEXT_PRODUCTS_PRICE_GROSS = 'Products Price (Gross)';
const TEXT_PRODUCTS_WEIGHT = 'Products Weight';

const EMPTY_CATEGORY = 'Empty Category';

const TEXT_HOW_TO_COPY = 'Copy Method';
const TEXT_COPY_AS_LINK = 'Link product';
const TEXT_COPY_AS_DUPLICATE = 'Duplicate product';

const ERROR_CANNOT_LINK_TO_SAME_CATEGORY = '<strong>Error:</strong> Can not link products in the same category.';
const ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE = '<strong>Error:</strong> Catalog images directory is not writeable:  %s';
const ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST = '<strong>Error:</strong> Catalog images directory does not exist:  %s';
const ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT = '<strong>Error:</strong> Category cannot be moved into child category.';

const TEXT_CATEGORIES_DESCRIPTION = 'Category Description:<br><small>shows in category page</small>';
const TEXT_EDIT_CATEGORIES_DESCRIPTION = 'Edit the Category Description';

const TEXT_CATEGORIES_SEO_DESCRIPTION = 'Category Meta Description for SEO:<br><small>Add a &lt;description&gt; Meta Element.</small>';
const TEXT_EDIT_CATEGORIES_SEO_DESCRIPTION = 'Edit the Category Meta Description for SEO:<br><small>Changes the &lt;description&gt; Meta Element.</small>';

const TEXT_PRODUCTS_GTIN = 'Products GTIN';
const TEXT_PRODUCTS_GTIN_HELP = 'GTIN must be stored as 14 Digits. Any GTIN smaller than this will be zero-padded per GTIN Specifications.';

const TEXT_PRODUCTS_SEO_DESCRIPTION = 'Product Meta Description for SEO';
const TEXT_PRODUCTS_SEO_DESCRIPTION_HELP = 'Add a &lt;description&gt; Meta Element.  HTML is not allowed.';
const TEXT_PRODUCTS_SEO_KEYWORDS = 'Product Keywords';
const TEXT_PRODUCTS_SEO_KEYWORDS_HELP = 'Helps the Keyword Search Engine. Must be comma separated. HTML is not allowed.';
const TEXT_PRODUCTS_SEO_TITLE = 'Products Title for SEO';
const TEXT_PRODUCTS_SEO_TITLE_HELP = 'Replaces the product name in the &lt;title&gt; Meta Element and optionally in the Breadcrumb Trail.<br>Leave blank to default to product name.';
const TEXT_CATEGORIES_SEO_TITLE = 'Category Title for SEO:<br><small>Replaces the category name in the &lt;title&gt; Meta Element.<br>Leave blank to default to category name.</small>';
const TEXT_EDIT_CATEGORIES_SEO_TITLE = 'Edit the Category Title for SEO:<br><small>Replaces the category name in the &lt;title&gt; Meta Element<br>and optionally in the Breadcrumb Trail.<br>Leave blank to default to category name.</small>';

const TEXT_PRODUCTS_OTHER_IMAGES = 'Gallery Images';
