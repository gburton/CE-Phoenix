<?php
/*
  $Id: espanol.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales..
// Array examples which should work on all servers:
//España: 'es_ES.UTF-8', 'es_ES.UTF8', 'esp_es'
//México 'es_MX.UTF-8', 'es_MX.UTF8', 'esm_es'
@setlocale(LC_ALL, array('es_ES.UTF-8', 'es_ES.UTF8', 'esp_es'));

define('DATE_FORMAT_SHORT', '%d/%m/%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A %d de %B de %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd/m/Y');  // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'd/m/Y H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('JQUERY_DATEPICKER_I18N_CODE', 'es'); // leave empty for en_US; see http://jqueryui.com/demos/datepicker/#localization
define('JQUERY_DATEPICKER_FORMAT', 'dd/mm/yy'); // see http://docs.jquery.com/UI/Datepicker/formatDate

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

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="es"');

// charset for web pages and emails
define('CHARSET', 'utf-8');

// page title
define('TITLE', 'osCommerce Online Merchant Herramienta de Administración');

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Administración');
define('HEADER_TITLE_SUPPORT_SITE', 'Soporte');
define('HEADER_TITLE_ONLINE_CATALOG', 'Catálogo');
define('HEADER_TITLE_ADMINISTRATION', 'Administración');

// text for gender
define('MALE', 'Señor');
define('FEMALE', 'Señora');

// text for date of birth example
define('DOB_FORMAT_STRING', 'dd/mm/aaaa');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', 'Configuración');
define('BOX_CONFIGURATION_MYSTORE', 'Mi Tienda');
define('BOX_CONFIGURATION_LOGGING', 'Registro');
define('BOX_CONFIGURATION_CACHE', 'Caché');
define('BOX_CONFIGURATION_ADMINISTRATORS', 'Administradores');
define('BOX_CONFIGURATION_STORE_LOGO', 'Logo de la tienda');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', 'Módulos');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', 'Catálogo');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', 'Categorias/Productos');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', 'Atributos de Productos');
define('BOX_CATALOG_MANUFACTURERS', 'Fabricantes');
define('BOX_CATALOG_REVIEWS', 'Comentarios');
define('BOX_CATALOG_SPECIALS', 'Ofertas');
define('BOX_CATALOG_PRODUCTS_EXPECTED', 'Productos próximamente disponibles');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', 'Clientes');
define('BOX_CUSTOMERS_CUSTOMERS', 'Clientes');

// orders box text in includes/boxes/orders.php
define('BOX_HEADING_ORDERS', 'Pedidos');
define('BOX_ORDERS_ORDERS', 'Pedidos');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', 'Zonas/Impuestos');
define('BOX_TAXES_COUNTRIES', 'Paises');
define('BOX_TAXES_ZONES', 'Provincias');
define('BOX_TAXES_GEO_ZONES', 'Zonas de Impuestos');
define('BOX_TAXES_TAX_CLASSES', 'Tipos de Impuestos');
define('BOX_TAXES_TAX_RATES', 'Tasas de Impuestos');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', 'Informes');
define('BOX_REPORTS_PRODUCTS_VIEWED', 'Los Mas Vistos');
define('BOX_REPORTS_PRODUCTS_PURCHASED', 'Los Mas Comprados');
define('BOX_REPORTS_ORDERS_TOTAL', 'Total por Cliente');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', 'Herramientas');
define('BOX_TOOLS_ACTION_RECORDER', 'Registro de Acciones');
define('BOX_TOOLS_BACKUP', 'Copia de Seguridad');
define('BOX_TOOLS_BANNER_MANAGER', 'Gestor de Banners');
define('BOX_TOOLS_CACHE', 'Control de Caché');
define('BOX_TOOLS_DEFINE_LANGUAGE', 'Definir Idiomas');
define('BOX_TOOLS_MAIL', 'Enviar Email');
define('BOX_TOOLS_NEWSLETTER_MANAGER', 'Gestor de Boletines');
define('BOX_TOOLS_SEC_DIR_PERMISSIONS', 'Permisos de seguridad de directorios');
define('BOX_TOOLS_SERVER_INFO', 'Información del Servidor');
define('BOX_TOOLS_VERSION_CHECK', 'Comprobar Versión');
define('BOX_TOOLS_WHOS_ONLINE', 'Usuarios conectados');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', 'Localización');
define('BOX_LOCALIZATION_CURRENCIES', 'Monedas');
define('BOX_LOCALIZATION_LANGUAGES', 'Idiomas');
define('BOX_LOCALIZATION_ORDERS_STATUS', 'Estado Pedidos');

// javascript messages
define('JS_ERROR', 'Ha habido errores procesando su formulario!\nPor favor, haga las siguientes modificaciones:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* El atributo necesita un precio\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* El atributo necesita un prefijo para el precio\n');

define('JS_PRODUCTS_NAME', '* El producto necesita un nombre\n');
define('JS_PRODUCTS_DESCRIPTION', '* El producto necesita una descripción\n');
define('JS_PRODUCTS_PRICE', '* El producto necesita un precio\n');
define('JS_PRODUCTS_WEIGHT', '* Debe especificar el peso del producto\n');
define('JS_PRODUCTS_QUANTITY', '* Debe especificar la cantidad\n');
define('JS_PRODUCTS_MODEL', '* Debe especificar el modelo\n');
define('JS_PRODUCTS_IMAGE', '* Debe suministrar una imagen\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* Debe rellenar el precio\n');

define('JS_GENDER', '* Debe elegir un \'Tratamiento\'.\n');
define('JS_FIRST_NAME', '* El \'Nombre\' debe tener al menos ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' letras.\n');
define('JS_LAST_NAME', '* El \'Apellido\' debe tener al menos ' . ENTRY_LAST_NAME_MIN_LENGTH . ' letras.\n');
define('JS_DOB', '* La \'Fecha de Nacimiento\' debe tener el formato: xx/xx/xxxx (dia/mes/año).\n');
define('JS_EMAIL_ADDRESS', '* El \'E-Mail\' debe tener al menos ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' letras.\n');
define('JS_ADDRESS', '* El \'Domicilio\' debe tener al menos ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' letras.\n');
define('JS_POST_CODE', '* El \'Código Postal\' debe tener al menos ' . ENTRY_POSTCODE_MIN_LENGTH . ' letras.\n');
define('JS_CITY', '* La \'Ciudad\' debe tener al menos ' . ENTRY_CITY_MIN_LENGTH . ' letras.\n');
define('JS_STATE', '* Debe indicar la \'Provincia\'.\n');
define('JS_STATE_SELECT', '-- Seleccione Arriba --');
define('JS_ZONE', '* La \'Provincia\' se debe seleccionar de la lista para este pais.');
define('JS_COUNTRY', '* Debe seleccionar un \'Pais\'.\n');
define('JS_TELEPHONE', '* El \'Teléfono\' debe tener al menos ' . ENTRY_TELEPHONE_MIN_LENGTH . ' letras.\n');
define('JS_PASSWORD', '* La \'Contraseña\' y \'Confirmación\' deben ser iguales y tener al menos ' . ENTRY_PASSWORD_MIN_LENGTH . ' letras.\n');

define('JS_ORDER_DOES_NOT_EXIST', 'El Número de pedido %s no existe!');

define('CATEGORY_PERSONAL', 'Personal');
define('CATEGORY_ADDRESS', 'Domicilio');
define('CATEGORY_CONTACT', 'Contacto');
define('CATEGORY_COMPANY', 'Empresa');
define('CATEGORY_OPTIONS', 'Opciones');

define('ENTRY_GENDER', 'Tratamiento:');
define('ENTRY_GENDER_ERROR', '&nbsp;<span class="errorText">obligatorio</span>');
define('ENTRY_FIRST_NAME', 'Nombre:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' letras</span>');
define('ENTRY_LAST_NAME', 'Apellidos:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_LAST_NAME_MIN_LENGTH . ' letras</span>');
define('ENTRY_DATE_OF_BIRTH', 'Fecha de Nacimiento:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<span class="errorText">(p.ej. 21/05/1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' letras</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<span class="errorText">Su Email no parece correcto!</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<span class="errorText">email ya existe!</span>');
define('ENTRY_COMPANY', 'Nombre empresa:');
define('ENTRY_STREET_ADDRESS', 'Dirección:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' letras</span>');
define('ENTRY_SUBURB', '');
define('ENTRY_POST_CODE', 'Código Postal:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_POSTCODE_MIN_LENGTH . ' letras</span>');
define('ENTRY_CITY', 'Población:');
define('ENTRY_CITY_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_CITY_MIN_LENGTH . ' letras</span>');
define('ENTRY_STATE', 'Provincia:');
define('ENTRY_STATE_ERROR', '&nbsp;<span class="errorText">obligatorio</span>');
define('ENTRY_COUNTRY', 'País:');
define('ENTRY_COUNTRY_ERROR', 'Debe seleccionar un país de la lista del menú desplegable.');
define('ENTRY_TELEPHONE_NUMBER', 'Teléfono:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_TELEPHONE_MIN_LENGTH . ' letras</span>');
define('ENTRY_FAX_NUMBER', 'Fax:');
define('ENTRY_NEWSLETTER', 'Boletín:');
define('ENTRY_NEWSLETTER_YES', 'suscrito');
define('ENTRY_NEWSLETTER_NO', 'no suscrito');

// images
define('IMAGE_ANI_SEND_EMAIL', 'Enviando E-Mail');
define('IMAGE_BACK', 'Volver');
define('IMAGE_BACKUP', 'Copiar');
define('IMAGE_CANCEL', 'Cancelar');
define('IMAGE_CONFIRM', 'Confirmar');
define('IMAGE_COPY', 'Copiar');
define('IMAGE_COPY_TO', 'Copiar A');
define('IMAGE_DETAILS', 'Detalle');
define('IMAGE_DELETE', 'Eliminar');
define('IMAGE_EDIT', 'Editar');
define('IMAGE_EMAIL', 'Email');
define('IMAGE_FILE_MANAGER', 'Ficheros');
define('IMAGE_ICON_STATUS_GREEN', 'Activado');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Activar');
define('IMAGE_ICON_STATUS_RED', 'Desactivado');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Desactivar');
define('IMAGE_ICON_INFO', 'Información');
define('IMAGE_INSERT', 'Insertar');
define('IMAGE_LOCK', 'Bloquear');
define('IMAGE_MODULE_INSTALL', 'Instalar Módulo');
define('IMAGE_MODULE_REMOVE', 'Quitar Módulo');
define('IMAGE_MOVE', 'Mover');
define('IMAGE_NEW_BANNER', 'Nuevo Banner');
define('IMAGE_NEW_CATEGORY', 'Nueva Categoría');
define('IMAGE_NEW_COUNTRY', 'Nuevo Pais');
define('IMAGE_NEW_CURRENCY', 'Nueva Moneda');
define('IMAGE_NEW_FILE', 'Nuevo Fichero');
define('IMAGE_NEW_FOLDER', 'Nueva Carpeta');
define('IMAGE_NEW_LANGUAGE', 'Nuevo Idioma');
define('IMAGE_NEW_NEWSLETTER', 'Nuevo Boletín');
define('IMAGE_NEW_PRODUCT', 'Nuevo Producto');
define('IMAGE_NEW_TAX_CLASS', 'Nuevo Tipo de Impuesto');
define('IMAGE_NEW_TAX_RATE', 'Nueva Tasa de Impuesto');
define('IMAGE_NEW_TAX_ZONE', 'Nueva Zona de Impuesto');
define('IMAGE_NEW_ZONE', 'Nueva Zona');
define('IMAGE_ORDERS', 'Pedidos');
define('IMAGE_ORDERS_INVOICE', 'Factura');
define('IMAGE_ORDERS_PACKINGSLIP', 'Albarán');
define('IMAGE_PREVIEW', 'Previsualizar');
define('IMAGE_RESTORE', 'Restablecer');
define('IMAGE_RESET', 'Reiniciar');
define('IMAGE_SAVE', 'Guardar');
define('IMAGE_SEARCH', 'Buscar');
define('IMAGE_SELECT', 'Seleccionar');
define('IMAGE_SEND', 'Enviar');
define('IMAGE_SEND_EMAIL', 'Enviar Email');
define('IMAGE_UNLOCK', 'Desbloqueado');
define('IMAGE_UPDATE', 'Actualizar');
define('IMAGE_UPDATE_CURRENCIES', 'Actualizar Tipo de Cambio');
define('IMAGE_UPLOAD', 'Subir');

define('ICON_CROSS', 'Falso');
define('ICON_CURRENT_FOLDER', 'Directorio Actual');
define('ICON_DELETE', 'Eliminar');
define('ICON_ERROR', 'Error');
define('ICON_FILE', 'Fichero');
define('ICON_FILE_DOWNLOAD', 'Descargar');
define('ICON_FOLDER', 'Carpeta');
define('ICON_LOCKED', 'Bloqueado');
define('ICON_PREVIOUS_LEVEL', 'Nivel Anterior');
define('ICON_PREVIEW', 'Previsualizar');
define('ICON_STATISTICS', 'Estadísticas');
define('ICON_SUCCESS', 'Éxito');
define('ICON_TICK', 'Verdadero');
define('ICON_UNLOCKED', 'Desbloqueado');
define('ICON_WARNING', 'Advertencia');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Página %s de %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> banners)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> paises)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> clientes)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Viendo de la <b>%d</b> a la <b>%d</b> (de <b>%d</b> monedas)');
define('TEXT_DISPLAY_NUMBER_OF_ENTRIES', 'Viendo de la <b>%d</b> a la <b>%d</b> (de <b>%d</b> entradas)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> idiomas)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> fabricantes)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> boletines)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> pedidos)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> estado de pedidos)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> productos)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> productos esperados)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> comentarios)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Viendo de la <b>%d</b> a la <b>%d</b> (de <b>%d</b> ofertas)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Viendo de la <b>%d</b> a la <b>%d</b> (de <b>%d</b> zonas de impuestos)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> porcentajes de impuestos)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> tipos de impuesto)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Viendo de la <b>%d</b> a la <b>%d</b> (de <b>%d</b> zonas)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');

define('TEXT_DEFAULT', 'predeterminado/a');
define('TEXT_SET_DEFAULT', 'Establecer como predeterminado/a');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Obligatorio</span>');

define('TEXT_CACHE_CATEGORIES', 'Categorias');
define('TEXT_CACHE_MANUFACTURERS', 'Fabricantes');
define('TEXT_CACHE_ALSO_PURCHASED', 'También Han Comprado');

define('TEXT_NONE', '--ninguno--');
define('TEXT_TOP', 'Principio');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Error: Destino no existe.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Error: No se puede escribir en el destino.');
define('ERROR_FILE_NOT_SAVED', 'Error: El fichero subido no se ha guardado.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Error: Extension de fichero no permitida.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Exito: Fichero guardado con éxito.');
define('WARNING_NO_FILE_UPLOADED', 'Advertencia: No se ha subido ningun archivo.');

// bootstrap helper
define('MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION', '<p>El ancho del contenido puede ser de 12 o menos por columna y fila.</p><p>12/12 = 100% ancho, 6/12 = 50% ancho, 4/12 = 33% ancho.</p><p>El total de todas las columnas en cualquier fila debe ser igual a 12 (ej:  3 cajas de 4 columnas cada una, 1 caja de 12 columnas etc).</p>');

// seo helper
define('PLACEHOLDER_COMMA_SEPARATION', 'Deben, Estar, Separadas, Por, Comas');
