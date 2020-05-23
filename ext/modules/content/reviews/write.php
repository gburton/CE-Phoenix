<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require "includes/languages/$language/modules/content/reviews/write.php";

  $reviewed = [];
  $reviewed_products_array = tep_db_query("SELECT DISTINCT products_id FROM reviews WHERE customers_id = " . (int)$_SESSION['customer_id']);
  while ($reviewed_products = tep_db_fetch_array($reviewed_products_array)) {
    $reviewed[] = $reviewed_products['products_id'];
  }

  if (in_array((int)$_GET['products_id'], $reviewed)) {
    $messageStack->add_session('product_action', sprintf(TEXT_ALREADY_REVIEWED, $customer->get_short_name()), 'error');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
  }

  if (ALLOW_ALL_REVIEWS == 'false') {
    $purchased = [];
    $purchased_products_array = tep_db_query("SELECT DISTINCT op.products_id FROM orders o, orders_products op WHERE o.customers_id = " . (int)$_SESSION['customer_id'] . " AND o.orders_id = op.orders_id GROUP BY products_id");

    while ($purchased_products = tep_db_fetch_array($purchased_products_array)) {
      $purchased[] = $purchased_products['products_id'];
    }

    $allowable_reviews = array_diff($purchased, $reviewed);

    if (!in_array((int)$_GET['products_id'], $allowable_reviews)) {
      $messageStack->add_session('product_action', sprintf(TEXT_NOT_PURCHASED, $customer->get_short_name()), 'error');

      tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
    }
  }

  $product_info_query = tep_db_query("SELECT p.products_id, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name, SUBSTRING_INDEX(pd.products_description, ' ', 40) AS products_description FROM products p, products_description pd WHERE p.products_id = " . (int)$_GET['products_id'] . " AND p.products_status = 1 AND p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id']);

  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
  }

  $product_info = tep_db_fetch_array($product_info_query);

  if (tep_validate_form_action_is('process')) {
    $rating = tep_db_prepare_input($_POST['rating']);
    $review = tep_db_prepare_input($_POST['review']);
    $nickname = tep_db_prepare_input($_POST['nickname']);
    
    if (ALLOW_ALL_REVIEWS == 'false') {
      if ($_POST['nickname'] != $customer->get_short_name()) {
        $nickname = sprintf(VERIFIED_BUYER, $nickname);
      }
    }

    tep_db_query("INSERT INTO reviews (products_id, customers_id, customers_name, reviews_rating, date_added) VALUES ('" . (int)$_GET['products_id'] . "', '" . (int)$_SESSION['customer_id'] . "', '" . tep_db_input($nickname) . "', '" . tep_db_input($rating) . "', NOW())");
    $insert_id = tep_db_insert_id();

    tep_db_query("INSERT INTO reviews_description (reviews_id, languages_id, reviews_text) VALUES ('" . (int)$insert_id . "', '" . (int)$_SESSION['languages_id'] . "', '" . tep_db_input($review) . "')");

    $messageStack->add_session('product_action', sprintf(TEXT_REVIEW_RECEIVED, $nickname), 'success');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
  }

  $tax_rate = tep_get_tax_rate($product_info['products_tax_class_id'], $customer->get_country_id(), $customer->get_zone_id());
  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<del>' . $currencies->display_price($product_info['products_price'], $tax_rate) . '</del> <span class="productPrice text-danger productSpecialPrice">' . $currencies->display_price($new_price, $tax_rate) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], $tax_rate);
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('ext/modules/content/reviews/write.php',  tep_get_all_get_params(), 'SSL'));

  require 'includes/template_top.php';
?>

<div class="row">
  <h1 class="display-4 col-sm-8"><?php echo $product_info['products_name']; ?></h1>
  <h2 class="display-4 col-sm-4 text-left text-sm-right"><?php echo $products_price; ?></h2>
</div>

<?php
  echo tep_draw_form('review', tep_href_link('ext/modules/content/reviews/write.php', 'action=process&products_id=' . (int)$_GET['products_id'], 'SSL'), 'post', '', true);
?>

  <div class="alert alert-warning" role="alert">
    <?php echo sprintf(TEXT_REVIEW_WRITING, tep_output_string_protected($customer->get_short_name()), $product_info['products_name']); ?>
  </div>

  <div class="form-group row">
    <label for="inputNickName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_FROM; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('nickname', tep_output_string_protected($customer->get_short_name()), 'required aria-required="true" id="inputNickName" placeholder="' . SUB_TITLE_REVIEW_NICKNAME . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  
  <div class="form-group row">
    <label for="inputReview" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_REVIEW; ?></label>
    <div class="col-sm-9">
<?php
  echo tep_draw_textarea_field('review', 'soft', 60, 15, null, 'required aria-required="true" id="inputReview" placeholder="' . SUB_TITLE_REVIEW_TEXT . '"');
  echo FORM_REQUIRED_INPUT;
?>
    </div>
  </div>

  <div class="form-group row align-items-center">
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SUB_TITLE_RATING; ?></label>
    <div class="col-sm-9">
      <div class="rating d-flex justify-content-end flex-row-reverse align-items-baseline">
        <?php echo sprintf(TEXT_GOOD, 5); ?>
        <input type="radio" id="r5" name="rating" required aria-required="true" value="5"><label title="<?php echo sprintf(TEXT_RATED, sprintf(TEXT_GOOD, 5)); ?>" for="r5">&nbsp;</label>
        <input type="radio" id="r4" name="rating" value="4"><label title="<?php echo sprintf(TEXT_RATED, 4); ?>" for="r4">&nbsp;</label>
        <input type="radio" id="r3" name="rating" value="3"><label title="<?php echo sprintf(TEXT_RATED, 3); ?>" for="r3">&nbsp;</label>
        <input type="radio" id="r2" name="rating" value="2"><label title="<?php echo sprintf(TEXT_RATED, 2); ?>" for="r2">&nbsp;</label>
        <input type="radio" id="r1" name="rating" checked value="1"><label title="<?php echo sprintf(TEXT_RATED, sprintf(TEXT_BAD, 1)); ?>" for="r1">&nbsp;</label>
      </div>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-pen', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']), null, null, 'btn-light mt-2'); ?></p>
  </div>

  <hr>

  <div class="row">
    <div class="col-sm-8"><?php echo $product_info['products_description']; ?>...</div>
    <div class="col-sm-4"><?php echo tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name'])); ?></div>
  </div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
