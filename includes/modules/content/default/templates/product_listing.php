<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/
?>

<?php
	
    $pageHeading = null;
    if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {

	  $osC_Manufacturer = new osC_Manufacturer($_GET['manufacturers_id']);
	  
	  $pageHeading = $osC_Manufacturer->getTitle();
      $pageDescription = $osC_Manufacturer->getDescription();
      $pageImage = $osC_Manufacturer->getImage();    
	
	} elseif ($current_category_id) {

	  $pageHeading = $osC_Category->getTitle();
      $pageDescription = $osC_Category->getDescription();
      $pageImage = $osC_Category->getImage();
    }
?>

<div class="page-header">
  <h1><?php echo $pageHeading; ?></h1>
</div>

<?php
if (tep_not_null($pageDescription)) {
  echo '<div class="well well-sm">' . $pageDescription . '</div>';
}
?>
<div class="contentContainer">
	
	<?php
		// optional Product List Filter
		if (PRODUCT_LIST_FILTER > 0) {
		
			if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
				$filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = '" . (int)$_GET['manufacturers'] . "' order by cd.categories_name";
				} else {
				$filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by m.manufacturers_name";
			}
			$filterlist_query = tep_db_query($filterlist_sql);
			if (tep_db_num_rows($filterlist_query) > 1) {
				echo '<div>' . tep_draw_form('filter', 'index.php', 'get') . '<p align="right">' . TEXT_SHOW . '&nbsp;';
				if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
					echo tep_draw_hidden_field('manufacturers', $_GET['manufacturers']);
					$options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
					} else {
					echo tep_draw_hidden_field('cPath', $cPath);
					$options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
				}
				echo tep_draw_hidden_field('sort', $_GET['sort']);
				while ($filterlist = tep_db_fetch_array($filterlist_query)) {
					$options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
				}
				echo tep_draw_pull_down_menu('filter', $options, (isset($_GET['filter']) ? $_GET['filter'] : ''), 'onchange="this.form.submit()"');
				echo tep_hide_session_id() . '</p></form></div>' . "\n";
			}
		}
		
	?>
	
</div>
<?php		
	if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
		$osC_Products->setManufacturer($_GET['manufacturers']);
	}
	
	$listing_sql = $osC_Products->execute();

	
	include('includes/modules/product_listing.php');
?>
