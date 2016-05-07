<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Búsqueda Avanzada');
define('NAVBAR_TITLE_2', 'Resultados de la Búsqueda');

define('HEADING_TITLE_1', 'Búsqueda Avanzada');
define('HEADING_TITLE_2', 'Productos que satisfacen los criterios de búsqueda');

define('HEADING_SEARCH_CRITERIA', 'Criterios de Búsqueda');

define('TEXT_SEARCH_IN_DESCRIPTION', 'Buscar también en la descripción');
define('ENTRY_CATEGORIES', 'Categoría:');
define('ENTRY_INCLUDE_SUBCATEGORIES', 'Incluir Subcategorías');
define('ENTRY_MANUFACTURERS', 'Fabricante:');
define('ENTRY_PRICE_FROM', ' Precio desde:');
define('ENTRY_PRICE_TO', 'hasta:');
define('ENTRY_DATE_FROM', 'De fecha de alta:');
define('ENTRY_DATE_TO', 'hasta:');

define('ENTRY_PRICE_FROM_TEXT', '');
define('ENTRY_PRICE_TO_TEXT', '');
define('ENTRY_DATE_FROM_TEXT', '');
define('ENTRY_DATE_TO_TEXT', '');

define('TEXT_ALL_CATEGORIES', 'Todas');
define('TEXT_ALL_MANUFACTURERS', 'Todos');

define('TEXT_SEARCH_HELP_LINK', '<span class="fa fa-info-circle"></span> Ayuda');

define('TEXT_ALL_CATEGORIES', 'Todas las Categorías');
define('TEXT_ALL_MANUFACTURERS', 'Todos los Fabricantes');

define('HEADING_SEARCH_HELP', 'Consejos para Búsqueda Avanzada');
define('TEXT_SEARCH_HELP', 'El motor de búsqueda le permite hacer una búsqueda por palabras clave en el modelo, nombre y descripción del producto y en el nombre del fabricante.<br><br>Cuando haga una búsqueda por palabras o frases clave, puede separar estas con los operadores lógicos AND y OR. Por ejemplo, puede hacer una busqueda por <u>microsoft AND raton</u>. Esta búsqueda daría como resultado los productos que contengan ambas palabras. Por el contrario, si teclea  <u>raton OR teclado</u>, conseguirá una lista de los productos que contengan las dos o sólo una de las palabras. Si no se separan las palabras o frases clave con AND o con OR, la búsqueda se hará usando por defecto el operador logico AND.<br><br>Puede realizar búsquedas exactas de varias palabras encerrándolas entre comillas. Por ejemplo, si busca <u>"ordenador portátil"</u>, obtendrá una lista de productos que tengan exactamente esa cadena en ellos.<br><br>Se pueden usar paréntesis para controlar el orden de las operaciones lógicas. Por ejemplo, puede introducir <u>microsoft and (teclado or raton or "visual basic")</u>.');
define('TEXT_CLOSE_WINDOW', '<u>Cerrar Ventana</u> [x]');

define('TEXT_NO_PRODUCTS', 'No hay productos que corresponden con los criterios de búsqueda.');

define('ERROR_AT_LEAST_ONE_INPUT', 'Debe introducir al menos un criterio de búsqueda.');
define('ERROR_INVALID_FROM_DATE', 'La Fecha de Alta inicial no es válida');
define('ERROR_INVALID_TO_DATE', 'La Fecha de Alta final es inválida');
define('ERROR_TO_DATE_LESS_THAN_FROM_DATE', 'Fecha de Alta final debe ser mayor que Fecha de Alta inicial');
define('ERROR_PRICE_FROM_MUST_BE_NUM', 'El Precio Desde debe ser númerico');
define('ERROR_PRICE_TO_MUST_BE_NUM', 'El Precio Hasta debe ser númerico');
define('ERROR_PRICE_TO_LESS_THAN_PRICE_FROM', 'Precio Hasta debe ser mayor o igual que Precio Desde');
define('ERROR_INVALID_KEYWORDS', 'Palabras clave incorrectas');

// text for date of birth example
define('DOB_FORMAT_STRING', 'dd/mm/yyyy');
