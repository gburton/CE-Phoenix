<?php
/*
  $Id: gv_mail.php,v 1.5.2.2 2003/04/27 12:36:00 wilt Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License
*/
define('HEADING_TITLE', 'Send Gift Voucher To Customers');
define('TEXT_CUSTOMER', 'Customer:');
define('TEXT_SUBJECT', 'Subject:');
define('TEXT_FROM', 'From:');
define('TEXT_TO', 'Email To:');
define('TEXT_AMOUNT', 'Amount');
define('TEXT_MESSAGE', 'Message:');
define('TEXT_SINGLE_EMAIL', '<span class="smallText">Use this for sending single emails, otherwise use dropdown above</span>');
define('TEXT_SELECT_CUSTOMER', 'Select Customer');
define('TEXT_ALL_CUSTOMERS', 'All Customers');
define('TEXT_NEWSLETTER_CUSTOMERS', 'To All Newsletter Subscribers');
define('NOTICE_EMAIL_SENT_TO', 'Notice: Email sent to: %s');
define('ERROR_NO_CUSTOMER_SELECTED', 'Error: No customer has been selected.');
define('ERROR_NO_AMOUNT_SELECTED', 'Error: No amount has been selected.');
define('TEXT_GV_WORTH', 'The Gift Voucher is worth ');
define('TEXT_TO_REDEEM', 'To redeem this Gift Voucher, please click on the link below. Please also write down the redemption code');
define('TEXT_WHICH_IS', ' which is ');
define('TEXT_IN_CASE', ' in case you have any problems.');
define('TEXT_OR_VISIT', ' or visit ');
define('TEXT_ENTER_CODE', ' and enter the code during the checkout process');
define('TEXT_SIGN_OFF', 'Kind Regards' . "\n\n" . '' . STORE_NAME .'');
define ('TEXT_REDEEM_COUPON_MESSAGE_HEADER', 'You recently purchased a Gift Voucher from our site, for security reasons, the amount of the Gift Voucher was not immediately credited to you. The shop owner has now released this amount.');
define ('TEXT_REDEEM_COUPON_MESSAGE_AMOUNT', "\n\n" . 'The value of the Gift Voucher was %s');
define ('TEXT_REDEEM_COUPON_MESSAGE_BODY', "\n\n" . 'You can now visit our site, login and send the Gift Voucher amount to anyone you want.');
define ('TEXT_REDEEM_COUPON_MESSAGE_FOOTER', "\n\n");
?>