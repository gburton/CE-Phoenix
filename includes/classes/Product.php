<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/
	
	class osC_Product {
		var $_data = array();
		
		function __construct($id) {
			global $languages_id;
			
			if ( !empty($id) ) {
				if ( is_numeric($id) ) {
					
					
					$Qproduct_Query = tep_db_query(" select products_id as id, parent_id, products_quantity as quantity, products_price as price, products_model as model, products_gtin as gtin, products_tax_class_id as tax_class_id, products_weight as weight, products_weight_class as weight_class_id, products_date_available as date_expected, products_date_added as date_added, manufacturers_id, has_children, products_date_available from products where products_id = '" . (int)$id . "' and products_status = '1'");
					$Qproduct = tep_db_fetch_array($Qproduct_Query);
					
					if ( tep_db_num_rows($Qproduct_Query) === 1 ) {					
						$this->_data = $Qproduct;
						
						$this->_data['master_id'] = $Qproduct['id'];
						$this->_data['has_children'] = $Qproduct['has_children'];
						
						// NOTICE: using the direct value, not via attributes as how it intended to be.
						// VIA FUNTION: getDateAvailable()  around line 317
						$this->_data['date_available'] = $Qproduct['products_date_available'];
						
						if ( $Qproduct['parent_id'] > 0 ) {
							$Qmaster_Query = tep_db_query("select products_id, has_children from products where products_id = '" . (int)$id . "' and products_status = '1'");
							$Qmaster = tep_db_fetch_array($Qmaster_Query);
							
							if ( tep_db_num_rows($Qmaster_Query) === 1 ) {
								$this->_data['master_id'] = $Qmaster['products_id'];
								$this->_data['has_children'] = $Qmaster['has_children'];
								} else { // master product is disabled so invalidate the product variant
								$this->_data = array();
							}
						}
						
						if ( !empty($this->_data) ) {
							$Qdesc_Query = tep_db_query("select products_name as name, products_description as description, products_keyword as keyword, products_tags as tags, products_url as url from products_description where products_id = '" . $this->_data['master_id'] . "' and language_id = '" . (int)$languages_id . "'");
							$Qdesc = tep_db_fetch_array($Qdesc_Query);
							
							$this->_data = array_merge($this->_data, $Qdesc);
						}
					}
				} else {
					$Qproduct_Query = tep_db_query("select p.products_id as id, p.parent_id, p.products_quantity as quantity, p.products_price as price, p.products_model as model, p.products_tax_class_id as tax_class_id, p.products_weight as weight, p.products_weight_class as weight_class_id, p.products_date_added as date_added, p.manufacturers_id, p.has_children, pd.products_name as name, pd.products_description as description, pd.products_keyword as keyword, pd.products_tags as tags, pd.products_url as url from products p, products_description pd where pd.products_keyword = '" . $id . "' and pd.language_id = '" . (int)$languages_id . "' and pd.products_id = p.products_id and p.products_status = '1'");
					$Qproduct = tep_db_fetch_array($Qproduct_Query);
					
					if (tep_db_num_rows($Qproduct_Query) === 1) {
						$this->_data = $Qproduct;
						
						$this->_data['master_id'] = $Qproduct['id'];
						$this->_data['has_children'] = $Qproduct['has_children'];
					}
				}
				
				if ( !empty($this->_data) ) {
					$this->_data['images'] = array();
					
					$Qimages_Query = tep_db_query("select id, image, htmlcontent, default_flag from products_images where products_id = '" . $this->_data['master_id'] . "' order by sort_order");
					
					while ( $Qimages = tep_db_fetch_array($Qimages_Query) ) {
						$this->_data['images'][] = $Qimages;
					}
					
					$Qcategory_Query = tep_db_query("select categories_id from products_to_categories where products_id = '" . $this->_data['master_id'] . "' limit 1");
					$Qcategory = tep_db_fetch_array($Qcategory_Query);
					
					$this->_data['category_id'] = $Qcategory['categories_id'];
					
					if ( (int)$this->_data['has_children'] === 1 ) {
						$this->_data['variants'] = array();
						
						$Qsubproducts_Query = tep_db_query("select * from products where parent_id = '" . $this->_data['master_id'] . "' and products_status = '1'");
						
						while ( $Qsubproducts = tep_db_fetch_array($Qsubproducts_Query) ) {
							$this->_data['variants'][$Qsubproducts['products_id']]['data'] = array('price' => $Qsubproducts['products_price'],
							'tax_class_id' => (int)$Qsubproducts['products_tax_class_id'],
							'model' => $Qsubproducts['products_model'],
							'quantity' => (int)$Qsubproducts['products_quantity'],
							'weight' => $Qsubproducts['products_weight'],
							'weight_class_id' => (int)$Qsubproducts['products_weight_class'],
							'availability_shipping' => 1);
							
							$Qvariants_Query = tep_db_query("select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title, pvv.sort_order as value_sort_order from products_variants pv, products_variants_groups pvg, products_variants_values pvv where pv.products_id = '" . $Qsubproducts['products_id'] . "' and pv.products_variants_values_id = pvv.id and pvv.languages_id = '" . (int)$languages_id . "' and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = '" . (int)$languages_id . "' order by pvg.sort_order, pvg.title");
							
							while ( $Qvariants = tep_db_fetch_array($Qvariants_Query) ) {
								$this->_data['variants'][$Qsubproducts['products_id']]['values'][$Qvariants['group_id']][$Qvariants['value_id']] = array('value_id' => $Qvariants['value_id'],
								'group_title' => $Qvariants['group_title'],
								'value_title' => $Qvariants['value_title'],
								'sort_order' => (int)$Qvariants['value_sort_order'],
								'default' => (bool)$Qvariants['default_combo'],
								'module' => $Qvariants['module']);
							}
						}
					}
					
					$this->_data['attributes'] = array();
					
					/*$Qattributes_Query = tep_db_query("select tb.code, pa.value from product_attributes pa, templates_boxes tb where pa.products_id = '" . $this->_data['master_id'] . "' and pa.languages_id in (0, '" . (int)$languages_id . "') and pa.id = tb.id");
					
					while ( $Qattributes = tep_db_fetch_array($Qattributes_Query) ) {
						$this->_data['attributes'][$Qattributes['code']] = $Qattributes['value'];
					}*/
					
					$Qavg_Query = tep_db_query("select count(*) as count, avg(reviews_rating) as avgrating from reviews r, reviews_description rd where products_id = '" . $this->_data['master_id'] . "' and languages_id = '" . (int)$languages_id . "' and reviews_status = 1");
					$Qavg = tep_db_fetch_array($Qavg_Query);
					
					if ($Qavg['count'] > 0) {
						$this->_data['reviews_count'] = $Qavg['count'];
						$this->_data['reviews_average_rating'] = round($Qavg['avgrating']);					
					}

				}
				
			}
		}
		
		function isValid() {
			if (!empty($this->_data['master_id'])) {
				return true;
				} else {
				return false;
			}
		}
		
		function getData($key = null) {
			if ( isset($this->_data[$key]) ) {
				return $this->_data[$key];
			}
			
			return $this->_data;
		}
		
		function getReviewsAvg() {
			return $this->_data['reviews_average_rating'];
		}
		
		function getReviewsCount() {
			return $this->_data['reviews_count'];
		}
		
		function getID() {
			return $this->_data['id'];
		}
		
		function getMasterID() {
			return $this->_data['master_id'];
		}
		
		function getTitle() {
			return $this->_data['name'];
		}
		
		function getDescription() {
			return $this->_data['description'];
		}
		
		function hasModel() {
			return (isset($this->_data['model']) && !empty($this->_data['model']));
		}
		
		function getModel() {
			return $this->_data['model'];
		}
		
		function hasKeyword() {
			return (isset($this->_data['keyword']) && !empty($this->_data['keyword']));
		}
		
		function getKeyword() {
			return $this->_data['keyword'];
		}
		
		function hasTags() {
			return (isset($this->_data['tags']) && !empty($this->_data['tags']));
		}
		
		function getTags() {
			return $this->_data['tags'];
		}
		
		function getPrice() {
		}
		
		function getPriceFormated($with_special = false) {
			global $osC_Services, $osC_Specials, $currencies;
			
			if (($with_special === true) && ($new_price = tep_get_products_special_price($this->_data['id']))) {
				$price = '<s>' . $currencies->display_price($this->_data['price'], $this->_data['tax_class_id']) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, $this->_data['tax_class_id']) . '</span>';
				} else {
				if ( $this->hasVariants() ) {
					//$price = 'from&nbsp;' . $currencies->display_price($this->getVariantMinPrice(), $this->_data['tax_class_id']);
					$price = $currencies->display_price($this->getVariantMinPrice(), $this->_data['tax_class_id']);
					} else {
					$price = $currencies->display_price($this->_data['price'], $this->_data['tax_class_id']);
				}
			}
			
			return $price;
		}
		
		function getVariantMinPrice() {
			$price = null;
			
			foreach ( $this->_data['variants'] as $variant ) {
				if ( ($price === null) || ($variant['data']['price'] < $price) ) {
					$price = $variant['data']['price'];
				}
			}
			
			return ( $price !== null ) ? $price : 0;
		}
		
		function getVariantMaxPrice() {
			$price = 0;
			
			foreach ( $this->_data['variants'] as $variant ) {
				if ( $variant['data']['price'] > $price ) {
					$price = $variant['data']['price'];
				}
			}
			
			return $price;
		}
		
		function getQuantity() {
			$quantity = $this->_data['quantity'];
			
			if ( $this->hasVariants() ) {
				$quantity = 0;
				
				foreach ( $this->_data['variants'] as $variants ) {
					$quantity += $variants['data']['quantity'];
				}
			}
			
			return $quantity;
		}
		
		function getWeight() {
			global $osC_Weight;
			
			$weight = 0;
			
			if ( $this->hasVariants() ) {
				foreach ( $this->_data['variants'] as $subproduct_id => $variants ) {
					foreach ( $variants['values'] as $group_id => $values ) {
						foreach ( $values as $value_id => $data ) {
							if ( $data['default'] === true ) {
								$weight = $osC_Weight->display($variants['data']['weight'], $variants['data']['weight_class_id']);
								
								break 3;
							}
						}
					}
				}
				} else {
				$weight = $osC_Weight->display($this->_data['weight'], $this->_data['weight_class_id']);
			}
			
			return $weight;
		}
		
		function hasManufacturer() {
			return ( $this->_data['manufacturers_id'] > 0 );
		}
		
		function getManufacturer() {
			if ( !class_exists('osC_Manufacturer') ) {
				include('includes/classes/manufacturer.php');
			}
			
			$osC_Manufacturer = new osC_Manufacturer($this->_data['manufacturers_id']);
			
			return $osC_Manufacturer->getTitle();
		}
		
		function getManufacturerID() {
			return $this->_data['manufacturers_id'];
		}
		
		function getCategoryID() {
			return $this->_data['category_id'];
		}
		
		function numberOfImages() {
			return sizeof($this->_data['images']);
		}		
		
		function getImages() {
			return $this->_data['images'];
		}
		
		function hasImage() {
			foreach ($this->_data['images'] as $image) {
				if ($image['default_flag'] == '1') {
					return true;
				}
			}
		}
		
		function getImage() {
			foreach ($this->_data['images'] as $image) {
				if ($image['default_flag'] == '1') {
					return $image['image'];
				}
			}
		}
		
		function hasURL() {
			return (isset($this->_data['url']) && !empty($this->_data['url']));
		}
		
		function getURL() {
			return $this->_data['url'];
		}
		
		function getDateAvailable() {
			// HPDL
			//return false; //$this->_data['date_available'];
			return $this->_data['date_available'];
		}
		
		function getDateAdded() {
			return $this->_data['date_added'];
		}
		
		function hasVariants() {
			return (isset($this->_data['variants']) && !empty($this->_data['variants']));
		}
		
		function getVariants($filter_duplicates = true) {
			if ( $filter_duplicates === true ) {
				$values_array = array();
				
				foreach ( $this->_data['variants'] as $product_id => $variants ) {
					foreach ( $variants['values'] as $group_id => $values ) {
						foreach ( $values as $value_id => $value ) {
							if ( !isset($values_array[$group_id]) ) {
								$values_array[$group_id]['group_id'] = $group_id;
								$values_array[$group_id]['title'] = $value['group_title'];
								$values_array[$group_id]['module'] = $value['module'];
							}
							
							$value_exists = false;
							
							if ( isset($values_array[$group_id]['data']) ) {
								foreach ( $values_array[$group_id]['data'] as $data ) {
									if ( $data['id'] == $value_id ) {
										$value_exists = true;
										
										break;
									}
								}
							}
							
							if ( $value_exists === false ) {
								$values_array[$group_id]['data'][] = array('id' => $value_id,
								'text' => $value['value_title'],
								'default' => $value['default'],
								'sort_order' => $value['sort_order']);
								} elseif ( $value['default'] === true ) {
								foreach ( $values_array[$group_id]['data'] as &$existing_data ) {
									if ( $existing_data['id'] == $value_id ) {
										$existing_data['default'] = true;
										
										break;
									}
								}
							}
						}
					}
				}
				
				foreach ( $values_array as $group_id => &$value ) {
					usort($value['data'], array('osC_Product', '_usortVariantValues'));
				}
				
				return $values_array;
			}
			
			return $this->_data['variants'];
		}
		
		function variantExists($variant) {
			return is_numeric($this->getProductVariantID($variant));
		}
		
		function getProductVariantID($variant) {
			$_product_id = false;
			
			$_size = sizeof($variant);
			
			foreach ( $this->_data['variants'] as $product_id => $variants ) {
				if ( sizeof($variants['values']) === $_size ) {
					$_array = array();
					
					foreach ( $variants['values'] as $group_id => $value ) {
						foreach ( $value as $value_id => $value_data ) {
							if ( is_array($variant[$group_id]) && array_key_exists($value_id, $variant[$group_id]) ) {
								$_array[$group_id][$value_id] = $variant[$group_id][$value_id];
								} else {
								$_array[$group_id] = $value_id;
							}
						}
					}
					
					if ( sizeof(array_diff_assoc($_array, $variant)) === 0 ) {
						$_product_id = $product_id;
						
						break;
					}
				}
			}
			
			return $_product_id;
		}
		
		function hasAttribute($code) {
			return isset($this->_data['attributes'][$code]);
		}
		
		function getAttribute($code) {
			if ( !class_exists('osC_ProductAttributes_' . $code) ) {
				//if ( file_exists('includes/modules/product_attributes/' . basename($code) . '.php') ) {
				include('includes/modules/product_attributes/' . basename($code) . '.php');
				//}
			}
			
			if ( class_exists('osC_ProductAttributes_' . $code) ) {
				return call_user_func(array('osC_ProductAttributes_' . $code, 'getValue'), $this->_data['attributes'][$code]);
			}
		}
		function checkEntry($id) {
			
			if ( is_numeric($id) ) {
				
				$Qproduct_Query = tep_db_query("select p.products_id from products p where p.products_id = '" . (int)$id . "' and p.products_status = 1 limit 1");

			} else {
				
				$Qproduct_Query = tep_db_query("select p.products_id from products p, products_description pd where pd.products_keyword = '" . $id . "' and pd.products_id = p.products_id and p.products_status = 1 limit 1");
			
			}
			
			$Qproduct = tep_db_fetch_array($Qproduct_Query);			
			
			return ( tep_db_num_rows($Qproduct_Query) === 1 );
		}
		
		function incrementCounter() {
			global $languages_id;			
			
			tep_db_query("update products_description set products_viewed = products_viewed+1 where products_id = '" . $this->_data['master_id'] . "' and language_id = '" . (int)$languages_id . "'");
			
		}
		
		protected static function _usortVariantValues($a, $b) {
			if ( $a['sort_order'] == $b['sort_order'] ) {
				return strnatcasecmp($a['text'], $b['text']);
			}
			
			return ( $a['sort_order'] < $b['sort_order'] ) ? -1 : 1;
		}
	}
?>