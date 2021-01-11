<div class="col-sm-<?= (int)MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH ?> cm-pi-reviews">
  <h4><?= MODULE_CONTENT_PRODUCT_INFO_REVIEWS_TEXT_TITLE; ?></h4>
  <div class="row">
    <?php
  while ($review = $review_query->fetch_assoc()) {
    echo '<div class="col-sm-' . (int)MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH_EACH . '">';
    echo '<blockquote class="blockquote">';
    echo '<p class="font-weight-lighter">' . htmlspecialchars($review['reviews_text']) . '</p>';
    echo '<footer class="blockquote-footer">'
       . sprintf(MODULE_CONTENT_PRODUCT_INFO_REVIEWS_TEXT_RATED,
                 tep_draw_stars($review['reviews_rating']),
                 htmlspecialchars($review['customers_name']))
       . '</footer>';
    echo '</blockquote>';
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
