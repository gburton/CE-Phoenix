<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	abstract class osC_Attributes_Abstract {
		abstract static public function parse($data);
		abstract static public function allowsMultipleValues();
		abstract static public function hasCustomValue();
		
		static public function getGroupTitle($data) {
			return $data['group_title'];
		}
		
		static public function getValueTitle($data) {
			return $data['value_title'];
		}
	}
	
	class osC_Attributes {
		static public function parse($module, $data) {
			if ( !class_exists('osC_Attributes_' . $module) ) {
				if ( file_exists('includes/modules/attributes/' . basename($module) . '.php') ) {
					include('includes/modules/attributes/' . basename($module) . '.php');
				}
			}
			
			if ( class_exists('osC_Attributes_' . $module) ) {
				return call_user_func(array('osC_Attributes_' . $module, 'parse'), $data);
			}
		}
		
		static public function getGroupTitle($module, $data) {
			if ( !class_exists('osC_Attributes_' . $module) ) {
				if ( file_exists('includes/modules/attributes/' . basename($module) . '.php') ) {
					include('includes/modules/attributes/' . basename($module) . '.php');
				}
			}
			
			if ( class_exists('osC_Attributes_' . $module) ) {
				return call_user_func(array('osC_Attributes_' . $module, 'getGroupTitle'), $data);
			}
			
			return $data['group_title'];
		}
		
		static public function getValueTitle($module, $data) {
			if ( !class_exists('osC_Attributes_' . $module) ) {
				if ( file_exists('includes/modules/attributes/' . basename($module) . '.php') ) {
					include('includes/modules/attributes/' . basename($module) . '.php');
				}
			}
			
			if ( class_exists('osC_Attributes_' . $module) ) {
				return call_user_func(array('osC_Attributes_' . $module, 'getValueTitle'), $data);
			}
			
			return $data['value_title'];
		}
		
		static public function allowsMultipleValues($module) {
			if ( !class_exists('osC_Attributes_' . $module) ) {
				if ( file_exists('includes/modules/attributes/' . basename($module) . '.php') ) {
					include('includes/modules/attributes/' . basename($module) . '.php');
				}
			}
			
			if ( class_exists('osC_Attributes_' . $module) ) {
				return call_user_func(array('osC_Attributes_' . $module, 'allowsMultipleValues'));
			}
			
			return false;
		}
		
		static public function hasCustomValue($module) {
			if ( !class_exists('osC_Attributes_' . $module) ) {
				if ( file_exists('includes/modules/attributes/' . basename($module) . '.php') ) {
					include('includes/modules/attributes/' . basename($module) . '.php');
				}
			}
			
			if ( class_exists('osC_Attributes_' . $module) ) {
				return call_user_func(array('osC_Attributes_' . $module, 'hasCustomValue'));
			}
			
			return false;
		}
		
		static public function defineJavascript($products) {
			global $currencies;
			
			$string = '<script>var combos = new Array();' . "\n";
			
			foreach ( $products as $product_id => $product ) {
				$string .= 'combos[' . $product_id . '] = new Array();' . "\n" .
				'combos[' . $product_id . '] = { price: "' . addslashes($currencies->display_price($product['data']['price'], $product['data']['tax_class_id'])) . '", model: "' . addslashes($product['data']['model']) . '", availability_shipping: ' . (int)$product['data']['availability_shipping'] . ', values: [] };' . "\n";
				
				foreach ( $product['values'] as $group_id => $attributes ) {
					$check_flag = false;
					
					foreach ( $attributes as $attribute ) {
						if ( !osC_Attributes::hasCustomValue($attribute['module']) ) {
							if ( $check_flag === false ) {
								$check_flag = true;
								
								$string .= 'combos[' . $product_id . ']["values"][' . $group_id . '] = new Array();' . "\n";
							}
							
							$string .= 'combos[' . $product_id . ']["values"][' . $group_id . '][' . $attribute['value_id'] . '] = ' . $attribute['value_id'] . ';' . "\n";
						}
					}
				}
			}
			
			$string .= '</script>';
			
			return $string;
		}
	}
?>
