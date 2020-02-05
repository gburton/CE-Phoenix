<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  // if password is not enabled, then no reason to be on this page
  if (!$customer_data->has(['password'])) {
    tep_redirect(tep_href_link('index.php'));
  }

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_password.php";

  $page_fields = [ 'password', 'password_confirmation' ];
  $message_stack_area = 'account_password';

  if (tep_validate_form_action_is('process')) {
    $password_current = tep_db_prepare_input($_POST['password_current']);

    $customer_details = $customer_data->process($page_fields);

    if (!empty($customer_details)) {
      $check_customer_query = tep_db_query($customer_data->build_read(['password'], 'customers', ['id' => (int)$_SESSION['customer_id']]));
      $check_customer = tep_db_fetch_array($check_customer_query);

      if (tep_validate_password($password_current, $customer_data->get('password', $check_customer))) {
        $customer_data->update(['password' => $customer_data->get('password', $customer_details)], ['id' => (int)$_SESSION['customer_id']]);

        tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$_SESSION['customer_id']);

        $messageStack->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');

        tep_redirect(tep_href_link('account.php', '', 'SSL'));
      } else {
        $messageStack->add($message_stack_area, ERROR_CURRENT_PASSWORD_NOT_MATCHING);
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_password.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  echo tep_draw_form('account_password', tep_href_link('account_password.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process');
  echo tep_draw_hidden_field('username', $customer->get('username'), 'readonly autocomplete="username"');
?>

<div class="contentContainer">
  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

<?php
  $input_id = 'inputCurrent';
  $label_text = ENTRY_PASSWORD_CURRENT;
  $input = tep_draw_input_field('password_current', null, abstract_customer_data_module::REQUIRED_ATTRIBUTE . 'autofocus="autofocus" id="' . $input_id . '" autocomplete="current-password" placeholder="' . ENTRY_PASSWORD_CURRENT_TEXT . '"', 'password')
         . FORM_REQUIRED_INPUT;
  include $oscTemplate->map_to_template('includes/modules/customer_data/cd_whole_row_input.php');

  $customer_data->display_input($page_fields);
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
