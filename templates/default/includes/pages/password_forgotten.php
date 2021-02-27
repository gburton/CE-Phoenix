<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('login.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('password_forgotten.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('password_forgotten') > 0) {
    echo $messageStack->output('password_forgotten');
  }

  if ($password_reset_initiated == true) {
?>

  <div class="alert alert-success" role="alert"><?php echo TEXT_PASSWORD_RESET_INITIATED; ?></div>

<?php
  } else {
?>

<?php echo tep_draw_form('password_forgotten', tep_href_link('password_forgotten.php', 'action=process', 'SSL'), 'post', '', true); ?>

  <div class="alert alert-warning" role="alert"><?php echo TEXT_MAIN; ?></div>

  <?php
  $customer_data->display_input(['email_address']);
  ?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_RESET_PASSWORD, 'fas fa-user-cog', null, 'primary', null, 'btn-warning btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('login.php', '', 'SSL')); ?></p>
  </div>

</form>

<?php
  }

  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
