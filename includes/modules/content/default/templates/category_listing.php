<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/
?>

<?php
	
	if (isset($cPath) && strpos('_', $cPath)) {
		// check to see if there are deeper categories within the current category
		$category_links = array_reverse($cPath_array);
		for($i=0, $n=sizeof($category_links); $i<$n; $i++) {
			$categories_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
			$categories = tep_db_fetch_array($categories_query);
			if ($categories['total'] < 1) {
				// do nothing, go through the loop
				} else {
				$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
				break; // we've found the deepest category the customer is in
			}
		}
		} else {
	
	echo $oscTemplate->getContent('index_nested');
				
	}
	

	

?>