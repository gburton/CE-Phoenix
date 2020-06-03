<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  $num_list = (isset($_GET['view']) && ($_GET['view'] == 'all') ) ? 999999 : MAX_DISPLAY_SEARCH_RESULTS;
  $listing_split = new splitPageResults($listing_sql, $num_list, 'p.products_id');

  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

  <div class="contentText">

<?php
  if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div class="row align-items-center">
  <div class="col-sm-6 d-none d-sm-block">
    <?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
  </div>
  <div class="col-sm-6">
    <?php echo $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info', 'x', 'y'])); ?>
  </div>
</div>
<?php
  }

  if ($listing_split->number_of_rows > 0) {
?>
    <div class="card mb-2 card-body alert-filters">
      <ul class="nav">
        <li class="nav-item dropdown">
          <a href="#" class="nav-link text-dark dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo TEXT_SORT_BY; ?><span class="caret"></span></a>

          <div class="dropdown-menu">
            <?php
    foreach ($column_list as $i => $column) {
      switch ($column) {
        case 'PRODUCT_LIST_MODEL':
          $lc_text = TABLE_HEADING_MODEL;
          break;
        case 'PRODUCT_LIST_NAME':
          $lc_text = TABLE_HEADING_PRODUCTS;
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $lc_text = TABLE_HEADING_MANUFACTURER;
          break;
        case 'PRODUCT_LIST_PRICE':
          $lc_text = TABLE_HEADING_PRICE;
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $lc_text = TABLE_HEADING_QUANTITY;
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $lc_text = TABLE_HEADING_WEIGHT;
          break;
        case 'PRODUCT_LIST_IMAGE':
          $lc_text = TABLE_HEADING_IMAGE;
          break;
        case 'PRODUCT_LIST_BUY_NOW':
          $lc_text = TABLE_HEADING_BUY_NOW;
          break;
        case 'PRODUCT_LIST_ID':
          $lc_text = TABLE_HEADING_LATEST_ADDED;
          break;
        case 'PRODUCT_LIST_ORDERED':
          $lc_text = TABLE_HEADING_ORDERED;
          break;
        }

        if ( ($column != 'PRODUCT_LIST_BUY_NOW') && ($column != 'PRODUCT_LIST_IMAGE') ) {
          $lc_text = tep_create_sort_heading($_GET['sort'], $i+1, $lc_text);
          echo $lc_text;
        }
      }
            ?>
          </div>

        </li>
      </ul>
    </div>

  <?php
  $listing_query = tep_db_query($listing_split->sql_query);

  $prod_list_contents = NULL;

  $item = 1;
  while ($listing = tep_db_fetch_array($listing_query)) {
    $prod_list_contents .= '<div class="card mb-2 is-product" data-is-special="' . (int)$listing['is_special'] . '" data-product-price="' . $currencies->display_raw($listing['final_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '" data-product-manufacturer="' . max(0, (int)$listing['manufacturers_id']) . '">' . PHP_EOL;
      if (isset($_GET['manufacturers_id'])  && tep_not_null($_GET['manufacturers_id'])) {
        $prod_list_contents .= '<a href="' . tep_href_link('product_info.php', 'manufacturers_id=' . (int)$_GET['manufacturers_id'] . '&products_id=' . (int)$listing['products_id']) . '">' . tep_image('images/' . $listing['products_image'], htmlspecialchars($listing['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top') . '</a>' . PHP_EOL;
      } else {
        $prod_list_contents .= '<a href="' . tep_href_link('product_info.php', (isset($sort) ? 'sort=' . $sort . '&' : '') . 'products_id=' . (int)$listing['products_id']) . '">' . tep_image('images/' . $listing['products_image'], htmlspecialchars($listing['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top') . '</a>' . PHP_EOL;
      }

      $prod_list_contents .= '<div class="card-body">' . PHP_EOL;
        $prod_list_contents .= '<h5 class="card-title">';
        if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
          $prod_list_contents .= '<a href="' . tep_href_link('product_info.php', 'manufacturers_id=' . (int)$_GET['manufacturers_id'] . '&products_id=' . (int)$listing['products_id']) . '">' . $listing['products_name'] . '</a>';
        } else {
          $prod_list_contents .= '<a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$listing['products_id']) . '">' . $listing['products_name'] . '</a>';
        }
        $prod_list_contents .= '</h5>' . PHP_EOL;
        $prod_list_contents .= '<h6 class="card-subtitle mb-2 text-muted">';
          if ($listing['is_special'] == 1) {
            $prod_list_contents .= sprintf(IS_PRODUCT_SHOW_PRICE_SPECIAL, $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])), $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])));
          }
          else {
            $prod_list_contents .= sprintf(IS_PRODUCT_SHOW_PRICE, $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])));
          }
        $prod_list_contents .= '</h6>' . PHP_EOL;
        if (tep_not_null($listing['products_seo_description'])) {
          $prod_list_contents .= '<div class="pt-2 font-weight-lighter">';
            $prod_list_contents .= $listing['products_seo_description'];
          $prod_list_contents .= '</div>' . PHP_EOL;
        }
      $prod_list_contents .= '</div>' . PHP_EOL;

      $prod_list_contents .= '<div class="card-footer bg-white pt-0 border-0">' . PHP_EOL;
        $prod_list_contents .= '<div class="btn-group" role="group">';
          $prod_list_contents .= tep_draw_button(IS_PRODUCT_BUTTON_VIEW, '', tep_href_link('product_info.php', tep_get_all_get_params(array('action')) . 'products_id=' . (int)$listing['products_id']), NULL, NULL, 'btn-info btn-product-listing btn-view') . PHP_EOL;
          $has_attributes = (tep_has_product_attributes((int)$listing['products_id']) === true) ? '1' : '0';
          if ($has_attributes == 0) $prod_list_contents .= tep_draw_button(IS_PRODUCT_BUTTON_BUY, '', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . (int)$listing['products_id']), NULL, array('params' => 'data-has-attributes="' . $has_attributes . '" data-in-stock="' . (int)$listing['in_stock'] . '" data-product-id="' . (int)$listing['products_id'] . '"'), 'btn-light btn-product-listing btn-buy') . PHP_EOL;
        $prod_list_contents .= '</div>' . PHP_EOL;
      $prod_list_contents .= '</div>' . PHP_EOL;

    $prod_list_contents .= '</div>' . PHP_EOL;

    if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_SM == 0 ) $prod_list_contents .= '<div class="w-100 d-none d-sm-block d-md-none"></div>' . PHP_EOL;
    if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_MD == 0 ) $prod_list_contents .= '<div class="w-100 d-none d-md-block d-lg-none"></div>' . PHP_EOL;
    if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_LG == 0 ) $prod_list_contents .= '<div class="w-100 d-none d-lg-block d-xl-none"></div>' . PHP_EOL;
    if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_XL == 0 ) $prod_list_contents .= '<div class="w-100 d-none d-xl-block"></div>' . PHP_EOL;
    $item++;
  }

  echo $GLOBALS['OSCOM_Hooks']->call('filter', 'drawForm');

  echo '<div class="' . IS_PRODUCT_PRODUCTS_LAYOUT . '">' . PHP_EOL;
    echo $prod_list_contents;
  echo '</div>' . PHP_EOL;

} else {
  echo '<div class="alert alert-info" role="alert">' . TEXT_NO_PRODUCTS . '</div>';
}

if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
  ?>
<div class="row align-items-center">
  <div class="col-sm-6 d-none d-sm-block">
    <?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
  </div>
  <div class="col-sm-6">
    <?php echo $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info', 'x', 'y'])); ?>
  </div>
</div>
  <?php
  }
?>

</div>
