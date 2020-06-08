<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $page_content = $oscTemplate->getContent('checkout_success');

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  require $oscTemplate->map_to_template('template_top.php', 'component');

  echo tep_draw_form('order', tep_href_link('checkout_success.php', 'action=update', 'SSL'), 'post', ' role="form"');
?>

<div class="contentContainer">
  <div class="row">
    <?php echo $page_content; ?>
  </div>
</div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
