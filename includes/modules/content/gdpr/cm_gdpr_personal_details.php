<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_gdpr_personal_details {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_TITLE;
      $this->description = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_DESCRIPTION;
      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_GDPR_PERSONAL_DETAILS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_GDPR_PERSONAL_DETAILS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $port_my_data, $customer;
      
      $content_width = (int)MODULE_CONTENT_GDPR_PERSONAL_DETAILS_CONTENT_WIDTH;
      
      $gdpr_fname = $port_my_data['YOU']['PERSONAL']['FNAME'] = $customer->get('firstname');         
      $gdpr_lname = $port_my_data['YOU']['PERSONAL']['LNAME'] = $customer->get('lastname');         
      switch ($customer->get('gender')) {
        case "m":
        $gdpr_gender = $port_my_data['YOU']['PERSONAL']['GENDER'] = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_GENDER_M;
        break;
        case "f":
        $gdpr_gender = $port_my_data['YOU']['PERSONAL']['GENDER'] = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_GENDER_F;
        break;
        default:
        $gdpr_gender = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_UNKNOWN;
        $port_my_data['YOU']['PERSONAL']['GENDER'] = MODULE_CONTENT_GDPR_PERSONAL_DETAILS_UNKNOWN;
      }
      
      $bad_dates = ['0000-00-00 00:00:00', '1970-01-01 00:00:01'];
      $gdpr_dob = (in_array($customer->get('customers_dob'), $bad_dates)) ? MODULE_CONTENT_GDPR_PERSONAL_DETAILS_UNKNOWN : tep_date_short($customer->get('customers_dob'));
      $port_my_data['YOU']['PERSONAL']['DOB'] = $gdpr_dob;
      
      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_GDPR_PERSONAL_DETAILS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Personal Details Module', 'MODULE_CONTENT_GDPR_PERSONAL_DETAILS_STATUS', 'True', 'Should this module be shown on the GDPR page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_GDPR_PERSONAL_DETAILS_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_GDPR_PERSONAL_DETAILS_SORT_ORDER', '100', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_GDPR_PERSONAL_DETAILS_STATUS', 'MODULE_CONTENT_GDPR_PERSONAL_DETAILS_CONTENT_WIDTH', 'MODULE_CONTENT_GDPR_PERSONAL_DETAILS_SORT_ORDER');
    }
  }
  