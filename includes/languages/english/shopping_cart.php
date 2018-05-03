<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Cart Contents');
define('HEADING_TITLE', 'What\'s In My Cart?');
define('TEXT_CART_EMPTY', 'Your Shopping Cart is empty!');
define('SUB_TITLE_SUB_TOTAL', 'Sub-Total:');
define('SUB_TITLE_TOTAL', 'Total:');

define('OUT_OF_STOCK_CANT_CHECKOUT', 'Products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' dont exist in desired quantity in our stock.<br />Please alter the quantity of products marked with (' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '), Thank you');
define('OUT_OF_STOCK_CAN_CHECKOUT', 'Products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' dont exist in desired quantity in our stock.<br />You can buy them anyway and check the quantity we have in stock for immediate deliver in the checkout process.');

define('TEXT_ALTERNATIVE_CHECKOUT_METHODS', '- OR -');

define('CART_BUTTON_UPDATE', '');
define('CART_BUTTON_REMOVE', '');

/* Add definations to display stock on hand quantity in shopping cart */
define('TEXT_NOT_INSTOCK_CAN_CHECKOUT', ' At the moment this product is not in stock - it will be ordered for you!');
define('TEXT_NOT_INSTOCK_CANT_CHECKOUT', ' At the moment this product is not in stock - please remove it from your shopping cart!');
define('TEXT_NOT_ENOUGH_CAN_CHECKOUT', ' There are only %d pieces in stock at the moment - the rest will be ordered for you!');
define('TEXT_NOT_ENOUGH_CANT_CHECKOUT', ' There are only %d pieces in stock at the moment - please reduce the quantity for your order!');
