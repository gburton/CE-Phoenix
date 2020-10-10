<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_account_gdpr {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      global $language;

      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_ACCOUNT_GDPR_TITLE;
      $this->description = MODULE_CONTENT_ACCOUNT_GDPR_DESCRIPTION;

      if ( defined('MODULE_CONTENT_ACCOUNT_GDPR_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_ACCOUNT_GDPR_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_ACCOUNT_GDPR_STATUS == 'True');
      }

      $this->public_title = MODULE_CONTENT_ACCOUNT_GDPR_LINK_TITLE;
    }

    function execute() {
     global $oscTemplate, $customer;

      if (isset($_SESSION['customer_id'])) {
        $geo_location = $customer->get('countries_id');

        $oscTemplate->_data['account']['gdpr'] = array('title' => $this->public_title,
                                                       'sort_order' => 100,
                                                       'links' => array());

        if (strlen(MODULE_CONTENT_ACCOUNT_GDPR_COUNTRIES) > 0) {
          $eu_countries = explode(';', MODULE_CONTENT_ACCOUNT_GDPR_COUNTRIES);

          if (in_array($geo_location, $eu_countries)) {
            $oscTemplate->_data['account']['gdpr']['links'][$this->group] = array('title' => MODULE_CONTENT_ACCOUNT_GDPR_SUB_TITLE, 'link' => tep_href_link('gdpr.php', '', 'SSL'), 'icon' => 'fa fa-user-secret fa-5x');
          }
        }
        else {
          $oscTemplate->_data['account']['gdpr']['links'][$this->group] = array('title' => MODULE_CONTENT_ACCOUNT_GDPR_SUB_TITLE, 'link' => tep_href_link('gdpr.php', '', 'SSL'), 'icon' => 'fa fa-user-secret fa-5x');
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_ACCOUNT_GDPR_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable GDPR Link', 'MODULE_CONTENT_ACCOUNT_GDPR_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Countries', 'MODULE_CONTENT_ACCOUNT_GDPR_COUNTRIES', '', 'Restrict the Link to Account Holders in these Countries.  Leave Blank to show link to all Countries!', '6', '2', 'gdpr_show_countries', 'gdpr_select_countries(', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_ACCOUNT_GDPR_SORT_ORDER', '10', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_ACCOUNT_GDPR_STATUS', 'MODULE_CONTENT_ACCOUNT_GDPR_COUNTRIES', 'MODULE_CONTENT_ACCOUNT_GDPR_SORT_ORDER');
    }
  }

  function gdpr_get_country_name($key) {
    $country_query = tep_db_query("select countries_name from countries where countries_id = " .(int)$key . " limit 1");
    $country = tep_db_fetch_array($country_query);

    return $country['countries_name'] ?? null;
  }

  function gdpr_show_countries($text) {
    $countries = explode(';', $text);

    $output_countries = array();
    foreach ($countries as $k) {
      $output_countries[] = gdpr_get_country_name($k);
    }

    return nl2br(implode("\n", $output_countries));
  }

  function gdpr_select_countries($values, $key) {
    $countries_array = tep_get_countries();

    $values_array = explode(';', $values);

    $output = '';
    foreach ($countries_array as $countries) {
      $output .= tep_draw_checkbox_field('gdpr_selected_countries[]', $countries['id'], in_array($countries['id'], $values_array)) . '&nbsp;' . tep_output_string($countries['text']) . '<br />';
    }

    if (!empty($output)) {
      $output = '<br />' . substr($output, 0, -6);
    }

    $output .= tep_draw_hidden_field('configuration[' . $key . ']', '', 'id="gdpr_countrys"');

    $output .= '<script>
                function gdpr_update_cfg_value() {
                  var gdpr_selected_countries = \'\';

                  if ($(\'input[name="gdpr_selected_countries[]"]\').length > 0) {
                    $(\'input[name="gdpr_selected_countries[]"]:checked\').each(function() {
                      gdpr_selected_countries += $(this).attr(\'value\') + \';\';
                    });

                    if (gdpr_selected_countries.length > 0) {
                      gdpr_selected_countries = gdpr_selected_countries.substring(0, gdpr_selected_countries.length - 1);
                    }
                  }

                  $(\'#gdpr_countrys\').val(gdpr_selected_countries);
                }

                $(function() {
                  gdpr_update_cfg_value();

                  if ($(\'input[name="gdpr_selected_countries[]"]\').length > 0) {
                    $(\'input[name="gdpr_selected_countries[]"]\').change(function() {
                      gdpr_update_cfg_value();
                    });
                  }
                });
                </script>';

    return $output;
  }
