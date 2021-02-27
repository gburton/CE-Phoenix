<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('advanced_search.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('advanced_search_result.php', tep_get_all_get_params(), 'NONSSL', true, false));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE_2; ?></h1>

<?php
  require 'includes/system/segments/sortable_product_listing.php';
?>

  <br>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('advanced_search.php', tep_get_all_get_params(['sort', 'page']), 'NONSSL', true, false)); ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
