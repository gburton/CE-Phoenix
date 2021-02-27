<div class="col-sm-<?php echo $content_width; ?> cm-login-form ">

  <p class="alert alert-success" role="alert"><?php echo MODULE_CONTENT_LOGIN_TEXT_RETURNING_CUSTOMER; ?></p>

<?php
  echo tep_draw_form('login', tep_href_link('login.php', 'action=process', 'SSL'), 'post', '', true);
  $GLOBALS['customer_data']->act_on('username', 'display_input');
  $GLOBALS['customer_data']->act_on('password', 'display_input');
?>

  <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_LOGIN, 'fas fa-sign-in-alt', null, 'primary', null, 'btn-success btn-block'); ?></p>

  </form>

</div>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
