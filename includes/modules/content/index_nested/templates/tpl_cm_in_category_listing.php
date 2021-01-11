<div class="col-sm-<?= (int)MODULE_CONTENT_IN_CATEGORY_LISTING_CONTENT_WIDTH ?> cm-in-category-listing">
  <div class="<?= MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW ?>">
    <?php
    foreach ($categories as $v) {
      echo '<div class="col">';
        echo '<div class="card is-category mb-2 card-body text-center border-0">';
          echo '<a href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '">' . tep_image('images/' . $v['image'], htmlspecialchars($v['title'])) . '</a>';
          echo '<div class="card-footer border-0 bg-white">';
            echo '<a class="card-link" href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '">' . $v['title'] . '</a>';
          echo '</div>';
        echo '</div>' . PHP_EOL;
      echo '</div>';
    }
    ?>
  </div>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
