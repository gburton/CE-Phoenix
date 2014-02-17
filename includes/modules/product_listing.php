<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  $listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
?>

  <div class="contentText">

<?php
  if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>

    <div>
      <span style="float: right;"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>

      <span><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></span>
    </div>

    <br />

<?php
  }

  if ($listing_split->number_of_rows > 0) {
    ?>
    <div class="well well-sm">
      <strong><?php echo TEXT_VIEW; ?></strong>
      <div class="btn-group"><a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list"></span><?php echo TEXT_VIEW_LIST; ?></a> <a href="#" id="grid" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th"></span><?php echo TEXT_VIEW_GRID; ?></a></div>
    </div>
    <?php
    $listing_query = tep_db_query($listing_split->sql_query);

    $prod_list_contents .= '<div id="products" class="row list-group">';

    while ($listing = tep_db_fetch_array($listing_query)) {
      $prod_list_contents .= '<div class="item col-xs-4 col-lg-4">';
      
      $prod_list_contents .= '<div class="thumbnail">';
      if (isset($HTTP_GET_VARS['manufacturers_id'])  && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="group list-group-image"') . '</a>';
      } else {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="group list-group-image"') . '</a>';
      }
      $prod_list_contents .= '<div class="caption">';
      $prod_list_contents .= '<h4 class="group inner list-group-item-heading">';
      if (isset($HTTP_GET_VARS['manufacturers_id']) && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
        $prod_list_contents .= '        <a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
      } else {
        $prod_list_contents .= '        <a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
      }
      $prod_list_contents .= '</h4>';
      $prod_list_contents .= '<p class="group inner list-group-item-text product_text">' . strip_tags($listing['products_description'], '<br>') . '&hellip;</p>';

      $prod_list_contents .= '<div class="row">';
      $prod_list_contents .= '<div class="col-xs-12 col-md-6"><p class="lead">';
      if (tep_not_null($listing['specials_new_products_price'])) {
        $prod_list_contents .= '<del>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</del>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>';
      } else {
        $prod_list_contents .= $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id']));
      }
      $prod_list_contents .= '</p></div>';
      
      $prod_list_contents .= '<div class="col-xs-12 col-md-6">' . tep_draw_button(IMAGE_BUTTON_BUY_NOW, 'cart', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']), NULL, NULL, 'btn-success') . '</div>';
      $prod_list_contents .= '</div>';
      $prod_list_contents .= '</div>';
      $prod_list_contents .= '</div>';
      $prod_list_contents .= '</div>';
    }

    $prod_list_contents .= '</div>';

    echo $prod_list_contents;
  } else {
?>

    <div class="alert alert-info"><?php echo TEXT_NO_PRODUCTS; ?></div>

<?php
  }

  if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

    <br />

    <div>
      <span style="float: right;"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>

      <span><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></span>
    </div>

<?php
  }
?>

</div>
