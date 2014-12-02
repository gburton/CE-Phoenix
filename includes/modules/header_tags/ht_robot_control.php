<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class ht_robot_control {
    var $code = 'ht_robot_control';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_robot_control() {
      $this->title = MODULE_HEADER_TAGS_ROBOT_CONTROL_TITLE;
      $this->description = MODULE_HEADER_TAGS_ROBOT_CONTROL_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_ROBOT_CONTROL_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_ROBOT_CONTROL_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_ROBOT_CONTROL_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate;
      $oscTemplate->addBlock('<meta name="robots" content="noodp, noydir" />' . "\n", $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_ROBOT_CONTROL_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Robot Control Module', 'MODULE_HEADER_TAGS_ROBOT_CONTROL_STATUS', 'True', 'Do you want to take back control of your snippets that show in SERP\'s (Search Engine Page Results)?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_ROBOT_CONTROL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_ROBOT_CONTROL_STATUS', 'MODULE_HEADER_TAGS_ROBOT_CONTROL_SORT_ORDER');
    }

  }
?>
