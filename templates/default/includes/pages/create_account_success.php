<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  $page_content = $oscTemplate->getContent('create_account_success');

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
