<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cfgm_customer_data {

    public $code = 'customer_data';
    public $directory;
    public $language_directory = DIR_FS_CATALOG . 'includes/languages/';
    public $key = 'MODULE_CUSTOMER_DATA_INSTALLED';
    public $title = MODULE_CFG_MODULE_CUSTOMER_DATA_TITLE;
    public $template_integration = false;

    function __construct() {
      $this->directory = DIR_FS_CATALOG . "includes/modules/$this->code/";
    }

  }
