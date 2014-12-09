<?php
/*
  $Id: gv_queue.php,v 1.2.2.1 2003/04/27 12:36:00 wilt Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 - 2003 osCommerce
  Gift Voucher System v1.0
  Copyright (c) 2001,2002 Ian C Wilson
  http://www.phesis.org
  Released under the GNU General Public License
*/
define('HEADING_TITLE', 'Gift Voucher Release Queue');
define('TABLE_HEADING_CUSTOMERS', 'Customers');
define('TABLE_HEADING_ORDERS_ID', 'Order-No.');
define('TABLE_HEADING_VOUCHER_VALUE', 'Voucher Value');
define('TABLE_HEADING_DATE_PURCHASED', 'Date Purchased');
define('TABLE_HEADING_ACTION', 'Action');
define('TEXT_REDEEM_COUPON_MESSAGE_HEADER', 'You recently purchased a Gift Voucher from our online store.' . "\n"
                                          . 'For security reasons this was not made immediately available to you.' . "\n"
                                          . 'However this amount has now been released. You can now visit our store' . "\n"
                                          . 'and send the value via email to someone else' . "\n\n");
define('TEXT_REDEEM_COUPON_MESSAGE_AMOUNT', 'The Gift Voucher(s) you purchased are worth %s' . "\n\n");
define('TEXT_REDEEM_COUPON_MESSAGE_BODY', '');
define('TEXT_REDEEM_COUPON_MESSAGE_FOOTER', '');
define('TEXT_REDEEM_COUPON_SUBJECT', 'Gift Voucher Purchase');
?>