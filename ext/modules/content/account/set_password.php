<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if ( MODULE_CONTENT_ACCOUNT_SET_PASSWORD_ALLOW_PASSWORD != 'True' ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $check_customer_query = tep_db_query("select customers_password from customers where customers_id = '" . (int)$customer_id . "'");
  $check_customer = tep_db_fetch_array($check_customer_query);

  if ( !empty($check_customer['customers_password']) ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/modules/content/account/cm_account_set_password.php');

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $password_new = tep_db_prepare_input($_POST['password_new']);
    $password_confirmation = tep_db_prepare_input($_POST['password_confirmation']);

    $error = false;

    if (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR);
    } elseif ($password_new != $password_confirmation) {
      $error = true;

      $messageStack->add('account_password', ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING);
    }

    if ($error == false) {
      tep_db_query("update customers set customers_password = '" . tep_encrypt_password($password_new) . "' where customers_id = '" . (int)$customer_id . "'");

      tep_db_query("update customers_info set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customer_id . "'");

      $messageStack->add_session('account', MODULE_CONTENT_ACCOUNT_SET_PASSWORD_SUCCESS_PASSWORD_SET, 'success');

      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  }

  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_SET_PASSWORD_NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_SET_PASSWORD_NAVBAR_TITLE_2, tep_href_link('ext/modules/content/account/set_password.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<div class="page-header">
  <h1 class="h3"><?php echo MODULE_CONTENT_ACCOUNT_SET_PASSWORD_HEADING_TITLE; ?></h1>
</div>

<?php
  if ($messageStack->size('account_password') > 0) {
    echo $messageStack->output('account_password');
  }
?>

<?php echo tep_draw_form('account_password', tep_href_link('ext/modules/content/account/set_password.php', '', 'SSL'), 'post', 'class="form-horizontal"', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">
  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

  <div class="contentText">
    <div class="form-group has-feedback">
      <label for="inputPassword" class="control-label col-sm-3"><?php echo ENTRY_PASSWORD_NEW; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_input_field('password_new', NULL, 'required aria-required="true" autofocus="autofocus" id="inputPassword" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_NEW_TEXT . '"', 'password'); ?>
        <?php echo FORM_REQUIRED_INPUT; ?>
      </div>
    </div>
    <div class="form-group has-feedback">
      <label for="inputConfirmation" class="control-label col-sm-3"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_input_field('password_confirmation', NULL, 'required aria-required="true" id="inputConfirmation" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '"', 'password'); ?>
        <?php echo FORM_REQUIRED_INPUT; ?>
      </div>
    </div>
  </div>

  <div class="buttonSet row">
    <div class="col-xs-6"><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></div>
    <div class="col-xs-6 text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', null, 'primary', null, 'btn-success'); ?></div>
  </div>
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
