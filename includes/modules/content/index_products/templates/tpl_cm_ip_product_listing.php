<?php
  if (!empty($output)) {
?>

<div class="filter-list">
  <?php
    echo tep_draw_form('filter', 'index.php', 'get') . PHP_EOL;
    echo $output;
?>

  </form>
</div><br class="d-block d-sm-none">

<?php
  }
?>
<div class="col-sm-<?php echo $content_width; ?> cm-ip-product-listing">
  <?php include $GLOBALS['oscTemplate']->map_to_template('product_listing.php', 'component'); ?>
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
