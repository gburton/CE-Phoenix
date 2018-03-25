<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
?>

<?php
	$content = '<div class="col-sm-'.$content_width.' category-new-products">';
	$content .= '	<h3>' . sprintf(MODULE_CONTENT_IN_NEW_PRODUCTS_HEADING, strftime('%B')) . '</h3>';
	$content .= '	<div class="row list-group" itemscope itemtype="http://schema.org/ItemList">';
	$content .= '		<meta itemprop="numberOfItems" content="' .  (int)$num_new_products . '" />';
	
	foreach ( $data as $product ) {				
		
		$content .= '<div class="col-sm-'. $product_width.'">';
		$content .= '	<div class="thumbnail equal-height is-product" data-is-special="' . $product['display_is_special'] .'">';			
		
		if( $showRating == true ){
			$content .= '  ' . tep_draw_stars($product['reviews_average_rating']) . ' ';
		}
		
		$content .= '		<a href="' .  tep_href_link('product_info.php', 'products_id=' . $product['id']) . '">' . $OSCOM_Image->show($product['display_image'], $product['name']) . '</a>';
		
		$content .= '		<div class="caption">';		
		$content .= '			<p class="text-center"><a itemprop="url" href="' .  tep_href_link('product_info.php', 'products_id=' . $product['id']) . '"><span itemprop="name">' .  $product['name'] . '</span></a></p>';
		$content .= '          <hr>';
	    $content .= '          <p class="text-center" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><meta itemprop="priceCurrency" content="' .  tep_output_string($currency) . '" /><span itemprop="price" content="' . $product['display_raw'] . '">' . $product['display_price'] . '</span></p>';
		
		$content .= '			<div class="text-center">';
		
		$content .= '				<div class="btn-group">';
		$content .= '					<a href="' .  tep_href_link('product_info.php', tep_get_all_get_params(array('action')) . 'products_id="' . $product['id']) . '" class="btn btn-default" role="button">' . MODULE_CONTENT_IN_NEW_PRODUCTS_BUTTON_VIEW . '</a>';				
		$content .= '					<a href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $product['id']) . '" data-has-attributes="' . (( $product['attributes'] > 0 ) ? 1:0) . '" data-in-stock="' . $product['display_stock'] . '" data-product-id="' . $product['id'] . '" class="btn btn-success btn-index btn-buy" role="button">' . MODULE_CONTENT_IN_NEW_PRODUCTS_BUTTON_BUY . '</a>';
		$content .= '				</div>';
		
		$content .= '			</div>';		
		
		$content .= '		</div>';		
		
		$content .= '	</div>';
		$content .= '</div>';
	}	
  
	$content .= '	</div>';
	$content .= '</div>';
	
	echo  $content;
?> 
         