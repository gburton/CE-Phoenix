<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Orders');
define('HEADING_TITLE_SEARCH', 'Order ID:');
define('HEADING_TITLE_STATUS', 'Status:');
define('HEADING_TITLE_ORDER', 'Order #%s');

define('TAB_TITLE_SUMMARY','<i class="fas fa-info-circle fa-fw mr-1"></i>Summary');
define('TAB_TITLE_PRODUCTS','<i class="fas fa-box-open fa-fw mr-1"></i>Products');
define('TAB_TITLE_STATUS_HISTORY','<i class="fas fa-history fa-fw mr-1"></i>Status History');

define('TABLE_HEADING_OID', 'Order');
define('TABLE_HEADING_COMMENTS', 'Comments');
define('TABLE_HEADING_CUSTOMERS', 'Customer');
define('TABLE_HEADING_ORDER_TOTAL', 'Order Total');
define('TABLE_HEADING_DATE_PURCHASED', 'Date Purchased');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_QUANTITY', 'Qty.');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model');
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price (inc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total (inc)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Customer Notified');
define('TABLE_HEADING_DATE_ADDED', 'Date Added');

define('ENTRY_CUSTOMER', 'Customer');
define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_DELIVERY_TO', 'Delivery To:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_SHIPPING_ADDRESS', 'Shipping Address');
define('ENTRY_BILLING_ADDRESS', 'Billing Address');
define('ENTRY_PAYMENT_METHOD', 'Payment Method');
define('ENTRY_CREDIT_CARD_TYPE', 'Credit Card Type:');
define('ENTRY_CREDIT_CARD_OWNER', 'Credit Card Owner:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Credit Card Number:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Credit Card Expires:');
define('ENTRY_SUB_TOTAL', 'Sub-Total');
define('ENTRY_TAX', 'Tax');
define('ENTRY_SHIPPING', 'Shipping');
define('ENTRY_TOTAL', 'Total');
define('ENTRY_DATE_PURCHASED', 'Date Purchased');
define('ENTRY_STATUS', 'Status');
define('ENTRY_DATE_LAST_UPDATED', 'Date Last Updated');
define('ENTRY_NOTIFY_CUSTOMER', 'Notify Customer');
define('ENTRY_NOTIFY_COMMENTS', 'Append Comments');
define('ENTRY_PRINTABLE', 'Print Invoice');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Delete Order');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this order?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Restock product quantity');
define('TEXT_DATE_ORDER_CREATED', 'Date Created: %s');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Last Modified: %s');
define('TEXT_INFO_PAYMENT_METHOD', 'Payment Method: %s');

define('TEXT_ALL_ORDERS', 'All Orders');
define('TEXT_NO_ORDER_HISTORY', 'No Order History Available');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Order Update');
define('EMAIL_TEXT_ORDER_NUMBER', 'Order Number:');
define('EMAIL_TEXT_INVOICE_URL', 'Detailed Invoice:');
define('EMAIL_TEXT_DATE_ORDERED', 'Date Ordered:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Your order has been updated to the following status.' . "\n\n" . 'New status: %s' . "\n\n" . 'Please reply to this email if you have any questions.' . "\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', 'The comments for your order are' . "\n\n%s\n\n");

define('ERROR_ORDER_DOES_NOT_EXIST', '<strong>Error:</strong> Order <strong>%s</strong> does not exist.');
define('SUCCESS_ORDER_UPDATED', '<strong>Success:</strong> Order has been successfully updated.');
define('WARNING_ORDER_NOT_UPDATED', '<strong>Warning:</strong> Nothing to change. The order was not updated.');

define('ENTRY_ADD_COMMENT', 'Add Comment:');

define('ENTRY_NOTIFY_CUSTOMER_TEXT', 'This will notify the customer that their order has been updated.');
define('ENTRY_NOTIFY_COMMENTS_TEXT', 'This will append your comments to the order and the email.');

define('TEXT_ORDER_STATUS', '<strong>%s</strong> [%s]');
define('TEXT_ORDER_PAYMENT', '<strong>%s</strong> [%s]');
