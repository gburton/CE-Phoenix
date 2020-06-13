<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('contact_us.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('contact') > 0) {
    echo $messageStack->output('contact');
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?>

  <div class="alert alert-info" role="alert"><?php echo TEXT_SUCCESS; ?></div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', tep_href_link('index.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>

<?php
  } else {
    echo tep_draw_form('contact_us', tep_href_link('contact_us.php', 'action=send'), 'post', '', true); 
    ?>
 
  <div class="row">
    <?php echo $oscTemplate->getContent('contact_us'); ?>
  </div>

  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>
  <div class="w-100"></div>
  
  <div class="form-group row">
    <label for="inputFromName" class="col-sm-3 col-form-label text-right"><?php echo ENTRY_NAME; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('name', NULL, 'required aria-required="true" id="inputFromName" placeholder="' . ENTRY_NAME_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="inputFromEmail" class="col-sm-3 col-form-label text-right"><?php echo ENTRY_EMAIL; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('email', NULL, 'required aria-required="true" id="inputFromEmail" placeholder="' . ENTRY_EMAIL_TEXT . '"', 'email');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <div class="form-group row">
    <label for="inputEnquiry" class="col-sm-3 col-form-label text-right"><?php echo ENTRY_ENQUIRY; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_textarea_field('enquiry', 'soft', 50, 15, NULL, 'required aria-required="true" id="inputEnquiry" placeholder="' . ENTRY_ENQUIRY_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>    

  <?php
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
  ?>
  
  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-block btn-lg'); ?></div>
  </div>

</form>

<?php
  }

  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
