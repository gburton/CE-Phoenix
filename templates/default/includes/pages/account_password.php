<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_password.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  echo tep_draw_form('account_password', tep_href_link('account_password.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process');
  echo tep_draw_hidden_field('username', $customer->get('username'), 'readonly autocomplete="username"');
?>

  <p class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

  <?php
  $input_id = 'inputCurrent';
  $label_text = ENTRY_PASSWORD_CURRENT;
  $input = tep_draw_input_field('password_current', null,
    abstract_customer_data_module::REQUIRED_ATTRIBUTE
      . 'autofocus="autofocus" autocapitalize="none" id="'
      . $input_id . '" autocomplete="current-password" placeholder="' . ENTRY_PASSWORD_CURRENT_TEXT . '"',
    'password') . FORM_REQUIRED_INPUT;
  
  include $oscTemplate->map_to_template('includes/modules/customer_data/cd_whole_row_input.php');

  $customer_data->display_input($page_fields);
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
