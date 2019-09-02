<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }
  
  require('includes/languages/' . $language . '/modules/content/reviews/write.php');

  $reviewed = array();
  $reviewed_products_array = tep_db_query("select distinct products_id from reviews where customers_id = '" . (int)$customer_id . "'");
  while ($reviewed_products = tep_db_fetch_array($reviewed_products_array)) {
    $reviewed[] = $reviewed_products['products_id'];
  }
    
  if (in_array((int)$_GET['products_id'], $reviewed)) {
    $messageStack->add_session('product_action', sprintf(TEXT_ALREADY_REVIEWED, $customer_first_name), 'error');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(array('action'))));
  }
  
  if (ALLOW_ALL_REVIEWS == 'false') {
    $purchased = array();
    $purchased_products_array = tep_db_query("select distinct op.products_id from orders o, orders_products op where o.customers_id = '" . (int)$customer_id . "' and o.orders_id = op.orders_id group by products_id");
  
    while ($purchased_products = tep_db_fetch_array($purchased_products_array)) {
      $purchased[] = $purchased_products['products_id'];
    }
  
    $allowable_reviews = array_diff($purchased, $reviewed);
    
    if (!in_array((int)$_GET['products_id'], $allowable_reviews)) {
      $messageStack->add_session('product_action', sprintf(TEXT_NOT_PURCHASED, $customer_first_name), 'error');

      tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(array('action'))));
    }
  }
  
  $product_info_query = tep_db_query("select p.products_id, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name, SUBSTRING_INDEX(pd.products_description, ' ', 40) as products_description from products p, products_description pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
  
  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $rating = tep_db_prepare_input($_POST['rating']);
    $review = tep_db_prepare_input($_POST['review']);

    tep_db_query("insert into reviews (products_id, customers_id, customers_name, reviews_rating, date_added) values ('" . (int)$_GET['products_id'] . "', '" . (int)$customer_id . "', '" . tep_db_input($customer_first_name) . "', '" . tep_db_input($rating) . "', now())");
    $insert_id = tep_db_insert_id();

    tep_db_query("insert into reviews_description (reviews_id, languages_id, reviews_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($review) . "')");

    $messageStack->add_session('product_action', sprintf(TEXT_REVIEW_RECEIVED, $customer_first_name), 'success');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(array('action'))));
  }

  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<del>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</del> <span class="productPrice text-danger productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
  }
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('ext/modules/content/reviews/write.php',  tep_get_all_get_params(), 'SSL'));
  
  require('includes/template_top.php');
?>

<div class="row">
  <h1 class="display-4 col-sm-8"><?php echo $product_info['products_name']; ?></h1>
  <h2 class="display-4 col-sm-4 text-left text-sm-right"><?php echo $products_price; ?></h2>
</div>

<?php echo tep_draw_form('review', 
tep_href_link('ext/modules/content/reviews/write.php', 'action=process&products_id=' . (int)$_GET['products_id'], 'SSL'), 'post', '', true); ?>

<div class="contentContainer">

  <div class="alert alert-warning" role="alert">
    <?php echo sprintf(TEXT_REVIEW_WRITING, tep_output_string_protected($customer_first_name), $product_info['products_name']); ?>
  </div>

  <div class="row">
    <p class="col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_FROM; ?></p>
    <p class="col-sm-9"><?php echo tep_output_string_protected($customer_first_name); ?></p>
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
      <div class="custom-control custom-radio">
        <input type="radio" id="Rating5" name="rating" class="custom-control-input" value="5">
        <label class="custom-control-label" for="Rating5"><?php echo sprintf(TEXT_GOOD, tep_draw_stars(5)); ?></label>
      </div>
      <div class="custom-control custom-radio">
        <input type="radio" id="Rating4" name="rating" class="custom-control-input" value="4" required aria-required="true">
        <label class="custom-control-label" for="Rating4"><?php echo tep_draw_stars(4); ?></label>
      </div>
      <div class="custom-control custom-radio">
        <input type="radio" id="Rating3" name="rating" class="custom-control-input" value="3">
        <label class="custom-control-label" for="Rating3"><?php echo tep_draw_stars(3); ?></label>
      </div>
      <div class="custom-control custom-radio">
        <input type="radio" id="Rating2" name="rating" class="custom-control-input" value="2">
        <label class="custom-control-label" for="Rating2"><?php echo tep_draw_stars(2); ?></label>
      </div>
      <div class="custom-control custom-radio">
        <input type="radio" id="Rating1" name="rating" class="custom-control-input" value="1">
        <label class="custom-control-label" for="Rating1"><?php echo sprintf(TEXT_BAD, tep_draw_stars(1)); ?></label>
      </div>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-pen', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'])); ?></p>
  </div>
  
  <hr>
  
  <div class="row">
    <div class="col-sm-8"><?php echo $product_info['products_description']; ?>...</div>
    <div class="col-sm-4"><?php echo tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name'])); ?></div>
  </div>
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
