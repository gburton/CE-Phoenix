<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_gdpr_site_details {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_GDPR_SITE_DETAILS_TITLE;
      $this->description = MODULE_CONTENT_GDPR_SITE_DETAILS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      
      if ( defined('MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_GDPR_SITE_DETAILS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id;
      global $port_my_data, $customer;
      
      $content_width = (int)MODULE_CONTENT_GDPR_SITE_DETAILS_CONTENT_WIDTH;
      
      $r_data_query = tep_db_query("select count(*) as reviews_count from reviews where customers_id = " . (int)$customer_id);
      $r_data = tep_db_fetch_array($r_data_query);

      $pn_data_query = tep_db_query("select count(*) as notifications_count from products_notifications where customers_id = " . (int)$customer_id);
      $pn_data = tep_db_fetch_array($pn_data_query);
 
      $site_details = array_merge($r_data, $pn_data);
      
      switch ($customer->get('customers_newsletter')) {
        case 1:
        $gdpr_newsletter = MODULE_CONTENT_GDPR_SITE_DETAILS_NEWSLETTER_SUB_YES;
        break;
        default:
        $gdpr_newsletter = MODULE_CONTENT_GDPR_SITE_DETAILS_NEWSLETTER_SUB_NO;
      }

      $port_my_data['YOU']['SITE']['NEWSLETTER'] = $gdpr_newsletter;
      $port_my_data['YOU']['SITE']['ACCOUNTCREATED'] = $customer->get('customers_info_date_account_created');
      $port_my_data['YOU']['SITE']['LOGONS']['COUNT'] = max($customer->get('customers_info_number_of_logons'), 1); 
      $port_my_data['YOU']['SITE']['LOGONS']['MOSTRECENT'] = $customer->get('customers_info_date_of_last_logon') ?? $customer->get('customers_info_date_account_created');
      $port_my_data['YOU']['REVIEW']['COUNT'] = $site_details['reviews_count'];
      $port_my_data['YOU']['NOTIFICATION']['COUNT'] = $site_details['notifications_count'];

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Site Details Module', 'MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS', 'True', 'Should this module be shown on the GDPR page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_GDPR_SITE_DETAILS_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_GDPR_SITE_DETAILS_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS', 'MODULE_CONTENT_GDPR_SITE_DETAILS_CONTENT_WIDTH', 'MODULE_CONTENT_GDPR_SITE_DETAILS_SORT_ORDER');
    }
  }
  