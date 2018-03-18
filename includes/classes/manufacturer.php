<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		https://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
		
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License v2 (1991)
		as published by the Free Software Foundation.
	*/
	
	class osC_Manufacturer {		
		/**
			* An array containing the manufacturer information
			*
			* @var array
			* @access private
		*/
		
		private $_data = array();
		
		/**
			* Constructor
			*
			* @param int $id The ID of the manufacturer to retrieve information from
			* @access public
		*/
		
		public function __construct($id) {
			global $osC_Manufacturers;
			
			if ( $osC_Manufacturers->exists($id) ) {
				$this->_data = $osC_Manufacturers->getData($id);
			}
		}
		
		
		
		/**
			* Return the ID of the manufacturer
			*
			* @access public
			* @return integer
		*/		
		function getID() {
			if (isset($this->_data['id'])) {
				return $this->_data['id'];
			}
			
			return false;
		}
		
		/**
			* Return the title of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getTitle() {
			if (isset($this->_data['name'])) {
				return $this->_data['name'];
			}
			
			return false;
		}
		
		/**
			* Check if the manufacturer has a description
			*
			* @access public
			* @return string
		*/
		public function hasDescription() {
			return ( !empty($this->_data['description']) );
		}
		
		/**
			* Return the description of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getDescription() {
			if (isset($this->_data['description'])) {
				return $this->_data['description'];
			}
			
			return false;
		}
		
		/**
			* Check if the manufacturer has a seo description
			*
			* @access public
			* @return string
		*/
		public function hasSeoDescription() {
			return ( !empty($this->_data['seo_description']) );
		}
		
		/**
			* Return the seo data of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getSeoDescription() {
			if (isset($this->_data['seo_description'])) {
				return $this->_data['seo_description'];
			}
			
			return false;
		}		
		/**
			* Check if the manufacturer has seo keywords
			*
			* @access public
			* @return string
		*/
		public function hasSeoKeyWords() {
			return ( !empty($this->_data['seo_keywords']) );
		}
		
		/**
			* Return the seo keywords of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getSeoKeyWords() {
			if (isset($this->_data['seo_keywords'])) {
				return $this->_data['seo_keywords'];
			}
			
			return false;
		}
		
		/**
			* Check if the manufacturer has an seo title
			*
			* @access public
			* @return string
		*/
		public function hasSeoTitle() {
			return ( !empty($this->_data['seo_title']) );
		}
		
		/**
			* Return the seo title of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getSeoTitle() {
			if (isset($this->_data['seo_title'])) {
				return $this->_data['seo_title'];
			}
			
			return false;
		}
		
		/**
			* Check if the manufacturer has an image
			*
			* @access public
			* @return string
		*/
		public function hasImage() {
			return ( !empty($this->_data['image']) );
		}
		
		/**
			* Return the image of the manufacturer
			*
			* @access public
			* @return string
		*/		
		function getImage() {
			if (isset($this->_data['image'])) {
				return $this->_data['image'];
			}
			
			return false;
		}
		
	}
?>
