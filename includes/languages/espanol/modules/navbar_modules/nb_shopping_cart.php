<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com 

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_NAVBAR_SHOPPING_CART_TITLE', 'Carrito de compra');
  define('MODULE_NAVBAR_SHOPPING_CART_DESCRIPTION', 'Mostrar el Carrito de Compra en la barra de navegación');
  
  define('MODULE_NAVBAR_SHOPPING_CART_CONTENTS', '<i class="fa fa-shopping-cart"></i> %s producto(s) <span class="caret"></span>');
  define('MODULE_NAVBAR_SHOPPING_CART_NO_CONTENTS', '<i class="fa fa-shopping-cart"></i> 0 productos');
  define('MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS', '%s producto(s), %s');
  define('MODULE_NAVBAR_SHOPPING_CART_VIEW_CART', 'Ver Carrito');
  define('MODULE_NAVBAR_SHOPPING_CART_CHECKOUT', '<i class="fa fa-angle-right"></i> Realizar Pedido');
  
  define('MODULE_NAVBAR_SHOPPING_CART_PRODUCT', '<a href="' . tep_href_link('product_info.php', 'products_id=%s') . '">%s x %s</a>');
  