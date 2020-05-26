<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');

  $message_stack_area = 'account_edit';
// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_edit.php";

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('account_edit'));
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (tep_form_processing_is_valid()) {
      $customer_data->update($customer_details, ['id' => $customer->get_id()], 'customers');
      tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$customer->get_id());

      $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_edit.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  echo tep_draw_form('account_edit', tep_href_link('account_edit.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process');
?>

  <div class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></div>

  <?php
  $customer_data->display_input($customer_data->get_fields_for_page('account_edit'), $customer->fetch_to_address());
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
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
