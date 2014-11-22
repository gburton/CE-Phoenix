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

<?php
  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

  <div class="contentText">

<?php
  if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber hidden-xs">
    <?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
  </div>
  <div class="col-sm-6">
    <div class="pull-right pagenav"><ul class="pagination"><?php echo $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></ul></div>
    <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
  </div>
</div>
<?php
  }

  if ($listing_split->number_of_rows > 0) { ?>
   
    <div class="well well-sm">
    <?php if (MODULE_HEADER_TAGS_GRID_LIST_VIEW_STATUS == 'True') {
    ?>
      <strong><?php echo TEXT_VIEW; ?></strong>
      <div class="btn-group"><a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list"></span><?php echo TEXT_VIEW_LIST; ?></a> <a href="#" id="grid" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th"></span><?php echo TEXT_VIEW_GRID; ?></a></div>
      
      <?php } ?>
      
      <div class="btn-group btn-group-sm pull-right">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php echo TEXT_SORT_BY; ?><span class="caret"></span></button>
        
          <ul class="dropdown-menu text-left">
          
          <?php
          $lc_show_model = false;
          $lc_show_manu = false;
          $lc_show_qty = false;
          $lc_show_lbs = false;
		    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
    switch ($column_list[$col]) {
      case 'PRODUCT_LIST_MODEL':
        $lc_text = TABLE_HEADING_MODEL;
		$lc_show_model = true;
        break;
      case 'PRODUCT_LIST_NAME':
        $lc_text = TABLE_HEADING_PRODUCTS;
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $lc_text = TABLE_HEADING_MANUFACTURER;
		$lc_show_manu = true;
        break;
      case 'PRODUCT_LIST_PRICE':
        $lc_text = TABLE_HEADING_PRICE;
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $lc_text = TABLE_HEADING_QUANTITY;
		$lc_show_qty = true;
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $lc_text = TABLE_HEADING_WEIGHT;
		$lc_show_lbs = true;
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
    }

    if ( ($column_list[$col] != 'PRODUCT_LIST_BUY_NOW') && ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
      $lc_text = tep_create_sort_heading($HTTP_GET_VARS['sort'], $col+1, $lc_text);
	  echo '        <li>' . $lc_text . '</li>';
    }
    
  }	
		  ?>
         </ul>  
       </div><!-- button-group dropdown -->
       <div class="clearfix"></div>
    </div>
    <?php
    $listing_query = tep_db_query($listing_split->sql_query);
    
    $prod_list_contents = NULL;

$prod_list_contents .= '<div id="product-listing">';
	$prod_list_contents .= '  <ul class="inline-span">';

    while ($listing = tep_db_fetch_array($listing_query)) {
		
      $prod_list_contents .= '    <li class="listingContainer">';
      
      $prod_list_contents .= '      <div class="placeholder">';
      
	  if (isset($HTTP_GET_VARS['manufacturers_id'])  && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>';
      } else {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>';
      }
	  $prod_list_contents .= '      </div> <!--placeholder-->';
	  
      $prod_list_contents .= '      <div class="caption">';
      
	  $prod_list_contents .= '        <h2>';
      if (isset($HTTP_GET_VARS['manufacturers_id']) && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
      } else {
        $prod_list_contents .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
      }
      $prod_list_contents .= '        </h2>';
	  
      $prod_list_contents .= '        <small>' . strip_tags($listing['products_description'], '<br>') . '&hellip;</small>';
	  
	  $prod_list_contents .= '      </div> <!--caption-->';
	  
	  $prod_list_contents .= '      <div class="clearfix"></div>';

      $prod_list_contents .= '      <div class="price-wrap">';
	  
      if (tep_not_null($listing['specials_new_products_price'])) {
        $prod_list_contents .= '<span class="price-text"><del>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</del></span>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>';
      } else {
        $prod_list_contents .= '<span class="price-text">' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>';
      }
      
      $prod_list_contents .= '      </div> <!--pricewrap-->';
      
      $prod_list_contents .= '      <div class="btn-wrap">' . tep_draw_button(IMAGE_BUTTON_BUY_NOW, 'cart', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']), NULL, NULL, 'btn-success btn-sm');
      $prod_list_contents .= '      </div><!--btn-wrap-->';

	  $prod_list_contents .= '      <ul class="optional-wrap">';
	// manufacturer  
	if (($lc_show_manu == true) && ($listing['manufacturers_id'] !=  0))    
		$prod_list_contents .= '<li><span class="list-label">' . TABLE_HEADING_MANUFACTURER . '</span>&nbsp;<span class="productManu"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing['manufacturers_id']) . '">' . $listing['manufacturers_name'] . '</a></span>&nbsp;</li>';
	  
	// model 	
	if ( ($lc_show_model == true) && tep_not_null($listing['products_model']))
		$prod_list_contents .= '<li><span class="list-label">' . TABLE_HEADING_MODEL . '</span>&nbsp;<span class="productModel">' . $listing['products_model'] . '</span>&nbsp;</li>';
		
  	// stock
	if (($lc_show_qty == true) && (tep_get_products_stock($listing['products_id'])!= 0) )
		$prod_list_contents .= '<li><span class="list-label">' . TABLE_HEADING_QUANTITY . '</span>&nbsp;<span class="productQty">' . tep_get_products_stock($listing['products_id']) . '</span>&nbsp;</li>';
		
 	// weight
	if (($lc_show_lbs == true) && ($listing['products_weight'] != 0))
		$prod_list_contents .= '<li><span class="list-label">' . TABLE_HEADING_WEIGHT . '</span>&nbsp;<span class="productWeight">' . $listing['products_weight'] . '</span>&nbsp;</li>';
		
      $prod_list_contents .= '      </ul><!--optional-wrap-->';
	  

      $prod_list_contents .= '    </li>';
    }
    $prod_list_contents .= '  </ul>';
    $prod_list_contents .= '</div>';

    echo $prod_list_contents;
  } else {
?>

    <div class="alert alert-info"><?php echo TEXT_NO_PRODUCTS; ?></div>

<?php
  }

  if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<div class="row">
  <div class="col-sm-6 pagenumber hidden-xs">
    <?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
  </div>
  <div class="col-sm-6">
    <div class="pull-right pagenav"><ul class="pagination"><?php echo $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></ul></div>
    <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
  </div>
</div>
<?php
  }
?>

</div>
<?php if (MODULE_HEADER_TAGS_GRID_LIST_VIEW_STATUS != 'True') { ?>
<script>
  $('#product-listing .inline-span').addClass('one-across fluid-one-across');
</script>
<?php  } ?>
