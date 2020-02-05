<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/create_account.php";

  $message_stack_area = 'create_account';

  if (tep_validate_form_action_is('process')) {
    $customer_details = $customer_data->process();

    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');

    if (isset($customer_details) && tep_not_null($customer_details)) {
      $customer_data->create($customer_details);

      $OSCOM_Hooks->call('siteWide', 'postAccountCreation');
      $OSCOM_Hooks->call('siteWide', 'postLogin');

      if (SESSION_RECREATE == 'True') {
        tep_session_recreate();
      }

      $customer = new customer($customer_data->get('id', $customer_details));
      $_SESSION['customer_id'] = $customer->get_id();
      $customer_id =& $_SESSION['customers_id'];

      tep_reset_session_token();
      $cart->restore_contents();

      tep_notify('create_account', $customer);

      tep_redirect(tep_href_link('create_account_success.php', '', 'SSL'));
    }
  }

  $grouped_modules = $customer_data->get_grouped_modules();
  $customer_data_group_query = tep_db_query(<<<'EOSQL'
SELECT customer_data_groups_id, customer_data_groups_name
 FROM customer_data_groups
 WHERE language_id = 
EOSQL
    . (int)$languages_id . ' ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('create_account.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }
?>

<div class="alert alert-warning" role="alert">
  <div class="row">
    <div class="col-sm-9"><?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link('login.php', tep_get_all_get_params(), 'SSL')); ?></div>
    <div class="col-sm-3 text-left text-sm-right"><span class="text-danger"><?php echo FORM_REQUIRED_INFORMATION; ?></span></div>
  </div>
</div>

<?php echo tep_draw_form('create_account', tep_href_link('create_account.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">
<?php
  while ($customer_data_group = tep_db_fetch_array($customer_data_group_query)) {
    if (empty($grouped_modules[$customer_data_group['customer_data_groups_id']])) {
      continue;
    }
?>
  <h2 class="h4"><?php echo $customer_data_group['customer_data_groups_name']; ?></h2>
<?php
    foreach ((array)$grouped_modules[$customer_data_group['customer_data_groups_id']] as $module) {
      $module->display_input();
    }
  }

  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-user', null, 'primary', null, 'btn-success btn-block btn-lg'); ?></div>
  </div>

</div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
