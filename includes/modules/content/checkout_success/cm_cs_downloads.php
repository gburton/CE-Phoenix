<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_cs_downloads extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      if ( 'true' === DOWNLOAD_ENABLED ) {
        ob_start();
        extract($GLOBALS, EXTR_SKIP);
        include $oscTemplate->map_to_template('downloads.php', 'component');

        $oscTemplate->addContent(ob_get_clean(), $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS' => [
          'title' => 'Enable Product Downloads Module',
          'value' => 'True',
          'desc' => 'Should ordered product download links be shown on the checkout success page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
