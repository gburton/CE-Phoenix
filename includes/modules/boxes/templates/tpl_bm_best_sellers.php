<div class="card mb-2 bm-best-sellers">
  <div class="card-header"><?php echo MODULE_BOXES_BEST_SELLERS_BOX_TITLE; ?></div>
  <div class="list-group list-group-flush">
    <?php
    foreach ($best_sellers as $best_seller) {
      echo '<a class="list-group-item list-group-item-action" href="' . $best_seller['link'] . '">' . $best_seller['text'] . '</a>' . PHP_EOL;
    }
    ?>
  </div>
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
