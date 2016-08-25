<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $dir_fs_document_root = $_POST['DIR_FS_DOCUMENT_ROOT'];
  if ((substr($dir_fs_document_root, -1) != '\\') && (substr($dir_fs_document_root, -1) != '/')) {
    if (strrpos($dir_fs_document_root, '\\') !== false) {
      $dir_fs_document_root .= '\\';
    } else {
      $dir_fs_document_root .= '/';
    }
  }
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
          <li class="text-muted">Servidor web</li>
          <li class="text-success"><strong>Configuración de la Tienda Online</strong></li>
          <li class="text-muted">Terminado!</li>
        </ol>
      </div>
    </div>
    <div class="progress">
      <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">75%</div>
    </div>
  </div>
</div>

<div class="clearfix"></div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">

    <div class="page-header">
      <p class="text-danger pull-right text-right"><span class="fa fa-asterisk text-danger"></span> Required information</p>
      <h2>Configuración de la Tienda Online</h2>
    </div>

    <form name="install" id="installForm" action="install.php?step=4" method="post" class="form-horizontal" role="form">

      <div class="form-group has-feedback">
        <label for="storeName" class="control-label col-xs-3">Nombre de la Tienda</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_STORE_NAME', NULL, 'required aria-required="true" id="storeName" placeholder="Your Store Name"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">El nombre de la tienda online visible al público.</span>
        </div>
      </div>
      

      <div class="form-group has-feedback">
        <label for="ownerName" class="control-label col-xs-3">Nombre del propietario de la tienda</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_STORE_OWNER_NAME', NULL, 'required aria-required="true" id="ownerName" placeholder="Your Name"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">El nombre del dueño de la tienda visible al público.</span>
        </div>
      </div>
      
      <div class="form-group has-feedback">
        <label for="ownerEmail" class="control-label col-xs-3">La Dirección de Correo Electrónico del Propietario de la tienda </label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_STORE_OWNER_EMAIL_ADDRESS', NULL, 'required aria-required="true" id="ownerEmail" placeholder="you@yours.com"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">La dirección de correo electrónico del dueño de la tienda visible al público.</span>
        </div>
      </div>
      
      <div class="form-group has-feedback">
        <label for="adminUsername" class="control-label col-xs-3">Nombre de usuario del administrador</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_ADMINISTRATOR_USERNAME', NULL, 'required aria-required="true" id="adminUsername" placeholder="Username"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">El nombre de usuario que el administrador utiliza para la herramienta de administración.</span>
        </div>
      </div>
      
      <div class="form-group has-feedback">
        <label for="adminPassword" class="control-label col-xs-3">Contraseña de administrador</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_ADMINISTRATOR_PASSWORD', NULL, 'required aria-required="true" id="adminPassword"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">La contraseña para la cuenta de administrador.</span>
        </div>
      </div>

<?php
  if (osc_is_writable($dir_fs_document_root) && osc_is_writable($dir_fs_document_root . 'admin')) {
?>
      <div class="form-group has-feedback">
        <label for="adminDir" class="control-label col-xs-3">Nombre del directorio de administración</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('CFG_ADMIN_DIRECTORY', 'admin', 'required aria-required="true" id="adminDir"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">Este es el directorio donde se instalará la sección de administración. Debe cambiarlo por razones de seguridad.</span>
        </div>
      </div>
<?php
  }

?>
      <div class="form-group has-feedback">
        <label for="Zulu" class="control-label col-xs-3">Zona horaria</label>
        <div class="col-xs-9">
          <?php echo osc_draw_time_zone_select_menu('CFG_TIME_ZONE'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">La zona horaria en la que se basa la fecha y la hora.</span>
        </div>
      </div>

      <p><?php echo osc_draw_button('Continuar con el paso 4', 'triangle-1-e', null, 'primary', null, 'btn-success btn-block'); ?></p>

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
        Paso 3: Configuración de la Tienda online
      </div>
      <div class="panel-body">
        <p>Aquí puede definir el nombre de su tienda online y la información de contacto del dueño de la tienda.</p>
        <p>El nombre de usuario y contraseña de administrador se utilizan para iniciar sesión en la sección de herramientas de administración protegida.</p>
      </div>
    </div>
  </div>
  
</div>
