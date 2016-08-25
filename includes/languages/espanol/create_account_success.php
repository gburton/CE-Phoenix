<?php
/*
  $Id: create_account_success.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Crear una Cuenta');
define('NAVBAR_TITLE_2', 'Éxito');
define('HEADING_TITLE', 'Su cuenta ha sido creada!');
define('TEXT_ACCOUNT_CREATED', '¡Enhorabuena! Su cuenta ha sido creada con éxito. Ahora puede disfrutar de las ventajas de disponer de una cuenta para mejorar su navegación en nuestro catalogo. Si tiene <strong>cualquier</strong> pregunta sobre el funcionamiento del catálogo, por favor comuníquela al <a href="' . tep_href_link('contact_us.php') . '">encargado</a>.<br><br>Se ha enviado una confirmación a la dirección de correo que nos ha proporcionado. Si no lo ha recibido en una hora póngase en contacto con <a href="' . (defined('FILENAME_MOBILE_CONTACT_US')? tep_mobile_link(FILENAME_MOBILE_CONTACT_US) : tep_href_link(FILENAME_CONTACT_US)) . '">nosotros</a>.');
