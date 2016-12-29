<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Gestor de Banners');

define('TABLE_HEADING_BANNERS', 'Banners');
define('TABLE_HEADING_GROUPS', 'Grupos');
define('TABLE_HEADING_STATISTICS', 'Vistas / Clics');
define('TABLE_HEADING_STATUS', 'Estado');
define('TABLE_HEADING_ACTION', 'Acción');

define('TEXT_BANNERS_TITLE', 'Título del Banner:');
define('TEXT_BANNERS_URL', 'URL del Banner:');
define('TEXT_BANNERS_GROUP', 'Grupo del Banner:');
define('TEXT_BANNERS_NEW_GROUP', ', o introduzca un grupo nuevo');
define('TEXT_BANNERS_IMAGE', 'Imagen:');
define('TEXT_BANNERS_IMAGE_LOCAL', ', o introduzca un fichero local');
define('TEXT_BANNERS_IMAGE_TARGET', 'Destino de la Imagen (Grabar en):');
define('TEXT_BANNERS_HTML_TEXT', 'Texto HTML:');
define('TEXT_BANNERS_EXPIRES_ON', 'Caduca el:');
define('TEXT_BANNERS_OR_AT', ', o  trás');
define('TEXT_BANNERS_IMPRESSIONS', 'vistas.');
define('TEXT_BANNERS_SCHEDULED_AT', 'Programado el:');
define('TEXT_BANNERS_BANNER_NOTE', '<strong>Notas sobre el Banner:</strong><ul><li>Use una imagen o texto HTML para el banner - no ambos.</li><li>El texto HTML tiene prioridad sobre una imagen</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<strong>Notas sobre la Imagen:</strong><ul><li>El directorio donde suba la imagen debe de tener configurados los permisos de escritura necesarios!</li><li>No rellene el campo \'Grabar en\' si no va a subir una imagen al servidor (como cuando usa una imagen ya existente en el servidor -fichero local).</li><li>El campo \'Grabar en\' debe de ser un directorio que existe y terminado en una barra (por ejemplo: banners/).</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<strong>Notas sobre la Caducidad:</strong><ul><li>Solo se debe rellenar uno de los dos campos</li><li>Si el banner no debe caducar, no rellene ninguno de los campos</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<strong>Notas sobre la Programación:</strong><ul><li>Si se configura una fecha de programación, el banner se activará en esa fecha.</li><li>Todos los banners programados se marcan como inactivos hasta que llegue su fecha, entonces se marcarán como activos.</li></ul>');

define('TEXT_BANNERS_DATE_ADDED', 'Añadido el:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', 'Programado el: <strong>%s</strong>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', 'Caduca el: <strong>%s</strong>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', 'Caduca tras: <strong>%s</strong> vistas');
define('TEXT_BANNERS_STATUS_CHANGE', 'Cambio Estado: %s');

define('TEXT_BANNERS_DATA', 'D<br />A<br />T<br />O<br />S');
define('TEXT_BANNERS_LAST_3_DAYS', 'Últimos 3 días');
define('TEXT_BANNERS_BANNER_VIEWS', 'Vistas');
define('TEXT_BANNERS_BANNER_CLICKS', 'Clics');

define('TEXT_INFO_DELETE_INTRO', '¿Seguro que quiere eliminar este banner?');
define('TEXT_INFO_DELETE_IMAGE', 'Borrar imagen');

define('SUCCESS_BANNER_INSERTED', 'Éxito: El banner ha sido insertado.');
define('SUCCESS_BANNER_UPDATED', 'Éxito: El banner ha sido actualizado.');
define('SUCCESS_BANNER_REMOVED', 'Éxito: El banner ha sido eliminado.');
define('SUCCESS_BANNER_STATUS_UPDATED', 'Éxito: El estado del banner ha sido actualizado.');

define('ERROR_BANNER_TITLE_REQUIRED', 'Error: Se necesita un título para el banner.');
define('ERROR_BANNER_GROUP_REQUIRED', 'Error: Se necesita un grupo para el banner.');
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Error: El directorio de destino no existe: %s');
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Error: No se puede escribir en el directorio de destino: %s');
define('ERROR_IMAGE_DOES_NOT_EXIST', 'Error: La imagen no existe.');
define('ERROR_IMAGE_IS_NOT_WRITEABLE', 'Error: La imagen no se puede eliminar.');
define('ERROR_UNKNOWN_STATUS_FLAG', 'Error: Estado desconocido.');

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', 'Error: El directorio de gráficos no existe. Por favor, crea un directorio \'graphs\' dentro de \'images\'.');
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', 'Error: No se puede escribir en el directorio de gráficos.');
?>