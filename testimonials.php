<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/testimonials.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('testimonials.php'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
  $testimonials_query_raw = "select t.testimonials_id, td.testimonials_text, t.date_added, t.customers_name from testimonials t, testimonials_description td where t.testimonials_id = td.testimonials_id and td.languages_id = '" . (int)$languages_id . "' and testimonials_status = 1 order by t.testimonials_id DESC";
  $testimonials_split = new splitPageResults($testimonials_query_raw, MAX_DISPLAY_NEW_REVIEWS);

  if ($testimonials_split->number_of_rows > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber d-none d-sm-block">
    <span class="align-middle"><?php echo $testimonials_split->display_count(TEXT_DISPLAY_NUMBER_OF_TESTIMONIALS); ?></span>
  </div>
  <div class="col-sm-6">
    <?php echo $testimonials_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?>
  </div>
</div>
<?php
    }
?>
    <div class="row">

<?php
    $testimonials_query = tep_db_query($testimonials_split->sql_query);
    while ($testimonials = tep_db_fetch_array($testimonials_query)) {
      echo '<div class="col-sm-6">' . PHP_EOL;
        echo '<blockquote class="blockquote">' . PHP_EOL;
          echo '<p>' . tep_output_string_protected($testimonials['testimonials_text']) . '</p>' . PHP_EOL;
          echo '<footer class="blockquote-footer">' . tep_output_string_protected($testimonials['customers_name']) . '</footer>' . PHP_EOL;
        echo '</blockquote>' . PHP_EOL;
      echo '</div>' . PHP_EOL;
    }
    ?>
    </div>
<?php
  } else {
?>

  <div class="alert alert-info">
    <?php echo TEXT_NO_TESTIMONIALS; ?>
  </div>

<?php
  }

  if (($testimonials_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber d-none d-sm-block">
    <span class="align-middle"><?php echo $testimonials_split->display_count(TEXT_DISPLAY_NUMBER_OF_TESTIMONIALS); ?></span>
  </div>
  <div class="col-sm-6">
    <?php echo $testimonials_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?>
  </div>
</div>
<?php
  }
?>

</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
