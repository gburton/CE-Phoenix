<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('specials.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  require 'includes/system/segments/sortable_product_listing.php';

  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
