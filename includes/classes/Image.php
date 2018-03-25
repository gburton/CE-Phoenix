<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/

	class Image {
		
		protected $_groups;

		public function __construct() {
			global $languages_id;

			$this->_groups = array();
			$products_images_groups_query = tep_db_query("select * from products_images_groups where language_id = '" . (int)$languages_id . "'");
			while($products_images_groups = tep_db_fetch_array($products_images_groups_query)){
			
				$this->_groups[(int)$products_images_groups['id']] = $products_images_groups;
			}
		}
		
		public function getID($code) {
			foreach ( $this->_groups as $group ) {
				if ( $group['code'] == $code ) {
					return $group['id'];
				}
			}

			return 0;
		}

		public function getCode($id) {
			return $this->_groups[$id]['code'];
		}

		public function getWidth($code) {
			return $this->_groups[$this->getID($code)]['size_width'];
		}

		public function getHeight($code) {
			return $this->_groups[$this->getID($code)]['size_height'];
		}

		public function exists($code) {
			return isset($this->_groups[$this->getID($code)]);
		}		

		public function show($image, $title, $parameters = null, $group = null) {
			
			if ( empty($group) || !$this->exists($group) ) {
				$group = $this->getCode(DEFAULT_IMAGE_GROUP_ID);
			}
			
            if($group == 0){
				$url = tep_image('images/' . $image, $title, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'itemprop="image"');
			}else{	
				$group_id = $this->getID($group);

				$width = $height = '';

				if ( ($this->_groups[$group_id]['force_size'] == '1') || empty($image) ) {
					$width = $this->_groups[$group_id]['size_width'];
					$height = $this->_groups[$group_id]['size_height'];
				}

				if ( empty($image) ) {
					$image = 'no_image_available_150_150.gif';
				} else {
					$image = $this->_groups[$group_id]['code'] . '/' . $image;
				}
				$url = tep_image('images/' . $image, $title, $width, $height, $parameters);
			}
			//$url = (OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server') : OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server');
			
			return $url;
		}

		public function getAddress($image, $group = 'default') {
			$group_id = $this->getID($group);

			$url = (OSCOM::getRequestType() == 'NONSSL') ? OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server') : OSCOM::getConfig('product_images_http_server') . OSCOM::getConfig('product_images_dir_ws_http_server');

			return $url . $this->_groups[$group_id]['code'] . '/' . $image;
		}
		
	}