<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class ht_manufacturer_seo {
    var $code = 'ht_manufacturer_seo';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_MANUFACTURERS_SEO_TITLE;
      $this->description = MODULE_HEADER_TAGS_MANUFACTURERS_SEO_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_MANUFACTURERS_SEO_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS == 'True');
      }
    }

    function execute() {
       global $PHP_SELF, $oscTemplate, $manufacturers, $languages_id;

      if (basename($PHP_SELF) == 'index.php') {
        if (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {
          $meta_info_query = tep_db_query("select manufacturers_seo_description, manufacturers_seo_keywords from manufacturers_info where manufacturers_id = '" . (int)$_GET['manufacturers_id']  . "' and languages_id = '" . (int)$languages_id . "'");
          $meta_info = tep_db_fetch_array($meta_info_query);

          if (tep_not_null($meta_info['manufacturers_seo_description'])) {
            $oscTemplate->addBlock('<meta name="description" content="' . tep_output_string($meta_info['manufacturers_seo_description']) . '" />' . PHP_EOL, $this->group);
          }
          if ( (tep_not_null($meta_info['manufacturers_seo_keywords'])) && (MODULE_HEADER_TAGS_MANUFACTURERS_SEO_KEYWORDS_STATUS == 'True') ) {
            $oscTemplate->addBlock('<meta name="keywords" content="' . tep_output_string($meta_info['manufacturers_seo_keywords']) . '" />' . PHP_EOL, $this->group);
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Manufacturer Meta Module', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS', 'True', 'Do you want to allow Manufacturer meta tags to be added to the page header?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Manufacturer Meta Description', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_DESCRIPTION_STATUS', 'True', 'Manufacturer Descriptions help your site and your sites visitors.', '6', '1', 'tep_cfg_select_option(array(\'True\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Manufacturer Meta Keywords', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_KEYWORDS_STATUS', 'False', 'Manufacturer Keywords are almost pointless.  If you are into the Chinese Market select True (for Baidu Search Engine) otherwise select False.', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_SORT_ORDER', '110', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_DESCRIPTION_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_KEYWORDS_STATUS', 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_SORT_ORDER');
    }
  }
  