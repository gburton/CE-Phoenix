<?php
/*
  $Id: espanol.php

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales
// or type locale -a on the server.
// Array examples which should work on all servers:
//España: 'es_ES.UTF-8', 'es_ES.UTF8', 'esp_es'
//México 'es_MX.UTF-8', 'es_MX.UTF8', 'esm_es'
@setlocale(LC_ALL, array('es_ES.UTF-8', 'es_ES.UTF8', 'esp_es'));

define('DATE_FORMAT_SHORT', '%d/%m/%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A %d de %B de %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd/m/Y'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('JQUERY_DATEPICKER_FORMAT', 'dd/mm/yyyy'); // see http://bootstrap-datepicker.readthedocs.org/en/release/options.html#format

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'EUR');

// Global entries for the <html> tag
define('HTML_PARAMS', '');

// charset for web pages and emails
define('CHARSET', 'utf-8');

// page title
define('TITLE', STORE_NAME);

// text in includes/modules/downloads.php
define('HEADER_TITLE_MY_ACCOUNT', 'Mi Cuenta');

// text in includes/application_top.php
define('HEADER_TITLE_TOP', '<i class="fa fa-home"><span class="sr-only">Inicio</span></i>');
define('HEADER_TITLE_CATALOG', 'Catálogo');

// text for gender
define('MALE', 'S<span class="hidden-xs">eño</span>r');
define('FEMALE', 'S<span class="hidden-xs">eño</span>ra');

// checkout procedure text
define('CHECKOUT_BAR_DELIVERY', 'Información de Entrega');
define('CHECKOUT_BAR_PAYMENT', 'Información de Pago');
define('CHECKOUT_BAR_CONFIRMATION', 'Confirmación');

// pull down default text
define('PULL_DOWN_DEFAULT', 'Seleccione, por favor');

// javascript messages
define('JS_ERROR', '¡Hay errores en su formulario!\n\nPor favor, haga las siguientes correciones:\n\n');

define('JS_REVIEW_TEXT', '* Su \'Comentario\' debe tener al menos ' . REVIEW_TEXT_MIN_LENGTH . ' letras.\n');
define('JS_REVIEW_RATING', '* Debe evaluar el producto sobre el que opina.\n');

define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* Por favor, seleccione un método de pago para su pedido.\n');

define('ERROR_NO_PAYMENT_MODULE_SELECTED', 'Por favor, seleccione un método de pago para su pedido.');

define('ENTRY_COMPANY', 'Nombre de la Empresa:');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', 'Tratamiento:');
define('ENTRY_GENDER_ERROR', 'Por favor seleccione un tratamiento.'); 
define('ENTRY_GENDER_TEXT', '');
define('ENTRY_FIRST_NAME', 'Nombre:');
define('ENTRY_FIRST_NAME_ERROR', 'Su Nombre debe tener al menos ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' letras.');
define('ENTRY_FIRST_NAME_TEXT', '');
define('ENTRY_LAST_NAME', 'Apellidos:');
define('ENTRY_LAST_NAME_ERROR', 'Sus apellidos deben tener al menos ' . ENTRY_LAST_NAME_MIN_LENGTH . ' letras.');
define('ENTRY_LAST_NAME_TEXT', '');
define('ENTRY_DATE_OF_BIRTH', 'Fecha de Nacimiento:');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'Su fecha de nacimiento debe tener este formato: DD/MM/AAAA (p.ej. 21/05/1970)');
define('ENTRY_DATE_OF_BIRTH_TEXT', 'ej. 21/05/1970');
define('ENTRY_EMAIL_ADDRESS', 'Correo electrónico:');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'Su dirección de Correo electrónico debe tener al menos ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' letras.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Su dirección de Correo electrónico no parece válida - por favor haga los cambios necesarios.');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'Su dirección de Correo electrónico ya figura entre nuestros clientes - puede entrar a su cuenta con esta dirección o crear una cuenta nueva con una dirección diferente.');
define('ENTRY_EMAIL_ADDRESS_TEXT', '');
define('ENTRY_STREET_ADDRESS', 'Dirección:');
define('ENTRY_STREET_ADDRESS_ERROR', 'Su dirección debe tener al menos ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' letras.');
define('ENTRY_STREET_ADDRESS_TEXT', '');
define('ENTRY_SUBURB', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', 'Código Postal:');
define('ENTRY_POST_CODE_ERROR', 'Su código postal debe tener al menos ' . ENTRY_POSTCODE_MIN_LENGTH . ' letras.');
define('ENTRY_POST_CODE_TEXT', '');
define('ENTRY_CITY', 'Población:');
define('ENTRY_CITY_ERROR', 'Su población debe tener al menos ' . ENTRY_CITY_MIN_LENGTH . ' letras.');
define('ENTRY_CITY_TEXT', '');
define('ENTRY_STATE', 'Provincia:');
define('ENTRY_STATE_ERROR', 'Su provincia debe tener al menos ' . ENTRY_STATE_MIN_LENGTH . ' letras.');
define('ENTRY_STATE_ERROR_SELECT', 'Por favor, seleccione una Provincia de la lista.');
define('ENTRY_STATE_TEXT', '');
define('PLEASE_SELECT_A_STATE', 'Por favor seleccione su Provincia/Estado.');
define('ENTRY_COUNTRY', 'País:');
define('ENTRY_COUNTRY_ERROR', 'Debe seleccionar un país de la lista desplegable.');
define('ENTRY_COUNTRY_TEXT', '');
define('ENTRY_TELEPHONE_NUMBER', 'Teléfono:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'Su número de teléfono debe tener al menos ' . ENTRY_TELEPHONE_MIN_LENGTH . ' dígitos.');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '');
define('ENTRY_FAX_NUMBER', 'Móvil:');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'Boletín de noticias:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', 'suscribirse');
define('ENTRY_NEWSLETTER_NO', 'no suscribirse');
define('ENTRY_PASSWORD', 'Contraseña:');
define('ENTRY_PASSWORD_ERROR', 'Su contraseña debe tener al menos ' . ENTRY_PASSWORD_MIN_LENGTH . ' carácteres.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'La confirmación de la contraseña debe ser igual a la contraseña.');
define('ENTRY_PASSWORD_TEXT', '');
define('ENTRY_PASSWORD_CONFIRMATION', 'Confirme Contraseña:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '');
define('ENTRY_PASSWORD_CURRENT', 'Contraseña Actual:');
define('ENTRY_PASSWORD_CURRENT_TEXT', '');
define('ENTRY_PASSWORD_CURRENT_ERROR', 'Su contraseña debe tener al menos ' . ENTRY_PASSWORD_MIN_LENGTH . ' carácteres.');
define('ENTRY_PASSWORD_NEW', 'Nueva Contraseña:');
define('ENTRY_PASSWORD_NEW_TEXT', '');
define('ENTRY_PASSWORD_NEW_ERROR', 'Su contraseña nueva debe tener al menos ' . ENTRY_PASSWORD_MIN_LENGTH . ' carácteres.');
define('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING', 'La confirmación de su contraseña debe coincidir con su nueva contraseña.');
define('PASSWORD_HIDDEN', '--OCULTO--');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Páginas de Resultados:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> productos)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> comentarios)');

define('PREVNEXT_TITLE_FIRST_PAGE', 'Principio');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', 'Anterior');
define('PREVNEXT_TITLE_NEXT_PAGE', 'Siguiente');
define('PREVNEXT_TITLE_LAST_PAGE', 'Final');
define('PREVNEXT_TITLE_PAGE_NO', 'Página %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Anteriores %d Páginas');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Siguientes %d Páginas');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;PRINCIPIO');
define('PREVNEXT_BUTTON_PREV', '[&lt;&lt; Anterior]');
define('PREVNEXT_BUTTON_NEXT', '[Siguiente &gt;&gt;]');
define('PREVNEXT_BUTTON_LAST', 'FINAL&gt;&gt;');

define('IMAGE_BUTTON_ADD_ADDRESS', 'Añadir Dirección');
define('IMAGE_BUTTON_BACK', 'Volver');
define('IMAGE_BUTTON_BUY_NOW', 'Comprar Ahora');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'Cambiar Dirección');
define('IMAGE_BUTTON_CHECKOUT', 'Realizar Pedido');
define('IMAGE_BUTTON_CONFIRM_ORDER', 'Confirmar Pedido');
define('IMAGE_BUTTON_CONTINUE', 'Continuar');
define('IMAGE_BUTTON_DELETE', 'Eliminar');
define('IMAGE_BUTTON_LOGIN', 'Entrar');
define('IMAGE_BUTTON_IN_CART', 'Añadir a la Cesta');
define('IMAGE_BUTTON_REVIEWS', 'Comentarios');
define('IMAGE_BUTTON_SEARCH', 'Buscar');
define('IMAGE_BUTTON_UPDATE', 'Actualizar');
define('IMAGE_BUTTON_WRITE_REVIEW', 'Escribir Comentario');

define('SMALL_IMAGE_BUTTON_DELETE', 'Eliminar');
define('SMALL_IMAGE_BUTTON_EDIT', 'Modificar');
define('SMALL_IMAGE_BUTTON_VIEW', 'Ver');
define('SMALL_IMAGE_BUTTON_BUY', 'Comprar');

define('ICON_ARROW_RIGHT', 'más');

define('TEXT_SORT_PRODUCTS', 'Ordenar');
define('TEXT_DESCENDINGLY', 'Descendentemente');
define('TEXT_ASCENDINGLY', 'Ascendentemente');
define('TEXT_BY', ' por ');

define('TEXT_NO_REVIEWS', 'En este momento, no hay ningun comentario.');

define('TEXT_UNKNOWN_TAX_RATE', 'Impuesto desconocido');

define('TEXT_CCVAL_ERROR_INVALID_DATE', 'La fecha de caducidad de la tarjeta de crédito es incorrecta. Compruebe la fecha e inténtelo de nuevo.');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', 'El número de la tarjeta de crédito es incorrecto. Compruebe el número e inténtelo de nuevo.');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', 'Los primeros cuatro dígitos de su tarjeta son: %s. Si este número es correcto, no aceptamos este tipo de tarjetas. Si es incorrecto, inténtelo de nuevo.');

// category views
define('TEXT_VIEW', 'Vista: ');
define('TEXT_VIEW_LIST', ' Listado');
define('TEXT_VIEW_GRID', 'Cuadrícula');

// search placeholder
define('TEXT_SEARCH_PLACEHOLDER','Buscar');

// message for required inputs
define('FORM_REQUIRED_INFORMATION', '<span class="fa fa-asterisk text-danger"></span> Información requerida');
define('FORM_REQUIRED_INPUT', '<span><span class="fa fa-asterisk form-control-feedback text-danger"></span></span>');

// reviews
define('REVIEWS_TEXT_RATED', 'Comentado %s por <cite title="%s" itemprop="reviewer">%s</cite>');
define('REVIEWS_TEXT_AVERAGE', 'Valoración media basada en <span itemprop="count">%s</span> comentarios(s) %s');
define('REVIEWS_TEXT_TITLE', 'Lo que opinan nuestros clientes...');

// grid/list
define('TEXT_SORT_BY', 'Ordenar por ');

// moved from index
define('TABLE_HEADING_IMAGE', '');
define('TABLE_HEADING_MODEL', 'Modelo');
define('TABLE_HEADING_PRODUCTS', 'Nombre Producto');
define('TABLE_HEADING_MANUFACTURER', 'Fabricante');
define('TABLE_HEADING_QUANTITY', 'Cantidad');
define('TABLE_HEADING_PRICE', 'Precio');
define('TABLE_HEADING_WEIGHT', 'Peso');
define('TABLE_HEADING_BUY_NOW', 'Comprar Ahora');
define('TABLE_HEADING_LATEST_ADDED', 'Novedades');

// product notifications
define('PRODUCT_SUBSCRIBED', '%s se ha agregado a su lista de notificaciones');
define('PRODUCT_UNSUBSCRIBED', '%s ha sido eliminado de su lista de notificaciones');
define('PRODUCT_ADDED', '%s se ha añadido a su carrito');
define('PRODUCT_REMOVED', '%s ha sido eliminado de su carrito');

// bootstrap helper
define('MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION', '');

//NIF start
define('ENTRY_NIF', 'NIF/CIF/NIE:');
define('ENTRY_NO_NIF_ERROR', 'Ha de introducir su NIF, CIF o NIE.');
define('ENTRY_FORMAT_NIF_LENGTH_ERROR', 'Su NIF, CIF o NIE no está correcto, por favor compruébe los dígitos.');
define('ENTRY_FORMAT_NIF_ERROR', 'Su NIF no ha podido ser validado, por favor compruébelo.');
define('ENTRY_FORMAT_CIF_ERROR', 'Su CIF no ha podido ser validado, por favor compruébelo.');
define('ENTRY_FORMAT_NIE_ERROR', 'Su NIE no ha podido ser validado, por favor compruébelo.');
define('ENTRY_LETRA_NIF_ERROR', 'La letra del NIF es incorrecta.');
define('ENTRY_NIF_TEXT_SPAIN', ' solo España');
define('ENTRY_NIF_EXAMPLE', '(ej.: 01234567L, B01234567, X0123456S)');
define('JS_NIF', 'NIF requerido');
//NIF end
