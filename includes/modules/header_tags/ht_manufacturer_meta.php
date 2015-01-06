<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class ht_manufacturer_meta {
    var $code = 'ht_manufacturer_meta';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_manufacturer_meta() {
      $this->title = MODULE_HEADER_TAGS_MANUFACTURERS_META_TITLE;
      $this->description = MODULE_HEADER_TAGS_MANUFACTURERS_META_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_MANUFACTURERS_META_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_MANUFACTURERS_META_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_MANUFACTURERS_META_STATUS == 'True');
      }
    }

    function execute() {
       global $PHP_SELF, $HTTP_GET_VARS, $oscTemplate, $manufacturers, $languages_id;

      if (basename($PHP_SELF) == FILENAME_DEFAULT) {
        if (isset($HTTP_GET_VARS['manufacturers_id']) && is_numeric($HTTP_GET_VARS['manufacturers_id'])) {
          $meta_info_query = tep_db_query("select manufacturers_seo_description, manufacturers_seo_keywords from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id']  . "' and languages_id = '" . (int)$languages_id . "'");
          $meta_info = tep_db_fetch_array($meta_info_query);

          if (tep_not_null($meta_info['manufacturers_seo_description'])) {
            $oscTemplate->addBlock('<meta name="description" content="' . tep_output_string($meta_info['manufacturers_seo_description']) . '" />', $this->group);
          }
          if ( (tep_not_null($meta_info['manufacturers_seo_keywords'])) && (MODULE_HEADER_TAGS_MANUFACTURERS_META_KEYWORDS_STATUS == 'True') ) {
            $oscTemplate->addBlock('<meta name="keywords" content="' . tep_output_string($meta_info['manufacturers_seo_keywords']) . '" />' . "\n", $this->group);
          }

        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_MANUFACTURERS_META_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Manufacturer Meta Module', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_STATUS', 'True', 'Do you want to allow Category meta tags to be added to the page header?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Category Meta Description', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_DESCRIPTION_STATUS', 'True', 'Manufacturer Descriptions help your site and your sites visitors.', '6', '1', 'tep_cfg_select_option(array(\'True\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Category Meta Keywords', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_KEYWORDS_STATUS', 'False', 'Manufacturer Keywords are pointless.  If you are into the Chinese Market select True (for Baidu Search Engine) otherwise select False.', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_MANUFACTURERS_META_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_DESCRIPTION_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_KEYWORDS_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_META_SORT_ORDER');
    }
  }
?>
