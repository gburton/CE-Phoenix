<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_gdpr_site_details extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_GDPR_SITE_DETAILS_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $port_my_data, $customer;

      $content_width = (int)MODULE_CONTENT_GDPR_SITE_DETAILS_CONTENT_WIDTH;

      $r_data_query = tep_db_query("SELECT COUNT(*) AS reviews_count FROM reviews WHERE customers_id = " . (int)$_SESSION['customer_id']);
      $r_data = tep_db_fetch_array($r_data_query);

      $pn_data_query = tep_db_query("SELECT COUNT(*) AS notifications_count FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id']);
      $pn_data = tep_db_fetch_array($pn_data_query);

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
      $port_my_data['YOU']['REVIEW']['COUNT'] = $r_data['reviews_count'];
      $port_my_data['YOU']['NOTIFICATION']['COUNT'] = $pn_data['notifications_count'];

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_GDPR_SITE_DETAILS_STATUS' => [
          'title' => 'Enable Site Details Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the GDPR page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_GDPR_SITE_DETAILS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_GDPR_SITE_DETAILS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
