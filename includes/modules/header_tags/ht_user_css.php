<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

  class ht_user_css {
    var $code = 'ht_user_css';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_USER_CSS_TITLE;
      $this->description = MODULE_HEADER_TAGS_USER_CSS_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_USER_CSS_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_USER_CSS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_USER_CSS_STATUS == 'True');
      }
    }

    function execute() {
      $oscTemplate->addBlock('<link href="user.css" rel="stylesheet">');
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_USER_CSS_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Robot NoIndex Module', 'MODULE_HEADER_TAGS_USER_CSS_STATUS', 'True', 'Do you want to enable the user.css module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_USER_CSS_SORT_ORDER', '9999', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_USER_CSS_STATUS', 'MODULE_HEADER_TAGS_USER_CSS_SORT_ORDER');
    }

  }
