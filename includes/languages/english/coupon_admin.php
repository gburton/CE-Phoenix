<?php
/*
  $Id: coupon_admin.php Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License
*/
define('TOP_BAR_TITLE', 'Statistics');
define('HEADING_TITLE', 'Discount Coupons');
define('HEADING_TITLE_STATUS', 'Status : ');
define('TEXT_CUSTOMER', 'Customer:');
define('TEXT_COUPON', 'Coupon Name');
define('TEXT_COUPON_ALL', 'All Coupons');
define('TEXT_COUPON_STATUS', 'Coupon Status');
define('TEXT_COUPON_ACTIVE', 'Active Coupons');
define('TEXT_COUPON_IS_ACTIVE', 'Active');
define('TEXT_COUPON_NOT_ACTIVE', 'Inactive');
define('TEXT_COUPON_INACTIVE', 'Inactive Coupons');
define('TEXT_SUBJECT', 'Subject:');
define('TEXT_FROM', 'From:');
define('TEXT_FREE_SHIPPING', 'Free Shipping');
define('TEXT_MESSAGE', 'Message:');
define('TEXT_SELECT_CUSTOMER', 'Select Customer');
define('TEXT_ALL_CUSTOMERS', 'All Customers');
define('TEXT_NEWSLETTER_CUSTOMERS', 'To All Newsletter Subscribers');
define('TEXT_CONFIRM_DELETE', 'Are you sure you want to delete this Coupon? Deleting this coupon will delete it completely from the database.');
// email discount vouchers to customers text
define('TEXT_TO_REDEEM', 'You can redeem this coupon during checkout. Just enter the code in the box provided, and click on the redeem button.');
define('TEXT_IN_CASE', ' in case you have any problems. ');
define('TEXT_VOUCHER_IS', 'The coupon code is ');
define('TEXT_REMEMBER', 'Don\'t lose the coupon code, make sure to keep the code safe so you can benefit from this special offer.');
define('TEXT_VISIT', 'when you visit ' . HTTP_SERVER . DIR_WS_CATALOG);
define('TEXT_ENTER_CODE', ' and enter the code ');
define('TEXT_SIGN_OFF', 'Kind Regards' . "\n\n" . '' . STORE_NAME .'');
define('TABLE_HEADING_ACTION', 'Action');
define('CUSTOMER_ID', 'Customer id');
define('CUSTOMER_NAME', 'Customer Name');
define('REDEEM_DATE', 'Date Redeemed');
define('IP_ADDRESS', 'IP Address');
define('COUPON_BUTTON_PREVIEW', 'Preview');
define('TEXT_REDEMPTIONS', 'Redemptions');
define('TEXT_REDEMPTIONS_TOTAL', 'In Total');
define('TEXT_REDEMPTIONS_CUSTOMER', 'For this Customer');
define('TEXT_NO_FREE_SHIPPING', 'No Free Shipping');
define('NOTICE_EMAIL_SENT_TO', 'Notice: Email sent to: %s');
define('ERROR_NO_CUSTOMER_SELECTED', 'Error: No customer has been selected.');
define('COUPON_INFO', 'Coupon Information');
define('COUPON_DELETE', 'Delete Coupon');
define('COUPON_ID', 'Coupon ID');
define('COUPON_NAME', 'Coupon Name');
define('COUPON_TYPE', 'Coupon Type');
define('COUPON_AMOUNT', 'Coupon Amount');
define('COUPON_CODE', 'Coupon Code');
define('COUPON_STARTDATE', 'Start Date');
define('COUPON_FINISHDATE', 'Expiry Date');
define('COUPON_FREE_SHIP', 'Free Shipping');
define('COUPON_DESC', 'Coupon Description');
define('COUPON_MIN_ORDER', 'Coupon Minimum Order');
define('COUPON_USES_COUPON', 'Uses per Coupon');
define('COUPON_USES_USER', 'Uses per Customer');
define('COUPON_PRODUCTS', 'Valid Product List');
define('COUPON_CATEGORIES', 'Valid Categories List');
define('VOUCHER_NUMBER_USED', 'Number Used');
define('DATE_CREATED', 'Date Created');
define('DATE_MODIFIED', 'Date Modified');
define('TEXT_HEADING_NEW_COUPON', 'Create New Coupon');
define('ERROR_NO_COUPON_AMOUNT', 'No Coupon Created. Please complete the fields below');
define('COUPON_STATUS_HELP', 'Set the status of the coupon');
define('COUPON_NAME_HELP', 'A short name for the coupon');
define('COUPON_AMOUNT_HELP', 'The value of the discount for the coupon, either fixed or add a % on the end for a percentage discount.');
define('COUPON_CODE_HELP', 'You can enter your own code here, or leave blank for an auto generated one.');
define('COUPON_STARTDATE_HELP', 'The date the coupon will be valid from');
define('COUPON_FINISHDATE_HELP', 'The date the coupon expires');
define('COUPON_FREE_SHIP_HELP', 'The coupon gives free shipping on an order. Note. This overrides the coupon_amount figure but respects the minimum order value');
define('COUPON_DESC_HELP', 'A description of the coupon for the customer');
define('COUPON_MIN_ORDER_HELP', 'The minimum order value before the coupon is valid');
define('COUPON_USES_COUPON_HELP', 'The maximum number of times the coupon can be used, leave blank if you want no limit.');
define('COUPON_USES_USER_HELP', 'Number of times a user can use the coupon, leave blank for no limit.');
define('COUPON_PRODUCTS_HELP', 'A comma separated list of product_ids that this coupon can be used with. Leave blank for no restrictions.');
define('COUPON_CATEGORIES_HELP', 'A comma separated list of cpaths that this coupon can be used with, leave blank for no restrictions.');
?>