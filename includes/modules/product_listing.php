<?php
	/*
		$Id$
		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2010 osCommerce
		
		Released under the GNU General Public License
	*/

	$listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
	
	// create column list
	$define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
	'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
	'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
	'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
	'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
	'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
	'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
	'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);
	
	asort($define_list);
	
	$column_list = array();

	foreach($define_list as $key => $value){
		if ($value > 0) $column_list[] = $key;		
	}
?>

<?php
	if ($messageStack->size('product_action') > 0) {
		echo $messageStack->output('product_action');
	}
?>
<div class="clearfix"></div>
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
		<div class="panel panel-default">
			<div class="panel-body">

				<?php
					if (MODULE_HEADER_TAGS_GRID_LIST_VIEW_STATUS == 'True') {
					?>
					<strong><?php echo TEXT_VIEW; ?></strong>
					<div class="btn-group" role="group">
						<a href="#" id="list" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span><?php echo TEXT_VIEW_LIST; ?></a>
						<a href="#" id="grid" class="btn btn-default"><span class="glyphicon glyphicon-th"></span><?php echo TEXT_VIEW_GRID; ?></a>
					</div>
					<?php
					}
				?>			

				<?php
				if(tep_not_null($filter_data)){
					echo $filter_data;
				}
				?>
				<div class="btn-group pull-right" role="group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<?php echo TEXT_SORT_BY; ?><span class="caret"></span>
					</button>
					
					<ul class="dropdown-menu">
						<?php
							$lc_show_model = false;
							$lc_show_manu = false;
							$lc_show_qty = false;
							$lc_show_lbs = false;
							for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
								$lc_key = false;
								
								switch ($column_list[$col]) {
									case 'PRODUCT_LIST_MODEL':
									$lc_text = TABLE_HEADING_MODEL;
									$lc_key = 'model';
									$lc_show_model = true;
									break;
									case 'PRODUCT_LIST_NAME':
									$lc_text = TABLE_HEADING_PRODUCTS;
									$lc_key = 'name';
									break;
									case 'PRODUCT_LIST_MANUFACTURER':
									$lc_text = TABLE_HEADING_MANUFACTURER;
									$lc_key = 'manufacturer';
									$lc_show_manu = true;
									break;
									case 'PRODUCT_LIST_PRICE':
									$lc_text = TABLE_HEADING_PRICE;
									$lc_key = 'price';
									break;
									case 'PRODUCT_LIST_QUANTITY':
									$lc_text = TABLE_HEADING_QUANTITY;
									$lc_key = 'quantity';
									$lc_show_qty = true;
									break;
									case 'PRODUCT_LIST_WEIGHT':
									$lc_text = TABLE_HEADING_WEIGHT;
									$lc_key = 'weight';
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
									//if ( ($column_list[$col] != 'PRODUCT_LIST_BUY_NOW') && ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {								
										  if ($lc_key !== false) {
											$lc_text = $OSCOM_Products->getListingSortLink($lc_key, $lc_text);
										  echo '      <li>' . $lc_text . '</li>';
										  }
									//}	  
								/*if ( ($column_list[$col] != 'PRODUCT_LIST_BUY_NOW') && ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
									if ($lc_key !== false) {	
										$lc_text = tep_create_sort_heading($lc_key, $lc_text);
										//$lc_text = tep_create_sort_heading($lc_key, $col+1, $lc_text);
										echo '        <li>' . $lc_text . '</li>';
									}
								}*/
							}
						?>
					</ul>
				</div>	
			</div>
		</div>
		<?php
			$listing_query = tep_db_query($listing_split->sql_query);
			
			$prod_list_contents = NULL;
			while ($listing = tep_db_fetch_array($listing_query)) {
				
				$osC_Product = new osC_Product($listing['products_id']);
				
				
				$prod_list_contents .= '<div class="item list-group-item col-sm-4">';
				$prod_list_contents .= '  <div class="productHolder equal-height">';
				
				if (isset($_GET['manufacturers_id'])  && tep_not_null($_GET['manufacturers_id'])) {
					$prod_list_contents .= '    <a href="' . tep_href_link('product_info.php', 'products_id=' . $osC_Product->getID() .'&manufacturers_id=' . $_GET['manufacturers_id']) . '">' . tep_image('images/' . $osC_Product->getImage(), $osC_Product->getTitle(), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, NULL, NULL, 'img-responsive thumbnail group list-group-image') . '</a>';
				} else {
				$prod_list_contents .= '    <a href="' . tep_href_link('product_info.php', 'products_id=' . $osC_Product->getID() . ($cPath ? '&cPath=' . $cPath : '')) . '">' . tep_image('images/' . $osC_Product->getImage(), $osC_Product->getTitle(), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, NULL, NULL, 'img-responsive thumbnail group list-group-image') . '</a>';
				}
				
				$prod_list_contents .= '    <div class="caption">';
				$prod_list_contents .= '      <h2 class="group inner list-group-item-heading">';
				
				if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
					$prod_list_contents .= '    <a href="' . tep_href_link('product_info.php',  'products_id=' . $osC_Product->getID() . '&manufacturers_id=' . $_GET['manufacturers_id'] ) . '">' . $osC_Product->getTitle() . '</a>';
				} else {
					$prod_list_contents .= '    <a href="' . tep_href_link('product_info.php', 'products_id=' . $osC_Product->getID() . ($cPath ? '&cPath=' . $cPath : '')) . '">' . $osC_Product->getTitle() . '</a>';		
				}
				
				$prod_list_contents .= '      </h2>';
				
				$prod_list_contents .= '      <p class="group inner list-group-item-text">' . strip_tags(substr($osC_Product->getDescription(), 0, 100), '<br>') . '&hellip;</p><div class="clearfix"></div>';
				
				// here it goes the extras, yuck
				$extra_list_contents = NULL;
				// manufacturer
				if (($lc_show_manu == true) && ($osC_Product->getManufacturerID() !=  0)) $extra_list_contents .= '<dt>' . TABLE_HEADING_MANUFACTURER . '</dt><dd><a href="' . tep_href_link('index.php', 'manufacturers=' . $osC_Product->getManufacturerID()) . '">' . $osC_Product->getManufacturer() . '</a></dd>';
				// model
				if ( ($lc_show_model == true) && tep_not_null($osC_Product->getModel())) $extra_list_contents .= '<dt>' . TABLE_HEADING_MODEL . '</dt><dd>' . $osC_Product->getModel() . '</dd>';
				// stock
				if ( ($lc_show_qty == true) ) $extra_list_contents .= '<dt>' . TABLE_HEADING_QUANTITY . '</dt><dd>' . $osC_Product->getQuantity() . '</dd>';
				// weight
				if (($lc_show_lbs == true) && ($osC_Product->getWeight() != 0)) $extra_list_contents .= '<dt>' . TABLE_HEADING_WEIGHT . '</dt><dd>' . $osC_Product->getWeight() . '</dd>';
				
				if (tep_not_null($extra_list_contents)) {
					$prod_list_contents .= '    <dl class="dl-horizontal list-group-item-text">';
					$prod_list_contents .=  $extra_list_contents;
					$prod_list_contents .= '    </dl>';
				}
				
				$prod_list_contents .= '      <div class="row">';

				$prod_list_contents .= '      <div class="col-xs-6"><div class="btn-group" role="group"><button type="button" class="btn btn-default">' . $osC_Product->getPriceFormated(true) . '</button></div></div>';
                $prod_list_contents .= '       <div class="col-xs-6 text-right">' . tep_draw_button(IMAGE_BUTTON_BUY_NOW, 'cart', tep_href_link(basename($PHP_SELF), 'products_id=' . $osC_Product->getID() . '&' . tep_get_all_get_params(array('action')) . '&action=cart_add'), NULL, NULL, 'btn-success btn-sm') . '</div>';

				$prod_list_contents .= '      </div>';
				
				$prod_list_contents .= '    </div>';
				$prod_list_contents .= '  </div>';
				$prod_list_contents .= '</div>';
				
			}
			
			echo '<div id="products" class="row list-group">' . $prod_list_contents . '</div>';
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
