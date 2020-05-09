<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $www_location = 'http://' . $_SERVER['HTTP_HOST'];

  if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strpos($www_location, 'install'));

  $dir_fs_www_root = osc_realpath(dirname(__FILE__) . '/../../../') . '/';
?>


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
          <li class="text-muted">Database Server</li>
          <li class="text-success"><strong>Web Server</strong></li>
          <li class="text-muted">Online Store Settings</li>
          <li class="text-muted">Finished!</li>
        </ol>
      </div>
      <div class="card-footer">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">50%</div>
        </div>
      </div>
    </div>    
  </div>
</div>

<div class="w-100"></div>

<div class="row">
  <div class="col-12 col-sm-9">
    <h4>Web Server</h4>
    <p class="text-danger pull-right text-right"><i class="fas fa-asterisk text-danger"></i> Required information</p>

    <form name="install" id="installForm" action="install.php?step=3" method="post" role="form">

      <div class="form-group row">
        <label for="wwwAddress" class="col-form-label col-sm-3 text-left text-sm-right">WWW Address</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('HTTP_WWW_ADDRESS', $www_location, 'required aria-required="true" id="wwwAddress" placeholder="http://"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The web address (URL) of your online store.</small>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="webRoot" class="col-form-label col-sm-3 text-left text-sm-right">Webserver Root Directory</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root, 'required aria-required="true" id="webRoot"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The directory where the online store is installed on the server.</small>
        </div>
      </div>

      <p><?php echo osc_draw_button('Continue To Step 3', '<i class="fas fa-angle-right mr-2"></i>', null, 'primary', null, 'btn-success btn-block'); ?></p>

      <?php
      foreach ( $_POST as $key => $value ) {
        if (($key != 'x') && ($key != 'y')) {
          echo osc_draw_hidden_field($key, $value);
        }
      }
      ?>

    </form>
  </div>
  <div class="col-12 col-sm-3">
    <h4>Step 2</h4>
    <div class="card mb-2 card-body">      
      <p>The web server takes care of serving the pages of your online store to your guests and customers. The web server parameters make sure the links to the pages point to the correct location.</p>
    </div>
  </div>
  
</div>
