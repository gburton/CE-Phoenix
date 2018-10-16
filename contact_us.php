<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/contact_us.php');

  if (isset($_GET['action']) && ($_GET['action'] == 'send') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $error = false;

    $name = tep_db_prepare_input($_POST['name']);
    $email_address = tep_db_prepare_input($_POST['email']);
    $enquiry = tep_db_prepare_input($_POST['enquiry']);

    if (!tep_validate_email($email_address)) {
      $error = true;

      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }

    $actionRecorder = new actionRecorder('ar_contact_us', (tep_session_is_registered('customer_id') ? $customer_id : null), $name);
    if (!$actionRecorder->canPerform()) {
      $error = true;

      $actionRecorder->record(false);

      $messageStack->add('contact', sprintf(ERROR_ACTION_RECORDER, (defined('MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES : 15)));
    }

    if ($error == false) {
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_SUBJECT, $enquiry, $name, $email_address);

      $actionRecorder->record();

      tep_redirect(tep_href_link('contact_us.php', 'action=success'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('contact_us.php'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('contact') > 0) {
    echo $messageStack->output('contact');
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?>

<div class="contentContainer">
  <div class="alert alert-info"><?php echo TEXT_SUCCESS; ?></div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>
</div>

<?php
  } else {
?>

<?php echo tep_draw_form('contact_us', tep_href_link('contact_us.php', 'action=send'), 'post', '', true); ?>

<div class="contentContainer">
  
  <div class="row">
    <?php echo $oscTemplate->getContent('contact_us'); ?>
  </div>

  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>
  <div class="clearfix"></div>
  
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
      echo tep_draw_input_field('email', NULL, 'required aria-required="true" id="inputFromEmail" placeholder="' . ENTRY_EMAIL_ADDRESS_TEXT . '"', 'email');
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

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-block btn-lg'); ?></div>
  </div>
  
</div>

</form>

<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
