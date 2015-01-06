<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce
  
  Edited by 2014 Newburns Design and Technology
  *************************************************
  ************ New addon definitions **************
  ************        Below          **************
  *************************************************
  Credit Class, Gift Vouchers & Discount Coupons osC2.3.3.4 (CCGV) added -- http://addons.oscommerce.com/info/9020  

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Create an Account');

define('HEADING_TITLE', 'My Account Information');

define('TEXT_ORIGIN_LOGIN', '<font color="#FF0000"><small><strong>NOTE:</strong></small></font> If you already have an account with us, please login at the <a href="%s"><u>login page</u></a>.');

define('EMAIL_SUBJECT', 'Welcome to ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Dear Mr. %s,' . "\n\n");
define('EMAIL_GREET_MS', 'Dear Ms. %s,' . "\n\n");
define('EMAIL_GREET_NONE', 'Dear %s' . "\n\n");
define('EMAIL_WELCOME', 'We welcome you to <strong>' . STORE_NAME . '</strong>.' . "\n\n");
define('EMAIL_TEXT', 'You can now take part in the <strong>various services</strong> we have to offer you. Some of these services include:' . "\n\n" . '<li><strong>Permanent Cart</strong> - Any products added to your online cart remain there until you remove them, or check them out.' . "\n" . '<li><strong>Address Book</strong> - We can now deliver your products to another address other than yours! This is perfect to send birthday gifts direct to the birthday-person themselves.' . "\n" . '<li><strong>Order History</strong> - View your history of purchases that you have made with us.' . "\n" . '<li><strong>Products Reviews</strong> - Share your opinions on products with our other customers.' . "\n\n");
define('EMAIL_CONTACT', 'For help with any of our online services, please email the store-owner: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', '<strong>Note:</strong> This email address was given to us by one of our customers. If you did not signup to be a member, please send an email to ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");
/*
************************************************************************
************** Custom Filenames can be defined below here **************
**************               Raymond Burns                **************
************************************************************************
*/
// CCGV
  define('EMAIL_GV_INCENTIVE_HEADER', "\n\n" .'As part of our welcome to new customers, we have sent you an e-Gift Voucher worth %s');
  define('EMAIL_GV_REDEEM', 'The redeem code for the e-Gift Voucher is %s, you can enter the redeem code when checking out while making a purchase');
  define('EMAIL_GV_LINK', 'or by following this link ');
  define('EMAIL_COUPON_INCENTIVE_HEADER', 'Congratulations, to make your first visit to our online shop a more rewarding experience we are sending you an e-Discount Coupon.' . "\n" . ' Below are details of the Discount Coupon created just for you' . "\n");
  define('EMAIL_COUPON_REDEEM', 'To use the coupon enter the redeem code which is %s during checkout while making a purchase');
?>
