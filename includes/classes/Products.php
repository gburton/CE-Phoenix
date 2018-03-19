<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/
	
	class osC_Products {
		var $_category,
        $_recursive = true,
        $_manufacturer,
        $_sql_query,
        $_sort_by,
        $_sort_by_direction;
		
		/* Class constructor */
		
		function __construct($id = null) {
			if (is_numeric($id)) {
				$this->_category = $id;
			}
		}
		
		/* Public methods */
		
		function hasCategory() {
			return isset($this->_category) && !empty($this->_category);
		}
		
		function isRecursive() {
			return $this->_recursive;
		}
		
		function hasManufacturer() {
			return isset($this->_manufacturer) && !empty($this->_manufacturer);
		}
		
		function setCategory($id, $recursive = true) {
			$this->_category = $id;
			
			if ($recursive === false) {
				$this->_recursive = false;
			}
		}
		
		function setManufacturer($id) {
			$this->_manufacturer = $id;
		}
		
		function setSortBy($field, $direction = '+') {
			switch ($field) {
				case 'model':
				$this->_sort_by = 'p.products_model';
				break;
				case 'manufacturer':
				$this->_sort_by = 'm.manufacturers_name';
				break;
				case 'quantity':
				$this->_sort_by = 'p.products_quantity';
				break;
				case 'weight':
				$this->_sort_by = 'p.products_weight';
				break;
				case 'price':
				$this->_sort_by = 'p.products_price';
				break;
				case 'date_added':
				$this->_sort_by = 'p.products_date_added';
				break;
			}
			
			$this->_sort_by_direction = ($direction == '-') ? '-' : '+';
		}
		
		function setSortByDirection($direction) {
			$this->_sort_by_direction = ($direction == '-') ? '-' : '+';
		}
		
		function execute() {
			global $languages_id, $osC_CategoryTree, $osC_Image;
			
			$listing_sql_Ext = '';
			
			if ($this->hasCategory()) {
				if ($this->isRecursive()) {
					
					$subcategories_array = array($this->_category);
					$categories_id = implode(',', $osC_CategoryTree->getChildren($this->_category, $subcategories_array));
					
					$listing_sql_Ext .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in ('" . (int)$categories_id . "')";
					} else {
					$categories_id = $this->_category;
					
					$listing_sql_Ext .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$categories_id . "'";
				}
			}
			
			if ($this->hasManufacturer()) {
				$manufacturers_id = $this->_manufacturer;
				$listing_sql_Ext .= " and p.manufacturers_id = '" . (int)$manufacturers_id . "' ";
			}
			
			$listing_sql_Ext .= " order by ";
			
			if (isset($this->_sort_by)) {
				
				$order_by = $this->_sort_by;
				$order_by_direction = (($this->_sort_by_direction == '-') ? 'desc' : '');
				
				$listing_sql_Ext .= " " . $order_by . " " . $order_by_direction . ", pd.products_name";
				
				} else {
				$order_by_direction = (($this->_sort_by_direction == '-') ? 'desc' : '');
				
				$listing_sql_Ext .= " pd.products_name " . $order_by_direction . " ";
			}			
			
			$listing_sql = "select distinct p.products_id, p.products_price, p.products_tax_class_id, p.products_image, pd.products_name, pd.products_description, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from products p left join products_attributes pa on (p.products_id = pa.products_id) left join manufacturers m on p.manufacturers_id = m.manufacturers_id left join specials s on p.products_id = s.products_id, products_description pd, categories c, products_to_categories p2c where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = " . (int)$languages_id . " and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id " . $listing_sql_Ext . " ";
						
			return $listing_sql;
		}
	}
?>
