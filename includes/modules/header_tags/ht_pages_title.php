<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class ht_pages_title {
    var $code = 'ht_pages_title';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_pages_title() {
      $this->title = MODULE_HEADER_TAGS_PAGES_TITLE_TITLE;
      $this->description = MODULE_HEADER_TAGS_PAGES_TITLE_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_PAGES_TITLE_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_PAGES_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PAGES_TITLE_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate;

      if ( (defined('META_SEO_TITLE')) && (strlen(META_SEO_TITLE) > 0) ) {
        $oscTemplate->setTitle(tep_output_string(META_SEO_TITLE)  . ', ' . $oscTemplate->getTitle());
      }

    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_PAGES_TITLE_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Title Module', 'MODULE_HEADER_TAGS_PAGES_TITLE_STATUS', 'True', 'Do you want to allow product titles to be added to the page title?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_PAGES_TITLE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_PAGES_TITLE_STATUS', 'MODULE_HEADER_TAGS_PAGES_TITLE_SORT_ORDER');
    }
  }
?>
