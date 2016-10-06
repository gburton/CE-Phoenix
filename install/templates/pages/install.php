<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<script>
<!--

  var dbServer;
  var dbUsername;
  var dbPassword;
  var dbName;

  var formSubmited = false;
  var formSuccess = false;

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    $('#mBox').show();

    $('#mBoxContents').html('<p><i class="fa fa-spinner fa-spin fa-2x"></i> Comprobando la conexíon con la base de datos..</p>');

    dbServer = $('#DB_SERVER').val();
    dbUsername = $('#DB_SERVER_USERNAME').val();
    dbPassword = $('#DB_SERVER_PASSWORD').val();
    dbName = $('#DB_DATABASE').val();

    $.get('rpc.php?action=dbCheck&server=' + encodeURIComponent(dbServer) + '&username=' + encodeURIComponent(dbUsername) + '&password=' + encodeURIComponent(dbPassword) + '&name=' + encodeURIComponent(dbName), function (response) {
      var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(response);
      result.shift();

      if (result[0] == '1') {
        $('#mBoxContents').html('<p><i class="fa fa-spinner fa-spin fa-2x"></i> La estructura de la base de datos se está importando. Por favor, sea paciente durante este procedimiento.</p>');

        $.get('rpc.php?action=dbImport&server=' + encodeURIComponent(dbServer) + '&username=' + encodeURIComponent(dbUsername) + '&password='+ encodeURIComponent(dbPassword) + '&name=' + encodeURIComponent(dbName), function (response2) {
          var result2 = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(response2);
          result2.shift();

          if (result2[0] == '1') {
            $('#mBoxContents').html('<p class="text-success"><i class="fa fa-thumbs-up fa-2x"></i> La base de datos ha sido importada con éxito.</p>');

            formSuccess = true;

            setTimeout(function() {
              $('#installForm').submit();
            }, 2000);
          } else {
            var result2_error = result2[1].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

            $('#mBoxContents').html('<p class="text-danger"><i class="fa fa-thumbs-down fa-2x text-danger"></i> Ha ocurrido un problema al importar la base de datos. Se ha producido el siguiente error:</p><p  class="text-danger"><strong>%s</strong></p><p class="text-danger">Por favor, compruebe los parámetros de conexión y vuelva a intentarlo.</p>'.replace('%s', result2_error));

            formSubmited = false;
          }
        }).fail(function() {
          formSubmited = false;
        });
      } else {
        var result_error = result[1].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

        $('#mBoxContents').html('<p class="text-danger"><i class="fa fa-thumbs-down fa-2x text-danger"></i> Ha ocurrido un problema al conectar con el servidor de la base de datos. Se ha producido el siguiente error:</p><p class="text-danger"><strong>%s</strong></p><p class="text-danger">Por favor, compruebe los parámetros de conexión y vuelva a intentarlo.</p></div>'.replace('%s', result_error));

        formSubmited = false;
      }
    }).fail(function() {
      formSubmited = false;
    });
  }

  $(function() {
    $('#installForm').submit(function(e) {
      if ( formSuccess == false ) {
        e.preventDefault();

        prepareDB();
      }
    });
  });

//-->
</script>
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
          <li class="text-success"><strong>Servidor de base de la datos</strong></li>
          <li class="text-muted">Servidor web</li>
          <li class="text-muted">Configuración de la Tienda Online</li>
          <li class="text-muted">Terminado!</li>
        </ol>
      </div>
    </div>
    <div class="progress">
      <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%">25%</div>
    </div>
  </div>
</div>
  
<div class="clearfix"></div>

<div class="row">
  <div class="col-xs-12 col-sm-push-3 col-sm-9">

    <div id="mBox">
      <div class="well well-sm">
        <div id="mBoxContents"></div>
      </div>
    </div>
    
    <div class="page-header">
      <p class="text-danger pull-right text-right"><span class="fa fa-asterisk text-danger"></span> Información requerida</p>
      <h2>Servidor de la Base de Datos</h2>
    </div>
    
    <form name="install" id="installForm" action="install.php?step=2" method="post" class="form-horizontal" role="form">
    
      <div class="form-group has-feedback">
        <label for="dbServer" class="control-label col-xs-3">Servidor de la Base de Datos</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('DB_SERVER', NULL, 'required aria-required="true" id="dbServer" placeholder="localhost"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">La dirección del servidor de la base de datos en forma de una dirección IP o nombre de host.</span>
        </div>
      </div>
    
      <div class="form-group has-feedback">
        <label for="userName" class="control-label col-xs-3">Usuario</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('DB_SERVER_USERNAME', NULL, 'required aria-required="true" id="userName" placeholder="Username"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">El nombre de usuario utilizado para conectarse al servidor de la base de datos.</span>
        </div>
      </div>
    
      <div class="form-group has-feedback">
        <label for="passWord" class="control-label col-xs-3">Contraseña</label>
        <div class="col-xs-9">
          <?php echo osc_draw_password_field('DB_SERVER_PASSWORD', NULL, 'required aria-required="true" id="passWord"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">La contraseña que se utiliza junto con el nombre de usuario para conectarse al servidor de la base de datos.</span>
        </div>
      </div>
    
      <div class="form-group has-feedback">
        <label for="dbName" class="control-label col-xs-3">Nombre de la base de datos</label>
        <div class="col-xs-9">
          <?php echo osc_draw_input_field('DB_DATABASE', NULL, 'required aria-required="true" id="dbName" placeholder="Database"'); ?>
          <span class="fa fa-asterisk form-control-feedback text-danger"></span>
          <span class="help-block">El nombre de la base de datos en la que se almacenan los datos.</span>
        </div>
      </div>

      <p><?php echo osc_draw_button('Continuar con el paso 2', 'triangle-1-e', null, 'primary', null, 'btn-success btn-block'); ?></p>

    </form>
    
  </div>
  <div class="col-xs-12 col-sm-pull-9 col-sm-3">
    <div class="panel panel-success">
      <div class="panel-heading">
        Paso 1: Servidor de la Base de Datos
      </div>
      <div class="panel-body">
        <p>El servidor de la base de datos almacena el contenido de la tienda online, tales como información de productos, información de clientes y los pedidos que se han hecho.</p>
        <p>Por favor, consulte a su administrador del servidor si  todavía no se sabe los parámetros del servidor de las bases de datos.</p>
      </div>
    </div>
  </div>
  
</div>
