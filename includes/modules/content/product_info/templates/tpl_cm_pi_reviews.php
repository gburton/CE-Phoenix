<div class="col-sm-<?php echo $content_width; ?> cm-pi-reviews">
  <h4><?php echo MODULE_CONTENT_PRODUCT_INFO_REVIEWS_TEXT_TITLE; ?></h4>
  <div class="row">
    <?php
  while ($review = tep_db_fetch_array($review_query)) {
    echo '<div class="col-sm-' . $item_width . '">';
    echo '<blockquote class="blockquote">';
    echo '<p class="font-weight-lighter">' . tep_output_string_protected($review['reviews_text']) . '</p>';
    echo '<footer class="blockquote-footer">'
       . sprintf(MODULE_CONTENT_PRODUCT_INFO_REVIEWS_TEXT_RATED,
                 tep_draw_stars($review['reviews_rating']),
                 tep_output_string_protected($review['customers_name']))
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
