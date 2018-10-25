<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class cm_login_form {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_LOGIN_FORM_TITLE;
      $this->description = MODULE_CONTENT_LOGIN_FORM_DESCRIPTION;

      if ( defined('MODULE_CONTENT_LOGIN_FORM_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_LOGIN_FORM_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_LOGIN_FORM_STATUS == 'True');
      }
    }

    function execute() {
      global $sessiontoken, $login_customer_id, $messageStack, $oscTemplate, $cart, $navigation, $customer_id, $customer_default_address_id, $customer_first_name, $customer_country_id, $customer_zone_id;

      $content_width = (int)MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH;
      
      $error = false;

      if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
        $email_address = tep_db_prepare_input($_POST['email_address']);
        $password = tep_db_prepare_input($_POST['password']);

// Check if email exists
        $customer_query = tep_db_query("select customers_id, customers_password from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' limit 1");
        if (!tep_db_num_rows($customer_query)) {
          $error = true;
        } else {
          $customer = tep_db_fetch_array($customer_query);

// Check that password is good
          if (!tep_validate_password($password, $customer['customers_password'])) {
            $error = true;
          } else {
// set $login_customer_id globally and perform post login code in catalog/login.php
            $login_customer_id = (int)$customer['customers_id'];

// migrate old hashed password to new phpass password
            if (tep_password_type($customer['customers_password']) != 'phpass') {
              tep_db_query("update " . TABLE_CUSTOMERS . " set customers_password = '" . tep_encrypt_password($password) . "' where customers_id = '" . (int)$login_customer_id . "'");
            }
//from login.php
            if ( is_int($login_customer_id) && ($login_customer_id > 0) ) {
              if (SESSION_RECREATE == 'True') {
                tep_session_recreate();
              }

              $customer_info_query = tep_db_query("select c.customers_firstname, c.customers_default_address_id, ab.entry_country_id, ab.entry_zone_id from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " ab on (c.customers_id = ab.customers_id and c.customers_default_address_id = ab.address_book_id) where c.customers_id = '" . (int)$login_customer_id . "'");
              $customer_info = tep_db_fetch_array($customer_info_query);

              $customer_id = $login_customer_id;
              tep_session_register('customer_id');

              $customer_default_address_id = $customer_info['customers_default_address_id'];
              tep_session_register('customer_default_address_id');

              $customer_first_name = $customer_info['customers_firstname'];
              tep_session_register('customer_first_name');

              $customer_country_id = $customer_info['entry_country_id'];
              tep_session_register('customer_country_id');

              $customer_zone_id = $customer_info['entry_zone_id'];
              tep_session_register('customer_zone_id');

              tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1, password_reset_key = null, password_reset_date = null where customers_info_id = '" . (int)$customer_id . "'");

// reset session token
              $sessiontoken = md5(tep_rand() . tep_rand() . tep_rand() . tep_rand());

// restore cart contents
              $cart->restore_contents();

              if (sizeof($navigation->snapshot) > 0) {
                $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
                $navigation->clear_snapshot();
                tep_redirect($origin_href);
              }

              tep_redirect(tep_href_link('index.php'));
            }
          }
        }
      }

      if ($error == true) {
        $messageStack->add('login', MODULE_CONTENT_LOGIN_TEXT_LOGIN_ERROR);
      }

      ob_start();
      include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
      $template = ob_get_clean();

      $oscTemplate->addContent($template, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_LOGIN_FORM_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Login Form Module', 'MODULE_CONTENT_LOGIN_FORM_STATUS', 'True', 'Do you want to enable the login form module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_LOGIN_FORM_SORT_ORDER', '1000', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_LOGIN_FORM_STATUS', 'MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH', 'MODULE_CONTENT_LOGIN_FORM_SORT_ORDER');
    }
  }
  