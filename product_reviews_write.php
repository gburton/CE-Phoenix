<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/product_reviews_write.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('product_reviews.php', tep_get_all_get_params(array('action'))));
  }

  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link('product_reviews.php', tep_get_all_get_params(array('action'))));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }

  $customer_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  $customer = tep_db_fetch_array($customer_query);

  if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $rating = tep_db_prepare_input($_POST['rating']);
    $review = tep_db_prepare_input($_POST['review']);

    $error = false;
    if (strlen($review) < REVIEW_TEXT_MIN_LENGTH) {
      $error = true;

      $messageStack->add('review', JS_REVIEW_TEXT);
    }

    if (($rating < 1) || ($rating > 5)) {
      $error = true;

      $messageStack->add('review', JS_REVIEW_RATING);
    }

    if ($error == false) {
      tep_db_query("insert into " . TABLE_REVIEWS . " (products_id, customers_id, customers_name, reviews_rating, date_added) values ('" . (int)$_GET['products_id'] . "', '" . (int)$customer_id . "', '" . tep_db_input($customer['customers_firstname']) . ' ' . tep_db_input($customer['customers_lastname']) . "', '" . tep_db_input($rating) . "', now())");
      $insert_id = tep_db_insert_id();

      tep_db_query("insert into " . TABLE_REVIEWS_DESCRIPTION . " (reviews_id, languages_id, reviews_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($review) . "')");

      $messageStack->add_session('product_reviews', TEXT_REVIEW_RECEIVED, 'success');
      tep_redirect(tep_href_link('product_reviews.php', tep_get_all_get_params(array('action'))));
    }
  }

  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<del>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</del> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
  }

  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br /><small class="text-muted">[' . $product_info['products_model'] . ']</small>';
  } else {
    $products_name = $product_info['products_name'];
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('product_reviews.php', tep_get_all_get_params()));

  require('includes/template_top.php');
?>

<div class="row">
  <h1 class="display-4 col-sm-8"><?php echo $products_name; ?></h1>
  <h2 class="display-4 col-sm-4 text-left text-sm-right"><?php echo $products_price; ?></h2>
</div>

<?php
  if ($messageStack->size('review') > 0) {
    echo $messageStack->output('review');
  }
?>

<?php echo tep_draw_form('product_reviews_write', tep_href_link('product_reviews_write.php', 'action=process&products_id=' . $_GET['products_id']), 'post', '', true); ?>

<div class="contentContainer">

<?php
  if (tep_not_null($product_info['products_image'])) {
?>

  <div class="text-center">
    <?php echo '<a href="' . tep_href_link('product_info.php', 'products_id=' . $product_info['products_id']) . '">' . tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>
  </div>

  <div class="clearfix"></div>

<?php
  }
?>

  <div class="row">
    <p class="col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_FROM; ?></p>
    <p class="col-sm-9"><?php echo tep_output_string_protected($customer['customers_firstname'] . ' ' . $customer['customers_lastname']); ?></p>
  </div>
  <div class="form-group row">
    <label for="inputReview" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_REVIEW; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_textarea_field('review', 'soft', 60, 15, NULL, 'required aria-required="true" id="inputReview" placeholder="' . SUB_TITLE_REVIEW_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
    
  <div class="form-group row">
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_RATING; ?></label>
    <div class="col-sm-9">
    
      <div class="form-check">
        <input class="form-check-input" type="radio" name="rating" id="Rating5" value="5" checked>
        <label class="form-check-label" for="exampleRadios1"><?php echo sprintf(TEXT_GOOD, tep_draw_stars(5)); ?></label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="rating" id="Rating4" value="4">
        <label class="form-check-label" for="exampleRadios1"><?php echo tep_draw_stars(4); ?></label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="rating" id="Rating3" value="3">
        <label class="form-check-label" for="exampleRadios1"><?php echo tep_draw_stars(3); ?></label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="rating" id="Rating2" value="2">
        <label class="form-check-label" for="exampleRadios1"><?php echo tep_draw_stars(2); ?></label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="rating" id="Rating1" value="1">
        <label class="form-check-label" for="exampleRadios1"><?php echo sprintf(TEXT_BAD, tep_draw_stars(1)); ?></label>
      </div>
   
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'])); ?></p>
  </div>
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
