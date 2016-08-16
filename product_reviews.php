<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('reviews.php'));
  }
  
  $product_check_query = tep_db_query("select count(*) as total from products p, products_description pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
  $product_check = tep_db_fetch_array($product_check_query);

  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link('reviews.php'));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }

  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<del>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</del> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
  }

  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br /><small>[' . $product_info['products_model'] . ']</small>';
  } else {
    $products_name = $product_info['products_name'];
  }

  require('includes/languages/' . $language . '/product_reviews.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('product_reviews.php', tep_get_all_get_params()));

  require('includes/template_top.php');
?>

<?php
  if ($messageStack->size('product_reviews') > 0) {
    echo $messageStack->output('product_reviews');
  }
?>

<div itemscope itemtype="http://schema.org/Product">

<div class="page-header">
  <div class="row">
    <h1 class="col-sm-4" itemprop="name"><?php echo $products_name; ?></h1>
    <h2 class="col-sm-8 text-right-not-xs"><?php echo $products_price; ?></h2>
  </div>
</div>

<div class="contentContainer">

<?php
$average_query = tep_db_query("select AVG(r.reviews_rating) as average, COUNT(r.reviews_rating) as count from " . TABLE_REVIEWS . " r where r.products_id = '" . (int)$product_info['products_id'] . "' and r.reviews_status = 1");
$average = tep_db_fetch_array($average_query);
echo '<div class="col-sm-8 text-center alert alert-success" itemprop="AggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><meta itemprop="ratingValue" content="' . max(1, (int)round($average['average'])) . '" /><meta itemprop="bestRating" content="5" />' . sprintf(REVIEWS_TEXT_AVERAGE, tep_output_string_protected($average['count']), tep_draw_stars(tep_output_string_protected(round($average['average'])))) . '</div>';
?>

<?php
  if (tep_not_null($product_info['products_image'])) {
?>

  <div class="col-sm-4 text-center">
    <?php echo '<a href="' . tep_href_link('product_info.php', 'products_id=' . $product_info['products_id']) . '">' . tep_image('images/' . $product_info['products_image'], addslashes($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>

    <p><?php echo tep_draw_button(IMAGE_BUTTON_IN_CART, 'fa fa-shopping-cart', tep_href_link($PHP_SELF, tep_get_all_get_params(array('action')) . 'action=buy_now')); ?></p>
  </div>
  
  <div class="clearfix"></div>

  <hr>
  
  <div class="clearfix"></div>

<?php
  }

  $reviews_query_raw = "select r.reviews_id, rd.reviews_text, r.reviews_rating, date(r.date_added) as date_added, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.products_id = '" . (int)$product_info['products_id'] . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and r.reviews_status = 1 order by r.reviews_rating desc";
  $reviews_split = new splitPageResults($reviews_query_raw, MAX_DISPLAY_NEW_REVIEWS);

  if ($reviews_split->number_of_rows > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber hidden-xs">
    <?php echo $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
  </div>
  <div class="col-sm-6">
    <span class="pull-right pagenav"><ul class="pagination"><?php echo $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></ul></span>
    <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
  </div>
</div>
<?php
    }
?>

    <div class="reviews">
<?php
    $reviews_query = tep_db_query($reviews_split->sql_query);
    while ($reviews = tep_db_fetch_array($reviews_query)) {
      $review_name = tep_output_string_protected($reviews['customers_name']);
?>
      <blockquote class="col-sm-6" itemprop="review" itemscope itemtype="http://schema.org/Review">
        <p itemprop="reviewBody"><?php echo tep_output_string_protected($reviews['reviews_text']); ?></p>
        <meta itemprop="datePublished" content="<?php echo $reviews['date_added']; ?>">
        <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
        <meta itemprop="ratingValue" content="<?php echo (int)$reviews['reviews_rating']; ?>"></span>
        <footer><?php echo sprintf(REVIEWS_TEXT_RATED, tep_draw_stars($reviews['reviews_rating']), $review_name, $review_name); ?></footer>
      </blockquote>
<?php
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
  <div class="col-sm-6 pagenumber hidden-xs">
    <?php echo $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
  </div>
  <div class="col-sm-6">
    <span class="pull-right pagenav"><ul class="pagination"><?php echo $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></ul></span>
    <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
  </div>
</div>
<?php
  }
?>

  <br />

  <div class="buttonSet row">
    <div class="col-xs-6">
      <?php
      $back = sizeof($navigation->path)-2;
      if (isset($navigation->path[$back])) {
        echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']));
      }
      ?>&nbsp;
    </div>
    <div class="col-xs-6 text-right"><?php echo tep_draw_button(IMAGE_BUTTON_WRITE_REVIEW, 'fa fa-commenting', tep_href_link('product_reviews_write.php', tep_get_all_get_params()), 'primary', NULL, 'btn-success'); ?></div>
  </div>
</div>

</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
