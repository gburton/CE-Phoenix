<?php
/*
  $Id: index.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
define('HEADING_TITLE', 'Bienvenido/a a ' . STORE_NAME);
define('TABLE_HEADING_NEW_PRODUCTS', 'Nuevos Productos en %s');

define('TEXT_NO_PRODUCTS2', 'No hay productos en esta categoría.');
define('TEXT_NUMBER_OF_PRODUCTS', 'Número de Productos: ');
define('TEXT_SHOW', '<STRONG>Mostrar:</STRONG>');
define('TEXT_BUY', 'Compre 1 \'');
define('TEXT_NOW', '\' ahora');
define('TEXT_ALL_CATEGORIES', 'Todas las categorías');
define('TEXT_ALL_MANUFACTURERS', 'Todos los fabricantes');

// seo
if ( ($category_depth == 'top') && (!isset($_GET['manufacturers_id'])) ) {
  define('META_SEO_TITLE', 'Título de la página Index');
  define('META_SEO_DESCRIPTION', 'Esta es la descripción de su sitio para ser utilizado en el META Elemento Descripción');
  /*
  Las palabras clave son INÚTILES a menos que usted está vendiendo en China y quiere que se enumeren en el motor de búsqueda Baidu
  */
  define('META_SEO_KEYWORDS', 'estos, son, las, palabras clave, separadas, por, coma, utilizados en el META Elemento Keywords');
}

