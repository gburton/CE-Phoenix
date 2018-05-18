<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  class ht_canonical {
    var $code = 'ht_canonical';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_CANONICAL_TITLE;
      $this->description = MODULE_HEADER_TAGS_CANONICAL_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_CANONICAL_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_CANONICAL_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $cPath, $oscTemplate, $category_depth;
      global $current_category_id, $OSCOM_category;
      
      switch (basename($PHP_SELF)) {
        case 'index.php':
          if (isset($cPath) && tep_not_null($cPath) && ($current_category_id > 0) && ($category_depth != 'top')) {
            $canonical = $OSCOM_category->buildBreadcrumb($current_category_id);
           
            $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link('index.php', 'view=all&cPath=' . $canonical, 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
          }
          elseif (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
            $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link('index.php', 'view=all&manufacturers_id=' . (int)$_GET['manufacturers_id'], 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
          }
          else {
            $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link('index.php', null, 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
          }
        break;
        
        case 'product_info.php':
          $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'], 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
        break;
        
        case 'products_new.php':
        case 'specials.php':
          $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link($PHP_SELF, 'view=all', 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
        break;
        
        default: 
          $oscTemplate->addBlock('<link rel="canonical" href="' . tep_href_link($PHP_SELF, '', 'NONSSL', false) . '" />' . PHP_EOL, $this->group);
        break;
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_CANONICAL_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Canonical Module', 'MODULE_HEADER_TAGS_CANONICAL_STATUS', 'True', 'Do you want to enable the Canonical module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_CANONICAL_STATUS', 'MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER');
    }
  }
?>
