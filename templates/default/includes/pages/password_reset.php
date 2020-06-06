<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('login.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>
<?php
  if ($messageStack->size('password_reset') > 0) {
    echo $messageStack->output('password_reset');
  }

  echo tep_draw_form('password_reset', tep_href_link('password_reset.php', 'account=' . urlencode($email_address) . '&key=' . $password_key . '&action=process', 'SSL'), 'post', '', true);
?>
<div class="contentContainer">
  <div class="alert alert-info" role="alert"><?php echo TEXT_MAIN; ?></div>
  
  <?php
  $customer_data->display_input($page_fields);
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
  ?>
  
  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>
</div>
</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
