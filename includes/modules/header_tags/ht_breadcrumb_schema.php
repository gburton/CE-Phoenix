<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class ht_breadcrumb_schema {
    var $code = 'ht_breadcrumb_schema';
    var $group = 'footer_scripts';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_TITLE;
      $this->description = MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_STATUS == 'True');
      }
    }
    
    function execute() {
      global $oscTemplate, $breadcrumb;
      
      $itemlistelement = array();
      foreach($breadcrumb->_trail as $k => $v) {
        $itemlistelement[] = array('@type' => 'ListItem', 
                                   'position' => $k, 
                                   'item' => array('@id' => $v['link'], 
                                                   'name' => strip_tags($v['title'])));
      }
      
      $schema_breadcrumb = array('@context' => 'http://schema.org', 
                                 '@type' => 'BreadcrumbList', 
                                 'itemListElement' => $itemlistelement);

      $data = json_encode($schema_breadcrumb);

      $oscTemplate->addBlock('<script type="application/ld+json">' . $data . '</script>', $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Breadcrumb Schema Module', 'MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_SORT_ORDER', '900', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_STATUS', 'MODULE_HEADER_TAGS_BREADCRUMB_SCHEMA_SORT_ORDER');
    }    
  }
  