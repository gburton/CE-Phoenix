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
			$selected = null;
			
			foreach ( $data['data'] as $option_value ) {

				if ( $option_value['default'] == $option_value['id'] ) {
					$selected = $option_value['id'];					
					break;
				}
			}
			
			$string = '<div class="row">';
			$string .='  <div class="well clearfix">';
			$string .='    <h4>' . $data['title'] . ':</h4>';
			$string .='    <div class="col-sm-4">' . tep_draw_pull_down_menu('id[' . $data['group_id'] . ']', $data['data'], $selected) . '</div>';
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