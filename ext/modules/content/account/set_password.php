<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) {
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if ( MODULE_CONTENT_ACCOUNT_SET_PASSWORD_ALLOW_PASSWORD != 'True' ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  if (!$customer_data->has(['password'])) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $check_customer_query = tep_db_query($customer_data->build_read(['password'], 'both', ['id' => (int)$_SESSION['customer_id']]));
  $check_customer = tep_db_fetch_array($check_customer_query);

  // only allow to set the password when it is blank
  if ( !empty($customer_data->get('password', $check_customer)) ) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/modules/content/account/cm_account_set_password.php";

  $page_fields = ['password', 'password_confirmation'];

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);

    if (tep_form_processing_is_valid()) {
      $customer_data->update(['password' => $customer_data->get('password', $customer_details)], ['id' => (int)$_SESSION['customer_id']]);

      tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$_SESSION['customer_id']);

      $messageStack->add_session('account', MODULE_CONTENT_ACCOUNT_SET_PASSWORD_SUCCESS_PASSWORD_SET, 'success');

      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  }

  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_SET_PASSWORD_NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_SET_PASSWORD_NAVBAR_TITLE_2, tep_href_link('ext/modules/content/account/set_password.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo MODULE_CONTENT_ACCOUNT_SET_PASSWORD_HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('account_password') > 0) {
    echo $messageStack->output('account_password');
  }

  echo tep_draw_form('account_password', tep_href_link('ext/modules/content/account/set_password.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process');
?>

  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

<?php
  $customer_data->display_input($page_fields);
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
