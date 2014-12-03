<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class ht_pages_meta {
    var $code = 'ht_pages_meta';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_pages_meta() {
      $this->title = MODULE_HEADER_TAGS_PAGES_META_TITLE;
      $this->description = MODULE_HEADER_TAGS_PAGES_META_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_PAGES_META_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_PAGES_META_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PAGES_META_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate;
      
      if ( (defined('META_SEO_DESCRIPTION')) && (strlen(META_SEO_DESCRIPTION) > 0) ) {
        $oscTemplate->addBlock('<meta name="description" content="' . tep_output_string(META_SEO_DESCRIPTION) . '" />', $this->group);
      }
      if ( (defined('META_SEO_KEYWORDS')) && (strlen(META_SEO_KEYWORDS) > 0) ) {
        $oscTemplate->addBlock('<meta name="keywords" content="' . tep_output_string(META_SEO_KEYWORDS) . '" />', $this->group);
      }
      
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_PAGES_META_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Pages Meta Module', 'MODULE_HEADER_TAGS_PAGES_META_STATUS', 'True', 'Do you want to allow page (eg: specials.php) meta tags to be added to the page header?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_PAGES_META_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_PAGES_META_STATUS', 'MODULE_HEADER_TAGS_PAGES_META_SORT_ORDER');
    }
  }
?>
