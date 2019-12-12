<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $error = false;

        $store_logo = new upload('store_logo');
        $store_logo->set_extensions(array('png', 'gif', 'jpg', 'svg', 'webp'));
        $store_logo->set_destination(DIR_FS_CATALOG_IMAGES);

        if ($store_logo->parse()) {
          if ($store_logo->save()) {
            $messageStack->add_session(SUCCESS_LOGO_UPDATED, 'success');
            tep_db_query("update configuration set configuration_value = '" . tep_db_input($store_logo->filename) . "' where configuration_value = '" . STORE_LOGO . "'");
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

  if (!tep_is_writable(DIR_FS_CATALOG_IMAGES)) {
    $messageStack->add(sprintf(ERROR_IMAGES_DIRECTORY_NOT_WRITEABLE, tep_href_link('sec_dir_permissions.php')), 'error');
  }

  require('includes/template_top.php');
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
    
    <div class="form-group row">
      <label for="inputLogo" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_LOGO_IMAGE; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_input_field('store_logo', '', 'required aria-required="true" class="form-control-file mt-2" id="inputLogo"', null, 'file'); ?>
      </div>
    </div>
    
    <?php echo tep_draw_bootstrap_button(IMAGE_UPLOAD, 'fas fa-file-upload', null, 'primary', null, 'btn-danger btn-block btn-lg'); ?>

  </form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
