<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require('includes/languages/' . $language . '/product_info.php');

  $product_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
  $product_check = tep_db_fetch_array($product_check_query);

  require('includes/template_top.php');

  if ($product_check['total'] < 1) {
?>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-warning"><?php echo TEXT_PRODUCT_NOT_FOUND; ?></div>
  </div>

  <div class="pull-right">
    <?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php')); ?>
  </div>
</div>

<?php
  } else {
    $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.products_gtin from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
    $product_info = tep_db_fetch_array($product_info_query);

    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . (int)$languages_id . "'");

    if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
      $products_price = '<del>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</del> <span class="productPrice productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
      $is_special = 1;
    } else {
      $products_price = '<span class="productPrice">' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
      $is_special = 0;
    }

    $products_name = '<a href="' . tep_href_link('product_info.php', 'products_id=' . $product_info['products_id']) . '">' . $product_info['products_name'] . '</a>';

    if (tep_not_null($product_info['products_model'])) {
      $products_name .= '<br /><small>[' . $product_info['products_model'] . ']</small>';
    }
?>

<?php echo tep_draw_form('cart_quantity', tep_href_link('product_info.php', tep_get_all_get_params(array('action')). 'action=add_product', 'NONSSL'), 'post', 'class="form-horizontal" role="form"'); ?>

<div class="page-header">
  <div class="row">  
    <h1 class="col-sm-8"><?php echo $products_name; ?></h1>
    <h2 class="col-sm-4 text-right-not-xs"><?php echo $products_price; ?></h2>
  </div>
</div>

<?php
  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<div class="contentContainer">
  <div class="contentText is-product" data-is-special="<?php echo (int)$is_special; ?>">

<?php
    if (tep_not_null($product_info['products_image'])) {

      $photoset_layout = (int)MODULE_HEADER_TAGS_PRODUCT_COLORBOX_LAYOUT;

      $pi_query = tep_db_query("select image, htmlcontent from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order");
      $pi_total = tep_db_num_rows($pi_query);

      if ($pi_total > 0) {
?>

    <div class="piGal pull-right" data-imgcount="<?php echo $photoset_layout; ?>">

<?php
        $pi_counter = 0;
        $pi_html = array();

        while ($pi = tep_db_fetch_array($pi_query)) {
          $pi_counter++;

          if (tep_not_null($pi['htmlcontent'])) {
            $pi_html[] = '<div id="piGalDiv_' . $pi_counter . '">' . $pi['htmlcontent'] . '</div>';
          }

          echo tep_image('images/' . $pi['image'], '', '', '', 'id="piGalImg_' . $pi_counter . '"');
        }
?>

    </div>

<?php
        if ( !empty($pi_html) ) {
          echo '    <div style="display: none;">' . implode('', $pi_html) . '</div>';
        }
      } else {
?>

    <div class="piGal pull-right">
      <?php echo tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name'])); ?>
    </div>

<?php
      }
    }
?>

  </div>

  <div class="row">
    <?php echo $oscTemplate->getContent('product_info'); ?>
  </div>

</div>

</form>

<?php
  }
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
