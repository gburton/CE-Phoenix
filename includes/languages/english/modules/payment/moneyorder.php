<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

const MODULE_PAYMENT_MONEYORDER_TEXT_TITLE = 'Check/Money Order';
const MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION = 'Require an offline payment before shipping order.';
const MODULE_PAYMENT_MONEYORDER_TEXT_CONFIRMATION = 'Make Payable To:&nbsp;%1$s<br /><br />Send To:<br />%2$s<br />%3$s<br /><br />Your order will not ship until we receive payment.';
const MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER = <<<'EOT'
Make Payable To: %1$s

Send To:
%2$s
%3$s

Your order will not ship until we receive payment.
EOT;

const MODULE_PAYMENT_MONEYORDER_WARNING_SETUP = "Please ensure you set up the 'Make Payable to' parameter.";
