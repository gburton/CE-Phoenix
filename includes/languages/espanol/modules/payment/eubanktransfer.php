<?php
/*
  $Id: eubanktransfer.php,v 1.9.1 2006/07/04 12:00:00 jb_gfx

	Thanks to all the developers from the EU-Standard Bank Transfer module $

	osCommerce, Open Source E-Commerce Solutions
	http://www.oscommerce.com

	Copyright (c) 2002 osCommerce

	Released under the GNU General Public License
*/

define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_TITLE', 'Transferencia bancaria');
define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_DESCRIPTION', '
Por favor, realice la transferencia bancaria con estos datos:<p>
Titular: ' . MODULE_PAYMENT_EU_ACCOUNT_HOLDER . '<br>
Número IBAN: ' . MODULE_PAYMENT_EU_IBAN . '<br>
Código BIC / SWIFT: ' . MODULE_PAYMENT_EU_BIC . '<br>
Entidad: ' . MODULE_PAYMENT_EU_BANKNAME . '<p>

<strong>RECUERDE</strong>, es muy importante que especifique su nombre y el número del pedido en la transferencia bancaria.<p> 
');

define('MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_EMAIL_FOOTER', '<strong>Nuestros comentarios:</strong><p>

Por favor, realice la transferencia bancaria con estos datos:<p>
Titular: ' . MODULE_PAYMENT_EU_ACCOUNT_HOLDER . '<br>
Número IBAN: ' . MODULE_PAYMENT_EU_IBAN . '<br>
Código BIC / SWIFT: ' . MODULE_PAYMENT_EU_BIC . '<br>
Entidad: ' . MODULE_PAYMENT_EU_BANKNAME . '<p>

<strong>RECUERDE</strong>, es muy importante que especifique su nombre y el número del pedido en la transferencia bancaria.<p> 
');