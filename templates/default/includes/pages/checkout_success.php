<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $page_content = $oscTemplate->getContent('checkout_success');

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  require $oscTemplate->map_to_template('template_top.php', 'component');

  echo tep_draw_form('order', tep_href_link('checkout_success.php', 'action=update', 'SSL'), 'post', ' role="form"');
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
