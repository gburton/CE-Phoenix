<?php
  if (isset($testimonials_query)) {
?>

<div class="col-sm-<?php echo $content_width; ?> cm-t-list">
  <div class="row">
    <?php
    while ($testimonials = tep_db_fetch_array($testimonials_query)) {
      echo '<div class="col-sm-' . $item_width . '">' . PHP_EOL;
        echo '<blockquote class="blockquote">' . PHP_EOL;
          echo '<p class="font-weight-lighter">' . tep_output_string_protected($testimonials['testimonials_text']) . '</p>' . PHP_EOL;
          echo '<footer class="blockquote-footer">' . sprintf(MODULE_CONTENT_TESTIMONIALS_LIST_WRITERS_NAME_DATE, tep_output_string_protected($testimonials['customers_name']), tep_date_short($testimonials['date_added'])) . '</footer>' . PHP_EOL;
        echo '</blockquote>' . PHP_EOL;
      echo '</div>' . PHP_EOL;
    }
    ?>
  </div>
  <div class="row align-items-center">
    <div class="col-sm-6 d-none d-sm-block">
      <?php echo $testimonials_split->display_count(MODULE_CONTENT_TESTIMONIALS_DISPLAY_NUMBER); ?>
    </div>
    <div class="col-sm-6">
      <?php echo $testimonials_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info'])); ?>
    </div>
  </div>
</div>

<?php
  } else {
?>

<div class="col">
  <div class="alert alert-info" role="alert">
    <?php echo MODULE_CONTENT_TESTIMONIALS_LIST_NO_TESTIMONIALS; ?>
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
