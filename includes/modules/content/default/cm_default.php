<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/
	
	class cm_default {
		var $code;
		var $group;
		var $title;
		var $description;
		var $sort_order;
		var $enabled = false;
		
		var $_page_contents = 'default.php';
		
		function __construct() {
			$this->code = get_class($this);
			$this->group = basename(dirname(__FILE__));
			
			$this->title = MODULE_CONTENT_DEFAULT_TEXT_TITLE;
			$this->description = MODULE_CONTENT_DEFAULT_TEXT_DESCRIPTION;
			
			if ( defined('MODULE_CONTENT_DEFAULT_TEXT_STATUS') ) {
				$this->sort_order = MODULE_CONTENT_DEFAULT_TEXT_SORT_ORDER;
				$this->enabled = (MODULE_CONTENT_DEFAULT_TEXT_STATUS == 'True');
			}
		}
		
		function execute() {
			global $cPath, $cPath_array, $page_title, $page_image, $current_category_id, $languages_id, $osC_Category, $oscTemplate;
			
			if (isset($cPath) && (empty($cPath) === false)) {
				
				$cat_id = implode(',', $cPath_array);
				$Qcategories_Query = tep_db_query("select categories_id, categories_name from categories_description where categories_id in ('" . $cat_id . "') and language_id = '" . (int)$languages_id . "'");
				
				
				$categories = array();
				while ( $Qcategories = tep_db_fetch_array($Qcategories_Query) ) {
					$categories[$Qcategories['categories_id']] = $Qcategories['categories_name'];
				}
				
				tep_db_free_result($Qcategories_Query);
				
				$osC_Category = new osC_Category($current_category_id);
				
				$page_title = $osC_Category->getTitle();
				
				if ( $osC_Category->hasImage() ) {
					$page_image = 'categories/' . $osC_Category->getImage();
				}
				
				$Qproducts_Query = tep_db_query("select products_id from products_to_categories where categories_id = '" . (int)$current_category_id . "' limit 1");
				
				
				if ( tep_db_num_rows($Qproducts_Query) > 0 ) {
					$this->_page_contents = 'product_listing.php';
					
					$this->_process();
				} else {
					$Qparent_Query = tep_db_query("select categories_id from categories where parent_id = '" . (int)$current_category_id . "' limit 1");
					
					
					if ( tep_db_num_rows($Qparent_Query) > 0 ) {
						$this->_page_contents = 'category_listing.php';
						$this->_process();
						
					} else {
						$this->_page_contents = 'product_listing.php';
						
						$this->_process();
					}
				}
			} elseif(isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])){
				$this->_page_contents = 'product_listing.php';				
				$this->_process();
			} else {
				
				$this->_process();
			
			}
			
		}
		
		
		function _process() {
			global $PHP_SELF, $cPath, $page_title, $page_image, $languages_id, $messageStack, $currencies, $current_category_id, $osC_Category, $osC_Products, $osC_Manufacturer, $oscTemplate;
			
			include('includes/classes/products.php');
			
			if (isset($cPath) && (empty($cPath) === false)) {			
				$osC_Products = new osC_Products($current_category_id);
				
				if (isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0)) {
					$osC_Products->setManufacturer($_GET['filter']);
				}
			}elseif(isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])){
				
				include('includes/classes/manufacturer.php');
				$osC_Manufacturer = new osC_Manufacturer($_GET['manufacturers']);				
				
				$osC_Products = new osC_Products();
				$osC_Products->setManufacturer($osC_Manufacturer->getID());
				
				$page_title = $osC_Manufacturer->getTitle();
				$page_image = 'manufacturers/' . $osC_Manufacturer->getImage();

				if (isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0)) {
					$osC_Products->setCategory($_GET['filter']);
				}			
			
			}
			
			if (isset($_GET['sort']) && !empty($_GET['sort'])) {
				if (strpos($_GET['sort'], '|d') !== false) {
					$osC_Products->setSortBy(substr($_GET['sort'], 0, -2), '-');
					} else {
					$osC_Products->setSortBy($_GET['sort']);
				}
			}
			
			$content_width = (int)MODULE_CONTENT_DEFAULT_TEXT_CONTENT_WIDTH;
			
			ob_start();
			include('includes/modules/content/' . $this->group . '/templates/' . $this->_page_contents);
			$template = ob_get_clean();
			
			$oscTemplate->addContent($template, $this->group);
		}
		
		
		
		function isEnabled() {
			return $this->enabled;
		}
		
		function check() {
			return defined('MODULE_CONTENT_DEFAULT_TEXT_STATUS');
		}
		
		function install() {
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable DefaultModule', 'MODULE_CONTENT_DEFAULT_TEXT_STATUS', 'True', 'Do you want to enable the Generic Text content module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_DEFAULT_TEXT_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_DEFAULT_TEXT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
		}
		
		function remove() {
			tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
		}
		
		function keys() {
			return array('MODULE_CONTENT_DEFAULT_TEXT_STATUS', 'MODULE_CONTENT_DEFAULT_TEXT_CONTENT_WIDTH', 'MODULE_CONTENT_DEFAULT_TEXT_SORT_ORDER');
		}
	}
	
