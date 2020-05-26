<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('store_logo', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $error = false;

        $store_logo = new upload('store_logo');
        $store_logo->set_extensions(['png', 'gif', 'jpg', 'svg', 'webp']);
        $store_logo->set_destination(DIR_FS_CATALOG_IMAGES);

        if ($store_logo->parse()) {
          if ($store_logo->save()) {
            $messageStack->add_session(SUCCESS_LOGO_UPDATED, 'success');
            tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($store_logo->filename) . "' WHERE configuration_value = '" . STORE_LOGO . "'");
            
            $OSCOM_Hooks->call('store_logo', 'saveAction');
            
          } else {
            $error = true;
          }
        } else {
          $error = true;
        }

        if ($error == false) {
          tep_redirect(tep_href_link('store_logo.php'));
        }
        break;
    }
  }
  
  $OSCOM_Hooks->call('store_logo', 'postAction');

  if (!tep_is_writable(DIR_FS_CATALOG_IMAGES)) {
    $messageStack->add(sprintf(ERROR_IMAGES_DIRECTORY_NOT_WRITEABLE, tep_href_link('sec_dir_permissions.php')), 'error');
  }

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  
  <div class="table-responsive">
    <table class="table border-bottom">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_LOGO; ?></th>
          <th><?php echo TABLE_HEADING_LOCATION; ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES .  STORE_LOGO); ?></td>
          <td><?php echo DIR_FS_CATALOG_IMAGES .  STORE_LOGO; ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <h2 class="display-4 mb-2"><?php echo HEADING_NEW_LOGO; ?></h2>
  
  <div class="alert alert-danger"><?php echo TEXT_FORMAT_AND_LOCATION; ?></div>
  
  <?php echo tep_draw_form('logo', 'store_logo.php', 'action=save', 'post', 'enctype="multipart/form-data"'); ?>

    <div class="custom-file mb-2">
      <?php echo tep_draw_input_field('store_logo', '', 'required="required" aria-required="true" id="inputLogo"', 'file', null, 'class="form-control-input"'); ?>
      <label class="custom-file-label" for="inputLogo"><?php echo TEXT_LOGO_IMAGE; ?></label>
    </div>
    
    <?php 
    echo $OSCOM_Hooks->call('store_logo', 'editForm');
    
    echo tep_draw_bootstrap_button(IMAGE_UPLOAD, 'fas fa-file-upload', null, 'primary', null, 'btn-danger btn-block'); 
    ?>    

  </form>

  <script>$(document).on('change', '#inputLogo', function (event) { $(this).next('.custom-file-label').html(event.target.files[0].name); });</script>  

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>


