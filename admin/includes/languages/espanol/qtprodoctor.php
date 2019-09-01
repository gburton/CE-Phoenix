<?php
/*
  $Id: qtprodoctor.php
  $Loc: catalog/admin/includes/languages/english/
      
  2017 QTPro 5.0 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'QTPro Doctor');

define('PAGE_HEADING', 'QTPro Doctor - visión general');

define('TEXT_EXAMINE_HEALTHY', '<span style="color: green;"><b>El producto está correcto</b><br> Las entradas de la base de datos para este stock de productos están como deberían.</span>');
define('TEXT_EXAMINE_MESSED', '<span style="color: red;"><b>Producto corrompido</b><br> Las entradas de la base de datos para este stock de productos están desordenadas. Esta es la razón por la que la tabla de arriba parece desordenada.</span>');
define('TEXT_AMPUTATE', '%s Entradas de la base de datos donde se amputó.');
define('TEXT_CHUCK_TRASH', '%s Las entradas de la base de datos que se identificaron como basura y se eliminaron.');
define('TEXT_UPDATE_SUMMARY', 'Se actualizó el stock de resumen del producto.');

define('QTPRO_OPTIONS_WARNING', '<strong>El Módulo de contenido QT Pro Product Info</strong> no está instalado. Es requerido.');
define('QTPRO_OPTIONS_INSTALL_NOW', '<u>Instalar Ahora el módulo QT Pro Product Info</u>');
define('QTPRO_HT_WARNING', '<strong>El Módulo QT Pro Header Tag</strong> no está instalado o no está habilitado. Es requerido.');
define('QTPRO_HT_INSTALL_NOW', '<u>Instalar Ahora el módulo QT Pro Header Tag</u>');

define('TEXT_PRODUCT_COUNT', 'Actualmente tiene <b>%s</b> productos en su tienda.<br>');
define('TEXT_PRODUCT_TRACKED_STOCK', '<b>%s</b> de ellos tienen opciones con Stock sometido a seguimiento.<br>');
define('TEXT_PRODUCT_TRASH_ROWS', 'En la base de datos actualmente hay <b>%s</b> filas de basura<br>');
define('TEXT_PRODUCT_SICK', '<b>%s</b> de los productos con opciones de seguimiento están corruptos.<br>');

define('WARNING_SICK_PRODUCTS', 'Productos corruptos en la base de datos:');
define('WARNING_PRODUCT_ID', 'Producto con ID ');
define('WARNING_PRODUCT_DATABASE_ENTRY_SUMMARY', 'Las entradas de la base de datos para este stock de productos están desordenadas y el cálculo de inventario de resumen es incorrecto. Por favor, compruebelo ');
define('WARNING_PRODUCTS_STOCK', 'Stock de productos');
define('WARNING_PRODUCT_SUMMARY_STOCK', 'El resumen del cálculo del stock es incorrecto. Por favor, compruebelo ');
define('WARNING_PRODUCT_DATABASE_ENTRY', 'Las entradas de la base de datos para este stock de productos están desordenadas. Por favor, compruebelo ');
define('WARNING_PRODUCT_OK', 'Este producto está correcto.');
