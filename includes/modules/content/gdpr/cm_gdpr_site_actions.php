<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_gdpr_site_actions {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_GDPR_SITE_ACTIONS_TITLE;
      $this->description = MODULE_CONTENT_GDPR_SITE_ACTIONS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_GDPR_SITE_ACTIONS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_GDPR_SITE_ACTIONS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_GDPR_SITE_ACTIONS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id;
      global $port_my_data;
      
      $content_width = (int)MODULE_CONTENT_GDPR_SITE_ACTIONS_CONTENT_WIDTH;
      
      $actions_query = tep_db_query("select * from action_recorder where user_id = '" . (int)$customer_id . "' and module != 'ar_admin_login' order by id desc");
      
      $num_actions = tep_db_num_rows($actions_query);
      
      if ($num_actions) {
        $port_my_data['YOU']['ACTIONS']['COUNT'] = $num_actions;
        $a = 1;
        while ($actions = tep_db_fetch_array($actions_query)) {
          $port_my_data['YOU']['ACTIONS']['LIST'][$a]['ACTION'] = constant($actions['module']);
          $port_my_data['YOU']['ACTIONS']['LIST'][$a]['DATE'] = $actions['date_added'];
          $a++;
        }
        
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_GDPR_SITE_ACTIONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Site Actions Module', 'MODULE_CONTENT_GDPR_SITE_ACTIONS_STATUS', 'True', 'Should this module be shown on the GDPR page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_GDPR_SITE_ACTIONS_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_GDPR_SITE_ACTIONS_SORT_ORDER', '225', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_GDPR_SITE_ACTIONS_STATUS', 'MODULE_CONTENT_GDPR_SITE_ACTIONS_CONTENT_WIDTH', 'MODULE_CONTENT_GDPR_SITE_ACTIONS_SORT_ORDER');
    }
  }
  