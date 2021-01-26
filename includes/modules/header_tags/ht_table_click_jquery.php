<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_table_click_jquery extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_TABLE_CLICK_JQUERY_';

    public function __construct() {
      parent::__construct(__FILE__);

      if ($this->base_constant('PLACEMENT') !== 'Header') {
        $this->group = 'footer_scripts';
      }
    }

    function execute() {
      if (!Text::is_empty($this->base_constant('PAGES'))
        && in_array(basename($GLOBALS['PHP_SELF']),
             page_selection::_get_pages($this->base_constant('PAGES'))))
      {
        $GLOBALS['oscTemplate']->addBlock(<<<'EOCSS'
<script>$('.table tr.table-selection').click(function() {
  $('.table tr.table-selection').removeClass('success').find('input').prop('checked', false);
  $(this).addClass('success').find('input').prop('checked', true);
});</script>
EOCSS
          , $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_TABLE_CLICK_JQUERY_STATUS' => [
          'title' => 'Enable Clickable Table Rows Module',
          'value' => 'True',
          'desc' => 'Do you want to enable the Clickable Table Rows module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_TABLE_CLICK_JQUERY_PAGES' => [
          'title' => 'Pages',
          'value' => 'checkout_payment.php;checkout_shipping.php',
          'desc' => 'The pages to add the jQuery Scripts to.',
          'use_func' => 'page_selection::_show_pages',
          'set_func' => 'page_selection::_edit_pages(',
        ],
        'MODULE_HEADER_TAGS_TABLE_CLICK_JQUERY_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
