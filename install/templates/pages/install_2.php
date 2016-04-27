<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $www_location = 'http://' . $_SERVER['HTTP_HOST'];

  if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strrpos($www_location, 'install/install.php'));

  $dir_fs_www_root = osc_realpath(dirname(__FILE__) . '/../../../') . '/';
?>


<div class="row">
  <div class="col-sm-9">
    <div class="alert alert-info">
      <h1>Instalación Nueva</h1>

      <p>Esta rutina de instalación basado en la web va a instalarar y configurar correctamente la tienda osCommerce para ejecutarla en este servidor.</p>
      <p>Por favor, siga las instrucciones en pantalla que le guiarán a través del servidor de la base de datos, del servidor web, y las opciones de configuración de la tienda. Si necesita ayuda en cualquiera de lo s pasos, por favor consulte la documentación o busque ayuda en los foros de soporte de la comunidad.</p>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <ol>
          <li class="text-muted">Servidor de base de la datos</li>
          <li class="text-success"><strong>Servidor web</strong></li>
          <li class="text-muted">Configuración de la Tienda Online</li>
          <li class="text-muted">Terminado!</li>
        </ol>
      </div>
    </div>
    <div class="progress">
      <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">50%</div>
    </div>
  </div>
</div>

<div class="clearfix"></div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">

    <div class="page-header">
      <p class="inputRequirement pull-right text-right"><span class="fa fa-asterisk inputRequirement"></span> Información requerida</p>
      <h2>Servidor web</h2>
    </div>

    <form name="install" id="installForm" action="install.php?step=3" method="post" class="form-horizontal" role="form">

      <div class="form-group has-feedback">
        <label for="wwwAddress" class="control-label col-xs-3">Dirección WWW</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('HTTP_WWW_ADDRESS', $www_location, 'required aria-required="true" id="wwwAddress" placeholder="http://"'); ?>
          <span class="fa fa-asterisk form-control-feedback inputRequirement"></span>
          <span class="help-block">La dirección web de la tienda online.</span>
        </div>
      </div>
    
      <div class="form-group has-feedback">
        <label for="webRoot" class="control-label col-xs-3">Directorio raíz del servidor Web</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root, 'required aria-required="true" id="webRoot"'); ?>
          <span class="fa fa-asterisk form-control-feedback inputRequirement"></span>
          <span class="help-block">El directorio del servidor donde se ha instalado la tienda online.</span>
        </div>
      </div>

      <p><?php echo osc_draw_button('Continuar con el paso 3', 'triangle-1-e', null, 'primary', null, 'btn-success btn-block'); ?></p>

      <?php
      foreach ( $_POST as $key => $value ) {
        if (($key != 'x') && ($key != 'y')) {
          echo osc_draw_hidden_field($key, $value);
        }
      }
      ?>

    </form>

  </div>
  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <div class="panel panel-success">
      <div class="panel-heading">
        Paso 2: Servidor Web
      </div>
      <div class="panel-body">
        <p>El servidor web se encarga de servir las páginas de su tienda online para sus invitados y clientes. Los parámetros del servidor Web aseguran que los enlaces a las páginas apuntan a la ubicación correcta.</p>
      </div>
    </div>
  </div>
  
</div>
