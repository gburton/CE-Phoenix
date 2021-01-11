<?php
  if (isset($testimonials_query)) {
?>

<div class="col-sm-<?= (int)MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH ?> cm-t-list">
  <div class="row">
    <?php
    while ($testimonials = $testimonials_query->fetch_assoc()) {
      echo '<div class="col-sm-' . (int)MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH . '">' . PHP_EOL;
        echo '<blockquote class="blockquote">' . PHP_EOL;
          echo nl2br($testimonials['testimonials_text']) . PHP_EOL;
          echo '<footer class="blockquote-footer">',
                  sprintf(MODULE_CONTENT_TESTIMONIALS_LIST_WRITERS_NAME_DATE, htmlspecialchars($testimonials['customers_name']), tep_date_short($testimonials['date_added'])),
               '</footer>' . PHP_EOL;
        echo '</blockquote>' . PHP_EOL;
      echo '</div>' . PHP_EOL;
    }
    ?>
  </div>
  <div class="row align-items-center">
    <div class="col-sm-6 d-none d-sm-block">
      <?= $testimonials_split->display_count(MODULE_CONTENT_TESTIMONIALS_DISPLAY_NUMBER) ?>
    </div>
    <div class="col-sm-6">
      <?= $testimonials_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info'])) ?>
    </div>
  </div>
</div>

<?php
  } else {
?>

<div class="col">
  <div class="alert alert-info" role="alert">
    <?= MODULE_CONTENT_TESTIMONIALS_LIST_NO_TESTIMONIALS ?>
  </div>
</div>

<?php
  }

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
