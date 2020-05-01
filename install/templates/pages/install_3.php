<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

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
          <li class="text-muted">Web Server</li>
          <li class="text-success"><strong>Online Store Settings</strong></li>
          <li class="text-muted">Finished!</li>
        </ol>
      </div>
      <div class="card-footer">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">75%</div>
        </div>
      </div>
    </div>    
  </div>
</div>

<div class="w-100"></div>

<div class="row">
  <div class="col-12 col-sm-9">
    <h4>Online Store Settings</h4>
    <p class="text-danger pull-right text-right"><i class="fas fa-asterisk text-danger"></i> Required information</p>

    <form name="install" id="installForm" action="install.php?step=4" method="post" role="form">

      <div class="form-group row">
        <label for="storeName" class="col-form-label col-sm-3 text-left text-sm-right">Store Name</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_STORE_NAME', NULL, 'required aria-required="true" id="storeName" placeholder="Your Store Name"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The name of the online store that is presented to the public.</small>
        </div>
      </div>
      

      <div class="form-group row">
        <label for="ownerName" class="col-form-label col-sm-3 text-left text-sm-right">Store Owner Name</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_STORE_OWNER_NAME', NULL, 'required aria-required="true" id="ownerName" placeholder="Your Name"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The name of the store owner that is presented to the public.</small>
        </div>
      </div>
      
      <div class="form-group row">
        <label for="ownerEmail" class="col-form-label col-sm-3 text-left text-sm-right">Store Owner E-Mail Address</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_STORE_OWNER_EMAIL_ADDRESS', NULL, 'required aria-required="true" id="ownerEmail" placeholder="you@yours.com"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The e-mail address of the store owner that is presented to the public.</small>
        </div>
      </div>
      
      <div class="form-group row">
        <label for="adminUsername" class="col-form-label col-sm-3 text-left text-sm-right">Administrator Username</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_ADMINISTRATOR_USERNAME', NULL, 'required aria-required="true" id="adminUsername" placeholder="Username"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The administrator username to use for the administration tool.</small>
        </div>
      </div>
      
      <div class="form-group row">
        <label for="adminPassword" class="col-form-label col-sm-3 text-left text-sm-right">Administrator Password</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_ADMINISTRATOR_PASSWORD', NULL, 'required aria-required="true" id="adminPassword"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The password to use for the administrator account.</small>
        </div>
      </div>

<?php
  if (osc_is_writable($dir_fs_document_root) && osc_is_writable($dir_fs_document_root . 'admin')) {
?>
      <div class="form-group row">
        <label for="adminDir" class="col-form-label col-sm-3 text-left text-sm-right">Administration Directory Name</label>
        <div class="col-sm-9">
          <?php echo osc_draw_input_field('CFG_ADMIN_DIRECTORY', 'admin', 'required aria-required="true" id="adminDir"'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">This is the directory where the administration section will be installed. You should change this for security reasons.</small>
        </div>
      </div>
<?php
  }

?>
      <div class="form-group row">
        <label for="Zulu" class="col-form-label col-sm-3 text-left text-sm-right">Time Zone</label>
        <div class="col-sm-9">
          <?php echo osc_draw_time_zone_select_menu('CFG_TIME_ZONE'); ?>
          <i class="fas fa-asterisk form-control-feedback text-danger"></i>
          <small class="form-text text-muted">The time zone to base the date and time on.</small>
        </div>
      </div>

      <p><?php echo osc_draw_button('Continue To Step 4', '<i class="fas fa-angle-right mr-2"></i>', null, 'primary', null, 'btn-success btn-block'); ?></p>

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
    <h4>Step 3</h4>
    <div class="card mb-2 card-body">      
      <p>Here you can define the name of your online store and the contact information for the store owner.  These can be changed in the admin interface later on.</p>
      <p>The administrator username and password are used to log into the secure administration tool section.</p>
    </div>
  </div>  
</div>
