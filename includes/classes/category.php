<?php
	/*
		$Id: $
		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2007 osCommerce
		
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License v2 (1991)
		as published by the Free Software Foundation.
	*/
	
	/**
		* The osC_Category class manages category information
	*/
	
	class osC_Category {
		
		/**
			* An array containing the category information
			*
			* @var array
			* @access private
		*/
		
		private $_data = array();
		
		/**
			* Constructor
			*
			* @param int $id The ID of the category to retrieve information from
			* @access public
		*/
		
		public function __construct($id) {
			global $osC_CategoryTree;
			
			if ( $osC_CategoryTree->exists($id) ) {
				$this->_data = $osC_CategoryTree->getData($id);
			}
		}
		
		/**
			* Return the ID of the assigned category
			*
			* @access public
			* @return integer
		*/
		
		public function getID() {
			return $this->_data['id'];
		}
		
		/**
			* Return the title of the assigned category
			*
			* @access public
			* @return string
		*/
		
		public function getTitle() {
			return $this->_data['name'];
		}
		
/**
			* Return the description of the assigned category
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
			* Return the seo data of the assigned category
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
		function getSeoKeyWords() {
			if (isset($this->_data['seo_description'])) {
				return $this->_data['seo_description'];
			}
			
			return false;
		}
		
		function getSeoTitle() {
			if (isset($this->_data['seo_title'])) {
				return $this->_data['seo_title'];
			}
			
			return false;
		}		
		
		/**
			* Check if the category has an image
			*
			* @access public
			* @return string
		*/
		
		public function hasImage() {
			return ( !empty($this->_data['image']) );
		}
		
		/**
			* Return the image of the assigned category
			*
			* @access public
			* @return string
		*/
		
		public function getImage() {
			return $this->_data['image'];
		}
		
		/**
			* Check if the assigned category has a parent category
			*
			* @access public
			* @return boolean
		*/
		
		public function hasParent() {
			return ( $this->_data['parent_id'] > 0 );
		}
		
		/**
			* Return the parent ID of the assigned category
			*
			* @access public
			* @return integer
		*/
		
		public function getParent() {
			return $this->_data['parent_id'];
		}
		
		/**
			* Return the breadcrumb path of the assigned category
			*
			* @access public
			* @return string
		*/
		
		public function getPath() {
			global $osC_CategoryTree;
			
			return $osC_CategoryTree->buildBreadcrumb($this->_data['id']);
		}
		
		/**
			* Return specific information from the assigned category
			*
			* @access public
			* @return mixed
		*/
		
		public function getData($keyword) {
			return $this->_data[$keyword];
		}
	}
?>