<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class breadcrumb {

    public $_trail;

    public function __construct() {
      $this->reset();
    }

    public function reset() {
      $this->_trail = [];
    }

    public function add($title, $link = '') {
      $this->_trail[] = ['title' => $title, 'link' => $link];
    }

    public function prepend($title, $link = '') {
      array_unshift($this->_trail, ['title' => $title, 'link' => $link]);
    }

    public function trail($separator = null) {
      return $this->_trail;
    }

  }
  