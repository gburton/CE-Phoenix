<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/account_newsletters.php');

  $newsletter_query = tep_db_query("select customers_newsletter from customers where customers_id = '" . (int)$customer_id . "'");
  $newsletter = tep_db_fetch_array($newsletter_query);

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
      $newsletter_general = tep_db_prepare_input($_POST['newsletter_general']);
    } else {
      $newsletter_general = '0';
    }

    if ($newsletter_general != $newsletter['customers_newsletter']) {
      $newsletter_general = (($newsletter['customers_newsletter'] == '1') ? '0' : '1');

      tep_db_query("update customers set customers_newsletter = '" . (int)$newsletter_general . "' where customers_id = '" . (int)$customer_id . "'");
    }

    $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_newsletters.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('account_newsletter', tep_href_link('account_newsletters.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">

  <div class="form-group row align-items-center">
    <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo MY_NEWSLETTERS_GENERAL_NEWSLETTER; ?></div>
    <div class="col-sm-8 pl-5 custom-control custom-switch">
      <?php echo tep_draw_checkbox_field('newsletter_general', '1', (($newsletter['customers_newsletter'] == '1') ? true : false), 'class="custom-control-input" id="inputNewsletter"');
      echo '<label for="inputNewsletter" class="custom-control-label text-muted"><small>' . MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION . '&nbsp;</small></label>';      
      ?>
    </div>
  </div>
    
  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_UPDATE_PREFERENCES, 'fas fa-users-cog', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
