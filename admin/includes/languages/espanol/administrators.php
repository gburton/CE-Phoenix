<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Administradores');

define('TABLE_HEADING_ADMINISTRATORS', 'Administradores');
define('TABLE_HEADING_HTPASSWD', 'Asegurado por htpasswd');
define('TABLE_HEADING_ACTION', 'Acción');

define('TEXT_INFO_INSERT_INTRO', 'Por favor introduzca el nuevo administrador y los datos relacionados');
define('TEXT_INFO_EDIT_INTRO', 'Por favor realice los cambios necesarios');
define('TEXT_INFO_DELETE_INTRO', '¿Está seguro que desea eliminar este administrador?');
define('TEXT_INFO_HEADING_NEW_ADMINISTRATOR', 'Nuevo Administrador');
define('TEXT_INFO_USERNAME', 'Nombre Usuario:');
define('TEXT_INFO_NEW_PASSWORD', 'Nueva Contraseña:');
define('TEXT_INFO_PASSWORD', 'Contraseña:');
define('TEXT_INFO_PROTECT_WITH_HTPASSWD', 'Protegido con htaccess/htpasswd');

define('ERROR_ADMINISTRATOR_EXISTS', 'Error: Administrador YA existe.');

define('HTPASSWD_INFO', '<strong>Protección adicional con htaccess/htpasswd</strong><p>Esta Instalación de la Herramienta de Administración osCommerce Online Merchant no está adicionalmente protegido a través de htaccess/htpasswd.</p><p>Si habilita la capa de seguridad htaccess/htpasswd, el nombre de usuario del administrador y su contraseña serán guardados automáticamente en el archivo htpasswd cuando actualice los datos de un administrador.</p><p><strong>Por favor, tenga en cuenta</strong>, que si esta capa adicional de seguridad está habilitada y usted no puede acceder a la herramienta de administración, realice los cambios siguientes y consulte a su proveedor de hosting para habilitar la protección de htaccess/htpasswd:</p><p><u><strong>1. Edite este fichero:</strong></u><br /><br />' . DIR_FS_ADMIN . '.htaccess</p><p>Quite las siguientes líneas si existen:</p><p><i>%s</i></p><p><u><strong>2. Elimine este archivo:</strong></u><br /><br />' . DIR_FS_ADMIN . '.htpasswd_oscommerce</p>');
define('HTPASSWD_SECURED', '<strong>Protección adicional con htaccess/htpasswd</strong><p>Esta Instalación de la Herramienta de Administración osCommerce Online Merchant no está adicionalemnte protegida a través de  htaccess/htpasswd.</p>');
define('HTPASSWD_PERMISSIONS', '<strong>Protección adicional con htaccess/htpasswd</strong><p>Esta Instalación de la Herramienta de Administración osCommerce Online Merchant no está adicionalmente protegida a través de  htaccess/htpasswd.</p><p>Los siguientes archivos necesitan tener permisos de escritura en el servidor web para poder activar la capa de seguridad htaccess/htpasswd:</p><ul><li>' . DIR_FS_ADMIN . '.htaccess</li><li>' . DIR_FS_ADMIN . '.htpasswd_oscommerce</li></ul><p>Actualice esta página para confirmar si los permisos de los archivos han sido configurados correctamente.</p>');
?>