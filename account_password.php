<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/account_password.php');

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $password_current = tep_db_prepare_input($_POST['password_current']);
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
      $check_customer_query = tep_db_query("select customers_password from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
      $check_customer = tep_db_fetch_array($check_customer_query);

      if (tep_validate_password($password_current, $check_customer['customers_password'])) {
        tep_db_query("update " . TABLE_CUSTOMERS . " set customers_password = '" . tep_encrypt_password($password_new) . "' where customers_id = '" . (int)$customer_id . "'");

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customer_id . "'");

        $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

        tep_redirect(tep_href_link('account.php', '', 'SSL'));
      } else {
        $error = true;

        $messageStack->add('account_password', ERROR_CURRENT_PASSWORD_NOT_MATCHING);
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_password.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('account_password') > 0) {
    echo $messageStack->output('account_password');
  }
?>

<?php echo tep_draw_form('account_password', tep_href_link('account_password.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<?php
$customer_info_query = tep_db_query("select customers_email_address from customers where customers_id = '" . (int)$customer_id . "'");
$customer_info = tep_db_fetch_array($customer_info_query);
echo tep_draw_hidden_field('username', $customer_info['customers_email_address'], 'readonly autocomplete="username"'); 
?>
    
<div class="contentContainer">
  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

  <div class="form-group row">
    <label for="inputCurrent" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PASSWORD_CURRENT; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('password_current', NULL, 'required aria-required="true" autofocus="autofocus" id="inputCurrent" autocomplete="current-password" placeholder="' . ENTRY_PASSWORD_CURRENT_TEXT . '"', 'password'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
    
  <div class="form-group row">
    <label for="inputPassword" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PASSWORD_NEW; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('password_new', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_NEW_TEXT . '"', 'password'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="inputConfirmation" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('password_confirmation', NULL, 'required aria-required="true" id="inputConfirmation" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '"', 'password'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
    
  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
