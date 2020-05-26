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

  if (!$customer_data->has(['newsletter'])) {
    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/account_newsletters.php";

  $customer_data->build_read(['newsletter'], 'customers', ['id' => (int)$_SESSION['customer_id']]);
  $newsletter_query = tep_db_query($customer_data->build_read(['newsletter'], 'customers', ['id' => (int)$_SESSION['customer_id']]));
  $newsletter = tep_db_fetch_array($newsletter_query);

  if (tep_validate_form_action_is('process')) {
    if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
      $newsletter_general = tep_db_prepare_input($_POST['newsletter_general']);
    } else {
      $newsletter_general = 0;
    }

    $saved_newsletter = $customer_data->get('newsletter', $newsletter);
    if ($newsletter_general != $saved_newsletter) {
      $customer_data->update(['newsletter' => (int)(('1' == $saved_newsletter) ? 0 : 1)], ['id' => (int)$_SESSION['customer_id']]);
    }

    $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_newsletters.php', '', 'SSL'));

  require 'includes/template_top.php';
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
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
