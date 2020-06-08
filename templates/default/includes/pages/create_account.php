<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('create_account.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
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

    <h4><?php echo $customer_data_group['customer_data_groups_name']; ?></h4>

    <?php
    foreach ((array)$grouped_modules[$customer_data_group['customer_data_groups_id']] as $module) {
      $module->display_input($customer_details);
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
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
