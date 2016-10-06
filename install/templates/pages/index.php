<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $compat_register_globals = true;

?>

<div class="alert alert-info">
  <h1>Bienvenido a osCommerce Online Merchant v<?php echo osc_get_version(); ?>!</h1>

  <p>osCommerce Online Merchant le ayuda a vender productos en todo el mundo con su propia tienda online. Su herramienta de administración gestiona productos, clientes, pedidos, boletines de noticias, ofertas especiales y más para establecer con éxito su negocio online.</p>
  <p>osCommerce ha atraído una gran comunidad de propietarios de tiendas y desarrolladores que se apoyan mutuamente y han proporcionado más de 7.000 complementos gratuitos que permiten ampliar las prestaciones y el potencial de su tienda online.</p>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">
    <div class="page-header">
      <h2>Instalación Nueva</h2>
    </div>

<?php
    $configfile_array = array();

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
      @chmod(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php', 0777);
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php')) {
      @chmod(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php', 0777);
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
      $configfile_array[] = osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php';
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php')) {
      $configfile_array[] = osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php';
    }

    $warning_array = array();

    if (function_exists('ini_get')) {
      if ($compat_register_globals == false) {
        $warning_array['register_globals'] = 'Compatibility with register_globals is supported from PHP 4.3+. This setting <u>must be enabled</u> due to an older PHP version being used.';
      }
    }

    if (!extension_loaded('mysql') && !extension_loaded('mysqli')) {
      $warning_array['mysql'] = 'The MySQL[i] extension is required but is not installed. Please enable it to continue installation.';
    }

    if ((sizeof($configfile_array) > 0) || (sizeof($warning_array) > 0)) {
?>

      <div class="noticeBox">

<?php
      if (sizeof($warning_array) > 0) {
?>

        <table class="table table-condensed table-striped">

<?php
        foreach ( $warning_array as $key => $value ) {
          echo '        <tr>' . "\n" .
               '          <td valign="top"><strong>' . $key . '</strong></td>' . "\n" .
               '          <td valign="top">' . $value . '</td>' . "\n" .
               '        </tr>' . "\n";
        }
?>

        </table>
<?php
      }

      if (sizeof($configfile_array) > 0) {
?>

        <div class="alert alert-danger">
          <p>El servidor web no puede guardar los parámetros de instalación en los archivos de configuración.</p>
          <p>Los siguientes archivos necesitan tener sus permisos establecidos en escritura global (chmod 777):</p>
          <p>

<?php
          for ($i=0, $n=sizeof($configfile_array); $i<$n; $i++) {
            echo $configfile_array[$i];

            if (isset($configfile_array[$i+1])) {
              echo '<br />';
            }
          }
?>

          </p>
        </div>

<?php
      }
?>

      </div>

<?php
    }

    if ((sizeof($configfile_array) > 0) || (sizeof($warning_array) > 0)) {
?>

      <div class="alert alert-danger">Por favor, corrija los errores anteriores y vuelva a intentar el procedimiento de instalación con los cambios hechos.</div>

<?php
      if (sizeof($warning_array) > 0) {
        echo '    <div class="alert alert-info"><i>El cambio de los parámetros de configuración del servidor web puede requerir el reinicio del servidor web para que los cambios tengan efecto.</i></div>' . "\n";
      }
?>

      <p><a href="index.php" class="btn btn-danger btn-block" role="button">Reintentar</a></p>

<?php
    } else {
?>

      <div class="alert alert-success">El entorno del servidor web ha sido verificado para proceder con la correcta instalación y configuración de su tienda online.</div>

      <div id="jsOn" style="display: none;">
        <p><a href="install.php" class="btn btn-success btn-block" role="button">Iniciar la instalación</a></p>
      </div>

      <div id="jsOff">
        <p class="text-danger">Por favor, active Javascript en su navegador para poder iniciar la instalación.</p>
        <p><a href="index.php" class="btn btn-danger btn-block" role="button">Reintentar</a></p>
      </div>

<script>
$(function() {
  $('#jsOff').hide();
  $('#jsOn').show();
});
</script>

<?php
  }
?>
  </div>
  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <div class="panel panel-success">
      <div class="panel-heading">
        Funcionalidades del servidor
      </div>
        <table class="table table-condensed table-striped">
          <tr>
            <th colspan="2">Versión PHP</th>
          </tr>
          <tr>
            <th><?php echo PHP_VERSION; ?></th>
            <td align="right" width="25"><?php echo ((PHP_VERSION >= 5.3) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
        </table>

<?php
  if (function_exists('ini_get')) {
?>

        <br />

        <table class="table table-condensed table-striped">
          <tr>
            <th colspan="3">Configuración PHP</th>
          </tr>
          <tr>
            <th>register_globals</th>
            <td align="right"><?php echo (((int)ini_get('register_globals') == 0) ? 'Off' : 'On'); ?></td>
            <td align="right"><?php echo (($compat_register_globals == true) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>magic_quotes</th>
            <td align="right"><?php echo (((int)ini_get('magic_quotes') == 0) ? 'Off' : 'On'); ?></td>
            <td align="right"><?php echo (((int)ini_get('magic_quotes') == 0) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>file_uploads</th>
            <td align="right"><?php echo (((int)ini_get('file_uploads') == 0) ? 'Off' : 'On'); ?></td>
            <td align="right"><?php echo (((int)ini_get('file_uploads') == 1) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>session.auto_start</th>
            <td align="right"><?php echo (((int)ini_get('session.auto_start') == 0) ? 'Off' : 'On'); ?></td>
            <td align="right"><?php echo (((int)ini_get('session.auto_start') == 0) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>session.use_trans_sid</th>
            <td align="right"><?php echo (((int)ini_get('session.use_trans_sid') == 0) ? 'Off' : 'On'); ?></td>
            <td align="right"><?php echo (((int)ini_get('session.use_trans_sid') == 0) ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
        </table>

        <br />

        <table class="table table-condensed table-striped">
          <tr>
            <th colspan="2">Extensiones PHP requeridas</th>
          </tr>
          <tr>
            <th>MySQL</th>
            <td align="right"><?php echo (extension_loaded('mysql') || extension_loaded('mysqli') ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
        </table>

        <br />

        <table class="table table-condensed table-striped">
          <tr>
            <th colspan="2">Extensiones PHP recomendadas</th>
          </tr>
          <tr>
            <th>GD</th>
            <td align="right"><?php echo (extension_loaded('gd') ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>cURL</th>
            <td align="right"><?php echo (extension_loaded('curl') ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
          <tr>
            <th>OpenSSL</th>
            <td align="right"><?php echo (extension_loaded('openssl') ? '<i class="fa fa-thumbs-up text-success"></i>' : '<i class="fa fa-thumbs-down text-danger"></i>'); ?></td>
          </tr>
        </table>

<?php
  }
?>
    </div>
  </div>
</div>
