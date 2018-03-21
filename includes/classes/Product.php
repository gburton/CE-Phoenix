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
			global $languages_id, $cart, $currencies;
			
			if ( !empty($id) ) {
				if ( is_numeric($id) ) {
					
					
					$Qproduct_Query = tep_db_query(" select products_id as id, products_quantity as quantity, products_price as price, products_model as model, products_gtin as gtin, products_tax_class_id as tax_class_id, products_weight as weight, products_weight_class as weight_class_id, products_date_available as date_expected, products_date_added as date_added, manufacturers_id, products_date_available from products where products_id = '" . (int)$id . "' and products_status = '1'");
					$Qproduct = tep_db_fetch_array($Qproduct_Query);
					
					if ( tep_db_num_rows($Qproduct_Query) === 1 ) {					
						$this->_data = $Qproduct;
						
						$this->_data['products_id'] = $Qproduct['id'];

						$this->_data['date_available'] = $Qproduct['products_date_available'];
						
						if ( !empty($this->_data) ) {
							$Qdesc_Query = tep_db_query("select products_name as name, products_description as description, products_keyword as keyword, products_tags as tags, products_url as url, products_seo_description as seo_description, products_seo_keywords as seo_keywords, products_seo_title as seo_title from products_description where products_id = '" . $this->_data['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
							$Qdesc = tep_db_fetch_array($Qdesc_Query);
							
							$this->_data = array_merge($this->_data, $Qdesc);
						}
					}
				} else {
					$Qproduct_Query = tep_db_query("select p.products_id as id, p.products_quantity as quantity, p.products_price as price, p.products_model as model, p.products_tax_class_id as tax_class_id, p.products_weight as weight, p.products_weight_class as weight_class_id, p.products_date_added as date_added, p.manufacturers_id, pd.products_name as name, pd.products_description as description, pd.products_keyword as keyword, pd.products_tags as tags, pd.products_url as url from products p, products_description pd where pd.products_keyword = '" . $id . "' and pd.language_id = '" . (int)$languages_id . "' and pd.products_id = p.products_id and p.products_status = '1'");
					$Qproduct = tep_db_fetch_array($Qproduct_Query);
					
					if (tep_db_num_rows($Qproduct_Query) === 1) {
						$this->_data = $Qproduct;
						
						$this->_data['products_id'] = $Qproduct['id'];
					}
				}
				
				if ( !empty($this->_data) ) {
					$this->_data['images'] = array();
					
					$Qimages_Query = tep_db_query("select id, image, htmlcontent, default_flag from products_images where products_id = '" . $this->_data['products_id'] . "' order by sort_order");
					
					while ( $Qimages = tep_db_fetch_array($Qimages_Query) ) {
						$this->_data['images'][] = $Qimages;
					}
					
					$Qcategory_Query = tep_db_query("select categories_id from products_to_categories where products_id = '" . $this->_data['products_id'] . "' limit 1");
					$Qcategory = tep_db_fetch_array($Qcategory_Query);
					
					$this->_data['category_id'] = $Qcategory['categories_id'];
					
					$this->_data['attributes'] = array();
					
	                $Qattributes_options_Query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name, popt.products_options_type from products_options popt, products_attributes patrib where patrib.products_id = '" . $this->_data['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");
					
					while ( $Qattributes_Options = tep_db_fetch_array($Qattributes_options_Query) ) {					
						
						
						$Qattributes_values_Query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pov.sort_order, pa.options_values_price, pa.price_prefix from products_attributes pa, products_options_values pov where pa.products_id = '" . $this->_data['products_id'] . "' and pa.options_id = '" . $Qattributes_Options['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'");
						
						while ( $Qattributes_Values = tep_db_fetch_array($Qattributes_values_Query) ) {
							
							if (is_string($_GET['products_id']) && isset($cart->contents[$_GET['products_id']]['attributes'][$Qattributes_Options['products_options_id']])) {
							  $selected_attribute = $cart->contents[$_GET['products_id']]['attributes'][$Qattributes_Options['products_options_id']];
							} else {
							  $selected_attribute = false;
							}
							
							$value_title = ($Qattributes_Values['options_values_price'] != '0') ? $Qattributes_Values['products_options_values_name'] . ' (' . $Qattributes_Values['price_prefix'] . $currencies->display_price($Qattributes_Values['options_values_price'], tep_get_tax_rate($this->_data['tax_class_id'])) .') ' : $Qattributes_Values['products_options_values_name'];

							$this->_data['attributes'][$this->_data['products_id']]['values'][$Qattributes_Options['products_options_id']][$Qattributes_Values['products_options_values_id']] = array('value_id' => $Qattributes_Values['products_options_values_id'],
							'group_title' => $Qattributes_Options['products_options_name'],
							'value_title' => $value_title,
							'sort_order' => (int)$Qattributes_Values['sort_order'],
							'default' => $selected_attribute,
							'module' => isset($Qattributes_Options['products_options_type']) ? $Qattributes_Options['products_options_type'] : 'pull_down_menu');
						}
					}
					
					$Qavg_Query = tep_db_query("select count(*) as count, avg(reviews_rating) as avgrating from reviews r, reviews_description rd where products_id = '" . $this->_data['products_id'] . "' and languages_id = '" . (int)$languages_id . "' and reviews_status = 1");
					$Qavg = tep_db_fetch_array($Qavg_Query);
					
					if ($Qavg['count'] > 0) {
						$this->_data['reviews_count'] = $Qavg['count'];
						$this->_data['reviews_average_rating'] = round($Qavg['avgrating']);					
					}

				}
				
			}
		}
		
		function isValid() {
			if (!empty($this->_data['products_id'])) {
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
			return $this->_data['products_id'];
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
				$price = $currencies->display_price($this->_data['price'], $this->_data['tax_class_id']);				
			}
			
			return $price;
		}
		
		function getQuantity() {
			return $this->_data['quantity'];
		}
		
		function getWeight() {
			global $osC_Weight;

			$weight = $osC_Weight->display($this->_data['weight'], $this->_data['weight_class_id']);
			
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
		
		function hasAttributes() {
			return (isset($this->_data['attributes']) && !empty($this->_data['attributes']));
		}
		
		function getAttributes($filter_duplicates = true) {
			if ( $filter_duplicates === true ) {
				$values_array = array();
				
				foreach ( $this->_data['attributes'] as $product_id => $attributes ) {
					foreach ( $attributes['values'] as $group_id => $values ) {
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
					usort($value['data'], array('osC_Product', '_usortAttributeValues'));
				}
				
				return $values_array;
			}
			
			return $this->_data['attributes'];
		}
		
		function attributeExists($attribute) {
			return is_numeric($this->getProductAttributeID($attribute));
		}
		
		function getProductAttributeID($attribute) {
			$_product_id = false;
			
			$_size = sizeof($attribute);
			
			foreach ( $this->_data['attributes'] as $product_id => $attributes ) {
				if ( sizeof($attributes['values']) === $_size ) {
					$_array = array();
					
					foreach ( $attributes['values'] as $group_id => $value ) {
						foreach ( $value as $value_id => $value_data ) {
							if ( is_array($attribute[$group_id]) && array_key_exists($value_id, $attribute[$group_id]) ) {
								$_array[$group_id][$value_id] = $attribute[$group_id][$value_id];
								} else {
								$_array[$group_id] = $value_id;
							}
						}
					}
					
					if ( sizeof(array_diff_assoc($_array, $attribute)) === 0 ) {
						$_product_id = $product_id;
						
						break;
					}
				}
			}
			
			return $_product_id;
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
			
			tep_db_query("update products_description set products_viewed = products_viewed+1 where products_id = '" . $this->_data['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
			
		}
		
		protected static function _usortAttributeValues($a, $b) {
			if ( $a['sort_order'] == $b['sort_order'] ) {
				return strnatcasecmp($a['text'], $b['text']);
			}
			
			return ( $a['sort_order'] < $b['sort_order'] ) ? -1 : 1;
		}
	}
?>