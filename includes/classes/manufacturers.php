<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		https://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
		
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License v2 (1991)
		as published by the Free Software Foundation.
	*/
	
	class osC_Manufacturers {
    
		protected $_data = array();

		public $root_start_string = '<ul>',
			$root_end_string = '</ul>',
			$child_start_string = '<li>',
			$child_end_string = '</li>';

		public function __construct() {
		  global $languages_id;

		  static $_manufacturers_data;

		  if ( isset($_manufacturers_data) ) {
			$this->_data = $_manufacturers_data;
		  } else {

			$Qmanufacturer_Query = tep_db_query("select m.manufacturers_id, m.manufacturers_image, m.manufacturers_name, mi.manufacturers_description, mi.manufacturers_seo_description, mi.manufacturers_seo_keywords, mi.manufacturers_seo_keywords from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id. "'");

			while( $Qmanufacturer = tep_db_fetch_array($Qmanufacturer_Query) ){
				if ( tep_db_num_rows($Qmanufacturer_Query) > 1 ) {
					$this->_data[$Qmanufacturer['manufacturers_id']] = array('id' => $Qmanufacturer['manufacturers_id'],
										   'name' => $Qmanufacturer['manufacturers_name'],
										   'description' => $Qmanufacturer['manufacturers_description'],
										   'seo_description' => $Qmanufacturer['manufacturers_seo_description'],
										   'seo_keywords' => $Qmanufacturer['manufacturers_seo_keywords'],
										   'seo_title' => $Qmanufacturer['manufacturers_seo_title'],
										   'image' => $Qmanufacturer['manufacturers_image']
					);
					
					$_manufacturers_data = $this->_data;
				}
			}
		  }
		}
		
		protected function _buildBranch() {
			global $request_type;
			$result = '';
			
			if ( isset($this->_data) ) {
				
				if (count($this->_data) <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
					$result .= $this->root_start_string;
					
					foreach ( $this->_data as $manufacturer_id => $manufacturer ) {

						$result .= $this->child_start_string;

						$link_title = ((strlen($manufacturer['name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturer['name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturer['name']);

						$result .= '<a href="' . tep_href_link('index.php', 'manufacturers_id=' . $manufacturer_id) . '">';
						$result .= $link_title . '</a>';

						$result .= $this->child_end_string;          
					}
					
					$result .= $this->root_end_string;
				
				}else{
					
					if (MAX_MANUFACTURERS_LIST < 2) {
						$manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
					}					

					foreach ( $this->_data as $manufacturer_id => $manufacturer ) {
						$manufacturers_array[] = array('id' => $manufacturer_id,
													   'text' => $manufacturer['name']);
					}	
					
					$result .=  tep_draw_form('manufacturers', tep_href_link('index.php', '', $request_type, false), 'get');
					$result .=			tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($_GET['manufacturers_id']) ? $_GET['manufacturers_id'] : ''), 'onchange="this.form.submit();"') . tep_hide_session_id();
					$result .= '</form>';				
				}
			}

			return $result;
		}		
		/**
		 * Return a formated string representation of the manufacturers
		 *
		 * @access public
		 * @return string
		 */

		public function getTree() {
			return $this->_buildBranch();
		}
		
		function exists($id) {
		  
			foreach ( $this->_data as $manufacturer_id) {
					  if ($id == $manufacturer_id) {
						return true;
					  }
			}
		  return false;
		}
    
		function setRootString($root_start_string, $root_end_string) {
		  $this->root_start_string = $root_start_string;
		  $this->root_end_string = $root_end_string;
		}
		
		function setChildString($child_start_string, $child_end_string) {
		  $this->child_start_string = $child_start_string;
		  $this->child_end_string = $child_end_string;
		}
	
	}
?>
