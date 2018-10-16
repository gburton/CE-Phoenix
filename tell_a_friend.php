<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id') && (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
    $product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
    if (tep_db_num_rows($product_info_query)) {
      $valid_product = true;

      $product_info = tep_db_fetch_array($product_info_query);
    }
  }

  if ($valid_product == false) {
    tep_redirect(tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
  }

  require('includes/languages/' . $language . '/tell_a_friend.php');

  if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $error = false;

    $to_email_address = tep_db_prepare_input($_POST['to_email_address']);
    $to_name = tep_db_prepare_input($_POST['to_name']);
    $from_email_address = tep_db_prepare_input($_POST['from_email_address']);
    $from_name = tep_db_prepare_input($_POST['from_name']);
    $message = tep_db_prepare_input($_POST['message']);

    if (empty($from_name)) {
      $error = true;

      $messageStack->add('friend', ERROR_FROM_NAME);
    }

    if (!tep_validate_email($from_email_address)) {
      $error = true;

      $messageStack->add('friend', ERROR_FROM_ADDRESS);
    }

    if (empty($to_name)) {
      $error = true;

      $messageStack->add('friend', ERROR_TO_NAME);
    }

    if (!tep_validate_email($to_email_address)) {
      $error = true;

      $messageStack->add('friend', ERROR_TO_ADDRESS);
    }

    $actionRecorder = new actionRecorder('ar_tell_a_friend', (tep_session_is_registered('customer_id') ? $customer_id : null), $from_name);
    if (!$actionRecorder->canPerform()) {
      $error = true;

      $actionRecorder->record(false);

      $messageStack->add('friend', sprintf(ERROR_ACTION_RECORDER, (defined('MODULE_ACTION_RECORDER_TELL_A_FRIEND_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_TELL_A_FRIEND_EMAIL_MINUTES : 15)));
    }

    if ($error == false) {
      $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $from_name, STORE_NAME);
      $email_body = sprintf(TEXT_EMAIL_INTRO, $to_name, $from_name, $product_info['products_name'], STORE_NAME) . "\n\n";

      if (tep_not_null($message)) {
        $email_body .= $message . "\n\n";
      }

      $email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'], 'NONSSL', false)) . "\n\n" .
                     sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

      tep_mail($to_name, $to_email_address, $email_subject, $email_body, $from_name, $from_email_address);

      $actionRecorder->record();

      $messageStack->add_session('header', sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $product_info['products_name'], tep_output_string_protected($to_name)), 'success');

      tep_redirect(tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
    }
  } elseif (tep_session_is_registered('customer_id')) {
    $account_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
    $account = tep_db_fetch_array($account_query);

    $from_name = $account['customers_firstname'] . ' ' . $account['customers_lastname'];
    $from_email_address = $account['customers_email_address'];
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('tell_a_friend.php', 'products_id=' . (int)$_GET['products_id']));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h1>

<?php
  if ($messageStack->size('friend') > 0) {
    echo $messageStack->output('friend');
  }
?>

<?php echo tep_draw_form('email_friend', tep_href_link('tell_a_friend.php', 'action=process&products_id=' . (int)$_GET['products_id']), 'post', '', true); ?>

<div class="contentContainer">

  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

  <h4><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></h4>

  <div class="form-group row">
    <label for="inputFromName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('from_name', NULL, 'required aria-required="true" id="inputFromName" placeholder="' . FORM_FIELD_CUSTOMER_NAME . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputFromEmail" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('from_email_address', NULL, 'required aria-required="true" id="inputFromEmail" placeholder="' . FORM_FIELD_CUSTOMER_EMAIL . '"', 'email');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <h4><?php echo FORM_TITLE_FRIEND_DETAILS; ?></h4>

  <div class="form-group row">
    <label for="inputToName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo FORM_FIELD_FRIEND_NAME; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('to_name', NULL, 'required aria-required="true" id="inputToName" placeholder="' . FORM_FIELD_FRIEND_NAME . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputToEmail" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('to_email_address', NULL, 'required aria-required="true" id="inputToEmail" placeholder="' . FORM_FIELD_FRIEND_EMAIL . '"', 'email');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <hr>

  <div class="form-group row">
    <label for="inputMessage" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo FORM_TITLE_FRIEND_MESSAGE; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_textarea_field('message', 'soft', 40, 8, NULL, 'required aria-required="true" id="inputMessage" placeholder="' . FORM_TITLE_FRIEND_MESSAGE . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'])); ?></p>
  </div>
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
