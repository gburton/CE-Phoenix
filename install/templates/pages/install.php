<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>

<script>
<!--

  var dbServer;
  var dbUsername;
  var dbPassword;
  var dbName;
  var dbImportSample;

  var formSubmited = false;
  var formSuccess = false;

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    $('.mBox').show();

    $('.mBoxContents').html('<div class="alert alert-warning"><i class="fas fa-spinner fa-spin fa-2x"></i> Testing database connection..</div>');

    dbServer = $('#DB_SERVER').val();
    dbUsername = $('#DB_SERVER_USERNAME').val();
    dbPassword = $('#DB_SERVER_PASSWORD').val();
    dbName = $('#DB_DATABASE').val();
    dbImportSample = $('#DB_IMPORT_SAMPLE').val();

    $.get('rpc.php?action=dbCheck&server=' + encodeURIComponent(dbServer) + '&username=' + encodeURIComponent(dbUsername) + '&password=' + encodeURIComponent(dbPassword) + '&name=' + encodeURIComponent(dbName), function (response) {
      var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(response);
      result.shift();

      if (result[0] == '1') {
        $('.mBoxContents').html('<div class="alert alert-success"><i class="fas fa-spinner fa-spin fa-2x"></i> The database structure is now being imported. Please be patient during this procedure.</div>');

        $.get('rpc.php?action=dbImport&server=' + encodeURIComponent(dbServer) + '&username=' + encodeURIComponent(dbUsername) + '&password='+ encodeURIComponent(dbPassword) + '&name=' + encodeURIComponent(dbName) + '&importsample=' + encodeURIComponent(dbImportSample), function (response2) {      
          var result2 = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(response2);
          result2.shift();

          if (result2[0] == '1') {
            $('.mBoxContents').html('<div class="alert alert-success"><i class="fas fa-thumbs-up fa-2x"></i> Database imported successfully.</div>');

            formSuccess = true;

            setTimeout(function() {
              $('#installForm').submit();
            }, 2000);
          } else {
            var result2_error = result2[1].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

            $('.mBoxContents').html('<div class="alert alert-danger"><p><i class="fas fa-thumbs-down fa-2x text-danger"></i> There was a problem importing the database. The following error had occured:</p><p  class="text-danger"><strong>%s</strong></p><p class="text-danger">Please verify the connection parameters and try again.</p></div>'.replace('%s', result2_error));

            formSubmited = false;
          }
        }).fail(function() {
          formSubmited = false;
        });
      } else {
        var result_error = result[1].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

        $('.mBoxContents').html('<div class="alert alert-danger"><p><i class="fas fa-thumbs-down fa-2x text-danger"></i> There was a problem connecting to the database server. The following error had occured:</p><p class="text-danger"><strong>%s</strong></p><p class="text-danger">Please verify the connection parameters and try again.</p></div>'.replace('%s', result_error));

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
    <div class="alert alert-info" role="alert">
      <h1>New Installation</h1>

      <p>This web-based installation routine will setup and configure <strong>Phoenix v<?php echo osc_get_version(); ?></strong> to run on this server.</p>
      <p>Please follow the on-screen instructions that will take you through the database server, web server, and store configuration options. If help is needed at any stage, please consult the documentation or seek help in the Phoenix Club.</p>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card mb-2">
      <div class="card-body">
        <ol>
          <li class="text-success"><strong>Database Server</strong></li>
          <li class="text-muted">Web Server</li>
          <li class="text-muted">Online Store Settings</li>
          <li class="text-muted">Finished!</li>
        </ol>
      </div>
      <div class="card-footer">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%">25%</div>
        </div>
      </div>
    </div>
    
  </div>
</div>
  
<div class="w-100"></div>

<div class="row">
  <div class="col-12 col-sm-9">

    <div class="mBox">
      <div class="mBoxContents"></div>
    </div>
    
    <h4>Database Server</h4>
    <p class="text-danger pull-right text-right"><i class="fas fa-asterisk text-danger"></i> Required information</p>

    <form name="install" id="installForm" action="install.php?step=2" method="post" role="form">
    
      <div class="form-group row">
        <label for="dbServer" class="col-form-label col-sm-3 text-left text-sm-right">Database Server</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('DB_SERVER', NULL, 'required aria-required="true" id="dbServer" placeholder="localhost"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The address of the database server in the form of a hostname or IP address.</small>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="userName" class="col-form-label col-sm-3 text-left text-sm-right">Username</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('DB_SERVER_USERNAME', NULL, 'required aria-required="true" id="userName" placeholder="Username"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The username used to connect to the database server.</small>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="passWord" class="col-form-label col-sm-3 text-left text-sm-right">Password</label>
        <div class="col-sm-9">
          <?php echo osc_draw_password_field('DB_SERVER_PASSWORD', NULL, 'required aria-required="true" id="passWord"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The password that is used together with the username to connect to the database server.</small>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="dbName" class="col-form-label col-sm-3 text-left text-sm-right">Database Name</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('DB_DATABASE', NULL, 'required aria-required="true" id="dbName" placeholder="Database"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The name of the database to hold the data in.</small>
        </div>
      </div>
      
      <div class="form-group row">
        <label for="dbName" class="col-form-label col-sm-3 text-left text-sm-right">Import Sample Data</label>
        <div class="col-sm-9">
          <?php echo osc_draw_select_menu('DB_IMPORT_SAMPLE', [['id' => '0', 'text' => 'Skip sample data'], ['id' => '1', 'text' => 'Import sample data']], '1'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">Import sample product and category data?</small>
        </div>
      </div>
      
      <div class="mBox">
        <div class="mBoxContents"></div>
      </div>
      
      <p><?php echo osc_draw_button('Continue To Step 2', '<i class="fas fa-angle-right mr-2"></i>', null, 'primary', null, 'btn-success btn-block'); ?></p>

    </form>    
  </div>
  <div class="col-12 col-sm-3">
    <h4>Step 1</h4>
    <div class="card card-body">      
      <p>The database server stores data such as product information, customer information, and the orders that have been made.</p>
      <p>Please consult your server administrator (host) if your database server parameters are not yet known.</p>
    </div>
  </div>
  
</div>
