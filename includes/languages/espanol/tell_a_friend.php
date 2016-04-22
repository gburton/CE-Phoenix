<?php
/*
  $Id: tell_a_friend.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Enviar a un Amigo');

define('HEADING_TITLE', 'Enviar información sobre \'%s\' a un amigo');

define('FORM_TITLE_CUSTOMER_DETAILS', 'Tus Datos');
define('FORM_TITLE_FRIEND_DETAILS', 'Los Datos de tu Amigo');
define('FORM_TITLE_FRIEND_MESSAGE', 'Tu Mensaje');

define('FORM_FIELD_CUSTOMER_NAME', 'Tu Nombre:');
define('FORM_FIELD_CUSTOMER_EMAIL', 'Tu Email:');
define('FORM_FIELD_FRIEND_NAME', 'El Nombre de tu Amigo:');
define('FORM_FIELD_FRIEND_EMAIL', 'El Email de tu Amigo:');

define('TEXT_EMAIL_SUCCESSFUL_SENT', 'Tu email sobre <b>%s</b> ha sido enviado con éxito a <b>%s</b>.');

define('TEXT_EMAIL_SUBJECT', 'Tu amigo %s te quiere recomendar "%s"');
define('TEXT_EMAIL_INTRO', 'Hola %s!' . "\n\n" . 'Tu amigo %s, ha pensado que estarías interesado en %s de %s.');
define('TEXT_EMAIL_LINK', 'Para ver el producto usa el siguiente enlace:' . "\n\n" . '%s');
define('TEXT_EMAIL_SIGNATURE', 'Atentamente,' . "\n\n" . '%s');

define('ERROR_TO_NAME', 'Error: La dirección de su amigo no puede estar vacia.');
define('ERROR_TO_ADDRESS', 'Error: La dirección de su amigo debe ser válida.');
define('ERROR_FROM_NAME', 'Error: Su nombre no debe estar vacio.');
define('ERROR_FROM_ADDRESS', 'Error: Su dirección de email debe de ser válida.');
define('ERROR_ACTION_RECORDER', 'Error: Ya ha sido enviado un Email. Por favor, inténtelo otra vez en %s minutos.');
