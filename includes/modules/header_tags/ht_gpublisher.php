<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class ht_gpublisher {
    var $code = 'ht_gpublisher';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_GPUBLISHER_TITLE;
      $this->description = MODULE_HEADER_TAGS_GPUBLISHER_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_GPUBLISHER_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GPUBLISHER_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate;

      $oscTemplate->addBlock('<link rel="publisher" href="' . tep_output_string(MODULE_HEADER_TAGS_GPUBLISHER_ID) . '" />' . PHP_EOL, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_GPUBLISHER_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable G+ Publisher Module', 'MODULE_HEADER_TAGS_GPUBLISHER_STATUS', 'True', 'Add G+ Publisher Link to your shop?  You MUST have a BUSINESS G+ account.  Once installed and configured, don\'t forget to link your G+ page back to your website.<br><br><b>Helper Links:</b><br>http://www.google.com/+/business/<br>http://www.advancessg.com/googles-relpublisher-tag-is-for-all-business-and-brand-websites-not-just-publishers/', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('G+ Publisher Address', 'MODULE_HEADER_TAGS_GPUBLISHER_ID', '', 'Your G+ URL.', '6', '0', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_GPUBLISHER_STATUS', 'MODULE_HEADER_TAGS_GPUBLISHER_ID', 'MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER');
    }
  }
  
