<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/reviews.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('reviews.php'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
  $reviews_query_raw = "select r.reviews_id, SUBSTRING_INDEX(rd.reviews_text, ' ', 20) as reviews_text, r.reviews_rating, r.date_added, p.products_id, pd.products_name, p.products_image, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and rd.languages_id = '" . (int)$languages_id . "' and reviews_status = 1 order by r.reviews_rating DESC";
  $reviews_split = new splitPageResults($reviews_query_raw, MAX_DISPLAY_NEW_REVIEWS);

  if ($reviews_split->number_of_rows > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber d-none d-sm-block">
    <span class="align-middle"><?php echo $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></span>
  </div>
  <div class="col-sm-6">
    <?php echo $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?>
  </div>
</div>
  <?php
  }
?>
    <div class="row">
<?php
  $reviews_query = tep_db_query($reviews_split->sql_query);
  while ($reviews = tep_db_fetch_array($reviews_query)) {
    echo '<div class="col-sm-6">' . PHP_EOL;
      echo '<blockquote class="blockquote">';
        echo '<p>' . tep_image('images/' . tep_output_string_protected($reviews['products_image']), htmlspecialchars($reviews['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, null, null, 'rounded float-left') . tep_output_string_protected($reviews['reviews_text']) . ' ... </p>';
        $reviews_name = tep_output_string_protected($reviews['customers_name']);
        echo '<div class="clearfix"></div><footer class="blockquote-footer">' . sprintf(REVIEWS_TEXT_RATED, tep_draw_stars($reviews['reviews_rating']), $reviews_name, $reviews_name) . '</footer>' . PHP_EOL;
        echo '<div class="clearfix"></div><p><a class="btn btn-light btn-sm btn-block" href="' . tep_href_link('product_reviews.php', 'products_id=' . (int)$reviews['products_id']) . '">' . REVIEWS_TEXT_READ_MORE . '</a></p>';
      echo '</blockquote>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
  }
?>
    </div>
    <div class="clearfix"></div>
<?php
  } else {
?>

  <div class="alert alert-info">
    <?php echo TEXT_NO_REVIEWS; ?>
  </div>

<?php
  }

  if (($reviews_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber d-none d-sm-block">
    <span class="align-middle"><?php echo $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></span>
  </div>
  <div class="col-sm-6">
    <?php echo $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?>
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
