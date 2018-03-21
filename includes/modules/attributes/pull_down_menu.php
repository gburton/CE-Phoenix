<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Attributes_pull_down_menu extends osC_Attributes_Abstract {
		const ALLOW_MULTIPLE_VALUES = false;
		const HAS_CUSTOM_VALUE = false;
		
		static public function parse($data) {
			global $cart;
			$selected = null;
			
			$fr_input = $fr_required = $fr_feedback = null;	
			if (MODULE_CONTENT_PI_OA_ENFORCE == 'True') {	
				$fr_input    = FORM_REQUIRED_INPUT;	
				$fr_required = 'required aria-required="true" '; 	
				$fr_feedback = ' has-feedback';	
			}	
			if (MODULE_CONTENT_PI_OA_HELPER == 'True') {	
				$enforce_selection[] = array('id' => '', 'text' => MODULE_CONTENT_PI_OA_ENFORCE_SELECTION);
				$data['data'] = array_merge($enforce_selection, $data['data']);			
			}			
			
			foreach ( $data['data'] as $option_value ) {

				if ( $option_value['default'] == $option_value['id'] ) {
                    
					if (is_string($_GET['products_id']) && isset($cart->contents[$_GET['products_id']]['attributes'][$data['group_id']])) {
						array_shift($data['data']);	
					}
					$selected = $option_value['id'];					
					break;
				}
			}
			
			$string = '<div class="form-group' . $fr_feedback . '">' . PHP_EOL;	
			$string .=   '<label for="input_' . $data['title'] . '" class="control-label col-sm-3">' . $data['title'] . '</label>' . PHP_EOL; 	
			$string .=   '<div class="col-sm-9">' . PHP_EOL;	
			$string .=     tep_draw_pull_down_menu('id[' . $data['group_id'] . ']', $data['data'], $selected, $fr_required . 'id="input_' . $data['group_id'] . '"') . PHP_EOL;
			$string .=     $fr_input;	
			$string .='	</div>';
			$string .='</div>';
			
			return $string;
		}
		
		static public function allowsMultipleValues() {
			return self::ALLOW_MULTIPLE_VALUES;
		}
		
		static public function hasCustomValue() {
			return self::HAS_CUSTOM_VALUE;
		}
	}
?>