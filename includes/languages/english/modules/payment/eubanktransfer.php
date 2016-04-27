<?php
/*
  $Id: eubanktransfer.php,v 1.9.1 2006/07/04 12:00:00 jb_gfx

	Thanks to all the developers from the EU-Standard Bank Transfer module $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_TITLE', 'EU Banktransfer');
define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_DESCRIPTION', '
	Please transfer the total amount to the following bank account:<p>
	
	Account holder: ' . MODULE_PAYMENT_EU_ACCOUNT_HOLDER . '<br>
	Account IBAN: ' . MODULE_PAYMENT_EU_IBAN . '<br>
	BIC / SWIFT code: ' . MODULE_PAYMENT_EU_BIC . '<br>
	Bank name: ' . MODULE_PAYMENT_EU_BANKNAME . '<p>
	
	<strong>REMEMBER</strong>, enter your name and your invoice number in the subject field.<p>
	');

define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_EMAIL_FOOTER',
	'<strong>Our comments:</strong><p>
	
	Please transfer the total amount to the following bank account:<p>
	
	Account holder: ' . MODULE_PAYMENT_EU_ACCOUNT_HOLDER . '<br>
	Account IBAN: ' . MODULE_PAYMENT_EU_IBAN . '<br>
	BIC / SWIFT code: ' . MODULE_PAYMENT_EU_BIC . '<br>
	Bank name: ' . MODULE_PAYMENT_EU_BANKNAME . '<p>
	
	<strong>REMEMBER</strong>, enter your name and your invoice number in the subject field.<p>
	');
?>
