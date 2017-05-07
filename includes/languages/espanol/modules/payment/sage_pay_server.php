<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_PAYMENT_SAGE_PAY_SERVER_TEXT_TITLE', 'Pago directo Sage');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_TEXT_PUBLIC_TITLE', 'Tarjeta de crédito (Gestionado por Sage Pay)');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_TEXT_DESCRIPTION', '<img src="images/icon_info.gif" border="0" />&nbsp;<a href="http://library.oscommerce.com/Package&en&sage_pay&oscom23&server" target="_blank" style="text-decoration: underline; font-weight: bold;">Ver documentación on-line</a><br /><br /><img src="images/icon_popup.gif" border="0">&nbsp;<a href="https://support.sagepay.com/apply/default.aspx?PartnerID=E194E079-84A9-493C-AB9A-91CB362D3238&PromotionCode=osc3MF" target="_blank" style="text-decoration: underline; font-weight: bold;">Sitio web de Sage</a>&nbsp;<a href="javascript:toggleDivBlock(\'sagePayInfo\');">(info)</a><span id="sagePayInfo" style="display: none;"><br /><i>Si utiliza el enlace anterior para suscribirse, Sagepay otorgara a osCommerce una pequeña bonificación económica por referir a un cliente.</i></span>');

  define('MODULE_PAYMENT_SAGE_PAY_SERVER_ERROR_ADMIN_CURL', 'Este módulo requiere que cURL esté habilitado en PHP y no funcionará hasta que se haya habilitado en este servidor.');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_ERROR_ADMIN_CONFIGURATION', 'Este módulo no funcionará hasta que configure los parámetros de Login del Vendedor. Por favor configure los ajustes del módulo.');

  define('MODULE_PAYMENT_SAGE_PAY_SERVER_ERROR_TITLE', 'Se ha producido un error al procesar su tarjeta de crédito');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_ERROR_GENERAL', 'Por favor, inténtelo de nuevo y si los problemas persisten, intente otra forma de pago.');

  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_LINK_TITLE', 'Probando Conexión servidor de API');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_TITLE', 'Probando Conexión servidor de API');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_GENERAL_TEXT', 'Probando conexión con el servidor..');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_BUTTON_CLOSE', 'Cerrar');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_TIME', 'Tiempo de conexión:');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_SUCCESS', '¡Hecho!');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_FAILED', '¡Error! Por favor, revise la configuración de certificados SSL y vuelva a intentarlo.');
  define('MODULE_PAYMENT_SAGE_PAY_SERVER_DIALOG_CONNECTION_ERROR', 'Ha ocurrido un error. Por favor, actualice la página, revise la configuración y vuelva a intentarlo.');
?>
