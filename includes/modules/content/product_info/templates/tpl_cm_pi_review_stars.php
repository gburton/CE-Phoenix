<div class="col-sm-<?php echo $content_width; ?> cm-pi-review-stars">
  <ul class="list-inline">
    <?php
  foreach ($review_stars_array as $k => $v) {
    echo '<li class="list-inline-item ' . $k . '">' . $v . '</li>';
  }
?>
    <li class="list-inline-item border-left ml-2 pl-3"><a href="<?php echo $review_link; ?>"><?php echo $do_review; ?></a></li>
  </ul>
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
