<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  class bm_greeting {
    var $code = 'bm_greeting';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_greeting() {
      $this->title = MODULE_BOXES_GREETING_TITLE;
      $this->description = MODULE_BOXES_GREETING_DESCRIPTION;

      if ( defined('MODULE_BOXES_GREETING_STATUS') ) {
        $this->sort_order = MODULE_BOXES_GREETING_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_GREETING_STATUS == 'True');
		$this->group = 'boxes_content';
      }
    }

    function execute() {
      global $oscTemplate, $customer_id, $customer_first_name, $current_category_id, $PHP_SELF;
      if (((!isset($current_category_id)) || ($current_category_id == '0')) && MODULE_BOXES_GREETING_STATUS == 'True') {// Main page, show
        if (tep_session_is_registered('customer_first_name') && tep_session_is_registered('customer_id')) {
          $greeting_string = sprintf(TEXT_GREETING_PERSONAL, tep_output_string_protected($customer_first_name), tep_href_link(FILENAME_PRODUCTS_NEW));
        } else {
          $greeting_string = sprintf(TEXT_GREETING_GUEST, tep_href_link(FILENAME_LOGIN, '', 'SSL'), tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
        }

        $data = '  <!-- customer gretings -->' . "\n";
        $data .=  '	  <div class="contentText">';
        $data .= $greeting_string .  $PHP_SELF .'</div>' . "\n";
		
        if (tep_not_null(TEXT_MAIN)) {
          $data .= '	  <div class="contentText">' . MODULE_BOXES_MESSAGE . '</div>' ."\n" . '        <!-- end customer gretings -->' ."\n";
        }
          $oscTemplate->addBlock($data, $this->group);
	  }
	}
	
    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_GREETING_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Front page message Module', 'MODULE_BOXES_GREETING_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_GREETING_SORT_ORDER', '1000', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_GREETING_STATUS', 'MODULE_BOXES_GREETING_SORT_ORDER');
    }
  }
?>