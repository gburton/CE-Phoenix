<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', 'El directorio de sesión no existe: ' . tep_session_save_path() . '. Las sesiones no funcionarán hasta que este directorio sea creado.');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', 'No se puede escribir en el directorio de sesiones: ' . tep_session_save_path() . '. Las sesiones no se guardarán hasta que se establezcan los permisos.');
?>
