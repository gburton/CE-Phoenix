<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_newsletters.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('account_newsletter', tep_href_link('account_newsletters.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

  <div class="form-group row align-items-center">
    <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo MY_NEWSLETTERS_GENERAL_NEWSLETTER; ?></div>
    <div class="col-sm-8 pl-5 custom-control custom-switch">
      <?php
      echo tep_draw_checkbox_field('newsletter_general', '1', ($customer_data->get('newsletter', $newsletter) == '1'), 'class="custom-control-input" id="inputNewsletter"');
      echo '<label for="inputNewsletter" class="custom-control-label text-muted"><small>' . MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION . '&nbsp;</small></label>';
      ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_UPDATE_PREFERENCES, 'fas fa-users-cog', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
